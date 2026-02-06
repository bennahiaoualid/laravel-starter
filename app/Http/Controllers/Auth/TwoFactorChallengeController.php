<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return Redirect::route('login');
        }

        // If user is already logged in and 2FA is already passed, redirect to index
        if ($request->session()->get('two_factor_passed') === true) {
            return Redirect::route('index');
        }

        return view('pages.user.auth.two-factor-challenge');
    }

    public function store(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return Redirect::route('login');
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (DecryptException) {
            return Redirect::route('login')->withErrors([
                'email' => __('user.two_factor.errors.cannot_verify_account'),
            ]);
        }

        if (! $google2fa->verifyKey($secret, $request->string('otp')->value(), config('google2fa.window'))) {
            return Redirect::back()->withErrors([
                'otp' => __('user.two_factor.errors.invalid_code'),
            ])->withInput();
        }

        $request->session()->put('two_factor_passed', true);
        $redirectTo = $request->session()->pull('two_factor:redirect', route('index'));

        return Redirect::to($redirectTo);
    }
}
