<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResellerInvitationController extends Controller
{
    public function show(string $token)
    {
        $user = $this->findInvitedUser($token);

        return view('reseller.activate', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function activate(string $token)
    {
        $user = $this->findInvitedUser($token);

        return view('reseller.invitation', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $user = $this->findInvitedUser($token);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_set_at' => now(),
            'email_verified_at' => $user->email_verified_at ?: now(),
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('reseller.dashboard')
            ->with('welcome', true)
            ->with('success', 'Votre accès revendeur est activé.');
    }

    private function findInvitedUser(string $token): User
    {
        $user = User::where('invitation_token', hash('sha256', $token))->first();

        if (! $user || ! $user->isReseller() || $user->password_set_at
            || ! $user->invitation_expires_at || $user->invitation_expires_at->isPast()) {
            abort(404);
        }

        return $user;
    }
}
