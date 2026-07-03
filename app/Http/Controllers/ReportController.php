<?php

namespace App\Http\Controllers;

use App\Models\AgentSetting;
use App\Models\Category;
use App\Models\Modal;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * ReportController — Generate laporan & export PDF/Excel.
 */
class ReportController extends Controller
{
    /**
     * Halaman laporan dengan form filter periode.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()->get();
        $transactions = null;
        $summary = null;

        $query = Transaction::with('category')
            ->dateRange($request->date_from, $request->date_to)
            ->ofCategory($request->category_id)
            ->latest();

        $transactions = $query->get();

        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_nominal'   => $transactions->sum('nominal'),
            'total_fee_bank'  => $transactions->sum('biaya_admin'),
            'total_biaya_op'  => $transactions->sum('setoran_bri'),
            'total_laba'      => $transactions->sum('laba_bersih'),
        ];

        return view('reports.index', compact('categories', 'transactions', 'summary'));
    }

    /**
     * Export laporan ke PDF atau Excel.
     */
    public function export(Request $request, string $format): mixed
    {
        $request->validate([
            'date_from'   => 'nullable|date',
            'date_to'     => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $transactions = Transaction::with('category')
            ->dateRange($request->date_from, $request->date_to)
            ->ofCategory($request->category_id)
            ->latest()
            ->get();

        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_nominal'   => $transactions->sum('nominal'),
            'total_fee_bank'  => $transactions->sum('biaya_admin'),
            'total_biaya_op'  => $transactions->sum('setoran_bri'),
            'total_laba'      => $transactions->sum('laba_bersih'),
            'saldo_modal'     => Modal::getSaldo(),
        ];

        $agentInfo = [
            'nama_agen'   => AgentSetting::get('nama_agen', 'Agen BRILink Rambo'),
            'alamat_agen' => AgentSetting::get('alamat_agen', '-'),
            'kode_agen'   => AgentSetting::get('kode_agen', '-'),
            'periode_dari' => $request->date_from ?? 'Semua',
            'periode_ke'   => $request->date_to ?? 'Semua',
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'summary', 'agentInfo'))
                ->setPaper('A4', 'landscape');

            $filename = 'laporan-brilink-' . now()->format('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        }

        // Excel — menggunakan maatwebsite/excel
        $filename = 'laporan-brilink-' . now()->format('Y-m-d') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TransactionExport($transactions),
            $filename
        );
    }
}
