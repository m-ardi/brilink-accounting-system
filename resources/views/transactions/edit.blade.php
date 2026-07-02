@extends('layouts.app')

@section('title', 'Edit Transaksi #' . $transaction->id)
@section('page-title', 'Edit Transaksi')

@section('content')

<!-- Bento Grid: Form & Preview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-margin_lg mb-8">
    
    <!-- Left: Form Input -->
    <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg flex flex-col gap-6">
        <div class="flex justify-between items-center border-b border-outline-variant pb-2">
            <h4 class="font-subheading text-subheading text-on-surface">Detail Transaksi</h4>
            <span class="font-label text-[10px] bg-primary-fixed text-on-primary-fixed px-2 py-0.5 rounded">ID: TRX-{{ $transaction->id }}</span>
        </div>
        
        <form method="POST" action="{{ route('transaksi.update', $transaction) }}" class="flex flex-col gap-4">
            @csrf @method('PUT')

            <!-- Kategori -->
            <div class="flex flex-col gap-2">
                <label for="category_id" class="font-label text-label text-on-surface-variant">Kategori Layanan <span class="text-error">*</span></label>
                <select id="category_id" name="category_id" class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface h-10" required>
                    <option value="" disabled>Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal -->
            <div class="flex flex-col gap-2">
                <label for="nominal" class="font-label text-label text-on-surface-variant">Nominal (Rp) <span class="text-error">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-medium text-sm tabular-nums">Rp</span>
                    <input type="number" id="nominal" name="nominal"
                        value="{{ old('nominal', $transaction->nominal) }}"
                        class="input-border input-focus rounded p-2 pl-10 bg-surface font-body text-body text-on-surface h-10 w-full tabular-nums"
                        placeholder="0"
                        min="1000" max="50000000" step="1000"
                        required autofocus>
                </div>
                @error('nominal')
                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="flex flex-col gap-2">
                <label for="keterangan" class="font-label text-label text-on-surface-variant">Keterangan <span class="font-normal">(Opsional)</span></label>
                <textarea id="keterangan" name="keterangan" rows="2"
                    class="input-border input-focus rounded p-2 bg-surface font-body text-body text-on-surface resize-none"
                    placeholder="Mis: nama nasabah, nomor rekening, dll.">{{ old('keterangan', $transaction->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-4 border-t border-outline-variant mt-2">
                <a href="{{ route('transaksi.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" id="btn-submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Right: Preview / Summary -->
    <div class="bg-surface-container-lowest card-border rounded-lg p-margin_lg flex flex-col gap-6 relative overflow-hidden">
        <div class="absolute left-0 top-0 h-full w-1 bg-primary-container"></div>
        <h4 class="font-subheading text-subheading text-on-surface border-b border-outline-variant pb-2 pl-4">Pratinjau Kalkulasi</h4>
        
        <div class="flex-1 flex flex-col justify-center items-center gap-6 p-6 bg-surface rounded card-border">
            <span class="material-symbols-outlined text-outline text-4xl">calculate</span>
            <div class="text-center">
                <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">NOMINAL TRANSAKSI</p>
                <p class="font-hero-figure text-hero-figure text-primary tabular-nums" id="nominal-preview">Rp {{ number_format(old('nominal', $transaction->nominal), 0, ',', '.') }}</p>
            </div>
            
            <div class="w-full mt-4 flex flex-col gap-2">
                <div class="flex justify-between items-center px-4 py-2 bg-surface rounded">
                    <span class="font-body text-sm text-on-surface-variant">Fee Bank</span>
                    <span class="font-body-semibold text-sm text-on-surface tabular-nums" id="preview-fee">Rp 0</span>
                </div>
                <div class="flex justify-between items-center px-4 py-2 bg-surface rounded">
                    <span class="font-body text-sm text-on-surface-variant">Ops (Agen)</span>
                    <span class="font-body-semibold text-sm text-error tabular-nums" id="preview-ops">-Rp 0</span>
                </div>
                <div class="flex justify-between items-center px-4 py-2 bg-primary-container/10 border border-primary/20 rounded">
                    <span class="font-body-semibold text-sm text-primary">Laba Bersih</span>
                    <span class="font-subheading text-subheading text-primary tabular-nums" id="preview-laba">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const categoriesData = @json($categories);

function updatePreview() {
    const nominalInput = document.getElementById('nominal').value;
    const val = parseInt(nominalInput.replace(/\D/g, '')) || 0;
    const catId = parseInt(document.getElementById('category_id').value);
    
    const previewNominal = document.getElementById('nominal-preview');
    const previewFee = document.getElementById('preview-fee');
    const previewOps = document.getElementById('preview-ops');
    const previewLaba = document.getElementById('preview-laba');

    previewNominal.textContent = 'Rp ' + val.toLocaleString('id-ID');

    if (val > 0 && catId) {
        // Cari kategori
        const category = categoriesData.find(c => c.id === catId);
        if (category && category.fee_rules) {
            // Cari rule yang cocok dengan nominal (min_nominal <= val && (max_nominal >= val || max_nominal === null))
            const rule = category.fee_rules.find(r => {
                if (!r.is_active) return false;
                const min = parseFloat(r.min_nominal);
                const max = r.max_nominal ? parseFloat(r.max_nominal) : Infinity;
                return val >= min && val <= max;
            });

            if (rule) {
                let fee = 0;
                if (rule.fee_type === 'persentase') {
                    fee = val * (parseFloat(rule.fee_value) / 100);
                } else {
                    fee = parseFloat(rule.fee_value);
                }
                const ops = parseFloat(rule.biaya_operasional);
                const laba = fee - ops;

                previewFee.textContent = 'Rp ' + fee.toLocaleString('id-ID');
                previewOps.textContent = '-Rp ' + ops.toLocaleString('id-ID');
                previewLaba.textContent = 'Rp ' + laba.toLocaleString('id-ID');
                return;
            }
        }
    }
    
    // Default jika tidak ketemu
    previewFee.textContent = 'Rp 0';
    previewOps.textContent = '-Rp 0';
    previewLaba.textContent = 'Rp 0';
}

document.getElementById('nominal').addEventListener('input', updatePreview);
document.getElementById('category_id').addEventListener('change', updatePreview);

// Run once on load
updatePreview();
</script>
@endpush
