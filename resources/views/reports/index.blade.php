@extends('layouts.app')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('content')

<!-- Filter Laporan -->
<div class="bg-surface-container-lowest border custom-border rounded p-margin_md mb-margin_lg flex flex-wrap items-end gap-margin_md">
    <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-end gap-4 w-full">
        <div class="flex-1 min-w-[200px]">
            <label class="block font-label text-label text-on-surface-variant mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full h-10 border custom-border rounded px-3 text-body focus:border-primary-container focus:ring-0 focus:outline-none bg-transparent">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block font-label text-label text-on-surface-variant mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full h-10 border custom-border rounded px-3 text-body focus:border-primary-container focus:ring-0 focus:outline-none bg-transparent">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block font-label text-label text-on-surface-variant mb-1">Kategori</label>
            <select name="category_id" class="w-full h-10 border custom-border rounded px-3 text-body focus:border-primary-container focus:ring-0 focus:outline-none bg-transparent">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="btn-primary h-10">
                Generate
            </button>
        </div>
    </form>
</div>

    <!-- Report Card Container -->
    <div class="bg-surface-container-lowest border custom-border rounded overflow-hidden">
        
        <!-- Header & Export -->
        <div class="p-margin_md border-b custom-border flex flex-wrap gap-4 justify-between items-center bg-surface-container-low">
            <h2 class="font-subheading text-subheading text-on-surface">Laporan Periode Terpilih</h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.export', 'pdf') }}?{{ http_build_query(request()->all()) }}" target="_blank" download="Laporan_BRILink.pdf" class="flex items-center px-3 py-1.5 border custom-border rounded text-body font-body-semibold text-primary hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[18px] mr-1">picture_as_pdf</span> PDF
                </a>
                <a href="{{ route('laporan.export', 'excel') }}?{{ http_build_query(request()->all()) }}" target="_blank" download="Laporan_BRILink.xlsx" class="flex items-center px-3 py-1.5 border custom-border rounded text-body font-body-semibold text-primary hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[18px] mr-1">table_view</span> Excel
                </a>
            </div>
        </div>

        <!-- Summary Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 bg-surface border-b custom-border">
            <div class="p-4 border-r custom-border border-b md:border-b-0">
                <p class="font-label text-label text-on-surface-variant mb-1">Total Transaksi</p>
                <p class="font-headline-md text-headline-md tabular-nums text-on-surface">{{ $summary['total_transaksi'] }}</p>
            </div>
            <div class="p-4 border-r custom-border border-b md:border-b-0">
                <p class="font-label text-label text-on-surface-variant mb-1">Total Nominal</p>
                <p class="font-body-semibold text-body-semibold tabular-nums text-on-surface">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</p>
            </div>
            <div class="p-4 border-r custom-border border-b md:border-b-0">
                <p class="font-label text-label text-on-surface-variant mb-1">Total Fee Bank <span class="text-[10px]">⚠️</span></p>
                <p class="font-body-semibold text-body-semibold tabular-nums text-on-surface">Rp {{ number_format($summary['total_fee_bank'], 0, ',', '.') }}</p>
            </div>
            <div class="p-4 border-r custom-border">
                <p class="font-label text-label text-on-surface-variant mb-1">Total Biaya Op <span class="text-[10px]">⚠️</span></p>
                <p class="font-body-semibold text-body-semibold tabular-nums text-on-surface">Rp {{ number_format($summary['total_biaya_op'], 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-primary-container/10">
                <p class="font-label text-label text-on-surface-variant mb-1">Total Laba <span class="text-[10px]">⚠️</span></p>
                <p class="font-headline-md text-headline-md tabular-nums text-primary-container">Rp {{ number_format($summary['total_laba'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface border-b custom-border">
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant w-16">No</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant">Waktu</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant">Kategori</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant text-right">Nominal</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant text-right">Fee Bank</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant text-right">Biaya Op</th>
                        <th class="py-2 px-margin_md font-label-caps text-label-caps text-on-surface-variant text-right">Laba</th>
                    </tr>
                </thead>
                <tbody class="font-body text-body tabular-nums text-on-surface">
                    @foreach($transactions as $i => $tx)
                    <tr class="zebra-row border-b custom-border border-opacity-50">
                        <td class="py-2 px-margin_md">{{ $i + 1 }}</td>
                        <td class="py-2 px-margin_md">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-margin_md">{{ $tx->category->name }}</td>
                        <td class="py-2 px-margin_md text-right">Rp {{ number_format($tx->nominal, 0, ',', '.') }}</td>
                        <td class="py-2 px-margin_md text-right text-on-surface-variant">Rp {{ number_format($tx->fee_bank, 0, ',', '.') }}</td>
                        <td class="py-2 px-margin_md text-right text-on-surface-variant">Rp {{ number_format($tx->biaya_operasional, 0, ',', '.') }}</td>
                        <td class="py-2 px-margin_md text-right font-body-semibold text-primary">Rp {{ number_format($tx->laba_bersih, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection
