@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg">
        <h4 class="font-subheading text-subheading text-on-surface border-b border-outline-variant pb-2 mb-6">Detail Pengguna Baru</h4>
        
        <form method="POST" action="{{ route('users.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Nama -->
            <div class="flex flex-col gap-2">
                <label for="name" class="font-label text-label text-on-surface-variant">Nama Lengkap <span class="text-error">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required autofocus>
                @error('name')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label for="email" class="font-label text-label text-on-surface-variant">Alamat Email <span class="text-error">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required>
                @error('email')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Role -->
            <div class="flex flex-col gap-2">
                <label for="role" class="font-label text-label text-on-surface-variant">Peran (Role) <span class="text-error">*</span></label>
                <select id="role" name="role" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required>
                    <option value="" disabled selected>Pilih Peran</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                @error('role')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2 mt-2">
                <label for="password" class="font-label text-label text-on-surface-variant">Kata Sandi <span class="text-error">*</span></label>
                <input type="password" id="password" name="password" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required minlength="8">
                @error('password')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="font-label text-label text-on-surface-variant">Ulangi Kata Sandi <span class="text-error">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required minlength="8">
            </div>

            <div class="pt-4 flex justify-end gap-4 border-t border-outline-variant mt-2">
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

@endsection
