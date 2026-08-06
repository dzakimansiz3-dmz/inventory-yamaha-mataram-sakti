@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ALERT ERROR --}}
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>👑 Owner Dashboard</h4>
    </div>

    {{-- EXPORT --}}
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

    {{-- KPI --}}
    <div class="row mt-3">

        {{-- PROFIT --}}
        <div class="col-md-3">
            <div class="card {{ $profit >= 0 ? 'bg-success' : 'bg-danger' }} text-white p-3">
                <h6>💰 Profit</h6>
                <h3>Rp {{ number_format($profit, 0, ',', '.') }}</h3>
                <small>Keuntungan periode terpilih</small>
            </div>
        </div>

        {{-- NILAI STOK --}}
        <div class="col-md-3">
            <div class="card bg-info text-white p-3">
                <h6>Total Nilai Stok</h6>
                <h3>Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</h3>
                <small>Total asset gudang</small>
            </div>
        </div>

        {{-- MASUK --}}
        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                <h6>Total Barang Masuk</h6>
                <h3>{{ number_format($barangMasukRange) }}</h3>
                <small>Total stok masuk periode terpilih</small>
            </div>
        </div>

        {{-- KELUAR --}}
        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">
                <h6>Total Barang Keluar</h6>
                <h3>{{ number_format($barangKeluarRange) }}</h3>
                <small>Total stok keluar periode terpilih</small>
            </div>
        </div>

    </div>

    {{-- INSIGHT --}}
    <div class="row mt-3">

        <div class="col-md-3">
            <div class="card bg-info text-white p-3">
                <h6>📈 Trend</h6>
                <h4>
                    {{ array_sum($masuk) > array_sum($keluar) ? '📈 Naik' : '📉 Turun' }}
                </h4>
                <small>Arah pergerakan stok</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white p-3">
                <h6 class="text-white">
                    <i class="bi bi-arrow-left-right"></i> Net Flow
                </h6>
                <h4 class="text-white">
                    {{ number_format($barangMasukRange - $barangKeluarRange) }}
                </h4>
                <small class="text-white">
                    Selisih barang masuk dan keluar
                </small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">
                <h6>⚠️ Risk</h6>
                <h4>
                    {{ $barangKeluarRange > $barangMasukRange ? 'High' : 'Safe' }}
                </h4>
                <small>Indikator risiko kekurangan stok</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-secondary text-white p-3">
                <h6>⚠️ Stok Kritis</h6>

                <h4>
                    {{ number_format($jumlahStokKritis) }}
                </h4>

                <small>
                    Sparepart di bawah batas minimum
                </small>
            </div>
        </div>

    </div>

    {{-- FILTER --}}
    <form id="filterForm" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ request('start_date') }}"
                    max="{{ now()->toDateString() }}"
                    class="form-control"
                    onclick="this.showPicker()"
                    onfocus="this.showPicker()">
            </div>

            <div class="col-md-3">
                <input type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ request('end_date') }}"
                    max="{{ now()->toDateString() }}"
                    class="form-control"
                    onclick="this.showPicker()"
                    onfocus="this.showPicker()">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <h5>Periode: {{ $rangeLabel }}</h5>

    {{-- CHART --}}
    <div class="card mt-3">
        <div class="card-header">
            <h3>Grafik Barang Masuk & Keluar</h3>
        </div>
        <div class="card-body" style="height:400px">
            <canvas id="chart"></canvas>
        </div>

        {{-- EMPTY STATE --}}
        @if(array_sum($masuk) == 0 && array_sum($keluar) == 0)
        <div class="text-center text-muted mb-3">
            Belum ada aktivitas pada periode ini
        </div>
        @endif
    </div>

    {{-- TOP PRODUK --}}
    <div class="card mt-3">
        <div class="card-header bg-dark text-white">
            Top Produk Keluar
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sparepart</th>
                        <th>Total Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topKeluar as $item)
                    <tr>
                        <td>{{ $item->sparepart->nama_sparepart ?? '-' }}</td>
                        <td>{{ number_format($item->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">
                            Tidak ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

{{-- CHART --}}
<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: @json($periode),
        datasets: [
            {
                label: 'Barang Masuk',
                data: @json($masuk),
                borderColor: '#28a745',
                fill: true
            },
            {
                label: 'Barang Keluar',
                data: @json($keluar),
                borderColor: '#dc3545',
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true },
            tooltip: { enabled: true }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    form.addEventListener('submit', function (e) {

        const start = startInput.value;
        const end = endInput.value;

        // Tidak memilih tanggal
        if (!start && !end) {
            e.preventDefault();

            Swal.fire({
                icon: 'info',
                title: 'Silahkan pilih tanggal'
            });

            return;
        }

        // Hanya tanggal awal
        if (start && !end) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Tanggal akhir wajib diisi'
            });

            startInput.value = '';
            endInput.value = '';

            return;
        }

        // Hanya tanggal akhir
        if (!start && end) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Tanggal awal wajib diisi'
            });

            startInput.value = '';
            endInput.value = '';

            return;
        }

        let startDate = new Date(start);
        let endDate = new Date(end);
        let today = new Date();

        // Tanggal akhir melebihi hari ini
        if (endDate > today) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Tanggal akhir tidak valid'
            });

            endInput.value = '';
            return;
        }

        // Tanggal awal lebih besar dari tanggal akhir
        if (startDate > endDate) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Tanggal tidak valid'
            });

            startInput.value = '';
            endInput.value = '';
            return;
        }

        // Maksimal 1 tahun
        let diffTime = endDate - startDate;
        let diffDays = diffTime / (1000 * 60 * 60 * 24);

        if (diffDays > 365) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Maksimal filter 1 tahun'
            });

            startInput.value = '';
            endInput.value = '';
            return;
        }

    });

});

</script>

@endsection