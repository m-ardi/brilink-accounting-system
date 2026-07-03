<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export transaksi ke format Excel (.xlsx).
 * Menggunakan maatwebsite/excel dengan heading + styling.
 */
class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(private readonly Collection $transactions) {}

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function title(): string
    {
        return 'Laporan Transaksi';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'Kategori',
            'Nominal (Rp)',
            'Biaya Admin (Rp)',
            'Setoran BRI (Rp)',
            'Laba Bersih (Rp)',
            'Saldo Sebelum (Rp)',
            'Saldo Sesudah (Rp)',
            'Keterangan',
            'Diinput Oleh',
        ];
    }

    public function map($transaction): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->category->name ?? '-',
            (float) $transaction->nominal,
            (float) $transaction->biaya_admin,
            (float) $transaction->setoran_bri,
            (float) $transaction->laba_bersih,
            (float) $transaction->saldo_modal_sebelum,
            (float) $transaction->saldo_modal_sesudah,
            $transaction->keterangan ?? '-',
            $transaction->user->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [ // Baris header
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['argb' => 'FF00273E']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
