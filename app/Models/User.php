<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model user aplikasi BRILink.
 *
 * Role saat ini: hanya 'owner' (pemilik agen).
 * Kolom `role` disiapkan untuk scale-up ke multi-user (owner + staff)
 * tanpa perlu mengubah struktur database.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'owner' | 'staff' — default 'owner' via database
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relasi ke transaksi yang diinput user ini
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi ke audit log yang dibuat user ini
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helper: cek apakah user adalah owner
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}
