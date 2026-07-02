<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model konfigurasi global agen — key-value store.
 * Digunakan untuk menyimpan: saldo_modal, nama_agen, alamat_agen, dll.
 */
class AgentSetting extends Model
{
    public $timestamps = false; // Hanya pakai updated_at

    protected $fillable = ['key', 'value', 'description'];

    /**
     * Ambil nilai setting berdasarkan key, dengan nilai default jika tidak ditemukan.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set atau update nilai setting berdasarkan key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Ambil saldo modal saat ini (helper shortcut).
     */
    public static function getSaldoModal(): float
    {
        return (float) static::get('saldo_modal', 0);
    }

    /**
     * Update saldo modal (dipanggil setiap kali ada transaksi baru).
     */
    public static function setSaldoModal(float $saldo): void
    {
        static::set('saldo_modal', $saldo);
    }
}
