<?php

namespace Database\Factories;

use App\Enums\JenisKelamin;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Tenant — membuat data dummy penghuni kost dengan nama Indonesia.
 *
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    // Daftar nama depan Indonesia
    private array $namaDepanPria = [
        'Budi', 'Agus', 'Hendra', 'Eko', 'Dedi', 'Slamet', 'Joko', 'Rizki',
        'Fajar', 'Dian', 'Arif', 'Wahyu', 'Bambang', 'Rudi', 'Iwan', 'Surya',
        'Teguh', 'Andi', 'Dimas', 'Bayu', 'Fauzi', 'Hadi', 'Irwan', 'Yusuf',
    ];

    private array $namaDepanWanita = [
        'Siti', 'Dewi', 'Ani', 'Rina', 'Yuni', 'Sri', 'Ratna', 'Linda',
        'Fitri', 'Wulan', 'Indah', 'Laras', 'Nanda', 'Putri', 'Ayu', 'Devi',
        'Mega', 'Sari', 'Tika', 'Rini', 'Nita', 'Vina', 'Mira', 'Lestari',
    ];

    private array $namaBelakang = [
        'Santoso', 'Wijaya', 'Kusuma', 'Purnomo', 'Rahayu', 'Setiawan',
        'Hidayat', 'Nugroho', 'Susanto', 'Wibowo', 'Kurniawan', 'Hartono',
        'Prasetyo', 'Saputra', 'Firmansyah', 'Sulistyo', 'Gunawan', 'Utama',
        'Hakim', 'Wahyudi', 'Mustafa', 'Iskandar', 'Perdana', 'Maulana',
    ];

    private array $kotaIndonesia = [
        'Jakarta Selatan', 'Jakarta Timur', 'Bandung', 'Surabaya', 'Yogyakarta',
        'Semarang', 'Medan', 'Makassar', 'Palembang', 'Depok', 'Bekasi',
        'Bogor', 'Tangerang', 'Malang', 'Solo', 'Manado', 'Banjarmasin',
    ];

    private array $jalanIndonesia = [
        'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Diponegoro', 'Jl. Gatot Subroto',
        'Jl. Ahmad Yani', 'Jl. Pahlawan', 'Jl. Veteran', 'Jl. Kebon Jeruk',
        'Jl. Cempaka Putih', 'Jl. Mangga Besar', 'Jl. Kebayoran Lama',
        'Jl. Cilandak', 'Jl. Tebet Raya', 'Jl. Duren Sawit', 'Jl. Cibubur',
    ];

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $gender  = fake()->randomElement(JenisKelamin::cases());
        $isPria  = $gender === JenisKelamin::Male;

        $namaDepan  = $isPria
            ? fake()->randomElement($this->namaDepanPria)
            : fake()->randomElement($this->namaDepanWanita);
        $namaBelakang = fake()->randomElement($this->namaBelakang);
        $namaLengkap  = $namaDepan . ' ' . $namaBelakang;

        $kota = fake()->randomElement($this->kotaIndonesia);
        $jalan = fake()->randomElement($this->jalanIndonesia);
        $nomorJalan = fake()->numberBetween(1, 200);
        $rt = fake()->numberBetween(1, 15);
        $rw = fake()->numberBetween(1, 10);

        // Generate NIK 16 digit: 2 digit kode provinsi + 2 kota + 2 kec + 6 tgl lahir + 4 urut
        $nik = (string) fake()->numberBetween(10, 99)
            . fake()->numberBetween(10, 99)
            . fake()->numberBetween(10, 99)
            . ($isPria
                ? fake()->date('d') . fake()->date('m') . fake()->date('y')
                : (fake()->numberBetween(41, 71)) . fake()->date('m') . fake()->date('y'))
            . str_pad((string) fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'name'                   => $namaLengkap,
            'nik'                    => substr($nik, 0, 16), // pastikan 16 digit
            'phone'                  => '08' . fake()->numerify('#########'),
            'email'                  => fake()->unique()->safeEmail(),
            'gender'                 => $gender,
            'birth_date'             => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
            'address'                => "{$jalan} No. {$nomorJalan} RT {$rt}/RW {$rw}, {$kota}",
            'ktp_photo_path'         => null,
            'tenant_photo_path'      => null,
            'emergency_contact_name' => fake()->randomElement($this->namaDepanPria) . ' ' . fake()->randomElement($this->namaBelakang),
            'emergency_contact_phone' => '08' . fake()->numerify('#########'),
        ];
    }
}
