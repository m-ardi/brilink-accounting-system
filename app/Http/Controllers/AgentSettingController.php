<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AgentSetting;
use App\Models\Modal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AgentSettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan.
     */
    public function index(): View
    {
        $settings = [
            'nama_agen'    => AgentSetting::get('nama_agen', 'Agen BRILink Rambo'),
            'alamat_agen'  => AgentSetting::get('alamat_agen', 'Dr. Ratulangi Kilometer 5'),
            'kode_agen'    => AgentSetting::get('kode_agen', 'RAMBO-001'),
            'saldo_modal'  => Modal::getSaldo(),    // ← sumber dari tabel modal
        ];

        return view('settings', compact('settings'));
    }

    /**
     * Update identitas agen dan kelola saldo modal.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_agen'              => 'required|string|max:150',
            'alamat_agen'            => 'required|string|max:255',
            'kode_agen'              => 'required|string|max:50',
            'tipe_penyesuaian'       => 'nullable|string|in:tambah,kurang',
            'nominal_penyesuaian'    => 'nullable|numeric|min:0',
            'keterangan_penyesuaian' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // ── Update identitas agen ──────────────────────────────────────
            AgentSetting::set('nama_agen',   $request->nama_agen);
            AgentSetting::set('alamat_agen', $request->alamat_agen);
            AgentSetting::set('kode_agen',   $request->kode_agen);

            // ── Penyesuaian saldo modal (top-up / penarikan) ──────────────
            $nominal = (float) $request->nominal_penyesuaian;

            if ($nominal > 0 && $request->tipe_penyesuaian) {
                $saldoLama = Modal::getSaldo();
                $keterangan = $request->keterangan_penyesuaian ?? '-';

                if ($request->tipe_penyesuaian === 'tambah') {
                    $saldoBaru = Modal::tambah($nominal);

                    ActivityLog::create([
                        'user_id'     => $user->id,
                        'action'      => 'updated',
                        'description' => "Top-up modal Rp " . number_format($nominal, 0, ',', '.') .
                                         ". Saldo: Rp " . number_format($saldoLama, 0, ',', '.') .
                                         " → Rp " . number_format($saldoBaru, 0, ',', '.') .
                                         ". Ket: {$keterangan}",
                        'created_at'  => now(),
                    ]);
                } elseif ($request->tipe_penyesuaian === 'kurang') {
                    try {
                        $saldoBaru = Modal::tarik($nominal);

                        ActivityLog::create([
                            'user_id'     => $user->id,
                            'action'      => 'updated',
                            'description' => "Penarikan modal Rp " . number_format($nominal, 0, ',', '.') .
                                             ". Saldo: Rp " . number_format($saldoLama, 0, ',', '.') .
                                             " → Rp " . number_format($saldoBaru, 0, ',', '.') .
                                             ". Ket: {$keterangan}",
                            'created_at'  => now(),
                        ]);
                    } catch (\RuntimeException $e) {
                        // Saldo tidak cukup — redirect dengan error
                        session()->flash('error', $e->getMessage());
                        return;
                    }
                }
            } else {
                // Hanya update identitas
                ActivityLog::create([
                    'user_id'     => $user->id,
                    'action'      => 'updated',
                    'description' => "Owner mengubah identitas agen (Nama/Alamat/Kode).",
                    'created_at'  => now(),
                ]);
            }
        });

        return redirect()->route('settings.index')->with('status', 'settings-updated');
    }
}
