<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- Dark mode init — runs BEFORE anything else to prevent flash --}}
    <script>
        (function() {
            var theme = JSON.parse(localStorage.getItem('mary-theme'));
            var cls = JSON.parse(localStorage.getItem('mary-class'));
            if (theme) document.documentElement.setAttribute('data-theme', theme);
            if (cls) document.documentElement.setAttribute('class', cls);
        })();
    </script>

    @php $title = __('Iniciar Sesión') @endphp
    @include('partials.head')

    {{-- Design system fonts & icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
            .glass-effect {
                background: color-mix(in srgb, var(--surface-container-lowest) 70%, transparent);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid color-mix(in srgb, var(--outline-variant) 30%, transparent);
            }
            .toggle:not(:checked) {
                border: 2px solid var(--outline-variant);
                background-color: var(--surface-container-low);
            }
            .toggle-primary:checked {
                --input-color: #3525cd !important;
            }
        </style>
</head>

<body class="bg-surface text-on-surface font-sans h-dvh overflow-hidden selection:bg-primary/20">
    <main class="flex h-full">
        {{-- Left Side: Ambient Lifestyle Imagery (Desktop Only) --}}
        <section class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/img/login-screen.png');">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10"></div>
            </div>
            <div class="relative z-10 flex flex-col justify-end p-[80px] w-full">
                <div class="glass-effect p-[24px] rounded-xl max-w-md shadow-2xl">
                    <h2 class="text-[32px] font-semibold tracking-tight text-on-surface leading-10">Experiencia Curada
                    </h2>
                    <p class="mt-1 text-[18px] leading-7 text-on-surface-variant">Descubre nuestra nueva colección premium
                        diseñada para el estilo de vida contemporáneo.</p>
                </div>
            </div>
        </section>

        {{-- Right Side: Login Canvas --}}
        <section class="w-full lg:w-1/2 flex flex-col bg-surface-container-lowest">
            {{-- Centered form area --}}
            <div class="flex-1 flex flex-col items-center justify-center px-[16px] md:px-[80px] py-6">
                <div class="w-full max-w-[440px]">
                    {{-- Brand Identity --}}
                    <header class="mb-8 flex flex-col items-center lg:items-start">
                        <x-app-logo-icon class="size-10 mb-4 fill-current text-primary" />
                        <h1
                            class="text-[36px] md:text-[48px] font-bold leading-[44px] md:leading-[56px] tracking-tight text-primary mb-1">
                            Bienvenido de nuevo</h1>
                        <p class="text-[18px] leading-7 text-on-surface-variant">Accede a tu cuenta premium</p>
                    </header>

                    {{-- Session Status --}}
                    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" novalidate>
                        @csrf

                        {{-- Email / Username --}}
                        <div class="space-y-1">
                            <label
                                class="text-[14px] font-semibold tracking-[0.05em] text-on-surface-variant flex items-center gap-1"
                                for="email">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                                Usuario
                            </label>
                            <x-mary-input name="email" id="email" :value="old('email')" type="email"
                                placeholder="ejemplo@vibrant.com" required autofocus autocomplete="email"
                                class="bg-surface-container-low border-outline-variant rounded-xl w-full focus-within:outline-hidden"
                                omitError />
                            @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <label
                                class="text-[14px] font-semibold tracking-[0.05em] text-on-surface-variant flex items-center gap-1"
                                for="password">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                Contraseña
                            </label>
                            <div class="relative" x-data="{ show: false }">
                                <x-mary-input name="password" id="password" type="password"
                                    x-bind:type="show ? 'text' : 'password'" placeholder="••••••••" required
                                    autocomplete="current-password"
                                    class="bg-surface-container-low border-outline-variant rounded-xl w-full pr-12 focus-within:outline-hidden"
                                    omitError />
                                <button type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors"
                                    @click="show = !show">
                                    <span class="material-symbols-outlined"
                                        x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Options Row --}}
                        <div class="flex items-center justify-between">
                            <x-mary-toggle name="remember" label="Recuérdame"
                                :checked="old('remember')" class="toggle-primary" />
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-[14px] font-semibold tracking-[0.05em] text-primary hover:underline">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <x-mary-button type="submit" label="Iniciar Sesión" icon="o-arrow-right"
                            class="btn-primary w-full rounded-xl" spinner="login" />
                    </form>

                    {{-- Register Link --}}
                    <p class="mt-8 text-center text-[16px] leading-6 text-on-surface-variant">
                        ¿No tienes una cuenta?
                        <a href="{{ route('register') }}" class="text-primary font-bold hover:underline ml-1">Crear
                            cuenta</a>
                    </p>
                </div>
            </div>

            {{-- Legal Footer Links & Theme Toggle --}}
            <div class="flex flex-col md:flex-row items-center justify-between px-[16px] md:px-[80px] pb-4">
                <div class="flex flex-wrap justify-center gap-4 md:gap-6 opacity-60">
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">Política de
                        Privacidad</a>
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">Términos de
                        Servicio</a>
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">Ayuda</a>
                </div>

                <div class="flex items-center gap-2 mt-2 md:mt-0" x-data>
                    <x-mary-theme-toggle />
                    <span @click="$el.parentElement.querySelector('input[type=checkbox]')?.click()"
                        class="text-[12px] font-medium cursor-pointer hover:text-primary transition-colors select-none opacity-60 hover:opacity-100">
                        Cambiar tema
                    </span>
                </div>
            </div>
        </section>
    </main>

        @livewireScripts
</body>

</html>