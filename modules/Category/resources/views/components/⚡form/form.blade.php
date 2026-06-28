<x-shared::page-wrapper>
    {{-- Breadcrumbs & Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <x-shared::breadcrumb :items="[
                ['label' => __('category.categories'), 'route' => route('category.index')],
                ['label' => $category ? $category->name : __('shared.new')],
            ]" />
            <h2 class="text-3xl font-semibold text-on-surface tracking-tight">
                {{ $category ? __('category.edit_category') : __('category.new_category') }}
            </h2>
            <p class="text-on-surface-variant text-sm mt-1">
                {{ $category ? __('category.update_category_details') : __('category.fill_category_details') }}
            </p>
        </div>
        <a
            href="{{ route('category.index') }}"
            wire:navigate
            class="flex items-center gap-2 px-6 py-2.5 rounded-lg border border-outline text-on-surface text-sm font-medium hover:bg-surface-container-high transition-all active:scale-[0.98]"
        >
            <flux:icon.arrow-left class="size-5" />
            {{ __('shared.back_to_list') }}
        </a>
    </div>

    {{-- Form Card --}}
    <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-8 border border-outline-variant shadow-sm">
        <div class="flex items-center gap-4 mb-4 sm:mb-8">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-primary-container flex-shrink-0">
                <flux:icon.pencil class="size-5 text-primary" />
            </div>
            <div>
                <h3 class="text-xl font-semibold text-on-surface">{{ __('category.main_info') }}</h3>
                <p class="text-on-surface-variant text-sm">{{ __('category.category_basic_info') }}</p>
            </div>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                {{-- Name --}}
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.name') }}</label>
                    <input
                        id="name"
                        wire:model="form.name"
                        type="text"
                        required
                        autofocus
                        class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary/20 text-sm outline-none text-on-surface placeholder:text-on-surface-variant/50"
                        placeholder="{{ __('category.category_name_placeholder') }}"
                    />
                    @error('form.name')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="space-y-1.5">
                    <label for="slug" class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.slug') }}</label>
                    <input
                        id="slug"
                        wire:model="form.slug"
                        type="text"
                        required
                        class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary/20 text-sm outline-none text-on-surface placeholder:text-on-surface-variant/50"
                        placeholder="{{ __('category.url_friendly_identifier') }}"
                    />
                    @error('form.slug')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 space-y-1.5">
                    <label for="description" class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.description') }}</label>
                    <textarea
                        id="description"
                        wire:model="form.description"
                        rows="3"
                        class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary/20 text-sm outline-none text-on-surface placeholder:text-on-surface-variant/50 resize-none"
                        placeholder="{{ __('category.description_placeholder') }}"
                    ></textarea>
                    @error('form.description')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Parent Category --}}
                <div class="space-y-1.5">
                    <label for="parent_id" class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.parent_category') }}</label>
                    <select
                        id="parent_id"
                        wire:model="form.parent_id"
                        class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary/20 text-sm outline-none text-on-surface"
                    >
                        <option value="" class="bg-surface-container-lowest text-on-surface-variant">{{ __('category.none_root_level') }}</option>
                        @foreach ($this->getParentOptions() as $option)
                            <option value="{{ $option['id'] }}" class="bg-surface-container-lowest text-on-surface">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                    @error('form.parent_id')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sort Order --}}
                <div class="space-y-1.5">
                    <label for="sort_order" class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.sort_order') }}</label>
                    <input
                        id="sort_order"
                        wire:model="form.sort_order"
                        type="number"
                        min="0"
                        max="999"
                        class="w-full bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary/20 text-sm outline-none text-on-surface placeholder:text-on-surface-variant/50"
                    />
                    @error('form.sort_order')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active Toggle --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.status') }}</label>
                    <x-mary-toggle
                        wire:model="form.is_active"
                        :label="__('category.active_category')"
                    />
                    @error('form.is_active')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Upload --}}
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('category.icon') }}</label>
                    <div class="bg-surface-container-low border border-outline-variant rounded-lg p-4">
                        <input
                            wire:model="iconUpload"
                            type="file"
                            accept="image/png,image/jpeg,image/svg+xml,image/webp"
                            class="w-full text-sm text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-container file:text-primary hover:file:bg-primary/20 cursor-pointer"
                        />
                        <p class="text-xs text-on-surface-variant mt-2">{{ __('category.icon_hint') }}</p>

                        @if ($iconUpload)
                            <div class="mt-4">
                                <img
                                    src="{{ $iconUpload->temporaryUrl() }}"
                                    alt="{{ __('category.icon_preview') }}"
                                    class="max-w-[50%] h-auto max-h-32 object-contain rounded-lg border border-outline-variant"
                                />
                            </div>
                        @elseif ($category && $category->icon && !$removeIcon)
                            <div class="mt-4 flex items-center gap-4">
                                <img
                                    src="{{ $category->iconUrl() }}"
                                    alt="{{ $category->name }}"
                                    class="max-w-[50%] h-auto max-h-32 object-contain rounded-lg border border-outline-variant"
                                />
                                <button
                                    type="button"
                                    wire:click="removeIconAction"
                                    class="text-error text-sm font-medium hover:underline transition-all"
                                >
                                    {{ __('category.remove_icon') }}
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('iconUpload')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-outline-variant">
                <button
                    type="submit"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90 shadow-sm transition-all active:scale-[0.98]"
                >
                    <flux:icon.check class="size-5" />
                    {{ __('shared.save') }}
                </button>
                <a
                    href="{{ route('category.index') }}"
                    wire:navigate
                    class="px-6 py-2.5 rounded-lg border border-outline text-on-surface text-sm font-medium hover:bg-surface-container-high transition-all"
                >
                    {{ __('shared.cancel') }}
                </a>
            </div>
        </form>
    </section>
</x-shared::page-wrapper>
