<?php

namespace Database\Factories;

use App\Enums\KategoriPengeluaran;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Expense — membuat data dummy pengeluaran operasional.
 *
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    private array $deskripsiPerKategori = [
        'electricity' => ['Tagihan PLN bulan ini', 'Tagihan listrik kos', 'Pembayaran rekening listrik'],
        'water'       => ['Tagihan PDAM bulan ini', 'Pembayaran air bulan ini', 'Rekening air'],
        'internet'    => ['Tagihan Indihome', 'Tagihan First Media', 'Pembayaran WiFi bulanan'],
        'repair'      => ['Perbaikan AC kamar 101', 'Ganti kran bocor', 'Cat ulang tembok', 'Perbaikan pompa air', 'Ganti bola lampu lorong'],
        'cleaning'    => ['Jasa bersih-bersih bulanan', 'Pembelian alat kebersihan', 'Jasa cuci tangki air'],
        'salary'      => ['Gaji penjaga kos bulan ini', 'Honor petugas kebersihan', 'Upah tukang'],
        'other'       => ['Pembelian peralatan kantor', 'Biaya administrasi', 'Pembelian CCTV', 'Servis lift'],
    ];

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $kategori    = fake()->randomElement(KategoriPengeluaran::cases());
        $deskripsiList = $this->deskripsiPerKategori[$kategori->value];

        $nominalMap = [
            'electricity' => [300_000, 1_500_000],
            'water'       => [100_000, 500_000],
            'internet'    => [300_000, 700_000],
            'repair'      => [50_000, 2_000_000],
            'cleaning'    => [100_000, 500_000],
            'salary'      => [500_000, 2_000_000],
            'other'       => [50_000, 1_000_000],
        ];

        [$min, $max] = $nominalMap[$kategori->value];

        return [
            'category'     => $kategori,
            'description'  => fake()->randomElement($deskripsiList),
            'amount'       => fake()->numberBetween($min / 1000, $max / 1000) * 1000,
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'receipt_path' => null,
            'created_by'   => User::factory(),
        ];
    }
}
