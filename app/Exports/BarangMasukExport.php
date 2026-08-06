<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarangMasukExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start, $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $query = BarangMasuk::with('sparepart')
            ->where('status', 'valid');

        if ($this->start && $this->end) {
            $query->whereBetween('tanggal', [
                Carbon::parse($this->start)->startOfDay(),
                Carbon::parse($this->end)->endOfDay()
            ]);
        }

        return $query->get();
    }

    // 🔥 HEADER KOLOM
    public function headings(): array
    {
        return [
            'No',
            'Nama Sparepart',
            'Jumlah',
            'Tanggal',
            'Keterangan',
        ];
    }

    // 🔥 FORMAT DATA
    public function map($row): array
    {
        static $no = 1;

        return [
            $no++,
            $row->sparepart->nama_sparepart ?? '-', // 🔥 relasi
            $row->jumlah,
            $row->tanggal,
            $row->keterangan,
        ];
    }
}