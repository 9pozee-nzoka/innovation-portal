<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Show the OTP verification form.
     */
    public function show(Request $request): View
    {
        return view('auth.otp-verify', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Verify the submitted OTP passcode.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ], [
            'otp_code.required' => 'Please enter the 6-digit OTP passcode.',
            'otp_code.size' => 'The OTP passcode must be exactly 6 digits.',
        ]);

        $verified = $this->otpService->verify(
            $request->user(),
            $request->input('otp_code')
        );

        if ($verified) {
            return redirect()
                ->route('innovations.index')
                ->with('status', 'One-Time Passcode verified successfully!');
        }

        return back()->withErrors([
            'otp_code' => 'The passcode you entered is invalid or has expired. Please request a new one.',
        ]);
    }

    /**
     * Generate and dispatch a fresh OTP passcode.
     */
    public function resend(Request $request): RedirectResponse
    {
        $this->otpService->generateAndSend($request->user());

        return back()->with('status', 'A fresh 6-digit OTP code has been sent to ' . $request->user()->email);
    }
}
