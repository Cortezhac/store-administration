@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'flex items-center gap-2 text-on-surface-variant text-xs font-medium mb-2']) }}>
    @foreach ($items as $item)
        @if (!$loop->first)
            <flux:icon.chevron-right class="size-4" />
        @endif

        @if (isset($item['route']))
            <a
                href="{{ $item['route'] }}"
                wire:navigate
                class="hover:text-primary transition-colors"
            >
                {{ $item['label'] }}
            </a>
        @else
            <span @class(['text-primary font-bold' => $loop->last])>
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
