<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Models\AdditionalFeeType;
use App\Models\BankAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SettingController extends Controller
{
    use AuthorizesRequests;

    public function edit()
    {
        $this->authorize('view', Setting::class);
        
        $setting = Setting::getInstance();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $feeTypes = AdditionalFeeType::where('is_active', true)->get();

        return view('settings.edit', compact('setting', 'bankAccounts', 'feeTypes'));
    }

    public function update(SettingRequest $request)
    {
        $this->authorize('update', Setting::class);
        
        $setting = Setting::getInstance();
        $data = $request->validated();
        
        if ($request->hasFile('kost_logo')) {
            if ($setting->kost_logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->kost_logo);
            }
            $data['kost_logo'] = $request->file('kost_logo')->store('logos', 'public');
        }

        $setting->update($data);

        return redirect()->route('settings.edit')
                         ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
