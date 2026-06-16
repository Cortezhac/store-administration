<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="text-xl font-bold text-on-surface">{{ __('profile.delete_account') }}</h2>
        <p class="text-on-surface-variant">{{ __('profile.delete_account_desc') }}</p>
    </div>

    <x-mary-button
        label="{{ __('profile.delete_account') }}"
        class="btn-error"
        wire:click="$toggle('showDeleteModal')"
    />

    <x-mary-modal wire:model="showDeleteModal" title="{{ __('profile.confirm_delete') }}" class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <p class="text-on-surface-variant">
                {{ __('profile.delete_permanently') }}
            </p>

            <x-mary-password
                wire:model="password"
                :label="__('profile.password')"
            />

            <x-slot:actions>
                <x-mary-button label="{{ __('profile.cancel') }}" @click="$wire.showDeleteModal = false" />
                <x-mary-button label="{{ __('profile.delete_account') }}" class="btn-error" type="submit" />
            </x-slot:actions>
        </form>
    </x-mary-modal>
</section>
