<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-4">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('One-Time Passcode (OTP) Security') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Test email delivery by requesting a fresh 6-digit OTP passcode to your registered email address.') }}
                            </p>
                        </header>
                        <div class="flex items-center space-x-3">
                            <form method="POST" action="{{ route('otp.resend') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    {{ __('Send Me an OTP Code') }}
                                </button>
                            </form>
                            <a href="{{ route('otp.verify') }}" class="text-xs text-indigo-600 hover:text-indigo-800 underline font-medium">
                                {{ __('Open OTP Verification Screen →') }}
                            </a>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
