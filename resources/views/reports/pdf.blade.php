<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi — {{ $agentInfo['nama_agen'] }}</title>
    <style>
        /* ── Reset & Base ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #1a1c1e;
            background: #f9f9fc;
        }

        /* ── Page Layout ── */
        .page { padding: 24px 28px; }

        /* ── Header Block ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 16px 20px;
            background: #00273e;
            color: #ffffff;
            margin-bottom: 16px;
            border-left: 4px solid #C8912E;
        }
        .header-left {}
        .header-left .title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }
        .header-left .subtitle {
            font-size: 9px;
            opacity: 0.75;
            margin-top: 3px;
        }
        .header-right {
            text-align: right;
        }
        .header-right .kode {
            font-size: 9px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .header-right .kode-value {
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        /* ── Meta Info Row ── */
        .meta {
            display: flex;
            gap: 0;
            margin-bottom: 14px;
            border: 1px solid #c2c7ce;
            background: #ffffff;
        }
        .meta-item {
            flex: 1;
            padding: 8px 12px;
            border-right: 1px solid #c2c7ce;
        }
        .meta-item:last-child { border-right: none; }
        .meta-item .label {
            font-size: 8px;
            color: #42474d;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }
        .meta-item .value {
            font-size: 10px;
            font-weight: 700;
            color: #1a1c1e;
            margin-top: 2px;
        }

        /* ── Summary Cards ── */
        .summary {
            display: flex;
            gap: 0;
            margin-bottom: 14px;
        }
        .summary-card {
            flex: 1;
            padding: 9px 12px;
            border: 1px solid #c2c7ce;
            border-right: none;
            background: #ffffff;
            text-align: center;
        }
        .summary-card:last-child { border-right: 1px solid #c2c7ce; }
        .summary-card .s-label {
            font-size: 8px;
            color: #42474d;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .summary-card .s-value {
            font-size: 11px;
            font-weight: 700;
            color: #1a1c1e;
            margin-top: 3px;
        }
        .summary-card.laba {
            border-left: 3px solid #C8912E;
            background: #fffdf8;
        }
        .summary-card.laba .s-value {
            color: #0b3d5c;
        }

        /* ── Section Label ── */
        .section-header {
            font-size: 9px;
            font-weight: 700;
            color: #42474d;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 2px solid #00273e;
            padding-bottom: 4px;
            margin-bottom: 0;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #00273e;
            color: #ffffff;
        }
        thead th {
            padding: 7px 8px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        thead th.right { text-align: right; }

        tbody tr:nth-child(even) { background: #f3f3f6; }
        tbody tr { border-bottom: 1px solid #e8e8eb; }
        tbody td {
            padding: 6px 8px;
            font-size: 9.5px;
            color: #1a1c1e;
        }
        tbody td.right {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }
        tbody td.center { text-align: center; }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .badge-transfer { background: #d2e1f2; color: #0b3d5c; }
        .badge-tarik    { background: #ffebd6; color: #7a2e00; }
        .badge-ppob     { background: #e8e8eb; color: #42474d; }

        /* ── Total Row ── */
        .total-row td {
            font-weight: 700;
            font-size: 9.5px;
            background: #eeedf0;
            border-top: 2px solid #00273e;
            border-bottom: none;
            padding: 7px 8px;
        }
        .total-row td.right {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }

        /* ── Laba Bersih Highlight Row ── */
        .laba-row td {
            font-weight: 700;
            font-size: 10px;
            background: #0b3d5c;
            color: #ffffff;
            padding: 8px 8px;
            border-top: none;
        }
        .laba-row td.right {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        /* ── Ledger Strip on table container ── */
        .table-container {
            border: 1px solid #c2c7ce;
            overflow: hidden;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #c2c7ce;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #72787e;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-left">
            <div class="title">{{ $agentInfo['nama_agen'] }}</div>
            <div class="subtitle">{{ $agentInfo['alamat_agen'] }}</div>
            <div class="subtitle" style="margin-top:6px; opacity:0.6; font-size:8px; text-transform:uppercase; letter-spacing:0.05em;">
                LAPORAN TRANSAKSI
            </div>
        </div>
        <div class="header-right">
            <div class="kode">Kode Agen</div>
            <div class="kode-value">{{ $agentInfo['kode_agen'] }}</div>
            <div class="kode" style="margin-top:6px;">Dicetak</div>
            <div class="kode-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- ── Meta Info ── --}}
    <div class="meta">
        <div class="meta-item">
            <div class="label">Periode</div>
            <div class="value">{{ $agentInfo['periode_dari'] }} s/d {{ $agentInfo['periode_ke'] }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $summary['total_transaksi'] }} transaksi</div>
        </div>
        <div class="meta-item">
            <div class="label">Saldo Modal Akhir</div>
            <div class="value">Rp {{ number_format($summary['saldo_modal'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="meta-item">
            <div class="label">Dicetak oleh</div>
            <div class="value">{{ auth()->user()->name ?? 'Sistem' }}</div>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="summary">
        <div class="summary-card">
            <div class="s-label">Total Nominal</div>
            <div class="s-value">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Biaya Admin</div>
            <div class="s-value">Rp {{ number_format($summary['total_fee_bank'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Setoran BRI</div>
            <div class="s-value">Rp {{ number_format($summary['total_biaya_op'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card laba">
            <div class="s-label">Total Laba Bersih</div>
            <div class="s-value">Rp {{ number_format($summary['total_laba'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ── Table Section ── --}}
    <div class="section-header" style="margin-bottom:0;">Rincian Transaksi</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:24px">No</th>
                    <th style="width:72px">Tanggal</th>
                    <th style="width:56px">Waktu</th>
                    <th style="width:68px">Kategori</th>
                    <th class="right">Nominal (Rp)</th>
                    <th class="right">Biaya Admin (Rp)</th>
                    <th class="right">Setoran BRI (Rp)</th>
                    <th class="right">Laba Bersih (Rp)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $i => $tx)
                <tr>
                    <td class="center" style="color:#72787e">{{ $i + 1 }}</td>
                    <td>{{ $tx->created_at->format('d/m/Y') }}</td>
                    <td>{{ $tx->created_at->format('H:i') }}</td>
                    <td>
                        <span class="badge
                            @if($tx->category->name === 'Transfer') badge-transfer
                            @elseif($tx->category->name === 'Tarik Tunai') badge-tarik
                            @else badge-ppob @endif">
                            {{ $tx->category->name }}
                        </span>
                    </td>
                    <td class="right">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($tx->biaya_admin, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($tx->setoran_bri, 0, ',', '.') }}</td>
                    <td class="right" style="font-weight:600; color:#0b3d5c;">{{ number_format($tx->laba_bersih, 0, ',', '.') }}</td>
                    <td style="color:#42474d">{{ $tx->keterangan ?? '-' }}</td>
                </tr>
                @endforeach

                {{-- Total Row --}}
                <tr class="total-row">
                    <td colspan="4">TOTAL</td>
                    <td class="right">{{ number_format($summary['total_nominal'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($summary['total_fee_bank'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($summary['total_biaya_op'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($summary['total_laba'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                {{-- Laba Bersih Highlight Row --}}
                <tr class="laba-row">
                    <td colspan="7" style="text-align:right; letter-spacing:0.05em; font-size:9px; text-transform:uppercase;">Laba Bersih Periode Ini</td>
                    <td class="right" style="font-size:11px; letter-spacing:0.01em;">Rp {{ number_format($summary['total_laba'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <span>Sistem Informasi Akuntansi &mdash; {{ $agentInfo['nama_agen'] }} &nbsp;|&nbsp; Kode: {{ $agentInfo['kode_agen'] }}</span>
        <span>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</span>
    </div>

</div>
</body>
</html>
