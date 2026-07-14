<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityRequest;
use App\Models\Facility;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * FacilityController — manajemen master data fasilitas kamar.
 * Dapat diakses oleh Owner dan Admin.
 */
class FacilityController extends Controller
{
    use AuthorizesRequests;

    /** Menampilkan daftar fasilitas. */
    public function index(): View
    {
        $this->authorize('viewAny', Facility::class);

        $facilities = Facility::withCount('rooms')->orderBy('name')->get();

        return view('facilities.index', compact('facilities'));
    }

    /** Form tambah fasilitas baru. */
    public function create(): View
    {
        $this->authorize('create', Facility::class);

        return view('facilities.create');
    }

    /** Menyimpan fasilitas baru. */
    public function store(FacilityRequest $request): RedirectResponse
    {
        $this->authorize('create', Facility::class);

        Facility::create($request->validated());

        return redirect()->route('facilities.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    /** Form edit fasilitas. */
    public function edit(Facility $facility): View
    {
        $this->authorize('update', $facility);

        return view('facilities.edit', compact('facility'));
    }

    /** Menyimpan perubahan fasilitas. */
    public function update(FacilityRequest $request, Facility $facility): RedirectResponse
    {
        $this->authorize('update', $facility);

        $facility->update($request->validated());

        return redirect()->route('facilities.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    /** Menghapus fasilitas — ditolak jika masih dipakai kamar. */
    public function destroy(Facility $facility): RedirectResponse
    {
        $this->authorize('delete', $facility);

        if ($facility->isUsed()) {
            return back()->with(
                'error',
                "Fasilitas \"{$facility->name}\" tidak dapat dihapus karena masih digunakan oleh {$facility->rooms()->count()} kamar. Hapus relasi fasilitas tersebut terlebih dahulu."
            );
        }

        $facility->delete();

        return redirect()->route('facilities.index')
            ->with('success', 'Fasilitas berhasil dihapus.');
    }
}
