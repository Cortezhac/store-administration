<div>
    @php($categories = $this->getCategories())

    {{-- Title & Action --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-semibold text-on-surface tracking-tight">{{ __('Gestión de Categorías') }}</h2>
            <p class="text-base text-on-surface-variant mt-1 max-w-2xl">{{ __('Organiza y gestiona las clasificaciones de tus productos para mejorar la navegación y experiencia de compra.') }}</p>
        </div>
        <a
            href="{{ route('category.create') }}"
            wire:navigate
            class="bg-primary hover:bg-primary-container text-on-primary px-6 py-3 rounded-xl font-medium text-sm flex items-center gap-2 shadow-sm transition-all active:scale-95"
        >
            <flux:icon.plus-circle class="size-5" />
            {{ __('Add Category') }}
        </a>
    </div>

    {{-- Table Controls --}}
    <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant shadow-sm mb-4 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="flex items-center gap-2 px-3 py-2 bg-surface-container-low rounded-lg border border-outline-variant">
                <flux:icon.funnel class="size-5 text-on-surface-variant" />
                <select class="bg-transparent border-none focus:ring-0 text-sm text-on-surface-variant cursor-pointer pr-8 outline-none autofill:text-on-surface-variant">
                    <option value="all" class="bg-surface-container-lowest text-on-surface">{{ __('Buscar en todo') }}</option>
                    <option value="name" class="bg-surface-container-lowest text-on-surface">{{ __('Nombre') }}</option>
                    <option value="description" class="bg-surface-container-lowest text-on-surface">{{ __('Descripción') }}</option>
                    <option value="status" class="bg-surface-container-lowest text-on-surface">{{ __('Estado') }}</option>
                </select>
            </div>
            <div class="relative flex-1 md:w-64">
                <input
                    wire:model.live.debounce="search"
                    class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/20 text-sm outline-none"
                    placeholder="{{ __('Filtrar resultados...') }}"
                    type="text"
                />
            </div>
        </div>
        <div class="flex items-center gap-4">
            <p class="text-xs font-medium text-on-surface-variant">
                {{ __('Mostrando :from-:to de :total categorías', [
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                    'total' => $categories->total(),
                ]) }}
            </p>
            @if ($categories->hasPages())
                <div class="flex rounded-lg border border-outline-variant overflow-hidden">
                    <button
                        wire:click="previousPage"
                        @if ($categories->onFirstPage()) disabled @endif
                        class="p-2 hover:bg-surface-container-high border-r border-outline-variant transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    >
                        <flux:icon.chevron-left class="size-5" />
                    </button>
                    <button
                        wire:click="nextPage"
                        @if (!$categories->hasMorePages()) disabled @endif
                        class="p-2 hover:bg-surface-container-high transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    >
                        <flux:icon.chevron-right class="size-5" />
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Categories Data Table --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider w-16">{{ __('Sort') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">{{ __('Name') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider hidden md:table-cell">{{ __('Description') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse ($categories as $row)
                    <tr class="hover:bg-surface-container transition-colors group">
                        <td class="px-6 py-5 text-sm text-on-surface-variant">{{ str_pad($row->sort_order, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                {{-- Icon / Avatar --}}
                                @if ($row->icon)
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-outline-variant">
                                        <img
                                            src="{{ $row->iconUrl() }}"
                                            alt="{{ $row->name }}"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-semibold text-primary">{{ strtoupper(substr($row->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('category.show', $row) }}" wire:navigate class="font-medium text-sm text-on-surface hover:text-primary transition-colors">
                                        {{ $row->name }}
                                    </a>
                                    @if ($row->parent)
                                        <p class="text-xs text-on-surface-variant mt-0.5">{{ $row->parent->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-on-surface-variant hidden md:table-cell">
                            {{ $row->description ? Str::limit($row->description, 100) : __('No description') }}
                        </td>
                        <td class="px-6 py-5">
                            @if ($row->is_active)
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary border border-primary/20">
                                    {{ __('Active') }}
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-surface-variant text-on-surface-variant border border-outline-variant">
                                    {{ __('Inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a
                                    href="{{ route('category.edit', $row) }}"
                                    wire:navigate
                                    class="p-2 text-on-surface-variant hover:text-primary transition-colors rounded-lg hover:bg-surface-container-high inline-flex"
                                >
                                    <flux:icon.pencil class="size-5" />
                                </a>
                                <button
                                    wire:click="delete({{ $row->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this category?') }}"
                                    class="p-2 text-on-surface-variant hover:text-error transition-colors rounded-lg hover:bg-surface-container-high"
                                >
                                    <flux:icon.trash class="size-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <flux:icon.tag class="size-12 text-on-surface-variant" />
                            <p class="mt-2 text-on-surface-variant">{{ __('No categories found.') }}</p>
                            <a href="{{ route('category.create') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 text-primary hover:underline text-sm font-medium">
                                <flux:icon.plus-circle class="size-4" />
                                {{ __('Create your first category') }}
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination Footer --}}
        @if ($categories->hasPages())
            <div class="bg-surface-container-low p-4 flex items-center justify-between border-t border-outline-variant">
                <a href="{{ route('category.index') }}" wire:navigate class="text-sm font-medium text-primary hover:underline transition-all">
                    {{ __('Ver todas las categorías') }}
                </a>
                <div class="flex gap-2">
                    @for ($i = 1; $i <= $categories->lastPage(); $i++)
                        @if ($i === $categories->currentPage())
                            <span class="px-3 py-1 bg-surface-container-lowest border border-outline-variant rounded text-xs font-medium">{{ $i }}</span>
                        @else
                            <button
                                wire:click="gotoPage({{ $i }})"
                                class="px-3 py-1 hover:bg-surface-container-high rounded text-xs font-medium transition-colors"
                            >
                                {{ $i }}
                            </button>
                        @endif
                    @endfor
                </div>
            </div>
        @endif
    </div>
</div>
