@extends('layouts.app')

@section('title', 'Pengaturan Agen')
@section('page-title', 'Pengaturan Agen')

@section('content')

<!-- Alert Status -->
@if (session('status') === 'settings-updated')
    <div class="mb-margin_lg p-4 bg-success-dim border border-success-text/20 text-success-text rounded-lg text-sm font-body">
        Pengaturan agen dan saldo modal berhasil diperbarui.
    </div>
@endif

<form method="POST" action="{{ route('settings.update') }}" class="flex flex-col gap-margin_lg">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-margin_lg mb-8">
        
        <!-- Left: Identitas Agen -->
        <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg flex flex-col gap-6">
            <h4 class="font-subheading text-subheading text-on-surface border-b border-outline-variant pb-2">Identitas Agen</h4>
            
            <div class="flex flex-col gap-4">
                <!-- Nama Agen -->
                <div class="flex flex-col gap-2">
                    <label for="nama_agen" class="font-label text-label text-on-surface-variant">Nama Agen <span class="text-error">*</span></label>
                    <input type="text" id="nama_agen" name="nama_agen" value="{{ old('nama_agen', $settings['nama_agen']) }}"
                           class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full"
                           required>
                    @error('nama_agen')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Agen -->
                <div class="flex flex-col gap-2">
                    <label for="kode_agen" class="font-label text-label text-on-surface-variant">Kode Agen <span class="text-error">*</span></label>
                    <input type="text" id="kode_agen" name="kode_agen" value="{{ old('kode_agen', $settings['kode_agen']) }}"
                           class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full"
                           required>
                    @error('kode_agen')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat Agen -->
                <div class="flex flex-col gap-2">
                    <label for="alamat_agen" class="font-label text-label text-on-surface-variant">Alamat Agen <span class="text-error">*</span></label>
                    <textarea id="alamat_agen" name="alamat_agen" rows="3"
                              class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface resize-none w-full"
                              required>{{ old('alamat_agen', $settings['alamat_agen']) }}</textarea>
                    @error('alamat_agen')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Right: Kelola Saldo Modal -->
        <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg flex flex-col gap-6 relative overflow-hidden">
            <div class="absolute left-0 top-0 h-full w-1 bg-[#C8912E]"></div>
            <h4 class="font-subheading text-subheading text-on-surface border-b border-outline-variant pb-2 pl-4">Kelola Saldo Modal</h4>
            
            <!-- Widget Saldo Saat Ini -->
            <div class="p-4 bg-surface rounded card-border flex flex-col gap-2 ml-4">
                <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-wider">SALDO MODAL AKTIF</span>
                <span class="font-hero-figure text-hero-figure text-primary tabular-nums">Rp {{ number_format($settings['saldo_modal'], 0, ',', '.') }}</span>
                <p class="text-xs text-on-surface-variant/70 italic">Diupdate otomatis saat Anda melakukan transaksi.</p>
            </div>

            <!-- Form Penyesuaian Saldo -->
            <div class="flex flex-col gap-4 ml-4">
                <p class="font-body text-sm text-on-surface-variant border-b border-outline-variant pb-2">Penyesuaian Saldo Modal Manual (Opsional)</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tipe Penyesuaian -->
                    <div class="flex flex-col gap-2">
                        <label for="tipe_penyesuaian" class="font-label text-label text-on-surface-variant">Aksi Penyesuaian</label>
                        <select id="tipe_penyesuaian" name="tipe_penyesuaian" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10">
                            <option value="">Pilih Aksi...</option>
                            <option value="tambah">Tambah Modal (Suntik Dana)</option>
                            <option value="kurang">Kurang Modal (Tarik Dana)</option>
                        </select>
                        @error('tipe_penyesuaian')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nominal Penyesuaian -->
                    <div class="flex flex-col gap-2">
                        <label for="nominal_penyesuaian" class="font-label text-label text-on-surface-variant">Nominal Penyesuaian (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium text-sm tabular-nums">Rp</span>
                            <input type="number" id="nominal_penyesuaian" name="nominal_penyesuaian"
                                   value="{{ old('nominal_penyesuaian') }}"
                                   class="input-border input-focus rounded p-2 pl-10 bg-surface font-body text-body text-on-surface h-10 w-full tabular-nums"
                                   placeholder="0" min="0" step="1000">
                        </div>
                        @error('nominal_penyesuaian')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan Penyesuaian -->
                <div class="flex flex-col gap-2">
                    <label for="keterangan_penyesuaian" class="font-label text-label text-on-surface-variant">Keterangan / Alasan Penyesuaian</label>
                    <input type="text" id="keterangan_penyesuaian" name="keterangan_penyesuaian"
                           value="{{ old('keterangan_penyesuaian') }}"
                           class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10 w-full"
                           placeholder="Contoh: Setor modal tunai via loket bank">
                    @error('keterangan_penyesuaian')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-4 flex justify-end gap-4 border-t border-outline-variant">
        <a href="{{ route('dashboard') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Pengaturan</button>
    </div>
</form>

@endsection
