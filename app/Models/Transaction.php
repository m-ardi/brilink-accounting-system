<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model transaksi BRILink.
 * Formula yang berlaku:
 *   laba_bersih    = biaya_admin - setoran_bri
 *   saldo_sesudah  = saldo_sebelum - nominal + laba_bersih
 */
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'nominal',
        'biaya_admin',
        'setoran_bri',
        'laba_bersih',
        'saldo_modal_sebelum',
        'saldo_modal_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'nominal'             => 'decimal:2',
        'biaya_admin'         => 'decimal:2',
        'setoran_bri'         => 'decimal:2',
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
