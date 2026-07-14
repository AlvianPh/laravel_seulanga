<?php

namespace App\Http\Controllers;

use App\Enums\JenisKelamin;
use App\Http\Requests\TenantRequest;
use App\Models\Tenant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * TenantController — manajemen data penghuni kost.
 */
class TenantController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan daftar penghuni.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tenant::class);

        $query = Tenant::query();

        // Search berdasarkan nama atau NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $tenants = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Form tambah penghuni baru.
     */
    public function create(): View
    {
        $this->authorize('create', Tenant::class);
        $genders = JenisKelamin::cases();
        return view('tenants.create', compact('genders'));
    }

    /**
     * Menyimpan data penghuni baru beserta lampiran foto.
     */
    public function store(TenantRequest $request): RedirectResponse
    {
        $this->authorize('create', Tenant::class);

        $validated = $request->validated();
        
        // Handle upload foto KTP
        if ($request->hasFile('ktp_photo')) {
            $validated['ktp_photo_path'] = $request->file('ktp_photo')->store('tenants/ktp', 'public');
        }

        // Handle upload foto penghuni
        if ($request->hasFile('tenant_photo')) {
            $validated['tenant_photo_path'] = $request->file('tenant_photo')->store('tenants/profiles', 'public');
        }

        Tenant::create($validated);

        return redirect()->route('tenants.index')
            ->with('success', 'Data penghuni berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail penghuni dan riwayat kontrak.
     */
    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);
        
        // Eager load relasi contracts beserta kamar yang bersangkutan
        $tenant->load(['contracts.room' => function ($q) {
            $q->latest('start_date');
        }]);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Form edit data penghuni.
     */
    public function edit(Tenant $tenant): View
    {
        $this->authorize('update', $tenant);
        $genders = JenisKelamin::cases();
        return view('tenants.edit', compact('tenant', 'genders'));
    }

    /**
     * Menyimpan perubahan data penghuni.
     */
    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        $validated = $request->validated();

        // Handle ganti foto KTP
        if ($request->hasFile('ktp_photo')) {
            // Hapus yang lama jika ada
            if ($tenant->ktp_photo_path && Storage::disk('public')->exists($tenant->ktp_photo_path)) {
                Storage::disk('public')->delete($tenant->ktp_photo_path);
            }
            $validated['ktp_photo_path'] = $request->file('ktp_photo')->store('tenants/ktp', 'public');
        }

        // Handle ganti foto profil
        if ($request->hasFile('tenant_photo')) {
            // Hapus yang lama jika ada
            if ($tenant->tenant_photo_path && Storage::disk('public')->exists($tenant->tenant_photo_path)) {
                Storage::disk('public')->delete($tenant->tenant_photo_path);
            }
            $validated['tenant_photo_path'] = $request->file('tenant_photo')->store('tenants/profiles', 'public');
        }

        $tenant->update($validated);

        return redirect()->route('tenants.index')
            ->with('success', 'Data penghuni berhasil diperbarui.');
    }

    /**
     * Menghapus penghuni (soft delete).
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorize('delete', $tenant);

        if ($tenant->contracts()->where('status', 'active')->exists()) {
            return back()->with('error', 'Penghuni ini masih memiliki kontrak aktif dan tidak dapat dihapus. Akhiri kontrak terlebih dahulu.');
        }

        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('success', 'Data penghuni berhasil dihapus.');
    }

    /**
     * Menghapus lampiran KTP.
     */
    public function deleteKtp(Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        if ($tenant->ktp_photo_path && Storage::disk('public')->exists($tenant->ktp_photo_path)) {
            Storage::disk('public')->delete($tenant->ktp_photo_path);
            $tenant->update(['ktp_photo_path' => null]);
        }

        return back()->with('success', 'Foto KTP berhasil dihapus.');
    }

    /**
     * Menghapus lampiran profil.
     */
    public function deletePhoto(Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        if ($tenant->tenant_photo_path && Storage::disk('public')->exists($tenant->tenant_photo_path)) {
            Storage::disk('public')->delete($tenant->tenant_photo_path);
            $tenant->update(['tenant_photo_path' => null]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
