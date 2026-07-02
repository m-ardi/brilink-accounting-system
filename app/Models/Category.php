<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model kategori layanan BRILink.
 * Kategori: Transfer, Tarik Tunai, PPOB.
 */
class Category extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke aturan fee aktif
    public function feeRules(): HasMany
    {
        return $this->hasMany(FeeRule::class);
    }

    // Ambil fee rule aktif pertama (digunakan untuk fallback/PPOB)
    public function activeFeeRule(): ?FeeRule
    {
        return $this->feeRules()->where('is_active', true)->first();
    }

    // Ambil fee rule aktif yang sesuai dengan jangkauan nominal transaksi
    public function activeFeeRuleFor(float $nominal): ?FeeRule
    {
        return $this->feeRules()
            ->where('is_active', true)
            ->where('min_nominal', '<=', $nominal)
            ->where(function ($query) use ($nominal) {
                $query->where('max_nominal', '>=', $nominal)
                      ->orWhereNull('max_nominal');
            })
            ->first();
    }

    // Relasi ke transaksi
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope: hanya kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
