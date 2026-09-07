<?php

namespace App\Http\Requests\Portal;

use App\Enums\DocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->tenant !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedTypes = array_map(fn (DocumentType $t) => $t->value, DocumentType::allowedForTenantUpload());

        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in($allowedTypes)],
            'file' => ['required', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul dokumen wajib diisi.',
            'title.max' => 'Judul dokumen maksimal 255 karakter.',
            'type.required' => 'Jenis dokumen wajib dipilih.',
            'type.in' => 'Jenis dokumen yang dipilih tidak diizinkan untuk diunggah mandiri.',
            'file.required' => 'Berkas dokumen wajib diunggah.',
            'file.file' => 'Berkas yang diunggah harus berupa file yang valid.',
            'file.mimes' => 'Format file dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran file dokumen maksimal 5MB.',
            'description.max' => 'Deskripsi dokumen maksimal 1000 karakter.',
        ];
    }
}
