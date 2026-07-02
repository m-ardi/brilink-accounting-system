<?php

namespace App\Http\Controllers;

use App\Models\AgentSetting;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * DashboardController — Tampilan ringkasan agen & endpoint stats untuk AJAX.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama.
     */
    public function index(): View
    {
        $saldoModal     = AgentSetting::getSaldoModal();
        $namaAgen       = AgentSetting::get('nama_agen', 'Agen BRILink');
        $labaHariIni    = $this->hitungLabaHariIni();
        $totalTxHariIni = Transaction::whereDate('created_at', today())->count();
        $transaksiTerbaru = Transaction::with('category')
            ->latest()
            ->take(5)
            ->get();
            
        $aktivitasTerbaru = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Data grafik mingguan (7 hari terakhir)
        $grafikMingguan = $this->dataGrafikMingguan();
        
        // Data breakdown per kategori hari ini
        $breakdownKategori = $this->dataBreakdownKategori();

        return view('dashboard', compact(
            'saldoModal', 'namaAgen', 'labaHariIni',
            'totalTxHariIni', 'transaksiTerbaru', 'grafikMingguan', 
            'aktivitasTerbaru', 'breakdownKategori'
        ));
    }

    /**
     * Endpoint AJAX — kembalikan stats terkini sebagai JSON.
     * Dipanggil oleh fetch() di frontend setelah input transaksi baru.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'saldo_modal'          => AgentSetting::getSaldoModal(),
            'saldo_modal_formatted' => 'Rp ' . number_format(AgentSetting::getSaldoModal(), 0, ',', '.'),
            'laba_hari_ini'        => $this->hitungLabaHariIni(),
            'laba_hari_ini_formatted' => 'Rp ' . number_format($this->hitungLabaHariIni(), 0, ',', '.'),
            'total_tx_hari_ini'    => Transaction::whereDate('created_at', today())->count(),
        ]);
    }

    /**
     * Hitung total laba bersih hari ini.
     */
    private function hitungLabaHariIni(): float
    {
        return (float) Transaction::whereDate('created_at', today())
            ->sum('laba_bersih');
    }

    /**
     * Data grafik transaksi 7 hari terakhir untuk Chart.js.
     * Returns: [labels, data_jumlah, data_laba]
     */
    private function dataGrafikMingguan(): array
    {
        $labels      = [];
        $jumlah      = [];
        $laba        = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal   = now()->subDays($i);
            $labels[]  = $tanggal->translatedFormat('D, d M');
            $jumlah[]  = Transaction::whereDate('created_at', $tanggal->toDateString())->count();
            $laba[]    = (float) Transaction::whereDate('created_at', $tanggal->toDateString())->sum('laba_bersih');
        }

        return compact('labels', 'jumlah', 'laba');
    }

    /**
     * Breakdown transaksi & laba per kategori hari ini.
     */
    private function dataBreakdownKategori(): array
    {
        $rows = Transaction::select(
                'category_id',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(nominal) as total_nominal'),
                DB::raw('SUM(laba_bersih) as total_laba')
            )
            ->with('category:id,name')
            ->whereDate('created_at', today())
            ->groupBy('category_id')
            ->get();

        return $rows->map(function ($row) {
            return [
                'nama'          => $row->category->name ?? 'Lainnya',
                'jumlah'        => $row->jumlah,
                'total_nominal' => (float) $row->total_nominal,
                'total_laba'    => (float) $row->total_laba,
            ];
        })->values()->toArray();
    }
}
