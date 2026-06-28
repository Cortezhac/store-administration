<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('profile.profile_settings') }}</h2>

    <x-settings.layout :heading="__('profile.profile')" :subheading="__('profile.update_name_email')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <x-mary-input wire:model="name" :label="__('profile.name')" type="text" required autofocus autocomplete="name" />

            <div>
                <x-mary-input wire:model="email" :label="__('profile.email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <p class="mt-4 text-on-surface">
                            {{ __('profile.email_unverified') }}

                            <button type="button" class="text-sm text-primary hover:underline cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('profile.resend_verification') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <x-mary-button label="{{ __('profile.save') }}" type="submit" class="btn-primary" />
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
