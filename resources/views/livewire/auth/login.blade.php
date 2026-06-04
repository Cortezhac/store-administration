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

    @php $title = __('login.title') @endphp
    @include('partials.head')

    {{-- Design system font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

        <style>
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
                    <h2 class="text-[32px] font-semibold tracking-tight text-on-surface leading-10">{{ __('login.curated_experience') }}
                    </h2>
                    <p class="mt-1 text-[18px] leading-7 text-on-surface-variant">{{ __('login.curated_description') }}</p>
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
                            {{ __('login.welcome_back') }}</h1>
                        <p class="text-[18px] leading-7 text-on-surface-variant">{{ __('login.access_premium') }}</p>
                    </header>

                    {{-- Session Status --}}
                    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" novalidate>
                        @csrf

                        {{-- Email / Username --}}
                        <x-mary-input :label="__('login.email')" name="email" icon="o-user" :value="old('email')"
                            type="email" :placeholder="__('login.email_placeholder')" required autofocus
                            autocomplete="email" error-field="email"
                            class="bg-surface-container-low border-outline-variant rounded-xl w-full focus-within:outline-hidden" />

                        {{-- Password --}}
                        <x-mary-password :label="__('login.password')" name="password" icon="o-lock-closed"
                            :placeholder="__('login.password_placeholder')" required autocomplete="current-password"
                            error-field="password" right
                            class="bg-surface-container-low border-outline-variant rounded-xl w-full focus-within:outline-hidden" />

                        {{-- Options Row --}}
                        <div class="flex items-center justify-between">
                            <x-mary-toggle name="remember" :label="__('login.remember_me')"
                                :checked="old('remember')" class="toggle-primary" />
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-[14px] font-semibold tracking-[0.05em] text-primary hover:underline">
                                    {{ __('login.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <x-mary-button type="submit" :label="__('login.submit')" icon="o-arrow-right"
                            class="btn-primary w-full rounded-xl" />
                    </form>

                    {{-- Register Link --}}
                    <p class="mt-8 text-center text-[16px] leading-6 text-on-surface-variant">
                        {{ __('login.no_account') }}
                        <a href="{{ route('register') }}" class="text-primary font-bold hover:underline ml-1">{{ __('login.create_account') }}</a>
                    </p>
                </div>
            </div>

            {{-- Legal Footer Links & Theme Toggle --}}
            <div class="flex flex-col md:flex-row items-center justify-between px-[16px] md:px-[80px] pb-4">
                <div class="flex flex-wrap justify-center gap-4 md:gap-6 opacity-60">
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">{{ __('login.privacy_policy') }}</a>
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">{{ __('login.terms_of_service') }}</a>
                    <a class="text-[12px] font-medium hover:text-primary transition-colors" href="#">{{ __('login.help') }}</a>
                </div>

                <div class="flex items-center gap-2 mt-2 md:mt-0" x-data>
                    <x-mary-theme-toggle />
                    <span @click="$el.parentElement.querySelector('input[type=checkbox]')?.click()"
                        class="text-[12px] font-medium cursor-pointer hover:text-primary transition-colors select-none opacity-60 hover:opacity-100">
                        {{ __('login.toggle_theme') }}
                    </span>
                </div>
            </div>
        </section>
    </main>

        @livewireScripts
</body>

</html>