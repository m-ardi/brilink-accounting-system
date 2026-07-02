@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')

@section('content')

<!-- Filter -->
<div class="bg-surface-container-lowest border custom-border rounded p-3 mb-6">
    <form method="GET" action="{{ route('audit-log.index') }}" class="flex flex-wrap items-center gap-4">
        
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">date_range</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
        </div>
        
        <div class="flex items-center gap-1">
            <span class="text-on-surface-variant text-sm font-body">-</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
        </div>

        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-on-surface-variant text-sm">filter_list</span>
            <select name="action" class="input-border input-focus rounded px-3 py-2 bg-surface font-body text-body text-on-surface h-10">
                <option value="">Semua Aksi</option>
                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Tambah</option>
                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Ubah</option>
                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Hapus</option>
            </select>
        </div>

        <div class="flex gap-2 h-10">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="{{ route('audit-log.index') }}" class="btn-secondary flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-surface-container-lowest card-border rounded-lg overflow-x-auto">
    <div class="px-4 py-3 border-b border-outline-variant bg-surface-container-low">
        <p class="font-body text-body text-on-surface-variant">Total <span class="font-semibold text-on-surface">{{ $logs->total() }}</span> log aktivitas</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left font-body text-body text-on-surface border-collapse">
            <thead class="bg-surface border-b border-outline-variant">
                <tr>
                    <th class="p-4 font-body-semibold text-on-surface-variant w-32">Waktu</th>
                    <th class="p-4 font-body-semibold text-on-surface-variant w-48">User</th>
                    <th class="p-4 font-body-semibold text-on-surface-variant w-32">Aksi</th>
                    <th class="p-4 font-body-semibold text-on-surface-variant">Deskripsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($logs as $log)
                <tr class="zebra-row transition-colors">
                    <td class="p-4 py-3 text-on-surface-variant tabular-nums">
                        {{ $log->created_at->format('d/m/Y') }} <br><span class="text-xs">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td class="p-4 py-3">
                        <p class="text-sm font-medium text-on-surface">{{ $log->user->name ?? 'Sistem' }}</p>
                        <p class="text-xs text-on-surface-variant capitalize">{{ $log->user->role ?? '-' }}</p>
                    </td>
                    <td class="p-4 py-3">
                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                            @if($log->action === 'created') bg-success-dim text-success-text
                            @elseif($log->action === 'updated') bg-primary-fixed text-primary
                            @else bg-danger-dim text-danger-text @endif">
                            {{ $log->action_label }}
                        </span>
                    </td>
                    <td class="p-4 py-3 text-on-surface-variant text-xs">{{ $log->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-on-surface-variant">Belum ada log aktivitas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="p-4 border-t border-outline-variant bg-surface">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
