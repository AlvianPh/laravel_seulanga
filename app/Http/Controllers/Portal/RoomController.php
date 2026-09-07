<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusKamar;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\TenantApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RoomController (Tenant Portal)
 *
 * Menampilkan katalog eksplorasi kamar yang tersedia (Room Discovery)
 * untuk calon penghuni kost.
 */
class RoomController extends Controller
{
    /**
     * Tampilkan daftar kamar yang tersedia untuk disewa.
     */
    public function index(Request $request): View
    {
        $query = Room::where('status', StatusKamar::Available)
            ->with(['roomType', 'facilities', 'photos']);

        // Filter tipe kamar
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // Filter lantai
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        $rooms = $query->orderBy('room_number')->paginate(12)->withQueryString();
        $roomTypes = RoomType::orderBy('name')->get();

        return view('portal.rooms.index', compact('rooms', 'roomTypes'));
    }

    /**
     * Tampilkan detail informasi kamar yang dipilih.
     */
    public function show(Room $room): View
    {
        $room->load(['roomType', 'facilities', 'photos']);

        $user = auth()->user();
        $canApply = $user->can('create', TenantApplication::class) && $room->isAvailable();

        return view('portal.rooms.show', compact('room', 'canApply'));
    }
}
