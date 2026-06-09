<div>
    <div class="flex items-center justify-between mb-6">
@php($categories = $this->getCategories())

<div>
            <h1 class="text-2xl font-semibold text-on-surface">{{ __('Categories') }}</h1>
            <p class="text-on-surface-variant">{{ __('Manage your product categories') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4 mb-4">
        <x-mary-input
            icon="o-magnifying-glass"
            placeholder="{{ __('Search categories...') }}"
            wire:model.live.debounce="search"
            class="max-w-xs"
        />
        <x-mary-toggle
            wire:model.live="activeOnly"
            :label="__('Active only')"
        />
    </div>

    {{-- Categories table --}}
    <x-mary-table
        :headers="[
            ['key' => 'name', 'label' => __('Name')],
            ['key' => 'parent.name', 'label' => __('Parent'), 'disableLink' => true],
            ['key' => 'sort_order', 'label' => __('Order')],
            ['key' => 'is_active', 'label' => __('Active')],
        ]"
        :rows="$categories"
        link="category/{id}/edit"
        :with-pagination="true"
        class="table-zebra"
    >
        {{-- Active status badge --}}
        @scope('cell_is_active', $row)
            <x-mary-badge
                :value="$row->is_active ? __('Yes') : __('No')"
                :class="$row->is_active ? 'badge-success' : 'badge-ghost'"
            />
        @endscope

        {{-- Actions --}}
        @scope('actions', $row)
            <x-mary-button
                icon="o-pencil-square"
                href="{{ route('category.edit', $row) }}"
                class="btn-ghost btn-sm"
                wire:navigate
            />
            <x-mary-button
                icon="o-trash"
                wire:click="delete({{ $row->id }})"
                wire:confirm="{{ __('Are you sure you want to delete this category?') }}"
                class="btn-ghost btn-sm text-error"
            />
        @endscope
    </x-mary-table>
</div>
