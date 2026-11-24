<?php

namespace Tests\Feature;

use App\Models\Reseller;
use App\Models\User;
use App\Notifications\ResellerAccountBlocked;
use App\Notifications\ResellerAccountUnblocked;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResellerStatusNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_blocking_reseller_notifies_admin_and_reseller(): void
    {
        Notification::fake();

        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();
        $reseller = Reseller::where('code', 'REV001')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.resellers.block', $reseller), [
            'reason' => 'Impayé',
        ])->assertRedirect();

        $this->assertDatabaseHas('resellers', ['id' => $reseller->id, 'status' => 'bloque']);
        $this->assertDatabaseHas('users', ['id' => $reseller->user_id, 'status' => 'bloque']);

        $blockedNotification = null;

        Notification::assertSentTo($admin, ResellerAccountBlocked::class, function ($notification) use (&$blockedNotification): bool {
            $blockedNotification = $notification;

            return $notification->resellerCode === 'REV001'
                && $notification->reason === 'Impayé';
        });
        Notification::assertSentTo($reseller->user, ResellerAccountBlocked::class);

        $html = $blockedNotification->toMail($admin)->render();
        $this->assertStringContainsString('REV001', $html);
        $this->assertStringContainsString('Impayé', $html);
    }

    public function test_unblocking_reseller_notifies_admin_and_reseller(): void
    {
        Notification::fake();

        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();
        $reseller = Reseller::where('code', 'REV002')->firstOrFail();
        $reseller->block('Test');

        $this->actingAs($admin)->post(route('admin.resellers.unblock', $reseller))->assertRedirect();

        $this->assertDatabaseHas('resellers', ['id' => $reseller->id, 'status' => 'actif']);
        $this->assertDatabaseHas('users', ['id' => $reseller->user_id, 'status' => 'actif']);

        $unblockedNotification = null;

        Notification::assertSentTo($admin, ResellerAccountUnblocked::class, function ($notification) use (&$unblockedNotification): bool {
            $unblockedNotification = $notification;

            return $notification->resellerCode === 'REV002';
        });
        Notification::assertSentTo($reseller->user, ResellerAccountUnblocked::class);

        $this->assertStringContainsString('REV002', $unblockedNotification->toMail($admin)->render());
    }
}
