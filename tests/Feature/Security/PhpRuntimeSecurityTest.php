<?php

/**
 * Piso de versión de PHP que corrige CVE-2026-109618 (GHSA-w8hw-w9fx-j79w) y
 * CVE-2026-109619 (GHSA-6wmf-4rv5-p3rp). Ambas son "PHP2Shell": RCE sin
 * autenticación en el runtime de PHP, CVSS 9.2.
 *
 * Versiones parcheadas por branch:
 *   8.2 → 8.2.35   ·   8.3 → 8.3.36   ·   8.4 → 8.4.27   ·   8.5 → 8.5.12
 */
const PHP_PATCHED_VERSION = '8.4.27';

/**
 * ¿Estamos corriendo en CI? GitHub Actions expone CI=true.
 */
function runningInCi(): bool
{
    return (bool) (getenv('CI') ?: ($_SERVER['CI'] ?? false) ?: env('CI'));
}

it('runs on a PHP runtime patched against CVE-2026-109618 and CVE-2026-109619', function () {
    $isPatched = version_compare(PHP_VERSION, PHP_PATCHED_VERSION, '>=');

    if (! $isPatched && ! runningInCi()) {
        // En local no bloqueamos el suite: el runtime de desarrollo es una
        // decisión de la máquina, no del repo. Pero queda a la vista.
        $this->markTestSkipped(
            'PHP local ('.PHP_VERSION.') es vulnerable a CVE-2026-109618 / CVE-2026-109619: '
            .'actualizá a '.PHP_PATCHED_VERSION.' o más.'
        );
    }

    expect($isPatched)->toBeTrue(
        'PHP '.PHP_VERSION.' es vulnerable a CVE-2026-109618 / CVE-2026-109619. Requiere >= '.PHP_PATCHED_VERSION.'.'
    );
});

it('pins the container base images to a patched PHP release', function () {
    foreach (['docker/php/Dockerfile', 'docker/php/Dockerfile.prod'] as $path) {
        $contents = file_get_contents(base_path($path));

        expect($contents)->toContain('php:'.PHP_PATCHED_VERSION.'-fpm-alpine');

        // Sin tags flotantes (`FROM php:8.4-fpm-alpine`): se mueven solos y no
        // dejan evidencia de qué versión corría en cada build.
        expect($contents)->not->toMatch('/^\s*FROM\s+php:\d+\.\d+-/m');
    }
});

it('pins the CI workflows to a patched PHP release', function () {
    foreach (['.github/workflows/tests.yml', '.github/workflows/lint.yml'] as $path) {
        $contents = file_get_contents(base_path($path));

        expect($contents)->toContain("php-version: '".PHP_PATCHED_VERSION."'");

        // Nada de `php-version: '8.4'` flotante: puede resolver a una
        // versión vulnerable según cuándo corre el job.
        expect($contents)->not->toMatch("/^\s*php-version:\s*'\d+\.\d+'/m");
    }
});

it('ships the PHP2Shell hardening in the container configuration', function () {
    $phpIni = file_get_contents(base_path('docker/php/php.ini'));

    // session.upload_progress.enabled tiene que estar en Off: ese decoder
    // corre ANTES del script de la aplicación y es el consumidor temprano
    // que envenena el estado global del worker (vía de explotación en FPM).
    expect($phpIni)->toMatch('/^\s*session\.upload_progress\.enabled\s*=\s*Off\s*$/m');

    // No publicar la versión de PHP en X-Powered-By.
    expect($phpIni)->toMatch('/^\s*expose_php\s*=\s*Off\s*$/m');

    // Topes de la request.
    expect($phpIni)->toMatch('/^\s*post_max_size\s*=\s*8M\s*$/m');
    expect($phpIni)->toMatch('/^\s*upload_max_filesize\s*=\s*4M\s*$/m');

    $pool = file_get_contents(base_path('docker/php/php-fpm.d/zz-security-hardening.conf'));

    // pm.max_requests=1: la única forma de que dos requests seguidas nunca
    // caigan en el mismo worker. El exploit encadena request que envenena el
    // estado + request que lo explota; con N>1 esa ventana sigue abierta.
    expect($pool)->toMatch('/^\s*pm\.max_requests\s*=\s*1\s*$/m');

    // memory_limit=-1 a nivel de pool: ninguna request puede abortar por OOM
    // en mitad de una inicialización (disparador del CVE-2026-109618).
    expect($pool)->toMatch('/^\s*php_admin_value\[memory_limit\]\s*=\s*-1\s*$/m');

    // Defensa en profundidad: el pool repite el bloqueo del upload progress.
    expect($pool)->toMatch('/^\s*php_admin_flag\[session\.upload_progress\.enabled\]\s*=\s*off\s*$/m');
});

it('bounds the container memory with a cgroup limit instead of memory_limit', function () {
    $compose = file_get_contents(base_path('docker-compose.prod.yml'));

    expect($compose)->toMatch('/^\s*mem_limit:\s*(\S+)\s*$/m');
    expect($compose)->toMatch('/^\s*memswap_limit:\s*(\S+)\s*$/m');

    preg_match('/^\s*mem_limit:\s*(\S+)\s*$/m', $compose, $memory);
    preg_match('/^\s*memswap_limit:\s*(\S+)\s*$/m', $compose, $swap);

    // memswap == mem desactiva el swap a propósito: el OOM killer actúa
    // de forma determinista en vez de dejar al worker swapeando.
    expect($swap[1])->toBe($memory[1]);
});

it('aligns the nginx request limits with the PHP limits', function () {
    $nginx = file_get_contents(base_path('docker/nginx/lion.cortez-dev.com.conf'));
    $phpIni = file_get_contents(base_path('docker/php/php.ini'));

    preg_match('/^\s*post_max_size\s*=\s*(\S+)/m', $phpIni, $postMaxSize);

    // Si nginx aceptara un body más grande que post_max_size, PHP vería un
    // body recortado y el tamaño real quedaría fuera del control de la app.
    expect($nginx)->toMatch(
        '/^\s*client_max_body_size\s+'.preg_quote($postMaxSize[1], '/').'\s*;/m'
    );
});

it('gates the deploy on a patched PHP runtime', function () {
    $deploy = file_get_contents(base_path('.github/workflows/deploy.yml'));

    // El deploy tiene que CORTAR si el runtime del contenedor no está
    // parcheado, en vez de dejar un worker vulnerable expuesto.
    expect($deploy)->toContain('SECURITY GATE FAILED');
    expect($deploy)->toContain(PHP_PATCHED_VERSION);
});
