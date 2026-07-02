@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')

<!-- User List Table -->
<div class="bg-surface-container-lowest card-border rounded-lg overflow-x-auto">
    <div class="flex items-center justify-between p-4 border-b border-outline-variant bg-surface-container-low">
        <p class="font-body text-body text-on-surface-variant">Menampilkan {{ $users->total() }} pengguna terdaftar</p>
        <a href="{{ route('users.create') }}" class="btn-primary flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Pengguna
        </a>
    </div>

    <table class="w-full text-left font-body text-body text-on-surface border-collapse">
        <thead class="bg-surface border-b border-outline-variant">
            <tr>
                <th class="p-4 font-body-semibold text-on-surface-variant w-48">Nama</th>
                <th class="p-4 font-body-semibold text-on-surface-variant">Email</th>
                <th class="p-4 font-body-semibold text-on-surface-variant w-32">Peran (Role)</th>
                <th class="p-4 font-body-semibold text-on-surface-variant w-48">Terdaftar Sejak</th>
                <th class="p-4 font-body-semibold text-on-surface-variant text-center w-24">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant">
            @forelse($users as $user)
            <tr class="zebra-row transition-colors">
                <td class="p-4 py-3 font-medium text-on-surface">{{ $user->name }}</td>
                <td class="p-4 py-3 text-on-surface-variant">{{ $user->email }}</td>
                <td class="p-4 py-3">
                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold uppercase
                        {{ $user->role === 'owner' ? 'bg-primary-fixed text-primary' : 'bg-secondary-fixed text-secondary' }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="p-4 py-3 text-on-surface-variant tabular-nums">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="p-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </a>
                        @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini? Tindakan ini tidak bisa dibatalkan.');" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-on-surface-variant hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-on-surface-variant">Tidak ada pengguna ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($users->hasPages())
    <div class="p-4 border-t border-outline-variant bg-surface">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
