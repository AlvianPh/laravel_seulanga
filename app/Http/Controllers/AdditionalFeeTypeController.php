<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdditionalFeeTypeRequest;
use App\Models\AdditionalFeeType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdditionalFeeTypeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', AdditionalFeeType::class);
        $feeTypes = AdditionalFeeType::paginate(10);
        
        return view('additional_fee_types.index', compact('feeTypes'));
    }

    public function create()
    {
        $this->authorize('create', AdditionalFeeType::class);
        return view('additional_fee_types.create');
    }

    public function store(AdditionalFeeTypeRequest $request)
    {
        $this->authorize('create', AdditionalFeeType::class);
        AdditionalFeeType::create($request->validated());

        return redirect()->route('additional_fee_types.index')
                         ->with('success', 'Jenis Denda/Biaya berhasil ditambahkan.');
    }

    public function edit(AdditionalFeeType $additionalFeeType)
    {
        $this->authorize('update', $additionalFeeType);
        return view('additional_fee_types.edit', compact('additionalFeeType'));
    }

    public function update(AdditionalFeeTypeRequest $request, AdditionalFeeType $additionalFeeType)
    {
        $this->authorize('update', $additionalFeeType);
        $additionalFeeType->update($request->validated());

        return redirect()->route('additional_fee_types.index')
                         ->with('success', 'Jenis Denda/Biaya berhasil diperbarui.');
    }

    public function destroy(AdditionalFeeType $additionalFeeType)
    {
        $this->authorize('delete', $additionalFeeType);
        $additionalFeeType->delete();

        return redirect()->route('additional_fee_types.index')
                         ->with('success', 'Jenis Denda/Biaya berhasil dihapus.');
    }
}
