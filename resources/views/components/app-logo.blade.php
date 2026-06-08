@props([
    'sidebar' => false,
])

<a {{ $attributes->merge(['href' => '#']) }}>
    <div class="flex items-center gap-2">
        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-primary">
            <x-app-logo-icon class="size-5 fill-current text-white" />
        </div>
        @if($sidebar)
            <span class="font-semibold text-sm truncate">{{ config('app.name', 'Store') }}</span>
        @endif
    </div>
</a>
