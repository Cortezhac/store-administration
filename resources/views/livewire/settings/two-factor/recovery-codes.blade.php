<div
    class="py-6 space-y-6 border shadow-sm rounded-xl border-outline-variant"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="px-6 space-y-2">
        <div class="flex items-center gap-2">
            <x-mary-icon name="o-lock-closed" class="size-4" />
            <h3 class="text-lg font-bold text-on-surface">{{ __('profile.two_fa_recovery_codes') }}</h3>
        </div>
        <p class="text-on-surface-variant">
            {{ __('profile.recovery_codes_desc') }}
        </p>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-mary-button
                x-show="!showRecoveryCodes"
                label="{{ __('profile.view_recovery_codes') }}"
                icon="o-eye"
                class="btn-primary"
                @click="showRecoveryCodes = true;"
                aria-expanded="false"
                aria-controls="recovery-codes-section"
            />

            <x-mary-button
                x-show="showRecoveryCodes"
                label="{{ __('profile.hide_recovery_codes') }}"
                icon="o-eye-slash"
                class="btn-primary"
                @click="showRecoveryCodes = false"
                aria-expanded="true"
                aria-controls="recovery-codes-section"
            />

            @if (filled($recoveryCodes))
                <x-mary-button
                    x-show="showRecoveryCodes"
                    label="{{ __('profile.regenerate_codes') }}"
                    icon="o-arrow-path"
                    class="btn-ghost"
                    wire:click="regenerateRecoveryCodes"
                />
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes"
        >
            <div class="mt-3 space-y-3">
                @error('recoveryCodes')
                    <x-mary-alert icon="o-x-circle" class="alert-error" title="{{ $message }}" />
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="grid gap-1 p-4 font-mono text-sm rounded-lg bg-surface-container-lowest dark:bg-white/5"
                        role="list"
                        aria-label="{{ __('profile.recovery_codes') }}"
                    >
                        @foreach($recoveryCodes as $code)
                            <div
                                role="listitem"
                                class="select-text"
                                wire:loading.class="opacity-50 animate-pulse"
                            >
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-on-surface-variant">
                        {{ __('profile.each_code_once') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
