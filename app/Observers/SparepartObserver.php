<?php

namespace App\Observers;

use App\Models\Sparepart;
use App\Models\SparepartLog;

class SparepartObserver
{
    /**
     * CREATE
     */
    public function created(Sparepart $sparepart)
    {
        SparepartLog::create([
            'nama_sparepart' => $sparepart->nama_sparepart,
            'kode_part'      => $sparepart->kode_part,
            'kategori'       => $sparepart->kategori,
            'harga'          => $sparepart->harga,
            'stok'           => $sparepart->stok,
            'tipe'           => 'created',
        ]);
    }

    /**
     * UPDATE
     */
    public function updated(Sparepart $sparepart)
    {
        // Jika status berubah (aktif / nonaktif)
        if ($sparepart->wasChanged('status')) {

            SparepartLog::create([
                'nama_sparepart' => $sparepart->nama_sparepart,
                'kode_part'      => $sparepart->kode_part,
                'kategori'       => $sparepart->kategori,
                'harga'          => $sparepart->harga,
                'stok'           => $sparepart->stok,
                'tipe'           => $sparepart->status === 'aktif'
                    ? 'aktif'
                    : 'nonaktif',
            ]);

            return;
        }

        // Update data sparepart
        SparepartLog::create([
            'nama_sparepart'     => $sparepart->nama_sparepart,
            'kode_part'          => $sparepart->kode_part,
            'kategori'           => $sparepart->kategori,
            'harga'              => $sparepart->harga,
            'stok'               => $sparepart->stok,

            // Data lama
            'old_nama_sparepart' => $sparepart->getOriginal('nama_sparepart'),
            'old_kode_part'      => $sparepart->getOriginal('kode_part'),
            'old_kategori'       => $sparepart->getOriginal('kategori'),
            'old_harga'          => $sparepart->getOriginal('harga'),

            'tipe'               => 'update',
        ]);
    }

    /**
     * DELETE
     * Tidak dipakai karena sistem Anda menggunakan
     * status aktif/nonaktif, bukan delete database.
     */
    public function deleted(Sparepart $sparepart)
    {
        //
    }
}