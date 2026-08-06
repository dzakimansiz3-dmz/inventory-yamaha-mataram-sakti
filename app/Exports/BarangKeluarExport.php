<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarangKeluarExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start, $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $query = BarangKeluar::with('sparepart')
            ->where('status', 'valid');

        if ($this->start && $this->end) {
            $query->whereBetween('tanggal', [
                Carbon::parse($this->start)->startOfDay(),
                Carbon::parse($this->end)->endOfDay()
            ]);
        }

        return $query->get();
    }

    // HEADER
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

    // DATA
    public function map($row): array
    {
        static $no = 1;

        return [
            $no++,
            $row->sparepart->nama_sparepart ?? '-',
            $row->jumlah,
            $row->tanggal,
            $row->keterangan,
        ];
    }
}