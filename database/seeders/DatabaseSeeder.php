<?php

namespace Database\Seeders;

use App\Enums\MetodePembayaran;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder — mengisi database dengan data contoh yang realistis.
 *
 * Data yang dibuat:
 *  - 2 User (1 owner, 1 admin)
 *  - 10 Kamar (7 occupied, 2 available, 1 maintenance)
 *  - 2 foto per kamar (1 foto utama)
 *  - 15 Penghuni
 *  - 12 Kontrak (8 active, 4 ended)
 *  - Tagihan & pembayaran untuk 3 bulan terakhir
 *  - Beberapa pengeluaran operasional
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. USER ────────────────────────────────────────────────────────────
        $owner = User::factory()->owner()->create([
            'name'  => 'Budi Santoso',
            'email' => 'owner@kost.test',
        ]);

        $admin = User::factory()->admin()->create([
            'name'  => 'Rina Wijaya',
            'email' => 'admin@kost.test',
        ]);

        // ── 2. KAMAR ───────────────────────────────────────────────────────────
        // Buat 10 kamar dengan nomor berurutan
        $rooms = collect();
        $lantai = [1, 1, 1, 1, 2, 2, 2, 2, 3, 3];
        $nomor  = ['101', '102', '103', '104', '201', '202', '203', '204', '301', '302'];

        foreach (range(0, 9) as $i) {
            $room = Room::factory()->create([
                'room_number' => $nomor[$i],
                'floor'       => $lantai[$i],
            ]);
            $rooms->push($room);
        }

        // Buat 2 foto per kamar (1 foto utama)
        foreach ($rooms as $room) {
            RoomPhoto::factory()->primary()->create(['room_id' => $room->id]);
            RoomPhoto::factory()->create(['room_id' => $room->id]);
        }

        // ── 3. PENGHUNI ────────────────────────────────────────────────────────
        $tenants = Tenant::factory(15)->create();

        // ── 4. KONTRAK ─────────────────────────────────────────────────────────
        // 4 kontrak ended — pakai kamar 0-3, penghuni 0-3
        $endedContracts = collect();
        foreach (range(0, 3) as $i) {
            $room      = $rooms[$i];
            $tenant    = $tenants[$i];
            $startDate = Carbon::now()->subMonths(fake()->numberBetween(14, 24));
            $endDate   = (clone $startDate)->addMonths(12);

            $contract = Contract::create([
                'tenant_id'      => $tenant->id,
                'room_id'        => $room->id,
                'start_date'     => $startDate->format('Y-m-d'),
                'end_date'       => $endDate->format('Y-m-d'),
                'rent_price'     => $room->monthly_price,
                'deposit_amount' => $room->deposit_price,
                'status'         => StatusKontrak::Ended,
                'created_by'     => $admin->id,
            ]);

            $endedContracts->push($contract);
        }

        // 8 kontrak active — pakai kamar 2-9, penghuni 4-11
        // (kamar 0-3 bisa dipakai ulang karena kontrak sebelumnya sudah ended)
        $activeContracts = collect();
        foreach (range(0, 7) as $i) {
            $room      = $rooms[$i + 2]; // kamar index 2-9
            $tenant    = $tenants[$i + 4]; // penghuni index 4-11
            $startDate = Carbon::now()->subMonths(fake()->numberBetween(2, 10));
            $endDate   = (clone $startDate)->addMonths(12);

            $contract = Contract::create([
                'tenant_id'      => $tenant->id,
                'room_id'        => $room->id,
                'start_date'     => $startDate->format('Y-m-d'),
                'end_date'       => $endDate->format('Y-m-d'),
                'rent_price'     => $room->monthly_price,
                'deposit_amount' => $room->deposit_price,
                'status'         => StatusKontrak::Active,
                'created_by'     => $admin->id,
            ]);

            // Update status kamar jadi occupied
            $room->update(['status' => StatusKamar::Occupied]);

            $activeContracts->push($contract);
        }

        // Kamar index 0 dan 1 sudah selesai kontraknya → kembali available (default)
        // Kamar index 9 (302) → maintenance
        $rooms[9]->update(['status' => StatusKamar::Maintenance]);

        // ── 5. TAGIHAN & PEMBAYARAN (3 bulan terakhir) ─────────────────────────
        $bulanTagihan = [
            Carbon::now()->subMonths(2)->startOfMonth(), // 2 bulan lalu
            Carbon::now()->subMonths(1)->startOfMonth(), // bulan lalu
            Carbon::now()->startOfMonth(),               // bulan ini
        ];

        foreach ($activeContracts as $contract) {
            foreach ($bulanTagihan as $bulan) {
                $isBulanIni     = $bulan->isSameMonth(Carbon::now());
                $isBulanLalu    = $bulan->isSameMonth(Carbon::now()->subMonth());
                $isBulan2Lalu   = $bulan->isSameMonth(Carbon::now()->subMonths(2));

                $electricityFee = fake()->numberBetween(500, 1500) * 100;
                $waterFee       = fake()->numberBetween(100, 500) * 100;
                $internetFee    = 100_000;
                $totalAmount    = (float) $contract->rent_price + $electricityFee + $waterFee + $internetFee;
                $dueDate        = $bulan->copy()->addDays(9);

                // Tentukan status tagihan
                if ($isBulan2Lalu) {
                    $statusTagihan = StatusTagihan::Paid;
                } elseif ($isBulanLalu) {
                    // 1 tagihan bulan lalu dibuat overdue (sebagai contoh kasus)
                    $statusTagihan = ($contract->id % 5 === 0)
                        ? StatusTagihan::Overdue
                        : StatusTagihan::Paid;
                } else {
                    // Bulan ini: sebagian paid, sebagian pending
                    $statusTagihan = ($contract->id % 3 === 0)
                        ? StatusTagihan::Pending
                        : StatusTagihan::Paid;
                }

                $invoice = Invoice::create([
                    'contract_id'     => $contract->id,
                    'tenant_id'       => $contract->tenant_id,
                    'room_id'         => $contract->room_id,
                    'year'            => (int) $bulan->format('Y'),
                    'month'           => (int) $bulan->format('n'),
                    'rent_amount'     => $contract->rent_price,
                    'electricity_fee' => $electricityFee,
                    'water_fee'       => $waterFee,
                    'internet_fee'    => $internetFee,
                    'penalty_fee'     => $statusTagihan === StatusTagihan::Overdue ? 50_000 : null,
                    'other_fee'       => null,
                    'total_amount'    => $statusTagihan === StatusTagihan::Overdue
                        ? $totalAmount + 50_000
                        : $totalAmount,
                    'due_date'        => $dueDate->format('Y-m-d'),
                    'status'          => $statusTagihan,
                ]);

                // Buat pembayaran untuk tagihan yang sudah paid
                if ($statusTagihan === StatusTagihan::Paid) {
                    $metodePembayaran = fake()->randomElement(MetodePembayaran::cases());
                    $bayarTanggal     = $bulan->copy()->addDays(fake()->numberBetween(1, 8));

                    Payment::create([
                        'invoice_id'   => $invoice->id,
                        'tenant_id'    => $contract->tenant_id,
                        'amount'       => $invoice->total_amount,
                        'payment_date' => $bayarTanggal->format('Y-m-d'),
                        'method'       => $metodePembayaran,
                        'status'       => StatusPembayaran::Verified,
                        'proof_path'   => $metodePembayaran !== MetodePembayaran::Cash
                            ? 'payments/bukti-' . uniqid() . '.jpg'
                            : null,
                        'notes'        => null,
                        'verified_by'  => $owner->id,
                    ]);
                }

                // Buat pembayaran pending untuk tagihan overdue (bukti sudah diupload tapi belum diverifikasi)
                if ($statusTagihan === StatusTagihan::Overdue) {
                    Payment::create([
                        'invoice_id'   => $invoice->id,
                        'tenant_id'    => $contract->tenant_id,
                        'amount'       => $invoice->total_amount,
                        'payment_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                        'method'       => MetodePembayaran::Transfer,
                        'status'       => StatusPembayaran::Pending,
                        'proof_path'   => 'payments/bukti-' . uniqid() . '.jpg',
                        'notes'        => 'Terlambat bayar, mohon diverifikasi',
                        'verified_by'  => null,
                    ]);
                }
            }
        }

        // ── 6. PENGELUARAN OPERASIONAL ─────────────────────────────────────────
        // Buat beberapa pengeluaran untuk 3 bulan terakhir
        $kategoriRutin = [
            ['category' => 'electricity', 'description' => 'Tagihan PLN bulan ini', 'amount' => 850_000],
            ['category' => 'water',       'description' => 'Tagihan PDAM bulan ini', 'amount' => 250_000],
            ['category' => 'internet',    'description' => 'Tagihan Indihome 50Mbps', 'amount' => 450_000],
            ['category' => 'salary',      'description' => 'Gaji penjaga kos', 'amount' => 1_500_000],
            ['category' => 'cleaning',    'description' => 'Jasa bersih-bersih mingguan', 'amount' => 200_000],
        ];

        foreach ($bulanTagihan as $bulan) {
            foreach ($kategoriRutin as $pengeluaran) {
                Expense::create([
                    'category'     => $pengeluaran['category'],
                    'description'  => $pengeluaran['description'],
                    'amount'       => $pengeluaran['amount'],
                    'expense_date' => $bulan->copy()->addDays(5)->format('Y-m-d'),
                    'receipt_path' => null,
                    'created_by'   => $admin->id,
                ]);
            }
        }

        // Tambah beberapa pengeluaran non-rutin
        Expense::create([
            'category'     => 'repair',
            'description'  => 'Perbaikan AC kamar 203',
            'amount'       => 350_000,
            'expense_date' => Carbon::now()->subWeeks(3)->format('Y-m-d'),
            'receipt_path' => null,
            'created_by'   => $admin->id,
        ]);

        Expense::create([
            'category'     => 'repair',
            'description'  => 'Ganti pompa air',
            'amount'       => 1_200_000,
            'expense_date' => Carbon::now()->subMonth()->subDays(10)->format('Y-m-d'),
            'receipt_path' => null,
            'created_by'   => $owner->id,
        ]);

        // ── Output ringkasan ───────────────────────────────────────────────────
        $this->command->info('✅ Seeder selesai!');
        $this->command->table(
            ['Data', 'Jumlah'],
            [
                ['Users',          User::count()],
                ['Kamar',          Room::count()],
                ['Foto Kamar',     RoomPhoto::count()],
                ['Penghuni',       Tenant::count()],
                ['Kontrak',        Contract::count()],
                ['  - Active',     Contract::where('status', 'active')->count()],
                ['  - Ended',      Contract::where('status', 'ended')->count()],
                ['Tagihan',        Invoice::count()],
                ['  - Paid',       Invoice::where('status', 'paid')->count()],
                ['  - Pending',    Invoice::where('status', 'pending')->count()],
                ['  - Overdue',    Invoice::where('status', 'overdue')->count()],
                ['Pembayaran',     Payment::count()],
                ['Pengeluaran',    Expense::count()],
            ]
        );
    }
}
