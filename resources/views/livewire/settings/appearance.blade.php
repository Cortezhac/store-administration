<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('profile.appearance_settings') }}</h2>

    <x-settings.layout :heading="__('profile.appearance')" :subheading=" __('profile.update_appearance')">
        <div
            x-data
            class="flex flex-wrap gap-2"
        >
            <template x-for="opt in ['light', 'dark', 'system']" :key="opt">
                <button
                    type="button"
                    @click="$store.theme.setTheme(opt)"
                    :class="$store.theme.appearance === opt ? 'btn-primary' : 'btn-ghost'"
                    class="btn"
                >
                    <x-mary-icon x-show="opt === 'light'" name="o-sun" />
                    <x-mary-icon x-show="opt === 'dark'" name="o-moon" />
                    <x-mary-icon x-show="opt === 'system'" name="o-computer-desktop" />
                    <span x-text="opt.charAt(0).toUpperCase() + opt.slice(1)"></span>
                </button>
            </template>
        </div>
    </x-settings.layout>
</section>
