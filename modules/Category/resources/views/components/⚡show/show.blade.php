<div class="pt-24 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumbs & Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <nav class="flex items-center gap-2 text-on-surface-variant text-xs font-medium mb-2">
                <a href="{{ route('category.index') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('Categorías') }}</a>
                <flux:icon.chevron-right class="size-4" />
                <span class="text-primary font-bold">{{ $category->name }}</span>
            </nav>
            <h2 class="text-3xl font-semibold text-on-surface tracking-tight">{{ __('Detalle de Categoría') }}</h2>
        </div>
        <div class="flex items-center gap-3">
            <a
                href="{{ route('category.index') }}"
                wire:navigate
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg border border-outline text-on-surface text-sm font-medium hover:bg-surface-container-high transition-all active:scale-[0.98]"
            >
                <flux:icon.arrow-left class="size-5" />
                {{ __('Volver a la lista') }}
            </a>
            <a
                href="{{ route('category.edit', $category) }}"
                wire:navigate
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-on-primary text-sm font-medium hover:opacity-90 shadow-sm transition-all active:scale-[0.98]"
            >
                <flux:icon.pencil class="size-5" />
                {{ __('Editar Categoría') }}
            </a>
        </div>
    </div>

    {{-- Main Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Main Column (Left) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Core Information Card --}}
            <section class="bg-surface-container-lowest rounded-xl p-4 sm:p-8 border border-outline-variant shadow-sm transition-all hover:shadow-md">
                <div x-data="{ expanded: false }" class="flex items-start gap-4 mb-4 sm:mb-8 flex-wrap">
                    <div :class="expanded ? 'w-full sm:w-1/2 aspect-square' : 'w-16 h-16'"
                         class="relative rounded-2xl flex items-center justify-center overflow-hidden border border-outline-variant transition-all duration-200 flex-shrink-0"
                    >
                        @if ($category->icon)
                            <img
                                src="{{ $category->iconUrl() }}"
                                alt="{{ $category->name }}"
                                class="w-full h-full object-cover"
                            />
                            <button
                                x-on:click="expanded = !expanded"
                                x-on:click.stop
                                class="absolute bottom-1 right-1 w-6 h-6 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition-all active:scale-90 z-10"
                            >
                                <flux:icon.arrow-down-right x-show="!expanded" class="size-3.5" />
                                <flux:icon.arrow-up-left x-show="expanded" x-cloak class="size-3.5" />
                            </button>
                        @else
                            @if ($category->parent_id)
                                <flux:icon.folder class="size-8 text-primary" />
                            @else
                                <flux:icon.tag class="size-8 text-primary" />
                            @endif
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-on-surface">{{ __('Información Principal') }}</h3>
                        <p class="text-on-surface-variant text-sm">{{ __('Atributos descriptivos de la categoría') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                    {{-- Name --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('Nombre') }}</label>
                        <p class="text-lg font-medium text-on-surface">{{ $category->name }}</p>
                    </div>

                    {{-- Slug --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('Slug') }}</label>
                        <div class="flex items-center gap-2">
                            <code class="bg-surface-container-high px-3 py-1 rounded-lg text-on-surface font-mono text-sm">{{ $category->slug }}</code>
                            <button
                                x-data="{ copied: false }"
                                x-on:click="navigator.clipboard.writeText('{{ $category->slug }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                @click.outside="copied = false"
                                class="text-outline hover:text-primary transition-colors"
                            >
                                <flux:icon.document-duplicate x-show="!copied" class="size-[18px]" />
                                <flux:icon.check x-show="copied" x-cloak class="size-[18px] text-green-500" />
                            </button>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('Descripción') }}</label>
                        <div class="bg-surface-container-low/50 p-4 sm:p-6 rounded-xl border border-dashed border-outline-variant">
                            @if ($category->description)
                                <p class="text-base text-on-surface-variant leading-relaxed italic">"{{ $category->description }}"</p>
                            @else
                                <p class="text-base text-on-surface-variant italic">{{ __('Sin descripción') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Parent Category --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-primary uppercase tracking-wider">{{ __('Categoría Padre') }}</label>
                        <div class="flex items-center gap-2 text-on-surface">
                            <flux:icon.folder-open class="size-5 text-secondary" />
                            @if ($category->parent)
                                <a href="{{ route('category.show', $category->parent) }}" wire:navigate class="font-medium text-primary hover:underline">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="font-medium">{{ __('Raíz') }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-secondary-fixed text-secondary border border-secondary/20">{{ __('Root') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Side Column (Right) --}}
        <aside class="lg:col-span-4 space-y-6">
            {{-- Metadata Card --}}
            <section class="bg-surface-container-high/50 rounded-xl p-4 sm:p-6 border border-outline-variant">
                <h3 class="text-sm font-medium text-on-surface mb-4 sm:mb-6 flex items-center gap-2">
                    <flux:icon.information-circle class="size-5" />
                    {{ __('Información del Sistema') }}
                </h3>
                <div class="space-y-4 sm:space-y-6">
                    {{-- ID --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-on-surface-variant">{{ __('ID de Registro') }}</span>
                        <div class="flex items-center justify-between bg-surface-container-lowest px-4 py-3 rounded-lg border border-outline-variant/50">
                            <span class="font-mono text-primary font-bold">CAT-{{ str_pad($category->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <flux:icon.lock-closed class="size-[18px] text-outline" />
                        </div>
                    </div>

                    {{-- Created At --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-on-surface-variant">{{ __('Fecha de Creación') }}</span>
                        <div class="flex items-center gap-3 bg-surface-container-lowest/40 p-3 rounded-lg">
                            <flux:icon.calendar class="size-5 text-secondary" />
                            <span class="text-sm text-on-surface">{{ $category->created_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>

                    {{-- Updated At --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-on-surface-variant">{{ __('Fecha de Actualización') }}</span>
                        <div class="flex items-center gap-3 bg-surface-container-lowest/40 p-3 rounded-lg">
                            <flux:icon.clock class="size-5 text-primary" />
                            <span class="text-sm text-on-surface">{{ $category->updated_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary-container text-primary text-center p-4 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-2xl font-semibold">{{ $category->children_count }}</span>
                    <span class="text-xs font-medium opacity-80">{{ __('Subcategorías') }}</span>
                </div>
                <div class="bg-surface-container-high text-on-surface p-4 rounded-xl flex flex-col items-center justify-center border border-outline-variant">
                    <span class="text-2xl font-semibold">—</span>
                    <span class="text-xs font-medium text-on-surface-variant">{{ __('Productos') }}</span>
                </div>
            </div>

            {{-- System Status --}}
            <div class="rounded-xl p-4 sm:p-6 border flex items-center gap-4"
                 @class([
                     'bg-primary-container/30 border-primary/20' => $category->is_active,
                     'bg-surface-variant border-outline-variant' => !$category->is_active,
                 ])>
                <div class="relative flex items-center justify-center w-3 h-3">
                    @if ($category->is_active)
                        <div class="absolute inset-0 rounded-full border-2 border-green-400 animate-expand-ring"></div>
                    @endif
                    <div class="w-2.5 h-2.5 rounded-full z-10 {{ $category->is_active ? 'bg-green-400' : 'bg-on-surface-variant' }}"></div>
                </div>
                <div>
                    <p class="text-sm font-bold text-on-surface">
                        {{ $category->is_active ? __('Categoría Activa') : __('Categoría Inactiva') }}
                    </p>
                    <p class="text-xs text-on-surface-variant">
                        {{ $category->is_active ? __('Visible en el Frontend') : __('Oculta en el Frontend') }}
                    </p>
                </div>
            </div>
        </aside>
    </div>
</div>
