<section
    x-data="{
        showSecret: false,
        toggleSecret() { this.showSecret = !this.showSecret }
    }"
>
    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900">{{ __('user.two_factor.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('user.two_factor.description') }}
            </p>
        </div>
        @if ($user->two_factor_enabled)
            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                {{ __('user.two_factor.enabled') }}
            </span>
        @endif
    </header>

    <div class="mt-6 space-y-4">
        @if (! $user->two_factor_secret)
            <p class="text-sm text-gray-600">
                {{ __('user.two_factor.setup_instructions') }}
            </p>
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <x-button color_type="primary">
                    {{ __('user.two_factor.enable_button') }}
                </x-button>
            </form>
        @elseif (! $user->two_factor_enabled)
            <div class="space-y-4">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-700">{{ __('user.two_factor.scan_qr') }}</p>
                    @if ($twoFactorQrCode)
                        <div class="rounded border border-dashed border-gray-300 bg-gray-50 p-4 text-center">
                            <img src="{{ $twoFactorQrCode }}" alt="{{ __('user.two_factor.qr_alt') }}" class="mx-auto h-48 w-48">
                        </div>
                    @else
                        <p class="text-sm text-red-600">
                            {{ __('user.two_factor.qr_error') }}
                        </p>
                    @endif
                    @if ($twoFactorSecret)
                        <button
                            type="button"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                            x-on:click="toggleSecret"
                            x-text="showSecret ? '{{ __('user.two_factor.hide_manual_code') }}' : '{{ __('user.two_factor.show_manual_code') }}'">
                        </button>
                        <div x-show="showSecret" x-cloak class="rounded bg-gray-900 px-4 py-3 font-mono text-sm text-white break-all">
                            {{ $twoFactorSecret }}
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="otp" value="{{ __('user.two_factor.enter_code') }}" />
                        <x-text-input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                                      class="mt-1 block w-48 uppercase" required autofocus />
                        <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                    </div>
                    <x-button color_type="primary">{{ __('user.two_factor.verify_enable') }}</x-button>
                </form>
                <form method="POST" action="{{ route('two-factor.disable') }}" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <x-button color_type="secondary" type="submit" class="mt-3">
                        {{ __('user.two_factor.cancel_setup') }}
                    </x-button>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-600">
                {{ __('user.two_factor.active_message') }}
            </p>
            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                @csrf
                @method('DELETE')
                <x-button color_type="danger">
                    {{ __('user.two_factor.disable_button') }}
                </x-button>
            </form>
        @endif
    </div>
</section>

