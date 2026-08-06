<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GabunganExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Tanggal awal filter.
     */
    protected ?string $start;

    /**
     * Tanggal akhir filter.
     */
    protected ?string $end;

    /**
     * Constructor untuk menerima tanggal awal dan tanggal akhir.
     */
    public function __construct(?string $start = null, ?string $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Mengambil dan menggabungkan data barang masuk
     * serta barang keluar.
     */
    public function collection(): Collection
    {
        $rows = collect();

        /*
        |--------------------------------------------------------------------------
        | Query Barang Masuk
        |--------------------------------------------------------------------------
        */

        $barangMasukQuery = BarangMasuk::with('sparepart')
            ->where('status', 'valid');

        /*
        |--------------------------------------------------------------------------
        | Query Barang Keluar
        |--------------------------------------------------------------------------
        */

        $barangKeluarQuery = BarangKeluar::with('sparepart')
            ->where('status', 'valid');

        /*
        |--------------------------------------------------------------------------
        | Filter berdasarkan tanggal
        |--------------------------------------------------------------------------
        */

        if (!empty($this->start) && !empty($this->end)) {
            $barangMasukQuery->whereBetween('tanggal', [
                $this->start,
                $this->end,
            ]);

            $barangKeluarQuery->whereBetween('tanggal', [
                $this->start,
                $this->end,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Memasukkan data Barang Masuk
        |--------------------------------------------------------------------------
        */

        foreach ($barangMasukQuery->get() as $item) {
            $rows->push([
                'kode_part' => $item->sparepart?->kode_part ?? '-',
                'nama_sparepart' => $item->sparepart?->nama_sparepart ?? '-',
                'tipe' => 'Masuk',
                'jumlah' => (int) $item->jumlah,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => !empty($item->keterangan)
                    ? $item->keterangan
                    : '-',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Memasukkan data Barang Keluar
        |--------------------------------------------------------------------------
        */

        foreach ($barangKeluarQuery->get() as $item) {
            $rows->push([
                'kode_part' => $item->sparepart?->kode_part ?? '-',
                'nama_sparepart' => $item->sparepart?->nama_sparepart ?? '-',
                'tipe' => 'Keluar',
                'jumlah' => (int) $item->jumlah,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => !empty($item->keterangan)
                    ? $item->keterangan
                    : '-',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Mengurutkan berdasarkan tanggal
        |--------------------------------------------------------------------------
        */

        return $rows
            ->sortBy('tanggal')
            ->values();
    }

    /**
     * Judul kolom pada file CSV.
     */
    public function headings(): array
    {
        return [
            'Kode Part',
            'Nama Sparepart',
            'Tipe',
            'Jumlah',
            'Tanggal',
            'Keterangan',
        ];
    }

    /**
     * Menentukan urutan data pada setiap baris CSV.
     */
    public function map($row): array
    {
        return [
            $row['kode_part'],
            $row['nama_sparepart'],
            $row['tipe'],
            $row['jumlah'],
            $row['tanggal'],
            $row['keterangan'],
        ];
    }
}