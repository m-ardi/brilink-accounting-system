@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-margin_lg">
        <!-- Saldo Modal -->
        <div
            class="bg-surface border custom-border rounded-xl p-5 flex flex-col gap-3 group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-primary rounded-r-xl"></div>
            <div class="flex justify-between items-start">
                <div class="flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Saldo
                        Modal</span>
                    <span class="text-[10px] text-on-surface-variant opacity-60">Dana yang beredar</span>
                </div>
                <div
                    class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-primary text-[20px]">account_balance_wallet</span>
                </div>
            </div>
            <div class="font-hero-figure text-hero-figure tabular-nums text-on-surface" id="stat-saldo">Rp
                {{ number_format($saldoModal, 0, ',', '.') }}</div>
            <div class="h-px bg-primary/10 group-hover:bg-primary/20 transition-colors"></div>
            <div class="flex justify-between items-center text-[11px] text-on-surface-variant font-body">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    <span>Diperbarui: {{ now()->format('d M Y') }}</span>
                </div>
                @if(auth()->user()->role === 'owner')
                <a href="{{ route('settings.index') }}" class="hover:text-primary transition-colors font-body-semibold">Kelola Modal →</a>
                @endif
            </div>
        </div>

        <!-- Laba Hari Ini -->
        <div
            class="bg-surface border custom-border rounded-xl p-5 flex flex-col gap-3 group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-secondary rounded-r-xl"></div>
            <div class="flex justify-between items-start">
                <div class="flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Laba Hari
                        Ini</span>
                    <span class="text-[10px] text-on-surface-variant opacity-60">⚠️ Provisional</span>
                </div>
                <div
                    class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                    <span class="material-symbols-outlined text-secondary text-[20px]">payments</span>
                </div>
            </div>
            <div class="font-hero-figure text-hero-figure tabular-nums text-on-surface" id="stat-laba">Rp
                {{ number_format($labaHariIni, 0, ',', '.') }}</div>
            <div class="h-px bg-secondary/10 group-hover:bg-secondary/20 transition-colors"></div>
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px] text-success-text">trending_up</span>
                <span class="text-[11px] text-on-surface-variant">Laba per transaksi hari ini</span>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div
            class="bg-surface border custom-border rounded-xl p-5 flex flex-col gap-3 group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-tertiary-container rounded-r-xl"></div>
            <div class="flex justify-between items-start">
                <div class="flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Jumlah
                        Transaksi</span>
                    <span class="text-[10px] text-on-surface-variant opacity-60">Total hari ini</span>
                </div>
                <div
                    class="w-10 h-10 rounded-lg bg-outline/10 flex items-center justify-center group-hover:bg-outline/20 transition-colors">
                    <span class="material-symbols-outlined text-on-surface-variant text-[20px]">receipt_long</span>
                </div>
            </div>
            <div class="font-hero-figure text-hero-figure tabular-nums text-on-surface" id="stat-total-tx">
                {{ $totalTxHariIni }}</div>
            <div class="h-px bg-outline/10 group-hover:bg-outline/20 transition-colors"></div>
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px] text-on-surface-variant">query_stats</span>
                <span class="text-[11px] text-on-surface-variant">
                    <a href="{{ route('transaksi.index') }}" class="hover:text-primary transition-colors">Lihat riwayat
                        →</a>
                </span>
            </div>
        </div>
    </div>

    <!-- Row 2: Chart + Log + Breakdown Donut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-margin_lg">
        <!-- Weekly Profit Trend Chart (span 2 cols) -->
        <div class="lg:col-span-2 bg-surface border custom-border rounded-xl p-margin_md flex flex-col"
            style="min-height:380px">
            <h2 class="font-subheading text-subheading text-primary mb-4">Tren Laba Mingguan</h2>
            <div class="flex-grow relative">
                <canvas id="grafikMingguan" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>
        <!-- Donut Chart Breakdown -->
        <div class="bg-surface border custom-border rounded-xl p-5 flex flex-col">
            <div class="mb-4">
                <h2 class="font-subheading text-subheading text-primary">Distribusi Laba</h2>
                <p class="text-[11px] text-on-surface-variant">Per kategori layanan hari ini</p>
            </div>
            <div class="flex-grow flex items-center justify-center relative" style="min-height:200px">
                <canvas id="grafikDonut"></canvas>
            </div>
            <div class="mt-4 space-y-2" id="donut-legend">
                @foreach($breakdownKategori as $i => $item)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ ['#00529c', '#f37021', '#4caf50', '#9c27b0', '#ff5722'][$i % 5] }}">
                            </div>
                            <span class="text-on-surface-variant">{{ $item['nama'] }}</span>
                        </div>
                        <span class="text-on-surface font-semibold tabular-nums">Rp
                            {{ number_format($item['total_laba'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Row 3: Ringkasan per Kategori +  Log Aktifitas -->
    @if(count($breakdownKategori) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-margin_lg">
            <!-- Ringkasan per Kategori (table cards) -->
            <div class="lg:col-span-2 bg-surface border custom-border rounded-xl overflow-hidden">
                <div class="p-4 border-b custom-border bg-surface-container-low flex justify-between items-center">
                    <div>
                        <h2 class="font-subheading text-subheading text-primary">Ringkasan per Kategori</h2>
                        <p class="text-[11px] text-on-surface-variant mt-0.5">Transaksi hari ini</p>
                    </div>
                    <span class="material-symbols-outlined text-primary/40 text-[28px]">category</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b custom-border bg-surface">
                            <tr class="font-label-caps text-label-caps text-on-surface-variant">
                                <th class="px-4 py-3 font-normal">Kategori Layanan</th>
                                <th class="px-4 py-3 font-normal text-center">Jml Transaksi</th>
                                <th class="px-4 py-3 font-normal text-right">Total Nominal</th>
                                <th class="px-4 py-3 font-normal text-right">Total Laba</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @foreach($breakdownKategori as $i => $item)
                                <tr class="table-row-hover transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2.5 h-2.5 rounded-full"
                                                style="background-color: {{ ['#00529c', '#f37021', '#4caf50', '#9c27b0', '#ff5722'][$i % 5] }}">
                                            </div>
                                            <span class="font-body text-body text-on-surface font-medium">{{ $item['nama'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary font-semibold text-sm tabular-nums">{{ $item['jumlah'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-body text-body text-on-surface tabular-nums">
                                        Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-success-text font-semibold text-sm tabular-nums">+Rp
                                            {{ number_format($item['total_laba'], 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Log Aktivitas -->
            <div class="bg-surface border custom-border rounded-xl overflow-hidden flex flex-col" style="min-height:380px">
                <div class="p-margin_md border-b custom-border bg-surface-container-low flex justify-between items-center">
                    <h2 class="font-subheading text-subheading text-primary">Log Aktivitas</h2>
                    <a href="{{ route('audit-log.index') }}"
                        class="text-primary hover:underline font-label text-label">Semua</a>
                </div>
                <div class="overflow-y-auto flex-grow p-4">
                    <ul class="space-y-4">
                        @forelse($aktivitasTerbaru as $log)
                            <li class="flex flex-col border-b custom-border border-opacity-50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="font-label-caps text-[10px] uppercase font-semibold text-on-surface-variant">{{ $log->action }}</span>
                                    <span
                                        class="text-[10px] text-on-surface-variant tabular-nums">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="font-body text-xs text-on-surface">{{ $log->description }}</p>
                                <p class="font-label text-[10px] text-on-surface-variant mt-1">Oleh:
                                    {{ $log->user->name ?? 'Sistem' }}</p>
                            </li>
                        @empty
                            <li class="text-xs text-on-surface-variant text-center py-4">Belum ada aktivitas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    @endif

    <!-- Transaksi Terbaru Table -->
    <div class="bg-surface border custom-border rounded-xl overflow-hidden flex flex-col w-full">
        <div class="p-margin_md border-b custom-border bg-surface-container-low flex justify-between items-center">
            <h2 class="font-subheading text-subheading text-primary">Transaksi Terbaru</h2>
            <a href="{{ route('transaksi.index') }}" class="text-primary hover:underline font-label text-label">Lihat
                Semua</a>
        </div>

        <div class="overflow-x-auto flex-grow">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b custom-border bg-surface font-label-caps text-label-caps text-on-surface-variant">
                        <th class="py-3 px-4 font-normal w-24">Waktu</th>
                        <th class="py-3 px-4 font-normal">Kategori</th>
                        <th class="py-3 px-4 font-normal text-right">Nominal</th>
                        <th class="py-3 px-4 font-normal text-right">Laba/Fee</th>
                    </tr>
                </thead>
                <tbody class="font-body text-body text-on-surface">
                    @forelse($transaksiTerbaru as $tx)
                        <tr class="zebra-row border-b custom-border transition-colors">
                            <td class="py-3 px-4 tabular-nums">{{ $tx->created_at->format('H:i') }}</td>
                            <td class="py-3 px-4">{{ $tx->category->name }}</td>
                            <td class="py-3 px-4 text-right tabular-nums">Rp {{ number_format($tx->nominal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums text-success-text font-semibold">+Rp
                                {{ number_format($tx->laba_bersih, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-on-surface-variant font-body">Belum ada transaksi hari
                                ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Grafik Mingguan (Chart.js) ────────────────────────────────────────
        const ctx = document.getElementById('grafikMingguan').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($grafikMingguan['labels']),
                datasets: [
                    {
                        label: 'Jumlah Transaksi',
                        data: @json($grafikMingguan['jumlah']),
                        backgroundColor: '#0b3d5c', // primary-container
                        borderRadius: 2,
                        yAxisID: 'y',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Laba (Rp)',
                        data: @json($grafikMingguan['laba']),
                        type: 'line',
                        borderColor: '#C8912E', // ledger accent
                        backgroundColor: 'rgba(200, 145, 46, 0.1)',
                        tension: 0, // Flat design implies straight lines, no tension
                        pointRadius: 4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'Inter', size: 12 } }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        type: 'linear', display: false, position: 'left'
                    },
                    y1: {
                        type: 'linear', display: true, position: 'right',
                        grid: { color: '#DEDEDA', drawBorder: false },
                        ticks: { font: { family: 'Inter', size: 10 }, callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                    },
                }
            }
        });

        // ── AJAX Stats Update ─────────────────────────────────────────────────
        async function refreshStats() {
            try {
                const res = await fetch('{{ route("dashboard.stats") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                document.getElementById('stat-saldo').textContent = data.saldo_modal_formatted;
                document.getElementById('stat-laba').textContent = data.laba_hari_ini_formatted;
                document.getElementById('stat-total-tx').textContent = data.total_tx_hari_ini;
            } catch (e) { /* silent fail */ }
        }
        setInterval(refreshStats, 30000);
        window.refreshDashboardStats = refreshStats;

        // ── Donut Chart Breakdown Kategori ────────────────────────────────────
        @if(count($breakdownKategori) > 0)
            const ctxDonut = document.getElementById('grafikDonut');
            if (ctxDonut) {
                const palette = ['#00529c', '#f37021', '#4caf50', '#9c27b0', '#ff5722'];
                new Chart(ctxDonut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: @json(collect($breakdownKategori)->pluck('nama')),
                        datasets: [{
                            data: @json(collect($breakdownKategori)->pluck('total_laba')),
                            backgroundColor: palette.slice(0, {{ count($breakdownKategori) }}),
                            borderWidth: 2,
                            borderColor: 'transparent',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ' Rp ' + ctx.parsed.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            }
        @endif
    </script>
@endpush