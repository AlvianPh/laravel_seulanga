<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PaymentMethodCrudTest — memastikan CRUD Metode Pembayaran berjalan dengan benar,
 * termasuk penolakan hapus jika Metode Pembayaran masih digunakan pembayaran.
 */
class PaymentMethodCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_payment_methods()
    {
        $this->get('/payment_methods')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_payment_methods_index()
    {
        $this->authenticate('admin');
        $this->get('/payment_methods')->assertOk();

        $this->authenticate('owner');
        $this->get('/payment_methods')->assertOk();
    }

    public function test_admin_can_create_payment_method()
    {
        $this->authenticate('admin');

        $response = $this->post('/payment_methods', [
            'name' => 'Bitcoin',
        ]);

        $response->assertRedirect('/payment_methods');
        $this->assertDatabaseHas('payment_methods', ['name' => 'Bitcoin']);
    }

    public function test_create_payment_method_fails_with_duplicate_name()
    {
        PaymentMethod::create(['name' => 'PayPal']);
        $this->authenticate('admin');

        $response = $this->post('/payment_methods', ['name' => 'PayPal']);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_payment_method()
    {
        $paymentMethod = PaymentMethod::create(['name' => 'PayLama']);
        $this->authenticate('admin');

        $response = $this->patch("/payment_methods/{$paymentMethod->id}", [
            'name' => 'PayBaru',
        ]);

        $response->assertRedirect('/payment_methods');
        $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id, 'name' => 'PayBaru']);
    }

    public function test_admin_can_delete_unused_payment_method()
    {
        $paymentMethod = PaymentMethod::create(['name' => 'Hapus Ini']);
        $this->authenticate('admin');

        $response = $this->delete("/payment_methods/{$paymentMethod->id}");

        $response->assertRedirect('/payment_methods');
        $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);
    }

    public function test_cannot_delete_payment_method_used_by_payments()
    {
        $paymentMethod = PaymentMethod::create(['name' => 'Metode Penting']);
        
        $roomType = RoomType::firstOrCreate(['name' => 'Standard'], ['default_price' => 1000000]);
        $room = Room::factory()->create(['room_number' => 'Y001', 'room_type_id' => $roomType->id]);
        $tenant = Tenant::factory()->create();
        $invoice = Invoice::factory()->create(['tenant_id' => $tenant->id, 'room_id' => $room->id]);
        
        Payment::factory()->create([
            'payment_method_id' => $paymentMethod->id,
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id
        ]);

        $this->authenticate('admin');

        $response = $this->delete("/payment_methods/{$paymentMethod->id}");

        // Harus redirect back dengan error
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id]);
    }
}
