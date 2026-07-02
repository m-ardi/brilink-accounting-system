<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model aturan fee per kategori transaksi.
 *
 * ⚠️ PENDING VALIDATION — Nilai fee_value dan biaya_operasional di sini
 * masih provisional dan belum diverifikasi ke pemilik Agen BRILink Rambo.
 */
class FeeRule extends Model
{
    protected $fillable = [
        'category_id',
        'min_nominal',
        'max_nominal',
        'fee_type',
        'fee_value',
        'biaya_operasional',
        'is_active',
    ];

    protected $casts = [
        'min_nominal'       => 'decimal:2',
        'max_nominal'       => 'decimal:2',
        'fee_value'         => 'decimal:2',
        'biaya_operasional' => 'decimal:2',
        'is_active'         => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Hitung fee bank dari nominal transaksi.
     * ⚠️ PENDING VALIDATION — Formula provisional.
     *
     * @param  float $nominal Nominal transaksi nasabah
     * @return float Fee bank yang diperoleh agen
     */
    public function hitungFeeBank(float $nominal): float
    {
        if ($this->fee_type === 'persentase') {
            return round($nominal * ($this->fee_value / 100), 2);
        }

        // Default: nominal flat
        return (float) $this->fee_value;
    }
}
