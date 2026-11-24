<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\User;
use App\Notifications\PerformanceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendTopReportCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_weekly_report_notifies_team_with_ranked_data(): void
    {
        Notification::fake();

        $this->travelTo('2026-11-18 10:00:00');

        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();
        $reseller = Reseller::where('code', 'REV001')->firstOrFail();
        $product = Product::firstOrFail();

        $order = new Order([
            'reference' => 'TOP-TEST-1',
            'client_name' => 'Client Top',
            'client_phone' => '22901000000',
            'reseller_id' => $reseller->id,
            'total' => 50000,
            'montant_du_techflex' => 20000,
            'payment_method' => 'momo_revendeur',
            'payment_status' => 'paye',
            'paid_at' => '2026-11-12 10:00:00',
            'status' => 'livree',
        ]);
        $order->timestamps = false;
        $order->created_at = '2026-11-12 10:00:00';
        $order->updated_at = '2026-11-12 10:00:00';
        $order->save();

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Produit Top',
            'quantity' => 2,
            'prix_techflex' => 10000,
            'prix_vente' => 25000,
        ]);

        $this->artisan('techflex:send-top', ['period' => 'weekly'])->assertSuccessful();

        $notification = null;

        Notification::assertSentTo($admin, PerformanceReport::class, function ($sent) use (&$notification): bool {
            $notification = $sent;

            return true;
        });
        Notification::assertSentTo($reseller->user, PerformanceReport::class);

        $html = $notification->toMail($admin)->render();
        $this->assertStringContainsString('Produit Top', $html);
        $this->assertStringContainsString('Revendeur Un', $html);

        $this->assertSame('de la semaine', $notification->periodLabel);
        $this->assertSame([
            ['name' => 'Produit Top', 'quantity' => 2, 'amount' => 50000],
        ], $notification->topProducts);
        $this->assertSame([
            [
                'name' => 'Revendeur Un',
                'code' => 'REV001',
                'orders' => 1,
                'amount' => 50000,
                'profit' => 30000,
            ],
        ], $notification->topResellers);
    }

    public function test_rejects_unknown_report_period(): void
    {
        $this->artisan('techflex:send-top', ['period' => 'yearly'])->assertFailed();
    }
}
