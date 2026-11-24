<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PerformanceReport;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendTopReportCommand extends Command
{
    protected $signature = 'techflex:send-top {period=weekly : weekly ou monthly}';

    protected $description = 'Envoie par email les tops produits et revendeurs de la période';

    public function handle(): int
    {
        $period = (string) $this->argument('period');

        if (! in_array($period, ['weekly', 'monthly'], true)) {
            $this->error('La période doit être « weekly » ou « monthly ».');

            return self::INVALID;
        }

        [$start, $end, $label, $dates] = $this->period($period);

        $notification = new PerformanceReport(
            periodLabel: $label,
            periodDates: $dates,
            topProducts: $this->topProducts($start, $end),
            topResellers: $this->topResellers($start, $end),
        );

        $recipients = 0;

        User::query()
            ->whereIn('role', ['admin', 'revendeur'])
            ->where('status', 'actif')
            ->chunkById(100, function ($users) use ($notification, &$recipients): void {
                Notification::send($users, $notification);

                $recipients += $users->count();
            });

        $this->info("Tops {$label} envoyés à {$recipients} destinataire(s).");

        return self::SUCCESS;
    }

    /** @return array{CarbonInterface, CarbonInterface, string, string} */
    private function period(string $period): array
    {
        // Les timestamps de la base sont écrits dans le fuseau applicatif.
        $timezone = (string) config('app.timezone');

        if ($period === 'weekly') {
            $start = now($timezone)->subWeek()->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

            return [
                $start,
                $end,
                'de la semaine',
                'du '.$start->translatedFormat('d/m/Y').' au '.$end->translatedFormat('d/m/Y'),
            ];
        }

        $start = now($timezone)->subMonthNoOverflow()->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return [
            $start,
            $end,
            'du mois',
            $start->translatedFormat('F Y'),
        ];
    }

    /** @return array<int, array{name: string, quantity: int, amount: int}> */
    private function topProducts(CarbonInterface $start, CarbonInterface $end): array
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paye')
            ->where('orders.status', '!=', 'annulee')
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('order_items.product_name as name')
            ->selectRaw('SUM(order_items.quantity) as quantity')
            ->selectRaw('SUM(order_items.quantity * order_items.prix_vente) as amount')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('quantity')
            ->orderBy('order_items.product_name')
            ->limit(5)
            ->get()
            ->map(fn ($row): array => [
                'name' => (string) $row->name,
                'quantity' => (int) $row->quantity,
                'amount' => (int) $row->amount,
            ])
            ->all();
    }

    /** @return array<int, array{name: string, code: string, orders: int, amount: int, profit: int}> */
    private function topResellers(CarbonInterface $start, CarbonInterface $end): array
    {
        return DB::table('orders')
            ->join('resellers', 'resellers.id', '=', 'orders.reseller_id')
            ->join('users', 'users.id', '=', 'resellers.user_id')
            ->where('orders.payment_status', 'paye')
            ->where('orders.status', '!=', 'annulee')
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('users.name as name')
            ->selectRaw('resellers.code as code')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(orders.total) as amount')
            ->selectRaw('SUM(orders.total - orders.montant_du_techflex) as profit')
            ->groupBy('orders.reseller_id', 'users.name', 'resellers.code')
            ->orderByDesc('amount')
            ->orderBy('resellers.code')
            ->limit(5)
            ->get()
            ->map(fn ($row): array => [
                'name' => (string) $row->name,
                'code' => (string) $row->code,
                'orders' => (int) $row->orders_count,
                'amount' => (int) $row->amount,
                'profit' => (int) $row->profit,
            ])
            ->all();
    }
}
