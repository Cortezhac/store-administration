<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <x-mary-menu activate-by-route active-bg-color="bg-surface-container-high">
            <x-mary-menu-item title="{{ __('profile.profile') }}" route="profile.edit" />
            <x-mary-menu-item title="{{ __('profile.security_settings') }}" route="security.edit" />
            <x-mary-menu-item title="{{ __('profile.appearance') }}" route="appearance.edit" />
        </x-mary-menu>
    </div>

    <hr class="border-outline-variant md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-2xl font-bold tracking-tight text-on-surface">{{ $heading ?? '' }}</h2>
        <p class="mt-1 text-on-surface-variant">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
