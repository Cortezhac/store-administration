@props([
    'rows',
    'paginator' => null,
    'headers' => [],
])

<div
    {{ $attributes->merge(['class' => 'bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden']) }}
>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant">
                @foreach ($headers as $header)
                    <th
                        @if (is_array($header))
                            class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider {{ $header['class'] ?? '' }}"
                        @else
                            class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider {{ $loop->last ? 'text-right' : '' }}"
                        @endif
                    >
                        {{ is_array($header) ? $header['label'] : $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            {{ $slot }}
        </tbody>
    </table>

    {{-- Empty State --}}
    @if ($rows instanceof \Illuminate\Support\Collection || $rows instanceof \Illuminate\Pagination\AbstractPaginator)
        @if ($rows->isEmpty())
            <div class="px-6 py-12 text-center">
                {{ $empty ?? '' }}
            </div>
        @endif
    @endif

    {{-- Pagination --}}
    @if ($paginator && $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $paginator->hasPages())
        <div class="bg-surface-container-low px-6 py-4 flex items-center justify-between border-t border-outline-variant">
            <div class="flex items-center gap-4">
                <div class="text-xs text-on-surface-variant font-medium">
                    {{ __('shared.showing_from_to', [
                        'from' => $paginator->firstItem(),
                        'to' => $paginator->lastItem(),
                        'total' => $paginator->total(),
                    ]) }}
                </div>
                @isset($paginationLeft)
                    <div class="hidden sm:block text-xs">
                        {{ $paginationLeft }}
                    </div>
                @endisset
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous --}}
                <button
                    wire:click="previousPage"
                    @if ($paginator->onFirstPage()) disabled @endif
                    class="p-2 rounded-lg hover:bg-surface-container-high transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="{{ __('shared.previous') }}"
                >
                    <flux:icon.chevron-left class="size-4" />
                </button>

                {{-- Page Numbers --}}
                @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                    @if ($i === $paginator->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-lowest border border-outline-variant text-xs font-semibold text-on-surface">{{ $i }}</span>
                    @else
                        <button
                            wire:click="gotoPage({{ $i }})"
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container-high text-xs font-medium text-on-surface-variant transition-colors"
                        >
                            {{ $i }}
                        </button>
                    @endif
                @endfor

                {{-- Next --}}
                <button
                    wire:click="nextPage"
                    @if (!$paginator->hasMorePages()) disabled @endif
                    class="p-2 rounded-lg hover:bg-surface-container-high transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="{{ __('shared.next') }}"
                >
                    <flux:icon.chevron-right class="size-4" />
                </button>
            </div>
        </div>
    @endif
</div>
