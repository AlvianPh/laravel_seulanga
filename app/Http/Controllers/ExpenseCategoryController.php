<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ExpenseCategory::class);
        $expense_categories = ExpenseCategory::withCount('expenses')->paginate(10);
        
        return view('expense_categories.index', compact('expense_categories'));
    }

    public function create()
    {
        $this->authorize('create', ExpenseCategory::class);
        return view('expense_categories.create');
    }

    public function store(ExpenseCategoryRequest $request)
    {
        $this->authorize('create', ExpenseCategory::class);
        ExpenseCategory::create($request->validated());

        return redirect()->route('expense_categories.index')
                         ->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $this->authorize('update', $expenseCategory);
        return view('expense_categories.edit', compact('expenseCategory'));
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        $this->authorize('update', $expenseCategory);
        $expenseCategory->update($request->validated());

        return redirect()->route('expense_categories.index')
                         ->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $this->authorize('delete', $expenseCategory);

        try {
            $expenseCategory->delete();
            return redirect()->route('expense_categories.index')
                             ->with('success', 'Kategori pengeluaran berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                                 ->with('error', 'Kategori pengeluaran tidak dapat dihapus karena masih digunakan pada data pengeluaran.');
            }
            throw $e;
        }
    }
}
