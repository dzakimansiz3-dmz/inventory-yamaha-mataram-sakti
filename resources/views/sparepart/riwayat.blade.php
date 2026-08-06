@extends('layouts.app')

@section('content')

<div class="container">

    {{-- =========================
        JUDUL DI ATAS
    ========================= --}}
    <div class="mb-2">
        <h3 class="mb-0">Riwayat Sparepart</h3>
    </div>

    {{-- =========================
        BARIS CONTROL (KEMBALI + STATUS)    
    ========================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        {{-- KANAN: STATUS --}}
        <div class="d-flex align-items-center ml-auto">
            <span class="font-weight-bold mr-2">Status:</span>

            <form method="GET" action="{{ route('sparepart.riwayat') }}" class="mb-0">

                <select name="status"
                        class="form-control form-control-sm font-weight-bold"
                        onchange="this.form.submit()"
                        style="width: 160px;">
                
                    <option value="semua" {{ ($status ?? 'semua') == 'semua' ? 'selected' : '' }}>
                        Semua
                    </option>
                
                    <option value="created" {{ ($status ?? '') == 'created' ? 'selected' : '' }}>
                        Created
                    </option>

                    <option value="update" {{ ($status ?? '') == 'update' ? 'selected' : '' }}>
                        Update
                    </option>

                    <option value="aktif" {{ ($status ?? '') == 'aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>
                
                    <option value="nonaktif" {{ ($status ?? '') == 'nonaktif' ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                

                
                </select>

            </form>

        </div>

    </div>

    {{-- TABLE --}}
    <table class="table table-bordered table-striped">
        <thead class="bg-dark text-white text-center">
            <tr>
                <th>Waktu</th>
                <th>Nama Sparepart</th>
                <th>Kode Part</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        @forelse($logs as $log)
        <tr>
            <td class="text-center">{{ $log->created_at }}</td>
            
            {{-- NAMA --}}
            <td>
                {{ $log->nama_sparepart }}
        
                @if($log->old_nama_sparepart)
                    <div class="text-muted small">
                        {{ $log->old_nama_sparepart }}
                    </div>
                @endif
            </td>
        
            {{-- KODE --}}
            <td class="text-center">
                {{ $log->kode_part }}
        
                @if($log->old_kode_part)
                    <div class="text-muted small">
                        {{ $log->old_kode_part }}
                    </div>
                @endif
            </td>
        
            {{-- KATEGORI --}}
            <td class="text-center">
                {{ $log->kategori }}
        
                @if($log->old_kategori)
                    <div class="text-muted small">
                        {{ $log->old_kategori }}
                    </div>
                @endif
            </td>
        
            {{-- HARGA --}}
            <td>
                Rp {{ number_format($log->harga, 0, ',', '.') }}
        
                @if($log->old_harga)
                    <div class="text-muted small">
                        Rp {{ number_format($log->old_harga, 0, ',', '.') }}
                    </div>
                @endif
            </td>
            
            <td class="text-center">{{ $log->stok }}</td>
        
            <td class="text-center">
                <span class="badge 
                    @if($log->tipe == 'created') bg-success
                    @elseif($log->tipe == 'aktif') bg-primary
                    @elseif($log->tipe == 'update') bg-warning
                    @elseif($log->tipe == 'nonaktif') bg-danger
                    @endif">
                    {{ ucfirst($log->tipe) }}
                </span>
            </td>
            
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted">
                Belum ada riwayat
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links() }}
    </div>

</div>

@endsection