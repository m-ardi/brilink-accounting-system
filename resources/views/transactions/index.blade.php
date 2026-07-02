@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@section('content')

<!-- Filter Bar -->
<div class="bg-surface-container-lowest card-border rounded-lg p-3 mb-6">
    <form method="GET" action="{{ route('transaksi.index') }}" class="flex flex-wrap items-center gap-4">
        
        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-sm">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..." class="w-full pl-9 pr-3 py-2 rounded input-border input-focus font-body text-body bg-surface h-10">
        </div>
        
        <!-- Kategori -->
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">filter_list</span>
            <select name="category_id" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Tanggal Dari -->
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">date_range</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
        </div>

        <!-- Tanggal Ke -->
        <div class="flex items-center gap-1">
            <span class="text-on-surface-variant text-sm font-body">-</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
        </div>

        <div class="flex gap-2 h-10">
            <button type="submit" class="btn-primary">Cari</button>
            <a href="{{ route('transaksi.index') }}" class="btn-secondary flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-surface-container-lowest card-border rounded-lg overflow-x-auto">
    <div class="flex items-center justify-between p-4 border-b border-outline-variant bg-surface-container-low">
        <p class="font-body text-body text-on-surface-variant">Menampilkan {{ $transactions->total() }} transaksi</p>
        <a href="{{ route('transaksi.create') }}" class="btn-primary flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> Input Baru
        </a>
    </div>

    <table class="w-full text-left font-body text-body text-on-surface border-collapse">
        <thead class="bg-surface border-b border-outline-variant">
            <tr>
                <th class="p-4 font-body-semibold text-on-surface-variant w-24">ID</th>
                <th class="p-4 font-body-semibold text-on-surface-variant w-32">Waktu</th>
                <th class="p-4 font-body-semibold text-on-surface-variant">Kategori</th>
                <th class="p-4 font-body-semibold text-on-surface-variant text-right">Nominal (Rp)</th>
                <th class="p-4 font-body-semibold text-on-surface-variant text-right">Laba/Fee (Rp)</th>
                <th class="p-4 font-body-semibold text-on-surface-variant text-center w-24">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @forelse($transactions as $tx)
            <tr class="table-row-hover transition-colors">
                <td class="p-4 py-3 tabular-nums text-on-surface-variant">TRX-{{ $tx->id }}</td>
                <td class="p-4 py-3 text-on-surface-variant tabular-nums">
                    {{ $tx->created_at->format('H:i') }} <span class="text-xs">{{ $tx->created_at->format('d/m/y') }}</span>
                </td>
                <td class="p-4 py-3">{{ $tx->category->name }}</td>
                <td class="p-4 py-3 text-right tabular-nums">{{ number_format($tx->nominal, 0, ',', '.') }}</td>
                <td class="p-4 py-3 text-right tabular-nums text-success-text">+{{ number_format($tx->laba_bersih, 0, ',', '.') }}</td>
                <td class="p-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('transaksi.edit', $tx) }}" class="text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        <form method="POST" action="{{ route('transaksi.destroy', $tx) }}" onsubmit="return confirm('Hapus transaksi ini?');" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-on-surface-variant hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-on-surface-variant">Tidak ada transaksi ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($transactions->hasPages())
    <div class="p-4 border-t border-outline-variant bg-surface">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@endsection
