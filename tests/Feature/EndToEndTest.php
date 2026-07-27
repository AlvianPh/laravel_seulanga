<?php

namespace Tests\Feature;

use App\Enums\RoleUser;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Enums\StatusPembayaran;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvoiceOverdueNotification;
use Tests\TestCase;

class EndToEndTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_alur_1_kamar_kosong_ke_paid()
    {
        // 1. Alur lengkap "Kamar kosong → Penghuni baru → Kontrak dibuat →
        //    Status kamar berubah occupied → Generate tagihan → Bayar →
        //    Verifikasi → Status tagihan jadi paid".
        $user = $this->authenticate('admin');

        $roomType = RoomType::factory()->create();
        
        // Kamar kosong
        $room = Room::factory()->create([
            'status' => StatusKamar::Available->value,
            'room_number' => 'E2E-1',
            'room_type_id' => $roomType->id,
        ]);

        $this->assertEquals(StatusKamar::Available, $room->status);

        // Penghuni baru
        $tenant = Tenant::factory()->create();

        // Kontrak dibuat
        $response = $this->post('/contracts', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'rent_price' => 1500000,
            'deposit_amount' => 500000,
        ]);
        $response->assertRedirect('/contracts');

        // Status kamar berubah occupied
        $room->refresh();
        $this->assertEquals(StatusKamar::Occupied, $room->status);
        $contract = Contract::where('room_id', $room->id)->first();
        $this->assertEquals(StatusKontrak::Active, $contract->status);

        // Generate tagihan
        $response = $this->post('/invoices/generate-manual', [
            'month' => now()->month,
            'year' => now()->year,
        ]);
        
        $invoice = Invoice::where('contract_id', $contract->id)->first();
        $this->assertNotNull($invoice);
        // By default, generated invoice status is Pending
        $this->assertEquals(StatusTagihan::Pending, $invoice->status);

        // Bayar
        $paymentMethod = PaymentMethod::factory()->create();
        $response = $this->post("/payments", [
            'invoice_id' => $invoice->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => 1500000,
            'payment_date' => now()->format('Y-m-d'),
            'sender_name' => 'John Doe',
            'sender_bank' => 'BCA',
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(StatusPembayaran::Pending, $payment->status);

        // Verifikasi (Hanya Owner)
        $owner = User::factory()->create(['role' => RoleUser::Owner->value]);
        $this->actingAs($owner);
        $response = $this->post("/payments/{$payment->id}/verify", [
            'action' => 'verify'
        ]);
        $response->assertRedirect();

        // Status tagihan jadi paid
        $invoice->refresh();
        $this->assertEquals(StatusTagihan::Paid, $invoice->status);
        $payment->refresh();
        $this->assertEquals(StatusPembayaran::Verified, $payment->status);
    }

    public function test_alur_2_kontrak_berakhir()
    {
        // 2. Alur "Kontrak berakhir → Status kamar kembali available → Kamar
        //    bisa dipakai kontrak baru lagi".
        $this->authenticate('owner');
        
        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value]);
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create([
            'room_id'   => $room->id,
            'tenant_id' => $tenant->id,
            'status'    => StatusKontrak::Active->value,
        ]);

        // Terminate
        $this->post("/contracts/{$contract->id}/terminate");
        
        $room->refresh();
        $this->assertEquals(StatusKamar::Available, $room->status);

        // Kamar bisa dipakai kontrak baru
        $tenant2 = Tenant::factory()->create();
        $response = $this->post('/contracts', [
            'tenant_id' => $tenant2->id,
            'room_id' => $room->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'rent_price' => 1500000,
            'deposit_amount' => 500000,
        ]);
        $response->assertSessionHasNoErrors();
        $room->refresh();
        $this->assertEquals(StatusKamar::Occupied, $room->status);
    }

    public function test_alur_3_tagihan_overdue()
    {
        // 3. Alur "Tagihan lewat jatuh tempo tanpa dibayar → Status jadi
        //    overdue → Notifikasi overdue terkirim ke Owner & Admin".
        Notification::fake();

        $admin = $this->authenticate('admin');
        $owner = User::factory()->create(['role' => RoleUser::Owner->value]);

        $contract = Contract::factory()->create();
        $invoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'status' => StatusTagihan::Pending->value,
            'due_date' => now()->subDays(1),
        ]);

        // Run artisan command
        $this->artisan('notify:invoice-overdue')->assertSuccessful();

        $invoice->refresh();
        $this->assertEquals(StatusTagihan::Overdue, $invoice->status);

        Notification::assertSentTo(
            [$admin, $owner],
            InvoiceOverdueNotification::class
        );
    }

    public function test_alur_4_hapus_kamar_penghuni_ditolak_jika_kontrak_aktif()
    {
        // 4. Alur "Hapus Penghuni/Kamar yang masih punya kontrak aktif → harus
        //    DITOLAK (soft-delete tidak boleh terjadi kalau kontrak masih
        //    aktif)"
        $this->authenticate('admin');

        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value]);
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create([
            'room_id'   => $room->id,
            'tenant_id' => $tenant->id,
            'status'    => StatusKontrak::Active->value,
        ]);

        // Hapus kamar
        $response = $this->delete("/rooms/{$room->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'deleted_at' => null]);

        // Hapus penghuni
        $response = $this->delete("/tenants/{$tenant->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'deleted_at' => null]);
    }

    public function test_alur_5_otorisasi_laporan_dan_user()
    {
        // 5. Alur otorisasi: user role Admin BISA akses laporan keuangan, TAPI
        //    HANYA Owner yang bisa akses menu manajemen user (create/edit/delete akun).
        
        $admin = User::factory()->create(['role' => RoleUser::Admin->value]);
        $owner = User::factory()->create(['role' => RoleUser::Owner->value]);

        // Admin
        $this->actingAs($admin);
        // Akses laporan
        $this->get('/reports')->assertOk();
        // Akses user
        $this->get('/users')->assertForbidden();

        // Owner
        $this->actingAs($owner);
        // Akses laporan
        $this->get('/reports')->assertOk();
        // Akses user
        $this->get('/users')->assertOk();
    }
}
