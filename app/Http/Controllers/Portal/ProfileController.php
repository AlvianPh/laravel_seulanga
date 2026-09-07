<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ProfileController (Tenant Portal)
 *
 * Mengelola informasi profil mandiri penghuni kost.
 * Keamanan ketat: hanya kolom data kontak & identitas yang boleh diubah.
 * Kolom sensitif (role, user_id, status keuangan) tidak dapat diakses.
 */
class ProfileController extends Controller
{
    /**
     * Tampilkan formulir profil penghuni.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        return view('portal.profile', compact('user', 'tenant'));
    }

    /**
     * Perbarui data profil penghuni.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update data kontak penghuni yang terhubung
        if ($tenant = $user->tenant) {
            $tenant->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $tenant->phone,
                'address' => $validated['address'] ?? $tenant->address,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? $tenant->emergency_contact_name,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $tenant->emergency_contact_phone,
            ]);
        }

        return redirect()->route('portal.profile.edit')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}
