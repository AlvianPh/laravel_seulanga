<?php

namespace App\Enums;

enum DocumentType: string
{
    case Ktp = 'ktp';
    case Contract = 'contract';
    case Agreement = 'agreement';
    case PaymentReceipt = 'receipt';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Ktp => 'KTP / Identitas',
            self::Contract => 'Dokumen Kontrak',
            self::Agreement => 'Tata Tertib / Agreement',
            self::PaymentReceipt => 'Bukti Pembayaran',
            self::Other => 'Dokumen Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ktp => 'indigo',
            self::Contract => 'emerald',
            self::Agreement => 'amber',
            self::PaymentReceipt => 'blue',
            self::Other => 'gray',
        };
    }

    /**
     * Tipe dokumen yang boleh diunggah mandiri oleh penghuni melalui portal.
     *
     * @return array<self>
     */
    public static function allowedForTenantUpload(): array
    {
        return [
            self::Ktp,
            self::Other,
        ];
    }
}
