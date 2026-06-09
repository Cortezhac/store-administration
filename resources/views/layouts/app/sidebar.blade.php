@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>
            (function() {
                var theme = JSON.parse(localStorage.getItem('mary-theme'));
                var cls = JSON.parse(localStorage.getItem('mary-class'));
                if (theme) document.documentElement.setAttribute('data-theme', theme);
                if (cls) document.documentElement.setAttribute('class', cls);
            })();
        </script>

        @include('partials.head')
    </head>
    <body class="min-h-screen bg-surface text-on-surface">
        <div class="drawer lg:drawer-open">
            <input id="app-drawer" type="checkbox" class="drawer-toggle" />

            <div class="drawer-content flex flex-col">
                <header class="flex items-center justify-between lg:hidden px-4 py-3 border-b border-outline-variant bg-surface-container-low">
                    <div class="flex items-center gap-2">
                        <label for="app-drawer" class="btn btn-ghost btn-circle">
                            <x-mary-icon name="o-bars-3" class="w-6 h-6" />
                        </label>
                        <x-app-logo href="{{ route('dashboard') }}" wire:navigate />
                    </div>

                    <x-mary-dropdown>
                        <x-slot:trigger>
                            <button class="btn btn-ghost btn-circle">
                                <x-mary-avatar :placeholder="auth()->user()->initials()" class="!w-8 !rounded-full !bg-primary !text-primary-content" />
                            </button>
                        </x-slot:trigger>
                        <div class="flex items-center gap-2 px-4 py-2">
                            <x-mary-avatar :placeholder="auth()->user()->initials()" class="!bg-primary !text-primary-content" />
                            <div>
                                <div class="font-semibold">{{ auth()->user()->name }}</div>
                                <div class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <hr class="border-outline-variant" />
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-base-200" wire:navigate>
                            <x-mary-icon name="o-cog-6-tooth" class="w-5 h-5" />
                            {{ __('Settings') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-base-200" data-test="logout-button">
                                <x-mary-icon name="o-arrow-right-start-on-rectangle" class="w-5 h-5" />
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </x-mary-dropdown>
                </header>

                <main class="flex-1 p-6 lg:p-10">
                    {{ $slot }}
                </main>
            </div>

            <div class="drawer-side z-30">
                <label for="app-drawer" aria-label="close sidebar" class="drawer-overlay"></label>

                <aside class="flex flex-col w-64 h-full bg-surface-container-low border-r border-outline-variant">
                    <div class="flex items-center justify-between px-4 py-5 border-b border-outline-variant">
                        <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                    </div>

                    <nav class="flex-1 overflow-y-auto px-2 py-4">
                        <x-mary-menu activate-by-route active-bg-color="bg-surface-container-high">
                            <x-mary-menu-item title="{{ __('Dashboard') }}" icon="o-home" route="dashboard" />
                            <x-mary-menu-sub title="{{ __('Categories') }}" icon="o-bookmark">
                                <x-mary-menu-item title="{{ __('List') }}" icon="o-list-bullet" route="category.index" />
                            </x-mary-menu-sub>
                        </x-mary-menu>
                    </nav>

                    <div class="border-t border-outline-variant px-2 py-2">
                        <x-desktop-user-menu />
                    </div>
                </aside>
            </div>
        </div>

        @persist('toast')
            <x-mary-toast position="toast-top toast-end" />
        @endpersist

        <script>
            document.addEventListener('alpine:init', () => {
                // Sync mary-appearance FROM mary-theme BEFORE Alpine persist reads it.
                // This ensures that when the user changes theme on the login page
                // (via x-mary-theme-toggle which manages mary-theme/mary-class), 
                // the dashboard's Alpine store respects that choice instead of 
                // defaulting to 'system' and overriding with OS preference.
                const _maryTheme = localStorage.getItem('mary-theme');
                if (_maryTheme) {
                    localStorage.setItem('mary-appearance', _maryTheme);
                }

                Alpine.store('theme', {
                    appearance: Alpine.$persist('system').as('mary-appearance'),

                    get isDark() {
                        return this.appearance === 'dark' ||
                            (this.appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    },

                    apply() {
                        const dark = this.isDark;
                        document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark', dark);
                        localStorage.setItem('mary-theme', JSON.stringify(dark ? 'dark' : 'light'));
                        localStorage.setItem('mary-class', JSON.stringify(dark ? 'dark' : ''));
                    },

                    setTheme(val) {
                        this.appearance = val;
                        this.apply();
                    }
                });

                // Sync theme right after store is created (handles persisted value)
                Alpine.store('theme').apply();
            });

            // Re-sync theme after each Livewire SPA navigation
            document.addEventListener('livewire:navigated', () => {
                if (Alpine.store('theme')) {
                    Alpine.store('theme').apply();
                }
            });

            // Listen for OS color scheme changes (handles 'system' appearance)
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (Alpine.store('theme') && Alpine.store('theme').appearance === 'system') {
                    Alpine.store('theme').apply();
                }
            });
        </script>
        @livewireScripts
    </body>
</html>
