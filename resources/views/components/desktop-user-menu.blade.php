@props([
    'name' => null,
])

<x-mary-dropdown>
    <x-slot:trigger>
        <button {{ $attributes->merge(['class' => 'flex items-center gap-3 w-full px-2 py-2 rounded-lg hover:bg-surface-container-high active:bg-surface-container-highest transition-colors']) }} data-test="sidebar-menu-button">
            <x-mary-avatar :placeholder="auth()->user()->initials()" class="!w-8 !rounded-full !bg-primary !text-primary-content" />
            {{-- Desktop: name, email, chevron --}}
            <div class="lg:block flex-1 text-left min-w-0 w-36">
                <div class="text-sm font-semibold truncate text-on-surface">{{ auth()->user()->name }}</div>
                <div class="text-xs truncate text-on-surface-variant">{{ auth()->user()->email }}</div>
            </div>
            <x-mary-icon name="o-chevron-double-down" class="lg:block w-4 h-4 text-on-surface-variant" />
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

    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-surface-container-high active:bg-surface-container-highest" wire:navigate>
        <x-mary-icon name="o-cog-6-tooth" class="w-5 h-5" />
        {{ __('Settings') }}
    </a>

    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-surface-container-high active:bg-surface-container-highest" data-test="logout-button">
            <x-mary-icon name="o-arrow-right-start-on-rectangle" class="w-5 h-5" />
            {{ __('Log out') }}
        </button>
    </form>
</x-mary-dropdown>
