<?php

namespace Tests\Feature;

use App\Enums\KategoriPengeluaran;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_expenses()
    {
        $this->get('/expenses')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_expenses_list()
    {
        $this->authenticate('admin');
        $this->get('/expenses')->assertOk();

        $this->authenticate('owner');
        $this->get('/expenses')->assertOk();
    }

    public function test_can_create_expense_with_created_by_assigned()
    {
        $admin = $this->authenticate('admin');

        $response = $this->post('/expenses', [
            'category'     => KategoriPengeluaran::Electricity->value,
            'description'  => 'Bayar token PLN 500k',
            'amount'       => 500000,
            'expense_date' => '2026-07-13',
        ]);

        $response->assertRedirect('/expenses');
        
        $this->assertDatabaseHas('expenses', [
            'category'    => KategoriPengeluaran::Electricity->value,
            'description' => 'Bayar token PLN 500k',
            'amount'      => 500000,
            'created_by'  => $admin->id,
        ]);
    }

    public function test_fails_validation_for_invalid_category_or_amount()
    {
        $this->authenticate('admin');

        $response = $this->post('/expenses', [
            'category'     => 'kategori_bodong', // Invalid
            'description'  => 'Test',
            'amount'       => -1000, // Invalid
            'expense_date' => '2026-07-13',
        ]);

        $response->assertSessionHasErrors(['category', 'amount']);
    }

    public function test_can_upload_receipt_photo()
    {
        Storage::fake('public');
        $this->authenticate('admin');

        $file = UploadedFile::fake()->image('struk_pln.jpg');

        $response = $this->post('/expenses', [
            'category'     => KategoriPengeluaran::Electricity->value,
            'description'  => 'Test Upload',
            'amount'       => 100000,
            'expense_date' => '2026-07-13',
            'receipt_photo'=> $file,
        ]);

        $response->assertRedirect('/expenses');
        $expense = Expense::first();
        
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);
    }

    public function test_can_update_expense_and_replace_receipt()
    {
        Storage::fake('public');
        $this->authenticate('admin');

        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldFile->store('expenses/receipts', 'public');

        $expense = Expense::factory()->create([
            'category' => KategoriPengeluaran::Internet->value,
            'receipt_path' => $oldPath,
        ]);

        $newFile = UploadedFile::fake()->image('new.jpg');

        $response = $this->patch("/expenses/{$expense->id}", [
            'category'     => KategoriPengeluaran::Water->value, // ubah kategori
            'description'  => 'Ubah deskripsi',
            'amount'       => 150000,
            'expense_date' => '2026-07-14',
            'receipt_photo'=> $newFile,
        ]);

        $response->assertRedirect('/expenses');
        $expense->refresh();

        $this->assertEquals(KategoriPengeluaran::Water->value, $expense->category->value);
        
        // Cek file lama dihapus, file baru disimpan
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($expense->receipt_path);
    }

    public function test_can_delete_expense_and_its_receipt_is_removed()
    {
        Storage::fake('public');
        $this->authenticate('owner');

        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('expenses/receipts', 'public');

        $expense = Expense::factory()->create(['receipt_path' => $path]);

        $response = $this->delete("/expenses/{$expense->id}");

        $response->assertRedirect('/expenses');
        
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
