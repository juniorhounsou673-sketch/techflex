<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
        }

        $user = Auth::user();

        // Compte bloqué : connexion refusée
        if ($user->isBlocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Votre compte a été bloqué. Contactez TECHFLEX.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($user));
    }

       public function logout(Request $request)
   {
       $estEquipe = in_array($request->user()?->role, ['admin', 'revendeur'], true);

       Auth::logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();

       return redirect()->route($estEquipe ? 'login' : 'home');
   }
    private function homeFor(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'revendeur' => route('reseller.dashboard'),
            default => route('home'),
        };
    }
}
