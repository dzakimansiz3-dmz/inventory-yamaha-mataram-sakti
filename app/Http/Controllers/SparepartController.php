<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sparepart;
use App\Models\SparepartLog; 
use App\Services\SparepartService;

class SparepartController extends Controller
{
    protected $service;

    public function __construct(SparepartService $service)
    {
        $this->service = $service;
    }

    // =========================
    // INDEX
    // =========================
    public function index(Request $request)
    {
    $status = $request->status ?? 'semua';

    $query = Sparepart::query();

    // FILTER STATUS
    if ($status == 'aktif') {
        $query->where('status', 'aktif');
    } elseif ($status == 'nonaktif') {
        $query->where('status', 'nonaktif');
    }

    $spareparts = $query->orderBy('nama_sparepart', 'asc')
                        ->paginate(10)
                        ->withQueryString();

    return view('sparepart.index', compact(
        'spareparts',
        'status'
    ));

    }


    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_sparepart' => 'required|unique:spareparts,nama_sparepart',
            'kode_part' => 'required|unique:spareparts,kode_part|regex:/^[A-Z]{3}-[0-9]{3}$/',
            'kategori' => 'required',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama_sparepart.unique' => 'Nama sparepart sudah ada',
            'kode_part.regex' => 'Format kode harus seperti OLI-001',
            'kode_part.unique' => 'Kode part sudah digunakan',
        ]);

        // FORMAT ANGKA
        $data['harga'] = preg_replace('/\D/', '', $data['harga']);

        // DEFAULT
        $data['stok'] = 0;
        $data['stok_minimum'] = 0;
        $data['status'] = 'aktif';
        // SIMPAN
        $sparepart = $this->service->create($data);

        return redirect()->route('sparepart.index')->with([
            'message' => 'Sparepart baru berhasil ditambahkan',
            'type' => 'success'
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);
    
        $data = $request->validate([
            'nama_sparepart' => 'required|unique:spareparts,nama_sparepart,' . $id . ',id_sparepart',
            'kode_part' => 'required|regex:/^[A-Z]{3}-[0-9]{3}$/|unique:spareparts,kode_part,' . $id . ',id_sparepart',
            'kategori' => 'required',
            'harga' => 'required|numeric|min:0',
        ]);
    
        // 🔥 SIMPAN DATA LAMA
        $old = $sparepart->replicate();
    
        // FORMAT
        $data['harga'] = preg_replace('/\D/', '', $data['harga']);
    
        // UPDATE
        $this->service->update($id, $data);
    
    
        return redirect()->route('sparepart.index')->with([
            'message' => 'Data berhasil diupdate',
            'type' => 'success'
        ]);
    }

    // =========================
    // AKTIFKAN
    // =========================
    public function activate($id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $sparepart->update([
            'status' => 'aktif'
        ]);

        return redirect()->route('sparepart.index')->with([
            'message' => 'Sparepart berhasil diaktifkan kembali',
            'type' => 'success'
        ]);
    }

    // =========================
    // NONAKTIFKAN
    // =========================
    public function destroy(Sparepart $sparepart)
    {
        $sparepart->update([
            'status' => 'nonaktif'
        ]);

        return redirect()->route('sparepart.index')->with([
            'message' => 'Sparepart berhasil dinonaktifkan',
            'type' => 'error' // 🔴 merah
        ]);
    }

    // =========================
    // RIWAYAT
    // =========================
    public function riwayat(Request $request)
    {
        $status = $request->status ?? 'semua';
    
        $query = SparepartLog::query();
    
        if ($status != 'semua') {
            $query->where('tipe', $status);
        }
    
        $logs = $query->latest()
                      ->paginate(10)
                      ->withQueryString();
    
        return view('sparepart.riwayat', compact('logs', 'status'));
    }
}