<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h2 class="text-xl font-bold text-on-surface">{{ __('Delete account') }}</h2>
        <p class="text-on-surface-variant">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <x-mary-button
        label="{{ __('Delete account') }}"
        class="btn-error"
        wire:click="$toggle('showDeleteModal')"
    />

    <x-mary-modal wire:model="showDeleteModal" title="{{ __('Are you sure you want to delete your account?') }}" class="max-w-lg">
        <form method="POST" wire:submit="deleteUser" class="space-y-6">
            <p class="text-on-surface-variant">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <x-mary-password
                wire:model="password"
                :label="__('Password')"
            />

            <x-slot:actions>
                <x-mary-button label="{{ __('Cancel') }}" @click="$wire.showDeleteModal = false" />
                <x-mary-button label="{{ __('Delete account') }}" class="btn-error" type="submit" />
            </x-slot:actions>
        </form>
    </x-mary-modal>
</section>
