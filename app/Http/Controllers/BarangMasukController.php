<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Sparepart;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangMasukController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        // ambil status dari URL
        $status = $request->status ?? 'semua';
    
        $query = BarangMasuk::with('sparepart')->latest();
    
        // filter berdasarkan status
        if ($status == 'valid') {
            $query->where('status', 'valid');
        } elseif ($status == 'dibatalkan') {
            $query->where('status', 'dibatalkan');
        }
    
        // pagination + bawa query string
        $data = $query->paginate(10)->withQueryString();
    
        $spareparts = Sparepart::orderBy('nama_sparepart')->get();
    
        return view('barang_masuk.index', compact('data', 'spareparts', 'status'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $data = $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id_sparepart',
            'jumlah'       => 'required|numeric|min:1',
            'tanggal'      => 'required|date',
            'keterangan'   => 'nullable|string'
        ]);

        $selectedDate = Carbon::parse($data['tanggal']);
        $today = Carbon::today();

        $diffDays = $today->diffInDays($selectedDate);

        // LOGIC KETERANGAN
        if ($diffDays == 0) {
            $data['keterangan'] = null;
        } else {
            if (!$request->keterangan) {
                return back()
                    ->withInput()
                    ->with('error', 'Keterangan wajib diisi jika tanggal bukan hari ini');
            }

            $data['keterangan'] = $request->keterangan;
        }

        // FORMAT ANGKA
        $data['jumlah'] = (int) str_replace(['.', ','], '', $request->jumlah);

        DB::beginTransaction();

        try {
            BarangMasuk::create($data);

            $this->stockService->tambahStok(
                $data['sparepart_id'],
                $data['jumlah']
            );

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat menyimpan');
        }

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil ditambahkan');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        DB::beginTransaction();
    
        try {
            $barang = BarangMasuk::findOrFail($id);
    
            // ❌ kalau sudah dibatalkan
            if ($barang->status == 'dibatalkan') {
                return redirect()->route('barang-masuk.index')
                    ->with('error', 'Data sudah dibatalkan sebelumnya');
            }
    
            // ambil stok sekarang
            $sparepart = Sparepart::findOrFail($barang->sparepart_id);
    
            // hitung pemakaian
            $terpakai = $barang->jumlah - $sparepart->stok;
    
            // ❌ jika stok tidak cukup
            if ($sparepart->stok < $barang->jumlah) {
    
                DB::rollBack(); // ❗ penting
    
                return redirect()->route('barang-masuk.index')
                    ->with('error',
                        '❌ Tidak bisa dibatalkan!<br><br>' .
                        'Jumlah masuk : ' . number_format($barang->jumlah,0,',','.') . '<br>' .
                        'Stok sekarang : ' . number_format($sparepart->stok,0,',','.') . '<br>' .
                        'Sudah terpakai : ' . number_format($terpakai,0,',','.')
                    );
            }
    
            // ✅ kurangi stok
            $this->stockService->kurangiStok(
                $barang->sparepart_id,
                $barang->jumlah
            );
    
            // update status
            $barang->update([
                'status' => 'dibatalkan'
            ]);
    
            DB::commit();
    
            return redirect()->route('barang-masuk.index')
                ->with('success', 'Data berhasil dibatalkan')
                ->with('type', 'batalkan');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->route('barang-masuk.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}