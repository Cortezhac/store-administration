<div>
    <div class="flex flex-col gap-6 max-w-2xl">
        <div>
            <h1 class="text-2xl font-semibold text-on-surface">
                {{ $category ? __('Edit Category') : __('Create Category') }}
            </h1>
            <p class="text-on-surface-variant">
                {{ $category ? __('Update the category details below.') : __('Fill in the details to create a new category.') }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-6">
            {{-- Name --}}
            <x-mary-input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
            />

            {{-- Slug --}}
            <x-mary-input
                wire:model="slug"
                :label="__('Slug')"
                type="text"
                required
            />

            {{-- Description --}}
            <x-mary-textarea
                wire:model="description"
                :label="__('Description')"
                rows="3"
            />

            {{-- Parent Category --}}
            <x-mary-select
                wire:model="parent_id"
                :label="__('Parent Category')"
                :options="$this->getParentOptions()"
                placeholder="{{ __('None (top level)') }}"
            />

            {{-- Sort Order --}}
            <x-mary-input
                wire:model="sort_order"
                :label="__('Sort Order')"
                type="number"
                min="0"
            />

            {{-- Active --}}
            <x-mary-toggle
                wire:model="is_active"
                :label="__('Active')"
            />

            {{-- Icon Upload --}}
            <div>
                <x-mary-file
                    wire:model="iconUpload"
                    :label="__('Icon')"
                    accept="image/png,image/jpeg,image/svg+xml,image/webp"
                    :hint="__('PNG, JPG, SVG or WebP. Max 1 MB.')"
                />

                @if ($iconUpload)
                    <div class="mt-2">
                        <img
                            src="{{ $iconUpload->temporaryUrl() }}"
                            alt="{{ __('Icon preview') }}"
                            class="max-w-[50%] h-auto max-h-32 object-contain rounded-lg border border-outline-variant"
                        />
                    </div>
                @elseif ($category && $category->icon && !$removeIcon)
                    <div class="mt-2 flex items-center gap-3">
                        <img
                            src="{{ $category->iconUrl() }}"
                            alt="{{ $category->name }}"
                            class="max-w-[50%] h-auto max-h-32 object-contain rounded-lg border border-outline-variant"
                        />
                        <x-mary-button
                            icon="o-trash"
                            :label="__('Remove icon')"
                            class="btn-ghost btn-sm text-error"
                            wire:click="removeIconAction"
                        />
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-4">
                <x-mary-button
                    :label="__('Save')"
                    type="submit"
                    class="btn-primary"
                />
                <x-mary-button
                    :label="__('Cancel')"
                    href="{{ route('category.index') }}"
                    class="btn-ghost"
                    wire:navigate
                />
            </div>
        </form>
    </div>
</div>
