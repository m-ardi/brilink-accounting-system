@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg">
        <h4 class="font-subheading text-subheading text-on-surface border-b border-outline-variant pb-2 mb-6">Ubah Profil Pengguna</h4>
        
        <form method="POST" action="{{ route('users.update', $user) }}" class="flex flex-col gap-4">
            @csrf @method('PUT')

            <!-- Nama -->
            <div class="flex flex-col gap-2">
                <label for="name" class="font-label text-label text-on-surface-variant">Nama Lengkap <span class="text-error">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required autofocus>
                @error('name')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label for="email" class="font-label text-label text-on-surface-variant">Alamat Email <span class="text-error">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required>
                @error('email')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Role -->
            <div class="flex flex-col gap-2">
                <label for="role" class="font-label text-label text-on-surface-variant">Peran (Role) <span class="text-error">*</span></label>
                <select id="role" name="role" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" required {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                    <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
                @if(auth()->id() === $user->id)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="text-[10px] text-on-surface-variant mt-1">Anda tidak dapat mengubah peran Anda sendiri.</p>
                @endif
                @error('role')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <hr class="border-outline-variant my-2 border-dashed">
            <h5 class="font-label-caps text-label-caps text-on-surface-variant mb-2">Ubah Kata Sandi (Opsional)</h5>

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label for="password" class="font-label text-label text-on-surface-variant">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" minlength="8" placeholder="Biarkan kosong jika tidak ingin mengubah sandi">
                @error('password')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="font-label text-label text-on-surface-variant">Ulangi Kata Sandi Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full" minlength="8">
            </div>

            <div class="pt-4 flex justify-end gap-4 border-t border-outline-variant mt-2">
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection
