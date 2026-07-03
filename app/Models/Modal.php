<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel modal — menyimpan saldo modal berjalan agen.
 *
 * Tabel ini selalu memiliki tepat SATU baris (singleton).
 * Gunakan static helper getSaldo() dan setSaldo() untuk membaca/mengubah nilai.
 */
class Modal extends Model
{
    protected $table = 'modal';

    protected $fillable = ['saldo_saat_ini'];

    protected $casts = [
        'saldo_saat_ini' => 'decimal:2',
    ];

    /**
     * Ambil saldo modal saat ini.
     */
    public static function getSaldo(): float
    {
        $modal = static::first();
        return $modal ? (float) $modal->saldo_saat_ini : 0.0;
    }

    /**
     * Set saldo modal ke nilai baru.
     */
    public static function setSaldo(float $nilai): void
    {
        $modal = static::first();

        if ($modal) {
            $modal->update(['saldo_saat_ini' => $nilai]);
        } else {
            static::create(['saldo_saat_ini' => $nilai]);
        }
    }

    /**
     * Tambahkan jumlah ke saldo saat ini (top-up).
     */
    public static function tambah(float $jumlah): float
    {
        $baru = static::getSaldo() + $jumlah;
        static::setSaldo($baru);
        return $baru;
    }

    /**
     * Kurangi saldo saat ini (penarikan modal).
     *
     * @throws \RuntimeException Jika saldo tidak mencukupi
     */
    public static function tarik(float $jumlah): float
    {
        $saldo = static::getSaldo();

        if ($jumlah > $saldo) {
            throw new \RuntimeException(
                "Penarikan Rp " . number_format($jumlah, 0, ',', '.') .
                " melebihi saldo modal saat ini Rp " . number_format($saldo, 0, ',', '.')
            );
        }

        $baru = $saldo - $jumlah;
        static::setSaldo($baru);
        return $baru;
    }
}
