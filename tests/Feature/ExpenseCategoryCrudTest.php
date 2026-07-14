<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ExpenseCategoryCrudTest — memastikan CRUD Kategori Pengeluaran berjalan dengan benar,
 * termasuk penolakan hapus jika kategori masih digunakan pengeluaran.
 */
class ExpenseCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_expense_categories()
    {
        $this->get('/expense_categories')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_expense_categories_index()
    {
        $this->authenticate('admin');
        $this->get('/expense_categories')->assertOk();

        $this->authenticate('owner');
        $this->get('/expense_categories')->assertOk();
    }

    public function test_admin_can_create_expense_category()
    {
        $this->authenticate('admin');

        $response = $this->post('/expense_categories', [
            'name' => 'Makan',
        ]);

        $response->assertRedirect('/expense_categories');
        $this->assertDatabaseHas('expense_categories', ['name' => 'Makan']);
    }

    public function test_create_expense_category_fails_with_duplicate_name()
    {
        ExpenseCategory::create(['name' => 'Minum']);
        $this->authenticate('admin');

        $response = $this->post('/expense_categories', ['name' => 'Minum']);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_expense_category()
    {
        $expenseCategory = ExpenseCategory::create(['name' => 'Beli Lama']);
        $this->authenticate('admin');

        $response = $this->patch("/expense_categories/{$expenseCategory->id}", [
            'name' => 'Beli Baru',
        ]);

        $response->assertRedirect('/expense_categories');
        $this->assertDatabaseHas('expense_categories', ['id' => $expenseCategory->id, 'name' => 'Beli Baru']);
    }

    public function test_admin_can_delete_unused_expense_category()
    {
        $expenseCategory = ExpenseCategory::create(['name' => 'Hapus Ini']);
        $this->authenticate('admin');

        $response = $this->delete("/expense_categories/{$expenseCategory->id}");

        $response->assertRedirect('/expense_categories');
        $this->assertDatabaseMissing('expense_categories', ['id' => $expenseCategory->id]);
    }

    public function test_cannot_delete_expense_category_used_by_expenses()
    {
        $expenseCategory = ExpenseCategory::create(['name' => 'Kategori Penting']);
        $user = $this->authenticate('admin');
        
        Expense::factory()->create([
            'expense_category_id' => $expenseCategory->id,
            'created_by' => $user->id
        ]);

        $response = $this->delete("/expense_categories/{$expenseCategory->id}");

        // Harus redirect back dengan error
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('expense_categories', ['id' => $expenseCategory->id]);
    }
}
