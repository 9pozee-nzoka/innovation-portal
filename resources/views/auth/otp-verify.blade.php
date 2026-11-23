<x-guest-layout>
    <div class="mb-4 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 text-indigo-700 rounded-full mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">
            One-Time Passcode (OTP)
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Kenya School of Government Authentication Security
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-center">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-5 text-xs text-gray-600 text-center leading-relaxed">
        We have generated a 6-digit verification code. Please enter the passcode sent to <strong>{{ $user->email }}</strong> below:
    </div>

    <form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-4">
        @csrf

        <!-- 6-digit OTP Code Input -->
        <div>
            <x-input-label for="otp_code" :value="__('Enter 6-Digit Passcode')" class="text-center" />
            <input id="otp_code"
                   name="otp_code"
                   type="text"
                   maxlength="6"
                   placeholder="123456"
                   class="mt-2 block w-full text-center text-2xl tracking-[0.5em] font-mono font-bold rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 shadow-xs"
                   required
                   autofocus
                   autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('otp_code')" class="mt-2 text-center" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs shadow-sm transition">
                Verify & Continue
            </button>
        </div>
    </form>

    <!-- Resend Code Form -->
    <div class="mt-6 pt-4 border-t border-gray-100 text-center">
        <p class="text-xs text-gray-500 mb-2">Didn't receive the code or did it expire?</p>
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                Resend New OTP Code
            </button>
        </form>
    </div>
</x-guest-layout>
