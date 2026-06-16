<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('profile.security_settings') }}</h2>

    <x-settings.layout :heading="__('profile.update_password')" :subheading="__('profile.ensure_secure_password')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-mary-password
                wire:model="current_password"
                :label="__('profile.current_password')"
                required
                autocomplete="current-password"
            />
            <x-mary-password
                wire:model="password"
                :label="__('profile.new_password')"
                required
                autocomplete="new-password"
            />
            <x-mary-password
                wire:model="password_confirmation"
                :label="__('profile.confirm_password')"
                required
                autocomplete="new-password"
            />

            <div class="flex items-center gap-4">
                <x-mary-button label="{{ __('profile.save') }}" type="submit" class="btn-primary" data-test="update-password-button" />
            </div>
        </form>

        @if ($canManageTwoFactor)
            <section class="mt-12">
                <h2 class="text-xl font-bold text-on-surface">{{ __('profile.two_factor_auth') }}</h2>
                <p class="text-on-surface-variant">{{ __('profile.manage_2fa') }}</p>

                <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="space-y-4">
                            <p class="text-on-surface">
                                {{ __('profile.enabled_2fa_prompt') }}
                            </p>

                            <div class="flex justify-start">
                                <x-mary-button
                                    label="{{ __('profile.disable_2fa') }}"
                                    class="btn-error"
                                    wire:click="disable"
                                />
                            </div>

                            <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="text-on-surface-variant">
                                {{ __('profile.disabled_2fa_prompt') }}
                            </p>

                            <x-mary-button
                                label="{{ __('profile.enable_2fa') }}"
                                class="btn-primary"
                                wire:click="enable"
                            />
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($canManageTwoFactor)
            <x-mary-modal
                wire:model="showModal"
                class="max-w-md"
                @close="$wire.closeModal()"
            >
                <div class="space-y-6">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="p-0.5 w-auto rounded-full border border-outline-variant bg-surface-container-lowest shadow-sm">
                            <div class="p-2.5 rounded-full border border-outline-variant overflow-hidden bg-surface-container-low relative">
                                <div class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-outline-variant justify-around opacity-50">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div></div>
                                    @endfor
                                </div>

                                <div class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-outline-variant justify-around opacity-50">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div></div>
                                    @endfor
                                </div>

                                <x-mary-icon name="o-qr-code" class="relative z-20" />
                            </div>
                        </div>

                        <div class="space-y-2 text-center">
                            <h3 class="text-lg font-bold text-on-surface">{{ $this->modalConfig['title'] }}</h3>
                            <p class="text-on-surface-variant">{{ $this->modalConfig['description'] }}</p>
                        </div>
                    </div>

                    @if ($showVerificationStep)
                        <div class="space-y-6">
                            <div
                                class="flex flex-col items-center space-y-3 justify-center"
                                x-data
                                x-init="$nextTick(() => $el.querySelector('input')?.focus())"
                            >
                                <x-mary-pin
                                    wire:model="code"
                                    size="6"
                                    numeric
                                />
                            </div>

                            <div class="flex items-center space-x-3">
                                <x-mary-button
                                    label="{{ __('profile.back') }}"
                                    class="btn-outline flex-1"
                                    wire:click="resetVerification"
                                />

                                <x-mary-button
                                    label="{{ __('profile.confirm') }}"
                                    class="btn-primary flex-1"
                                    wire:click="confirmTwoFactor"
                                    x-bind:disabled="$wire.code.length < 6"
                                />
                            </div>
                        </div>
                    @else
                        @error('setupData')
                            <x-mary-alert icon="o-x-circle" class="alert-error" title="{{ $message }}" />
                        @enderror

                        <div class="flex justify-center">
                            <div class="relative w-64 overflow-hidden border rounded-lg border-outline-variant aspect-square">
                                @empty($qrCodeSvg)
                                    <div class="absolute inset-0 flex items-center justify-center bg-surface-container-low animate-pulse">
                                        <x-mary-icon name="o-arrow-path" class="animate-spin w-6 h-6" />
                                    </div>
                                @else
                                <div x-data class="flex items-center justify-center h-full p-4">
                                    <div
                                        class="bg-white p-3 rounded"
                                        :style="$store.theme.isDark ? 'filter: invert(1) brightness(1.5)' : ''"
                                    >
                                            {!! $qrCodeSvg !!}
                                        </div>
                                    </div>
                                @endempty
                            </div>
                        </div>

                        <div>
                            <x-mary-button
                                :disabled="$errors->has('setupData')"
                                :label="$this->modalConfig['buttonText']"
                                class="btn-primary w-full"
                                wire:click="showVerificationIfNecessary"
                            />
                        </div>

                        <div class="space-y-4">
                            <div class="relative flex items-center justify-center w-full">
                                <div class="absolute inset-0 w-full h-px top-1/2 bg-outline-variant"></div>
                                <span class="relative px-2 text-sm bg-surface text-on-surface-variant">
                                    {{ __('profile.or_enter_manually') }}
                                </span>
                            </div>

                            <div
                                class="flex items-center space-x-2"
                                x-data="{
                                    copied: false,
                                    async copy() {
                                        try {
                                            await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 1500);
                                        } catch (e) {
                                            console.warn('Could not copy to clipboard');
                                        }
                                    }
                                }"
                            >
                                <div class="flex items-stretch w-full border rounded-xl border-outline-variant">
                                    @empty($manualSetupKey)
                                        <div class="flex items-center justify-center w-full p-3 bg-surface-container-low">
                                            <x-mary-icon name="o-arrow-path" class="animate-spin w-5 h-5" />
                                        </div>
                                    @else
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $manualSetupKey }}"
                                            class="w-full p-3 bg-transparent outline-none text-on-surface"
                                        />

                                        <button
                                            @click="copy()"
                                            class="px-3 transition-colors border-l cursor-pointer border-outline-variant"
                                        >
                                            <x-mary-icon name="o-document-duplicate" x-show="!copied" />
                                            <x-mary-icon name="o-check" x-show="copied" class="text-green-500" />
                                        </button>
                                    @endempty
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-mary-modal>
        @endif

        @if ($canManagePasskeys)
            <section class="mt-12">
                <h2 class="text-xl font-bold text-on-surface">{{ __('profile.passkeys') }}</h2>
                <p class="text-on-surface-variant">{{ __('profile.manage_passkeys') }}</p>

                <div class="mt-6 flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
                    <div class="border rounded-lg border-outline-variant overflow-hidden">
                        @forelse ($passkeys as $passkey)
                            <div class="flex items-center justify-between p-4 {{ ! $loop->last ? 'border-b border-outline-variant' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-surface-container-low">
                                        <x-mary-icon name="o-key" class="size-5 text-on-surface-variant" />
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2.5">
                                            <p class="font-medium tracking-tight text-on-surface">{{ $passkey['name'] }}</p>
                                            @if ($passkey['authenticator'])
                                                <x-mary-badge value="{{ $passkey['authenticator'] }}" class="badge-sm" />
                                            @endif
                                        </div>
                                        <p class="text-on-surface-variant text-xs">
                                            {{ __('Added :time', ['time' => $passkey['created_at_diff']]) }}
                                            @if ($passkey['last_used_at_diff'])
                                                <span class="opacity-50 mx-1">/</span>
                                                {{ __('Last used :time', ['time' => $passkey['last_used_at_diff']]) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <x-mary-button
                                    icon="o-trash"
                                    class="btn-ghost btn-sm text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50"
                                    wire:click="confirmDelete({{ $passkey['id'] }})"
                                />
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-surface-container-low">
                                    <x-mary-icon name="o-key" class="size-7 text-on-surface-variant" />
                                </div>
                                <p class="font-medium text-on-surface">{{ __('profile.no_passkeys_yet') }}</p>
                                <p class="mt-1 text-on-surface-variant">{{ __('profile.add_passkey_to_signin') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <x-passkey-registration />
                </div>
            </section>
        @endif
    </x-settings.layout>

    <x-mary-modal
        wire:model="showDeleteModal"
        class="max-w-md"
        @close="$wire.closeDeleteModal()"
    >
        <div class="space-y-6">
            <div class="space-y-2">
                <h3 class="text-lg font-bold text-on-surface">{{ __('profile.remove_passkey') }}</h3>
                <p class="text-on-surface-variant">
                    {{ __('Are you sure you want to remove the passkey ":name"? You will no longer be able to use it to sign in.', ['name' => $deletingPasskeyName]) }}
                </p>
            </div>

            <div class="flex gap-3 justify-end">
                <x-mary-button
                    label="{{ __('profile.cancel') }}"
                    class="btn-outline"
                    wire:click="closeDeleteModal"
                />
                <x-mary-button
                    label="{{ __('profile.remove_passkey') }}"
                    class="btn-error"
                    wire:click="deletePasskey"
                />
            </div>
        </div>
    </x-mary-modal>
</section>
