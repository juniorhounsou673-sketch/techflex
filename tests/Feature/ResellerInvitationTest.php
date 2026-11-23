<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResellerInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResellerInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Notification::fake();
    }

    public function test_admin_creates_reseller_without_choosing_password_and_sends_invitation(): void
    {
        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.resellers.store'), [
            'name' => 'Nouveau Revendeur',
            'email' => 'nouveau@techflex.test',
            'phone' => '+229 01 23 45 67',
            'momo_number' => '22901234567',
            'code' => 'REV900',
        ])->assertRedirect(route('admin.resellers.index'));

        $invited = User::where('email', 'nouveau@techflex.test')->firstOrFail();
        $this->assertNull($invited->password_set_at);
        $this->assertNotNull($invited->invitation_token);

        Notification::assertSentTo($invited, ResellerInvitation::class);
    }

    public function test_reseller_chooses_his_password_from_the_email_link_and_enters_his_area(): void
    {
        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.resellers.store'), [
            'name' => 'Revendeur Invité',
            'email' => 'invite@techflex.test',
            'momo_number' => '22901999999',
            'code' => 'REV901',
        ])->assertRedirect();

        $invited = User::where('email', 'invite@techflex.test')->firstOrFail();
        $url = null;

        Notification::assertSentTo($invited, ResellerInvitation::class, function ($notification) use (&$url) {
            $url = $notification->url;

            return str_contains($url, '/invitation/');
        });

        $this->get($url)->assertOk()->assertSee('Définir mon mot de passe');

        $this->post($url, [
            'password' => 'MonMotDePasse123!',
            'password_confirmation' => 'MonMotDePasse123!',
        ])->assertRedirect(route('reseller.dashboard'));

        $this->assertAuthenticatedAs($invited->fresh());
        $this->get('/revendeur')->assertOk();
        $this->assertNotNull($invited->fresh()->password_set_at);
        $this->assertNull($invited->fresh()->invitation_token);

        // Le lien n'est plus utilisable une fois le mot de passe défini.
        $this->get($url)->assertNotFound();
    }

    public function test_invited_reseller_cannot_log_in_before_setting_his_password(): void
    {
        $admin = User::where('email', 'admin@techflex.test')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.resellers.store'), [
            'name' => 'Revendeur En Attente',
            'email' => 'attente@techflex.test',
            'momo_number' => '22901888888',
            'code' => 'REV902',
        ])->assertRedirect();

        $invited = User::where('email', 'attente@techflex.test')->firstOrFail();
        // Même avec un mot de passe connu, l'accès reste fermé tant que l'invitation n'est pas acceptée.
        $invited->forceFill(['password' => Hash::make('MotDePasse123!')])->save();
        $this->assertTrue(Hash::check('MotDePasse123!', $invited->fresh()->password));
        $this->assertSame('revendeur', $invited->fresh()->role);
        $this->assertNull($invited->fresh()->password_set_at);
        $this->assertTrue($invited->fresh()->needsPasswordSetup());

        // On se déconnecte de la session admin pour tester une vraie tentative de connexion.
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => 'attente@techflex.test',
            'password' => 'MotDePasse123!',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
