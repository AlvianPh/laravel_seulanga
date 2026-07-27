<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BankAccountController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', BankAccount::class);
        $bankAccounts = BankAccount::paginate(10);
        
        return view('bank_accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $this->authorize('create', BankAccount::class);
        return view('bank_accounts.create');
    }

    public function store(BankAccountRequest $request)
    {
        $this->authorize('create', BankAccount::class);
        BankAccount::create($request->validated());

        return redirect()->route('bank_accounts.index')
                         ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function edit(BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        return view('bank_accounts.edit', compact('bankAccount'));
    }

    public function update(BankAccountRequest $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        $bankAccount->update($request->validated());

        return redirect()->route('bank_accounts.index')
                         ->with('success', 'Rekening berhasil diperbarui.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);
        $bankAccount->delete();

        return redirect()->route('bank_accounts.index')
                         ->with('success', 'Rekening berhasil dihapus.');
    }
}
