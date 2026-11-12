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
            'total_a_recevoir' => $resellers->sum(fn (Reseller $r) => $r->balance()),
            'commandes' => Order::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
