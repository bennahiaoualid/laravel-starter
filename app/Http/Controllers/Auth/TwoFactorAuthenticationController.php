<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Generate and persist a secret for the authenticated user.
     */
    public function store(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => Crypt::encryptString($secret),
                'two_factor_enabled' => false,
            ])->save();
        }

        return Redirect::route('profile.edit')->with('status', 'two-factor-secret-generated');
    }

    /**
     * Confirm the provided OTP and enable two-factor authentication.
     */
    public function confirm(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();

        if (! $user->two_factor_secret) {
            return Redirect::route('profile.edit')->withErrors([
                'otp' => __('user.two_factor.errors.no_secret'),
            ]);
        }

        $secret = $this->decryptSecret($user->two_factor_secret);

        if (! $secret) {
            return Redirect::route('profile.edit')->withErrors([
                'otp' => __('user.two_factor.errors.cannot_read_secret'),
            ]);
        }

        if (! $google2fa->verifyKey($secret, $request->string('otp')->value(), config('google2fa.window'))) {
            return Redirect::back()->withErrors([
                'otp' => __('user.two_factor.errors.invalid_code'),
            ])->withInput();
        }

        $user->forceFill([
            'two_factor_enabled' => true,
        ])->save();

        $request->session()->put('two_factor_passed', true);

        return Redirect::route('profile.edit')->with('status', 'two-factor-enabled');
    }

    /**
     * Disable two-factor authentication and clear the stored secret.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ])->save();

        $request->session()->forget('two_factor_passed');

        return Redirect::route('profile.edit')->with('status', 'two-factor-disabled');
    }

    private function decryptSecret(string $payload): ?string
    {
        try {
            return Crypt::decryptString($payload);
        } catch (DecryptException) {
            return null;
        }
    }
}
