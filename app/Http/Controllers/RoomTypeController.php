<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * RoomTypeController — manajemen master data tipe kamar.
 * Dapat diakses oleh Owner dan Admin.
 */
class RoomTypeController extends Controller
{
    use AuthorizesRequests;

    /** Menampilkan daftar tipe kamar. */
    public function index(): View
    {
        $this->authorize('viewAny', RoomType::class);

        $roomTypes = RoomType::withCount('rooms')->orderBy('name')->get();

        return view('room_types.index', compact('roomTypes'));
    }

    /** Form tambah tipe kamar baru. */
    public function create(): View
    {
        $this->authorize('create', RoomType::class);

        return view('room_types.create');
    }

    /** Menyimpan tipe kamar baru. */
    public function store(RoomTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', RoomType::class);

        RoomType::create($request->validated());

        return redirect()->route('room_types.index')
            ->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    /** Form edit tipe kamar. */
    public function edit(RoomType $roomType): View
    {
        $this->authorize('update', $roomType);

        return view('room_types.edit', compact('roomType'));
    }

    /** Menyimpan perubahan tipe kamar. */
    public function update(RoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $this->authorize('update', $roomType);

        $roomType->update($request->validated());

        return redirect()->route('room_types.index')
            ->with('success', 'Tipe kamar berhasil diperbarui.');
    }

    /** Menghapus tipe kamar — ditolak jika masih dipakai kamar. */
    public function destroy(RoomType $roomType): RedirectResponse
    {
        $this->authorize('delete', $roomType);

        if ($roomType->isUsed()) {
            return back()->with(
                'error',
                "Tipe kamar \"{$roomType->name}\" tidak dapat dihapus karena masih digunakan oleh {$roomType->rooms()->count()} kamar. Ubah tipe kamar tersebut terlebih dahulu."
            );
        }

        $roomType->delete();

        return redirect()->route('room_types.index')
            ->with('success', 'Tipe kamar berhasil dihapus.');
    }
}
