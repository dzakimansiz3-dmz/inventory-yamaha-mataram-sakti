<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

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

    public function getTerpakaiAttribute()
    {
        return \App\Models\BarangKeluar::where('sparepart_id', $this->sparepart_id)
            ->where('status', 'valid')
            ->sum('jumlah');
    }
}