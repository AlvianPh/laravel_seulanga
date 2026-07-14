<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Pindahkan data fasilitas dari kolom JSON rooms.facilities
 * ke tabel pivot room_facilities (melalui tabel master facilities).
 *
 * Setiap nama fasilitas unik akan di-firstOrCreate di tabel facilities,
 * lalu relasinya disimpan di room_facilities.
 * Tidak ada data fasilitas kamar yang hilang.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rooms = DB::table('rooms')
            ->whereNotNull('facilities')
            ->get(['id', 'facilities']);

        $totalRooms      = 0;
        $totalPivotRows  = 0;
        $skipped         = 0;

        foreach ($rooms as $room) {
            $facilitiesJson = $room->facilities;

            // Decode JSON — bisa berupa string JSON atau sudah array
            $names = is_string($facilitiesJson)
                ? json_decode($facilitiesJson, true)
                : $facilitiesJson;

            if (! is_array($names) || empty($names)) {
                $skipped++;
                continue;
            }

            $totalRooms++;
            $facilityIds = [];

            foreach ($names as $name) {
                $name = trim((string) $name);
                if ($name === '') {
                    continue;
                }

                // Cari atau buat row di tabel facilities
                $existing = DB::table('facilities')->where('name', $name)->first();

                if ($existing) {
                    $facilityId = $existing->id;
                } else {
                    $facilityId = DB::table('facilities')->insertGetId([
                        'name'       => $name,
                        'icon'       => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $facilityIds[] = $facilityId;
            }

            // Insert ke pivot (ignore duplicate jika sudah ada)
            foreach (array_unique($facilityIds) as $fId) {
                $exists = DB::table('room_facilities')
                    ->where('room_id', $room->id)
                    ->where('facility_id', $fId)
                    ->exists();

                if (! $exists) {
                    DB::table('room_facilities')->insert([
                        'room_id'     => $room->id,
                        'facility_id' => $fId,
                    ]);
                    $totalPivotRows++;
                }
            }
        }

        echo "\n[facilities migration] {$totalRooms} kamar dimigrasi, {$totalPivotRows} baris pivot dibuat, {$skipped} kamar dilewati (JSON kosong/null).\n";
    }

    public function down(): void
    {
        // Kembalikan data pivot ke JSON di kolom facilities (best-effort)
        $rooms = DB::table('room_facilities')
            ->select('room_id')
            ->distinct()
            ->pluck('room_id');

        foreach ($rooms as $roomId) {
            $facilityIds = DB::table('room_facilities')
                ->where('room_id', $roomId)
                ->pluck('facility_id');

            $names = DB::table('facilities')
                ->whereIn('id', $facilityIds)
                ->pluck('name')
                ->toArray();

            DB::table('rooms')
                ->where('id', $roomId)
                ->update(['facilities' => json_encode($names)]);
        }

        DB::table('room_facilities')->truncate();
    }
};
