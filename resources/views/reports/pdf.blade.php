<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi — {{ $agentInfo['nama_agen'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #1f2937; }
        .header { background: #1d4ed8; color: white; padding: 16px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 10px; opacity: 0.85; margin-top: 2px; }
        .meta { display: flex; gap: 20px; margin: 0 20px 12px; }
        .meta-item { }
        .meta-item .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .meta-item .value { font-weight: 600; color: #111827; }
        .summary { display: flex; gap: 10px; margin: 0 20px 14px; }
        .summary-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; text-align: center; }
        .summary-card .s-label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
        .summary-card .s-value { font-size: 11px; font-weight: bold; color: #1f2937; margin-top: 2px; }
        .summary-card.highlight { border-color: #10b981; background: #f0fdf4; }
        .summary-card.highlight .s-value { color: #059669; }
        table { width: 100%; border-collapse: collapse; margin: 0 20px; width: calc(100% - 40px); }
        thead tr { background: #1d4ed8; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        thead th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:hover { background: #eff6ff; }
        tbody td { padding: 6px 8px; font-size: 9.5px; border-bottom: 1px solid #f3f4f6; }
        tbody td.right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 9999px; font-size: 8px; font-weight: 600; }
        .badge-transfer { background: #dbeafe; color: #1d4ed8; }
        .badge-tarik { background: #ffedd5; color: #c2410c; }
        .badge-ppob { background: #ede9fe; color: #7c3aed; }
        .total-row td { font-weight: bold; background: #f3f4f6; border-top: 2px solid #d1d5db; }
        .footer { margin: 14px 20px 0; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .provisional-note { margin: 8px 20px; padding: 6px 10px; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 4px; font-size: 8px; color: #92400e; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>Laporan Transaksi — {{ $agentInfo['nama_agen'] }}</h1>
    <p>{{ $agentInfo['alamat_agen'] }} &nbsp;|&nbsp; Kode: {{ $agentInfo['kode_agen'] }}</p>
</div>

<!-- Meta Info -->
<div class="meta">
    <div class="meta-item">
        <div class="label">Periode</div>
        <div class="value">{{ $agentInfo['periode_dari'] }} s/d {{ $agentInfo['periode_ke'] }}</div>
    </div>
    <div class="meta-item">
        <div class="label">Dicetak</div>
        <div class="value">{{ now()->format('d/m/Y H:i') }}</div>
    </div>
    <div class="meta-item">
        <div class="label">Total Transaksi</div>
        <div class="value">{{ $summary['total_transaksi'] }} transaksi</div>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary">
    <div class="summary-card">
        <div class="s-label">Total Nominal</div>
        <div class="s-value">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="s-label">Total Fee Bank ⚠️</div>
        <div class="s-value">Rp {{ number_format($summary['total_fee_bank'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
        <div class="s-label">Total Biaya Op ⚠️</div>
        <div class="s-value">Rp {{ number_format($summary['total_biaya_op'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card highlight">
        <div class="s-label">Total Laba Bersih ⚠️</div>
        <div class="s-value">Rp {{ number_format($summary['total_laba'], 0, ',', '.') }}</div>
    </div>
</div>

<!-- Provisional Note -->
<div class="provisional-note">
    ⚠️ PENDING VALIDATION: Nilai Fee Bank, Biaya Operasional, dan Laba Bersih menggunakan formula provisional yang belum diverifikasi ke pemilik agen.
</div>

<!-- Tabel Transaksi -->
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal & Waktu</th>
            <th>Kategori</th>
            <th class="right">Nominal (Rp)</th>
            <th class="right">Fee Bank ⚠️</th>
            <th class="right">Biaya Op ⚠️</th>
            <th class="right">Laba Bersih ⚠️</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $tx)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <span class="badge
                    @if($tx->category->name === 'Transfer') badge-transfer
                    @elseif($tx->category->name === 'Tarik Tunai') badge-tarik
                    @else badge-ppob @endif">
                    {{ $tx->category->name }}
                </span>
            </td>
            <td class="right">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($tx->fee_bank, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($tx->biaya_operasional, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($tx->laba_bersih, 0, ',', '.') }}</td>
            <td>{{ $tx->keterangan ?? '-' }}</td>
        </tr>
        @endforeach
        <!-- Total Row -->
        <tr class="total-row">
            <td colspan="3">TOTAL</td>
            <td class="right">{{ number_format($summary['total_nominal'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($summary['total_fee_bank'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($summary['total_biaya_op'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($summary['total_laba'], 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Dicetak oleh sistem BRILink Rambo &nbsp;|&nbsp; {{ $agentInfo['nama_agen'] }} &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>
