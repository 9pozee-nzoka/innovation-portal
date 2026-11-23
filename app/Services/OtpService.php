<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendOtpNotification;

class OtpService
{
    /**
     * Generate a new 6-digit OTP code, save it to the user, and email it.
     */
    public function generateAndSend(User $user): string
    {
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new SendOtpNotification($code));

        return $code;
    }

    /**
     * Verify the supplied OTP code.
     */
    public function verify(User $user, string $code): bool
    {
        if (
            ! empty($user->otp_code) &&
            hash_equals((string) $user->otp_code, trim($code)) &&
            $user->otp_expires_at &&
            $user->otp_expires_at->isFuture()
        ) {
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return true;
        }

        return false;
    }
}
