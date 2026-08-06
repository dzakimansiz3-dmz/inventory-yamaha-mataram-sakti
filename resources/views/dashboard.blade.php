@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <!-- ✅ EXPORT BUTTON -->
    <div class="mb-3">
        <div class="btn-group">
            <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                Export Data
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item"
                   href="{{ route('export.data', ['type'=>'masuk','start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}">
                    Barang Masuk
                </a>

                <a class="dropdown-item"
                   href="{{ route('export.data', ['type'=>'keluar','start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}">
                    Barang Keluar
                </a>

                <a class="dropdown-item"
                   href="{{ route('export.data', ['type'=>'gabungan','start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}">
                    Gabungan
                </a>
            </div>
        </div>
    </div>

    <!-- KPI -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card modern-card bg-gradient-info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Sparepart</h6>
                        <h2 class="fw-bold text-white">{{ $totalSparepart }}</h2>
                    </div>
                    <i class="fas fa-cogs fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card modern-card bg-gradient-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Stok</h6>
                        <h2 class="fw-bold text-white">{{ number_format($totalStok) }}</h2>
                    </div>
                    <i class="fas fa-boxes fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card modern-card bg-gradient-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Masuk Hari Ini</h6>
                        <h2 class="fw-bold text-white">{{ number_format($todayMasuk) }}</h2>
                    </div>
                    <i class="fas fa-arrow-down fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card modern-card bg-gradient-danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Keluar Hari Ini</h6>
                        <h2 class="fw-bold text-white">{{ number_format($todayKeluar) }}</h2>
                    </div>
                    <i class="fas fa-arrow-up fa-2x text-white-50"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <form id="filterForm" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" id="start_date" name="start_date"
                    value="{{ request('start_date') }}"
                    max="{{ now()->toDateString() }}"                    
                    class="form-control"
                    onclick="this.showPicker()" 
                    onfocus="this.showPicker()">
            </div>
            <div class="col-md-3">
                <input type="date" id="end_date" name="end_date"
                    value="{{ request('end_date') }}"
                    max="{{ now()->toDateString() }}"
                    class="form-control"
                    onclick="this.showPicker()" 
                    onfocus="this.showPicker()">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <h5>Periode: {{ $rangeLabel }}</h5>

    <!-- GRAFIK -->
    <div class="card collapsed-card mt-3">
        <div class="card-header">
            <h3 class="card-title">Grafik Barang Masuk & Keluar</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="height:500px">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <!-- STOK KRITIS -->
    <div class="card mt-3">
        <div class="card-header bg-danger text-white">Stok Kritis</div>
        <div class="card-body">
    
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle stok-table">
                    <thead class="table-danger text-center">
                        <tr>
                            <th class="col-no">No</th>
                            <th>Nama Sparepart</th>
                            <th class="col-no">Stok</th>
                        </tr>
                    </thead>
            
                    <tbody>
                    @forelse($stokKritis as $key => $item)
                    <tr>
                        <td class="text-center">
                            {{ $stokKritis->firstItem() + $key }}
                        </td>
                        <td class="nama">
                            {{ $item->nama_sparepart }}
                        </td>
                        <td class="text-center fw-bold text-danger">
                            {{ number_format($item->stok) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Tidak ada stok kritis
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
    
            <div class="d-flex justify-content-center mt-3">
                {{ $stokKritis->links() }}
            </div>
    
        </div>
    </div>

</div>

{{-- =========================
    SCRIPT
========================= --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    let chart;
    let topChart;

    // =========================
    // CHART BARANG MASUK & KELUAR
    // =========================

    const chartElement = document.getElementById('chart');

    if (chartElement) {

        chart = new Chart(chartElement, {
            type: 'line',

            data: {
                labels: @json($periode ?? []),

                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: @json($masuk ?? []),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        tension: 0.3
                    },

                    {
                        label: 'Barang Keluar',
                        data: @json($keluar ?? []),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

    }



    // =========================
    // TOP BARANG KELUAR
    // =========================

    const topChartElement = document.getElementById('topChart');


    if (topChartElement) {

        topChart = new Chart(topChartElement, {

            type: 'bar',

            data: {

                labels: @json(
                    $topKeluar->pluck('sparepart.nama_sparepart')->toArray() ?? []
                ),

                datasets: [
                    {
                        label: 'Jumlah Keluar',

                        data: @json(
                            $topKeluar->pluck('total')->toArray() ?? []
                        ),

                        backgroundColor: 'rgba(255,99,132,0.6)'
                    }
                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    }

                }

            }

        });

    }


});
</script>

{{-- =========================
    STYLE (TETAP ADA)
========================= --}}
<style>
.small-box {
    border-radius: 10px;
    transition: 0.3s;
}
.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.small-box .icon {
    font-size: 40px;
    opacity: 0.3;
}
</style>

<style>
.modern-card {
    border-radius: 15px;
    border: none;
    transition: all 0.3s ease;
}
.modern-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}
.fw-bold {
    font-weight: bold;
}
.g-3 > [class*="col-"] {
    margin-bottom: 15px;
}
</style>

<style>
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);

    .stok-table {
    font-size: 15px; /* 🔥 lebih besar biar kebaca */
    min-width: 500px;
}

/* Kolom kecil */
.col-no {
    width: 60px;
    text-align: center;
}

/* Kolom nama */
.stok-table .nama {
    font-weight: 500;
    font-size: 15px;
    white-space: normal; /* 🔥 biar bisa turun baris */
    line-height: 1.5;
}

/* Hover effect biar interaktif */
.stok-table tbody tr:hover {
    background-color: #fff5f5;
}

/* Padding biar lega */
.stok-table td,
.stok-table th {
    padding: 10px 12px;
}

.table {
    width: auto !important;
}    
}
</style>



@endsection