<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportDataSeeder extends Seeder
{
    public function run(): void
    {
        // Import spareparts
        $spareparts = DB::connection('sqlite_old')
            ->table('spareparts')
            ->get();

        foreach ($spareparts as $item) {
            DB::table('spareparts')->insert([
                'id_sparepart' => $item->id_sparepart,
                'nama_sparepart' => $item->nama_sparepart,
                'kode_part' => $item->kode_part,
                'kategori' => $item->kategori,
                'harga' => $item->harga,
                'stok' => $item->stok,
                'stok_minimum' => $item->stok_minimum,
                'keterangan' => $item->keterangan,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        // Import barang masuk
        $barangMasuk = DB::connection('sqlite_old')
            ->table('barang_masuk')
            ->get();

        foreach ($barangMasuk as $item) {
            DB::table('barang_masuk')->insert([
                'id' => $item->id,
                'sparepart_id' => $item->sparepart_id,
                'jumlah' => $item->jumlah,
                'tanggal' => $item->tanggal,
                'keterangan' => $item->keterangan,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        // Import barang keluar
        $barangKeluar = DB::connection('sqlite_old')
            ->table('barang_keluar')
            ->get();

        foreach ($barangKeluar as $item) {
            DB::table('barang_keluar')->insert([
                'id' => $item->id,
                'sparepart_id' => $item->sparepart_id,
                'jumlah' => $item->jumlah,
                'tanggal' => $item->tanggal,
                'keterangan' => $item->keterangan,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        // Import sparepart logs
        $logs = DB::connection('sqlite_old')
            ->table('sparepart_logs')
            ->get();

        foreach ($logs as $item) {
            DB::table('sparepart_logs')->insert([
                'id' => $item->id,
                'nama_sparepart' => $item->nama_sparepart,
                'kode_part' => $item->kode_part,
                'kategori' => $item->kategori,
                'harga' => $item->harga,
                'stok' => $item->stok,
                'tipe' => $item->tipe,
                'old_nama_sparepart' => $item->old_nama_sparepart,
                'old_kode_part' => $item->old_kode_part,
                'old_kategori' => $item->old_kategori,
                'old_harga' => $item->old_harga,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }
    }
}