<x-guest-layout>
    <h4 class="fw-bold mb-1 text-center">Confirm Password</h4>
    <p class="text-muted text-center small mb-4">{{ __("Please confirm your password before continuing.") }}</p>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>
        <x-primary-button class="w-100 justify-content-center py-2">{{ __('Confirm') }}</x-primary-button>
    </form>
</x-guest-layout>
