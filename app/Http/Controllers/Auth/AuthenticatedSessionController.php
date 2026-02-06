<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('pages.user.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        $request->session()->regenerate();

        if ($user && $user->hasTwoFactorEnabled()) {
            return $this->redirectToTwoFactorChallenge($request);
        }

        return redirect()->intended(route('index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectToTwoFactorChallenge(Request $request): RedirectResponse
    {
        $redirectTo = $request->session()->pull('url.intended', route('index'));

        $request->session()->put('two_factor:redirect', $redirectTo);
        $request->session()->forget('two_factor_passed');

        return redirect()->route('two-factor.challenge');
    }
}
