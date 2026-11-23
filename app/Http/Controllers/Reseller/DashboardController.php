<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $reseller = auth()->user()->reseller;

        abort_unless($reseller && ! $reseller->isBlocked(), 403);

        $paid = $reseller->orders()
            ->where('payment_status', 'paye')
            ->where('status', '!=', 'annulee')
            ->where('taken_over_by_admin', false);

        $sales = (int) (clone $paid)->sum('total');

        return view('reseller.dashboard', [
            'reseller' => $reseller,
            'link' => url('/r/' . $reseller->code),
            'orders' => $reseller->orders()->latest()->take(20)->get(),
            'products' => Product::where('is_active', true)->with('category')->orderBy('category_id')->orderBy('name')->get(),
            'sales' => $sales,
            'profit' => $reseller->totalGain(),
            'balance' => $reseller->balance(),
            'settlements' => $reseller->settlements()->latest('paid_at')->latest('id')->take(10)->get(),
        ]);
    }
}
