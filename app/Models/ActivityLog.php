<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model audit trail — read-only log semua perubahan data transaksi.
 */
class ActivityLog extends Model
{
    public $timestamps = false; // Hanya pakai created_at manual

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Kembalikan label aksi yang mudah dibaca manusia
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Tambah',
            'updated' => 'Ubah',
            'deleted' => 'Hapus',
            default   => ucfirst($this->action),
        };
    }

    // Scope: filter per aksi
    public function scopeOfAction($query, $action)
    {
        return $query->when($action, fn ($q) => $q->where('action', $action));
    }
}
