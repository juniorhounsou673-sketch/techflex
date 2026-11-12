<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::with('user')->latest('id')->get();

        return view('admin.resellers.index', compact('resellers'));
    }

    public function create()
    {
        return view('admin.resellers.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => $request->filled('code') ? strtoupper(trim($request->input('code'))) : null,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'momo_number' => 'required|string|max:20',
            'code' => 'nullable|alpha_dash|max:20|unique:resellers,code',
            'password' => 'required|string|min:8',
        ]);

        $reseller = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],   // haché automatiquement (cast "hashed")
                'role' => 'revendeur',
                'status' => 'actif',
            ]);

            return Reseller::create([
                'user_id' => $user->id,
                'code' => $data['code'] ?: $this->nextCode(),
                'momo_number' => $data['momo_number'],
                'status' => 'actif',
            ]);
        });

        return redirect()->route('admin.resellers.index')
            ->with('success', "Accès donné à {$reseller->user->name}. Lien de vente : " . url('/r/' . $reseller->code));
    }

    public function block(Request $request, Reseller $reseller)
    {
        $request->validate(['reason' => 'nullable|string|max:190']);

        $reseller->block($request->input('reason'));

        return back()->with('success', "{$reseller->user->name} est bloqué. Il n'a plus accès à rien.");
    }

    public function unblock(Reseller $reseller)
    {
        $reseller->unblock();

        return back()->with('success', "{$reseller->user->name} est réactivé.");
    }

    private function nextCode(): string
    {
        $n = (int) Reseller::max('id') + 1;

        do {
            $code = 'REV' . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
            $n++;
        } while (Reseller::where('code', $code)->exists());

        return $code;
    }
}
