<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model aturan fee per kategori transaksi BRILink.
 * Formula laba: laba_bersih = biaya_admin - setoran_bri
 */
class FeeRule extends Model
{
    protected $fillable = [
        'category_id',
        'min_nominal',
        'max_nominal',
        'fee_type',
        'biaya_admin',
        'setoran_bri',
        'is_active',
    ];

    protected $casts = [
        'min_nominal'  => 'decimal:2',
        'max_nominal'  => 'decimal:2',
        'biaya_admin'  => 'decimal:2',
        'setoran_bri'  => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param  float 
     * @return float           
     */
    public function getBiayaAdmin(float $nominal): float
    {
        if ($this->fee_type === 'persentase') {
            return round($nominal * ((float) $this->biaya_admin / 100), 2);
        }

        // Default: flat nominal
        return (float) $this->biaya_admin;
    }
}
