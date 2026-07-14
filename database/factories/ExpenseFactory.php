<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
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
        'Listrik'    => ['Tagihan PLN bulan ini', 'Tagihan listrik kos', 'Pembayaran rekening listrik'],
        'Air'        => ['Tagihan PDAM bulan ini', 'Pembayaran air bulan ini', 'Rekening air'],
        'Internet'   => ['Tagihan Indihome', 'Tagihan First Media', 'Pembayaran WiFi bulanan'],
        'Perbaikan'  => ['Perbaikan AC kamar 101', 'Ganti kran bocor', 'Cat ulang tembok', 'Perbaikan pompa air', 'Ganti bola lampu lorong'],
        'Kebersihan' => ['Jasa bersih-bersih bulanan', 'Pembelian alat kebersihan', 'Jasa cuci tangki air'],
        'Gaji'       => ['Gaji penjaga kos bulan ini', 'Honor petugas kebersihan', 'Upah tukang'],
        'Lainnya'    => ['Pembelian peralatan kantor', 'Biaya administrasi', 'Pembelian CCTV', 'Servis lift'],
    ];

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $kategoriList = ['Listrik', 'Air', 'Internet', 'Perbaikan', 'Kebersihan', 'Gaji', 'Lainnya'];
        $kategoriName = fake()->randomElement($kategoriList);
        $kategori = ExpenseCategory::firstOrCreate(['name' => $kategoriName]);

        $deskripsiList = $this->deskripsiPerKategori[$kategoriName];

        $nominalMap = [
            'Listrik'    => [300_000, 1_500_000],
            'Air'        => [100_000, 500_000],
            'Internet'   => [300_000, 700_000],
            'Perbaikan'  => [50_000, 2_000_000],
            'Kebersihan' => [100_000, 500_000],
            'Gaji'       => [500_000, 2_000_000],
            'Lainnya'    => [50_000, 1_000_000],
        ];

        [$min, $max] = $nominalMap[$kategoriName];

        return [
            'expense_category_id' => $kategori->id,
            'description'  => fake()->randomElement($deskripsiList),
            'amount'       => fake()->numberBetween($min / 1000, $max / 1000) * 1000,
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'receipt_path' => null,
            'created_by'   => User::factory(),
        ];
    }
}
