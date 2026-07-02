<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model transaksi BRILink.
 *
 * Catatan arsitektur:
 * - Nilai fee_bank, biaya_operasional, laba_bersih, saldo_modal_sebelum,
 *   dan saldo_modal_sesudah adalah SNAPSHOT saat transaksi terjadi.
 *   Tidak menggunakan FK ke fee_rules agar laporan historis tidak berubah
 *   jika aturan fee diperbarui di kemudian hari.
 *
 * ⚠️ PENDING VALIDATION — Formula laba bersih provisional.
 */
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'nominal',
        'fee_bank',
        'biaya_operasional',
        'laba_bersih',
        'saldo_modal_sebelum',
        'saldo_modal_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'nominal'             => 'decimal:2',
        'fee_bank'            => 'decimal:2',
        'biaya_operasional'   => 'decimal:2',
        'laba_bersih'         => 'decimal:2',
        'saldo_modal_sebelum' => 'decimal:2',
        'saldo_modal_sesudah' => 'decimal:2',
        'created_at'          => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope: filter rentang tanggal
    public function scopeDateRange($query, $from, $to)
    {
        return $query
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('created_at', '<=', $to));
    }

    // Scope: filter kategori
    public function scopeOfCategory($query, $categoryId)
    {
        return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));
    }

    // Scope: filter range nominal
    public function scopeNominalRange($query, $min, $max)
    {
        return $query
            ->when($min, fn ($q) => $q->where('nominal', '>=', $min))
            ->when($max, fn ($q) => $q->where('nominal', '<=', $max));
    }

    // Scope: pencarian teks (ID atau keterangan)
    public function scopeSearch($query, $keyword)
    {
        return $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function ($inner) use ($keyword) {
                $inner->where('id', 'like', "%{$keyword}%")
                      ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        });
    }
}
