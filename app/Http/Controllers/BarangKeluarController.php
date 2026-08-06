<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Sparepart;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    // =========================
    // INDEX
    // =========================
    public function index(Request $request)
    {
        $status = $request->status ?? 'semua';
    
        $query = BarangKeluar::with('sparepart')->latest();
    
        // FILTER STATUS
        if ($status == 'valid') {
            $query->where('status', 'valid');
        } elseif ($status == 'dibatalkan') {
            $query->where('status', 'dibatalkan');
        }
    
        $data = $query->paginate(10)->appends([
            'status' => $status
        ]);
    
        $spareparts = Sparepart::orderBy('nama_sparepart')->get();
    
        return view('barang_keluar.index', compact('data', 'spareparts', 'status'));
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

        $data['status'] = 'valid';
    
        $selectedDate = Carbon::parse($data['tanggal']);
        $today = Carbon::today();
    
        $diffDays = $today->diffInDays($selectedDate);

        // logic keterangan
        if ($diffDays == 0) {
            $data['keterangan'] = null;
        } else {
            if (!$request->keterangan) {
                return back()
                    ->withInput()
                    ->with('error', 'Keterangan wajib diisi jika tanggal bukan hari ini')
                    ->with('open_modal', true);
            }
            $data['keterangan'] = $request->keterangan;
        }
    
        // FORMAT ANGKA
        $data['jumlah'] = (int) str_replace(['.', ','], '', $request->jumlah);
        
        $sparepart = Sparepart::findOrFail($data['sparepart_id']);

        if ($data['jumlah'] > $sparepart->stok) {

            return redirect()
                ->route('barang-keluar.index')
                ->with('error',
                    '❌ Stok tidak mencukupi<br><br>' .
                    'Stok tersedia : ' . number_format($sparepart->stok,0,',','.') . '<br>' .
                    'Permintaan : ' . number_format($data['jumlah'],0,',','.')
                );
}

        DB::beginTransaction();
    
        try {
            BarangKeluar::create($data);
            $this->stockService->kurangiStok(
                $data['sparepart_id'],
                $data['jumlah']
            );
    
            DB::commit();
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan')
                ->with('open_modal', true);
        }
    
        return redirect()->route('barang-keluar.index')
            ->with('success', 'Barang keluar berhasil ditambahkan');
    }

    // =========================
    // DELETE
    // =========================
    public function destroy($id)
    {
        DB::beginTransaction();
    
        try {
            $barang = BarangKeluar::findOrFail($id);
    
            // 🚫 CEK: jika sudah dibatalkan
            if ($barang->status === 'dibatalkan') {
                return back()->with('error', 'Data sudah dibatalkan sebelumnya');
            }
    
            // 🔄 Balikin stok
            $this->stockService->tambahStok(
                $barang->sparepart_id,
                $barang->jumlah
            );
    
            // ✅ Ubah status saja (tidak delete)
            $barang->update([
                'status' => 'dibatalkan'
            ]);
    
            DB::commit();
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return back()->with('error', 'Gagal membatalkan data');
        }
    
        return redirect()->route('barang-keluar.index')
            ->with('success', 'Data berhasil dibatalkan')
            ->with('type', 'batalkan');
    }
}