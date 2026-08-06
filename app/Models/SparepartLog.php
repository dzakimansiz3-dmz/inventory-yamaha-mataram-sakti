<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartLog extends Model
{
    protected $table = 'sparepart_logs';

    protected $fillable = [
        // 🔥 DATA BARU
        'nama_sparepart',
        'kode_part',
        'kategori',
        'harga',
        'stok',

        // 🔥 DATA LAMA
        'old_nama_sparepart',
        'old_kode_part',
        'old_kategori',
        'old_harga',

        // 🔥 TIPE AKSI
        'tipe',
    ];

    /**
     * 🔥 RELASI KE USER (OPTIONAL)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}