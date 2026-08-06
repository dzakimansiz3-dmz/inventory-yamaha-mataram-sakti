<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'sparepart_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'status',
    ];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id', 'id_sparepart');
    }
}