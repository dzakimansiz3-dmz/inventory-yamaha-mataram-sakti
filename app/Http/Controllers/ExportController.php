<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Exports\BarangMasukExport;
use App\Exports\BarangKeluarExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar,gabungan',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $type = $request->input('type');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if ($type === 'masuk') {
            return Excel::download(
                new BarangMasukExport($start, $end),
                'barang_masuk.xlsx'
            );
        }

        if ($type === 'keluar') {
            return Excel::download(
                new BarangKeluarExport($start, $end),
                'barang_keluar.xlsx'
            );
        }

        if ($type === 'gabungan') {
            return $this->exportGabunganCsv($start, $end);
        }

        return redirect()
            ->back()
            ->with('error', 'Tipe export tidak valid.');
    }

    private function exportGabunganCsv(
        ?string $start = null,
        ?string $end = null
    ) {
        $barangMasukQuery = BarangMasuk::with('sparepart')
            ->where('status', 'valid');

        $barangKeluarQuery = BarangKeluar::with('sparepart')
            ->where('status', 'valid');

        if (!empty($start)) {
            $barangMasukQuery->whereDate('tanggal', '>=', $start);
            $barangKeluarQuery->whereDate('tanggal', '>=', $start);
        }

        if (!empty($end)) {
            $barangMasukQuery->whereDate('tanggal', '<=', $end);
            $barangKeluarQuery->whereDate('tanggal', '<=', $end);
        }

        $rows = collect();

        foreach ($barangMasukQuery->get() as $item) {
            $rows->push([
                'kode_part' => $item->sparepart?->kode_part ?? '-',
                'nama_sparepart' => $item->sparepart?->nama_sparepart ?? '-',
                'tipe' => 'Masuk',
                'jumlah' => $item->jumlah,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => $item->keterangan ?? '-',
            ]);
        }

        foreach ($barangKeluarQuery->get() as $item) {
            $rows->push([
                'kode_part' => $item->sparepart?->kode_part ?? '-',
                'nama_sparepart' => $item->sparepart?->nama_sparepart ?? '-',
                'tipe' => 'Keluar',
                'jumlah' => $item->jumlah,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => $item->keterangan ?? '-',
            ]);
        }

        $rows = $rows
            ->sortBy('tanggal')
            ->values();

        return response()->streamDownload(
            function () use ($rows) {
                $file = fopen('php://output', 'w');

                /*
                 * BOM UTF-8 agar teks terbaca dengan baik di Excel.
                 */
                fwrite($file, "\xEF\xBB\xBF");

                /*
                 * Header CSV ditulis langsung.
                 * Dijamin huruf kecil dan menggunakan underscore.
                 */
                fputcsv(
                    $file,
                    [
                        'kode_part',
                        'nama_sparepart',
                        'tipe',
                        'jumlah',
                        'tanggal',
                        'keterangan',
                    ],
                    ',',
                    '"',
                    '\\'
                );

                foreach ($rows as $row) {
                    fputcsv(
                        $file,
                        [
                            $row['kode_part'],
                            $row['nama_sparepart'],
                            $row['tipe'],
                            $row['jumlah'],
                            $row['tanggal'],
                            $row['keterangan'],
                        ],
                        ',',
                        '"',
                        '\\'
                    );
                }

                fclose($file);
            },
            'data_transaksi_inventory.csv',
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache',
            ]
        );
    }
}