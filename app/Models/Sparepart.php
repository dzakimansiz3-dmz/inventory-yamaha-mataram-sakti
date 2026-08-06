<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $table = 'spareparts';

    protected $primaryKey = 'id_sparepart';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'nama_sparepart',
        'kode_part',
        'kategori',
        'harga',
        'stok',
        'stok_minimum',
        'keterangan',
        'status',
    ];

    // =========================
    // CASTING (BIAR AMAN)
    // =========================
    protected $casts = [
        'harga' => 'integer',
        'stok' => 'integer',
    ];

    // =========================
    // RELASI
    // =========================
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'sparepart_id', 'id_sparepart');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'sparepart_id', 'id_sparepart');
    }

    // =========================
    // HELPER: CEK STOK KRITIS
    // =========================
    public function isStokKritis()
    {
        return $this->stok <= $this->stok_minimum;
    }

    // =========================
    // HELPER: FORMAT HARGA
    // =========================
    public function getHargaFormatAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }

    // =========================
    // HELPER: FORMAT STOK
    // =========================
    public function getStokFormatAttribute()
    {
        return number_format($this->stok, 0, ',', '.');
    }
}