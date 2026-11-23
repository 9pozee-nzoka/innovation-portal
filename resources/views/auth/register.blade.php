<x-guest-layout>
    <div class="mb-5 text-center">
        <h1 class="text-xl font-extrabold text-gray-900">Create Staff Account</h1>
        <p class="text-xs text-gray-500 mt-1">Select your role to get started on the KSG Innovation Portal</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Account Role Selection -->
        <div class="mb-5">
            <x-input-label :value="__('Register as')" class="font-semibold text-gray-700 mb-2" />

            <div class="flex items-center space-x-8 mt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="role" value="innovator" class="text-emerald-700 focus:ring-emerald-600 border-gray-300" {{ old('role', 'innovator') === 'innovator' ? 'checked' : '' }}>
                    <span class="ms-2 text-sm font-medium text-gray-800">Innovator</span>
                </label>

                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="role" value="reviewer" class="text-emerald-700 focus:ring-emerald-600 border-gray-300" {{ old('role') === 'reviewer' ? 'checked' : '' }}>
                    <span class="ms-2 text-sm font-medium text-gray-800">Reviewer</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="e.g. Dr. Jane Mwangi" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('KSG Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@ksg.ac.ke" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Department / Campus -->
        <div class="mt-4">
            <x-input-label for="department" :value="__('Department & Campus')" />
            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" placeholder="e.g. Lower Kabete - ICT & Research" />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number (Optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="+254 700 000000" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
            <a class="underline text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button class="ms-4 bg-emerald-800 hover:bg-emerald-900">
                {{ __('Complete Registration') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
