@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <p class="text-on-surface-variant">{{ __('Passkeys are not supported in this browser.') }}</p>
    </template>

    <template x-if="supported && !showForm">
        <div>
            <x-mary-button
                label="{{ __('Add passkey') }}"
                icon="o-plus"
                class="btn-primary"
                x-on:click="showForm = true"
            />
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-lg border border-outline-variant bg-surface-container-low p-4">
            <label class="block">
                <span class="block text-sm font-medium mb-1 text-on-surface">{{ __('Passkey name') }}</span>
                <input
                    type="text"
                    x-model="name"
                    placeholder="{{ __('e.g., MacBook Pro, iPhone') }}"
                    x-on:keydown.enter.prevent="register()"
                    x-ref="passkeyNameInput"
                    x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
                    class="input input-bordered w-full"
                />
            </label>
            <p class="text-on-surface-variant !mt-1">{{ __('Give this passkey a name to help you identify it later.') }}</p>

            <p x-show="error" x-text="error" x-cloak class="text-sm text-red-600 dark:text-red-400"></p>

            <div class="flex gap-2">
                <x-mary-button
                    class="btn-primary"
                    x-on:click="register()"
                    x-bind:disabled="loading || !name.trim()"
                >
                    <span x-show="!loading">{{ __('Register passkey') }}</span>
                    <span x-show="loading" x-cloak>{{ __('Registering...') }}</span>
                </x-mary-button>
                <x-mary-button
                    class="btn-ghost"
                    x-on:click="cancel()"
                >
                    {{ __('Cancel') }}
                </x-mary-button>
            </div>
        </div>
    </template>
</div>
