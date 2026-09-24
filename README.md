# Entorno de desarrollo (Podman)

```bash
podman compose up -d --build   # levantar (construye la imagen si falta)
podman compose ps              # estado
podman compose logs -f         # logs
podman compose down            # bajar (la data de la BD persiste en el volumen)
```

- App: http://localhost:8000 · Vite: http://localhost:5173 · DB: localhost:5433 (DBeaver)
- Carga automáticamente `docker-compose.override.yml` (puertos, bind mount y red `web`).
- `podman compose` ejecuta el provider externo `/usr/local/bin/docker-compose`
  (binario standalone) contra el socket de Podman vía `DOCKER_HOST`.

# Servidor de producción (Hetzner VPS, Podman rootful)

El server ya no usa Docker: los 3 proyectos viven en `/opt/docker/apps/` y se
administran con Podman rootful (socket en `/run/podman/podman.sock`).

```
/opt/docker/apps/
├── store-administration/   # este repo (web, db, nginx)
├── landing-page/           # segundo proyecto detrás del proxy
└── nginx-proxy/            # proxy central (80/443, los certificados NO se versionan)
```

El usuario de deploy SSH es `root`. Los comandos de deploy quedaron en
`/etc/profile.d/podman-compose.sh`: exporta `DOCKER_HOST` y define el alias
`compose-store` (compose con los TRES archivos, ver abajo por qué).

## Lanzar el stack (orden estricto)

El proxy central **siempre al final**: nginx resuelve sus upstreams
(`store-nginx`, `landing-page`) solo al arrancar, y muere con
`[emerg] host not found in upstream` si las apps no están DNS-resolviendo.

```bash
# 1) App (db → web → nginx). El service nginx vive SOLO en docker-compose.prod.yml,
#    por eso los tres archivos; compose NO mergea .prod.yml por defecto.
compose-store up -d --build

# 2) Segundo proyecto
cd /opt/docker/apps/landing-page && podman compose up -d --build

# 3) Proxy central SIEMPRE ÚLTIMO
cd /opt/docker/nginx-proxy && podman compose up -d
```

`compose-store` = `podman compose -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.prod.yml`.

## Deploy automático (GitHub Actions)

`.github/workflows/deploy.yml` deployea este repo por SSH al server: pull →
`up -d --build` → migraciones + caches → `podman restart nginx-proxy` (por si una
recreación cambió la IP en la red `web`) → smoke test HTTP → `image prune`.

## Diagnóstico rápido

- DNS interno: registros de aardvark en
  `/run/containers/networks/aardvark-dns/web` — si un nombre no está, ese
  contenedor no corre (o se perdió un `compose up` con los 3 `-f`).
- Firewall: UFW necesita las 3 reglas del pool de redes de Podman
  (`ufw allow from <pool>` + `route allow from/to`) — sin ellas cae el
  DNS interno y los puertos publicados.
- Tras un reboot los contenedores vuelven solos (podman.service habilitado,
  restart policy `unless-stopped`); si alguno quedó en `Created`, lanzar el
  orden de arriba.

# Notas sobre el runtime (Podman)

- Docker no está instalado: `podman compose` delega en el binario standalone
  `docker-compose` v5.5.1, que habla con el socket de Podman vía `DOCKER_HOST`
  (definida en `~/.zshrc` / `~/.bashrc`).
- Requiere el socket de Podman activo: `systemctl --user enable --now podman.socket`
  (local, rootless) o `systemctl enable --now podman.socket` (server, rootful).
- La red externa `web` (`external: true` en el compose) debe existir:
  `podman network create web`
- Backups pre-cutover del server en `/root/backups/` (pg_dump + volúmenes).