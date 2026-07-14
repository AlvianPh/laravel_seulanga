<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentMethodController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $methods = PaymentMethod::withCount('payments')->paginate(10);
        
        return view('payment_methods.index', compact('methods'));
    }

    public function create()
    {
        $this->authorize('create', PaymentMethod::class);
        return view('payment_methods.create');
    }

    public function store(PaymentMethodRequest $request)
    {
        $this->authorize('create', PaymentMethod::class);
        PaymentMethod::create($request->validated());

        return redirect()->route('payment_methods.index')
                         ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        return view('payment_methods.edit', compact('paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        $paymentMethod->update($request->validated());

        return redirect()->route('payment_methods.index')
                         ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);

        try {
            $paymentMethod->delete();
            return redirect()->route('payment_methods.index')
                             ->with('success', 'Metode pembayaran berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                                 ->with('error', 'Metode pembayaran tidak dapat dihapus karena masih digunakan pada data pembayaran.');
            }
            throw $e;
        }
    }
}
