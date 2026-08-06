<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\Sparepart;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ========================
        // AUTH
        // ========================
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ========================
        // VALIDASI TANGGAL
        // ========================
        if ($request->start_date && !$request->end_date) {
            return back()->with('error', 'Tanggal akhir wajib diisi');
        }

        if (!$request->start_date && $request->end_date) {
            return back()->with('error', 'Tanggal awal wajib diisi');
        }

        if ($request->start_date && $request->end_date) {

            $startCheck = Carbon::parse($request->start_date);
            $endCheck = Carbon::parse($request->end_date);

            if ($endCheck->gt(Carbon::today())) {
                return back()->with('error', 'Tanggal tidak boleh melebihi hari ini');
            }

            if ($startCheck->gt($endCheck)) {
                return back()->with('error', 'Tanggal tidak valid');
            }

            if ($startCheck->diffInDays($endCheck) > 365) {
                return back()->with('error', 'Maksimal filter 1 tahun');
            }
        }

        // ========================
        // RANGE TANGGAL
        // ========================
        $start = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        if ($end->gt(Carbon::today())) {
            $end = Carbon::today()->endOfDay();
        }

        if ($start->gt($end)) {
            $end = $start->copy()->endOfDay();
        }

        // ========================
        // KPI
        // ========================
        $totalSparepart = Sparepart::count();

        $barangMasukRange = BarangMasuk::where('status', 'valid')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('jumlah');

        $barangKeluarRange = BarangKeluar::where('status', 'valid')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('jumlah');

        // ========================
        // 🔥 GRAFIK OPTIMIZED (NO N+1)
        // ========================
        $masukData = BarangMasuk::selectRaw('DATE(tanggal) as tgl, SUM(jumlah) as total')
            ->where('status', 'valid')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $keluarData = BarangKeluar::selectRaw('DATE(tanggal) as tgl, SUM(jumlah) as total')
            ->where('status', 'valid')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $periode = [];
        $masuk = [];
        $keluar = [];

        $current = $start->copy();

        while ($current <= $end) {
            $tgl = $current->format('Y-m-d');

            $periode[] = $current->format('d M');
            $masuk[] = $masukData[$tgl] ?? 0;
            $keluar[] = $keluarData[$tgl] ?? 0;

            $current->addDay();
        }

        $rangeLabel = $start->format('d M Y') . ' - ' . $end->format('d M Y');

        // ========================
        // DATA TAMBAHAN
        // ========================
        $latestMasuk = BarangMasuk::latest()->take(5)->get();
        $latestKeluar = BarangKeluar::latest()->take(5)->get();

        $topKeluar = BarangKeluar::with('sparepart')
            ->where('status', 'valid')
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw('sparepart_id, SUM(jumlah) as total')
            ->groupBy('sparepart_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $stokKritis = Sparepart::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->paginate(10);

        $jumlahStokKritis = $stokKritis->total();

        $totalStok = Sparepart::sum('stok');

        $todayMasuk = BarangMasuk::whereDate('tanggal', today())->sum('jumlah');
        $todayKeluar = BarangKeluar::whereDate('tanggal', today())->sum('jumlah');

        // ========================
        // ROLE CHECK
        // ========================
        $isOwner = strtolower($user->role ?? '') === 'owner';

        // ========================
        // 🔥 OWNER DATA (ERP)
        // ========================
        $totalNilaiStok = 0;
        $totalRevenue = 0;
        $totalModal = 0;
        $profit = 0;
        $topProfit = collect();

        if ($isOwner) {

            // NILAI STOK
            $totalNilaiStok = Sparepart::sum(DB::raw('stok * harga'));

            // 🔥 REVENUE (dari barang keluar)
            $totalRevenue = BarangKeluar::join('spareparts', 'spareparts.id_sparepart', '=', 'barang_keluar.sparepart_id')
                ->where('barang_keluar.status', 'valid')
                ->whereBetween('barang_keluar.tanggal', [$start, $end])
                ->sum(DB::raw('barang_keluar.jumlah * spareparts.harga'));

            // 🔥 MODAL (dari barang masuk)
            $totalModal = BarangMasuk::join('spareparts', 'spareparts.id_sparepart', '=', 'barang_masuk.sparepart_id')
                ->where('barang_masuk.status', 'valid')
                ->whereBetween('barang_masuk.tanggal', [$start, $end])
                ->sum(DB::raw('barang_masuk.jumlah * spareparts.harga'));

            // 🔥 PROFIT
            $profit = $totalRevenue - $totalModal;

            // 🔥 TOP PROFIT PRODUK
            $topProfit = BarangKeluar::join('spareparts', 'spareparts.id_sparepart', '=', 'barang_keluar.sparepart_id')
                ->selectRaw('barang_keluar.sparepart_id, spareparts.nama_sparepart, SUM(barang_keluar.jumlah * spareparts.harga) as total')
                ->where('barang_keluar.status', 'valid')
                ->groupBy('barang_keluar.sparepart_id', 'spareparts.nama_sparepart')
                ->orderByDesc('total')
                ->take(5)
                ->get();
        }

        // ========================
        // VIEW
        // ========================
        $view = $isOwner ? 'dashboard-owner' : 'dashboard';

        return view($view, compact(
            'totalSparepart',
            'barangMasukRange',
            'barangKeluarRange',
            'periode',
            'masuk',
            'keluar',
            'latestMasuk',
            'latestKeluar',
            'topKeluar',
            'stokKritis',
            'jumlahStokKritis',
            'totalStok',
            'todayMasuk',
            'todayKeluar',
            'rangeLabel',


            // OWNER
            'totalNilaiStok',
            'totalRevenue',
            'totalModal',
            'profit',
            'topProfit',
        ));
    }
}