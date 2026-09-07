<?php

namespace App\Http\Requests\Portal;

use App\Enums\KategoriMaintenance;
use App\Enums\PrioritasMaintenance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->tenant !== null
            && $this->user()->tenant->activeContract() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', new Enum(KategoriMaintenance::class)],
            'priority' => ['required', new Enum(PrioritasMaintenance::class)],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Kategori kerusakan wajib dipilih.',
            'category.Illuminate\Validation\Rules\Enum' => 'Kategori kerusakan yang dipilih tidak valid.',
            'priority.required' => 'Prioritas perbaikan wajib dipilih.',
            'priority.Illuminate\Validation\Rules\Enum' => 'Prioritas perbaikan yang dipilih tidak valid.',
            'location.required' => 'Lokasi kerusakan di dalam kamar/fasilitas wajib diisi.',
            'location.max' => 'Lokasi kerusakan maksimal 255 karakter.',
            'description.required' => 'Deskripsi keluhan perbaikan wajib diisi.',
            'description.max' => 'Deskripsi keluhan perbaikan maksimal 1000 karakter.',
            'photo.image' => 'Bukti foto harus berupa file gambar.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }
}
