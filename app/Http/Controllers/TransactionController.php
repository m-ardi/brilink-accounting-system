<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * TransactionController — CRUD transaksi.
 * Logika bisnis didelegasikan ke TransactionService.
 */
class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    /**
     * Daftar riwayat transaksi dengan filter dinamis.
     */
    public function index(Request $request): View
    {
        $query = Transaction::with('category', 'user')->latest();

        // Filter dinamis menggunakan Eloquent scopes (when() chain)
        $query
            ->dateRange($request->date_from, $request->date_to)
            ->ofCategory($request->category_id)
            ->nominalRange($request->nominal_min, $request->nominal_max)
            ->search($request->search);

        $transactions = $query->paginate(15)->withQueryString();
        $categories   = Category::active()->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    /**
     * Form input transaksi baru.
     */
    public function create(): View
    {
        $categories = Category::with('feeRules')->active()->get();
        return view('transactions.create', compact('categories'));
    }

    /**
     * Simpan transaksi baru via TransactionService.
     * Setelah berhasil: kembalikan response JSON untuk update dashboard AJAX.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        try {
            $transaction = $this->transactionService->store($request->validated());

            return redirect()
                ->route('transaksi.create')
                ->with('success', "Transaksi #{$transaction->id} berhasil disimpan. Laba: Rp " .
                    number_format($transaction->laba_bersih, 0, ',', '.'));
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Detail satu transaksi.
     */
    public function show(Transaction $transaksi): View
    {
        return view('transactions.show', ['transaction' => $transaksi->load('category', 'user')]);
    }

    /**
     * Form edit transaksi.
     */
    public function edit(Transaction $transaksi): View
    {
        $categories = Category::with('feeRules')->active()->get();
        return view('transactions.edit', [
            'transaction' => $transaksi,
            'categories'  => $categories,
        ]);
    }

    /**
     * Update transaksi via TransactionService.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaksi): RedirectResponse
    {
        try {
            $this->transactionService->update($transaksi, $request->validated());

            return redirect()
                ->route('transaksi.index')
                ->with('success', "Transaksi #{$transaksi->id} berhasil diperbarui.");
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus transaksi via TransactionService.
     */
    public function destroy(Transaction $transaksi): RedirectResponse
    {
        try {
            $this->transactionService->delete($transaksi);

            return redirect()
                ->route('transaksi.index')
                ->with('success', "Transaksi berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
