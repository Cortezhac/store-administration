<x-layouts::auth :title="__('auth.confirm_password_page')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.confirm_password_page')"
            :description="__('auth.secure_area')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('auth.confirm_with_passkey')"
            :loading-label="__('auth.confirming')"
            :separator="__('auth.or_confirm_with_password')"
        />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('auth.password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('auth.password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('auth.confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
