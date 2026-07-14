<?php

namespace App\Http\Controllers;

use App\Enums\StatusKamar;
use App\Http\Requests\RoomRequest;
use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\RoomType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * RoomController — manajemen data kamar kost (CRUD).
 * Dapat diakses oleh Owner dan Admin.
 */
class RoomController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan daftar kamar dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Room::class);

        $query = Room::query()->with(['roomType', 'photos']); // Eager load tipe & foto

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tipe kamar
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // Search berdasarkan nomor kamar
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('room_number', 'like', "%{$search}%");
        }

        $rooms     = $query->orderBy('room_number')->paginate(10)->withQueryString();
        $statuses  = StatusKamar::cases();
        $roomTypes = RoomType::orderBy('name')->get();

        return view('rooms.index', compact('rooms', 'statuses', 'roomTypes'));
    }

    /**
     * Form tambah kamar baru.
     */
    public function create(): View
    {
        $this->authorize('create', Room::class);

        $statuses   = StatusKamar::cases();
        $roomTypes  = RoomType::orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();

        return view('rooms.create', compact('statuses', 'roomTypes', 'facilities'));
    }

    /**
     * Menyimpan data kamar baru beserta foto dan fasilitas.
     */
    public function store(RoomRequest $request): RedirectResponse
    {
        $this->authorize('create', Room::class);

        $validated = $request->validated();

        // Simpan data kamar
        $room = Room::create([
            'room_number'   => $validated['room_number'],
            'floor'         => $validated['floor'],
            'room_type_id'  => $validated['room_type_id'],
            'size_m2'       => $validated['size_m2'],
            'monthly_price' => $validated['monthly_price'],
            'deposit_price' => $validated['deposit_price'],
            'status'        => $validated['status'],
        ]);

        // Sync fasilitas via pivot
        $room->facilities()->sync($validated['facilities'] ?? []);

        // Proses upload foto jika ada
        if ($request->hasFile('photos')) {
            $isFirst = true;
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('rooms', 'public');

                RoomPhoto::create([
                    'room_id'    => $room->id,
                    'file_path'  => $path,
                    'is_primary' => $isFirst, // Foto pertama otomatis jadi primary
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('rooms.index')
            ->with('success', 'Data kamar berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail kamar.
     */
    public function show(Room $room): View
    {
        $this->authorize('view', $room);
        $room->load(['roomType', 'facilities', 'photos', 'contracts' => function ($q) {
            $q->latest()->limit(5); // Tampilkan 5 kontrak terakhir di detail
        }]);

        return view('rooms.show', compact('room'));
    }

    /**
     * Form edit data kamar.
     */
    public function edit(Room $room): View
    {
        $this->authorize('update', $room);
        $room->load(['photos', 'facilities']);

        $statuses   = StatusKamar::cases();
        $roomTypes  = RoomType::orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();

        return view('rooms.edit', compact('room', 'statuses', 'roomTypes', 'facilities'));
    }

    /**
     * Menyimpan perubahan data kamar.
     */
    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $validated = $request->validated();

        $room->update([
            'room_number'   => $validated['room_number'],
            'floor'         => $validated['floor'],
            'room_type_id'  => $validated['room_type_id'],
            'size_m2'       => $validated['size_m2'],
            'monthly_price' => $validated['monthly_price'],
            'deposit_price' => $validated['deposit_price'],
            'status'        => $validated['status'],
        ]);

        // Sync fasilitas via pivot
        $room->facilities()->sync($validated['facilities'] ?? []);

        // Proses penambahan foto baru
        if ($request->hasFile('photos')) {
            // Jika kamar belum punya foto sama sekali, set foto baru pertama sebagai primary
            $hasPrimary = $room->photos()->where('is_primary', true)->exists();
            $isFirst    = ! $hasPrimary;

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('rooms', 'public');

                RoomPhoto::create([
                    'room_id'    => $room->id,
                    'file_path'  => $path,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('rooms.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    /**
     * Menghapus kamar (soft delete).
     */
    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        if ($room->contracts()->where('status', 'active')->exists()) {
            return back()->with('error', 'Kamar ini masih memiliki kontrak aktif dan tidak dapat dihapus. Akhiri kontrak terlebih dahulu.');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Kamar berhasil dihapus.');
    }

    /**
     * Menghapus spesifik foto kamar (dipanggil via rute khusus).
     */
    public function deletePhoto(Room $room, RoomPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $room);

        // Pastikan foto milik kamar ini
        if ($photo->room_id !== $room->id) {
            abort(403);
        }

        // Hapus file
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $wasPrimary = $photo->is_primary;
        $photo->delete();

        // Jika foto utama dihapus, dan masih ada foto lain, jadikan foto pertama yang ada sebagai primary
        if ($wasPrimary) {
            $nextPhoto = $room->photos()->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Set foto menjadi primary.
     */
    public function setPrimaryPhoto(Room $room, RoomPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $room);

        if ($photo->room_id !== $room->id) {
            abort(403);
        }

        // Unset semua primary di kamar ini
        $room->photos()->update(['is_primary' => false]);

        // Set foto ini jadi primary
        $photo->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diubah.');
    }
}
