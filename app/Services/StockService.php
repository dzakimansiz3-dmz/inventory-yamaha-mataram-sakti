<?php

namespace App\Services;

use App\Models\Sparepart;
use Exception;

class StockService
{
    public function tambahStok($sparepart_id, $jumlah)
    {
        $sparepart = Sparepart::findOrFail($sparepart_id);

        $sparepart->increment('stok', $jumlah);
    }

    public function kurangiStok($sparepart_id, $jumlah)
    {
        $sparepart = Sparepart::findOrFail($sparepart_id);

        if ($sparepart->stok < $jumlah) {
            throw new Exception("Stok tidak mencukupi");
        }

        $sparepart->decrement('stok', $jumlah);
    }

    public function bisaKurangi($sparepart_id, $jumlah)
    {
        $sparepart = Sparepart::find($sparepart_id);
        return $sparepart && $sparepart->stok >= $jumlah;
    }
}