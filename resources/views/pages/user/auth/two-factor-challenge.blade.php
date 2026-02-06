<x-guest-layout>
    <div class="flex flex-col max-w-160 mx-auto px-2 sm:px-4 md:px-6 lg:px-8 py-4 w-full shadow-lg">
        <h1 class="mb-4 font-medium self-center text-xl sm:text-2xl uppercase text-gray-800 ">
            {{ __('user.two_factor.challenge.title') }}
        </h1>
        <p class="mb-6 text-center text-sm text-gray-600">
            {{ __('user.two_factor.challenge.description') }}
        </p>
        <form action="{{ route('two-factor.challenge.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="otp" :value="__('user.two_factor.challenge.code_label')" />
                <x-text-input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                              class="block mt-1 w-full text-center text-2xl tracking-widest" required autofocus />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <x-button class="flex items-center justify-center text-xl w-full">
                {{ __('user.two_factor.challenge.verify_button') }}
            </x-button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-500">
            {{ __('user.two_factor.challenge.lost_access') }}
        </p>
    </div>
</x-guest-layout>

