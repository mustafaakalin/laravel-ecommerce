<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended($this->redirectPath(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Bu bilgilerle bir hesap bulunamadı.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath(Auth::user()));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectPath($user): string
    {
        if ($user->hasRole('admin')) {
            return route('filament.admin.pages.dashboard');
        }

        $intended = session('url.intended');

        if ($intended && str_starts_with((string) parse_url($intended, PHP_URL_PATH), '/admin')) {
            return route('home');
        }

        return route('home');
    }
}
