<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reseller;

class DashboardController extends Controller
{
    public function index()
    {
        $resellers = Reseller::all();

        $stats = [
            'actifs' => $resellers->where('status', 'actif')->count(),
            'bloques' => $resellers->where('status', 'bloque')->count(),
            'ils_doivent' => $resellers->sum(fn (Reseller $r) => max($r->balance(), 0)),
            'vous_devez' => $resellers->sum(fn (Reseller $r) => max(-$r->balance(), 0)),
            'commandes' => Order::count(),
            'a_confirmer' => Order::where('payment_status', 'en_attente')
                ->whereIn('payment_method', ['momo_direct', 'momo_revendeur'])
                ->whereNotNull('momo_transaction_id')
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
