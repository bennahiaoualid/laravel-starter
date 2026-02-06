<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use PragmaRX\Google2FAQRCode\Google2FA;

class UserProfileController extends Controller
{
    public function __construct(
    ) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, Google2FA $google2fa): View
    {
        $user = Auth::user();
        $decryptedSecret = $this->decryptSecret($user?->two_factor_secret);
        $qrCode = null;

        if ($decryptedSecret) {
            $svgString = $google2fa->getQRCodeInline(
                config('app.name', 'FA'),
                $user?->email ?? '',
                $decryptedSecret
            );
            // Convert SVG string to data URI for use in img tag
            $qrCode = 'data:image/svg+xml;base64,'.base64_encode($svgString);
        }

        return view('pages.user.profile.edit', [
            'user' => $user,
            'twoFactorSecret' => $decryptedSecret,
            'twoFactorQrCode' => $qrCode,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    /*public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }*/

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function decryptSecret(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }
    }
}
