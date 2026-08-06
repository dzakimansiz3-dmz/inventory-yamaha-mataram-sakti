@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Edit Sparepart</h3>

    {{-- =========================
        NOTIF (TENGAH)
    ========================= --}}
    @if(session('success'))
        <div id="centerAlert" class="center-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="centerAlert" class="center-alert error">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('sparepart.update', $sparepart->id_sparepart) }}">
        @csrf
        @method('PUT')

        {{-- NAMA (LOCK) --}}
        <div class="form-group mb-3">
            <label class="font-weight-bold">Nama Sparepart</label>
            <input type="text" value="{{ $sparepart->nama_sparepart }}" class="form-control bg-light" readonly>
            <small class="text-muted">Tidak dapat diubah</small>
        </div>

        {{-- KODE (LOCK) --}}
        <div class="form-group mb-3">
            <label class="font-weight-bold">Kode Part</label>
            <input type="text" value="{{ $sparepart->kode_part }}" class="form-control bg-light" readonly>
            <small class="text-muted">Tidak dapat diubah</small>
        </div>

        {{-- KATEGORI (LOCK) --}}
        <div class="form-group mb-3">
            <label class="font-weight-bold">Kategori</label>
            <input type="text" value="{{ $sparepart->kategori }}" class="form-control bg-light" readonly>
            <small class="text-muted">Tidak dapat diubah</small>
        </div>

        {{-- HARGA --}}
        <div class="form-group">
            <label>Harga</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                </div>
                <input type="text" name="harga" id="harga"
                    value="{{ number_format($sparepart->harga,0,',','.') }}"
                    class="form-control" required>
            </div>
        </div>

        {{-- STOK --}}
        <div class="form-group">
            <label>Stok</label>
            <input type="text" name="stok" id="stok" 
                   value="{{ isset($sparepart) ? number_format($sparepart->stok,0,',','.') : '' }}" 
                   class="form-control" required>
        </div>

        <button class="btn btn-primary mt-2">Update</button>
        <a href="{{ route('sparepart.index') }}" class="btn btn-secondary mt-2">Kembali</a>

    </form>

</div>

<script>
    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString();
        let split = number_string.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
    
        return rupiah;
    }
    
    document.querySelectorAll('#harga, #stok').forEach(function(el) {
        el.addEventListener('keyup', function() {
            this.value = formatRupiah(this.value);
        });
    });
    </script>

{{-- =========================
    STYLE POPUP
========================= --}}
<style>
.center-alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    padding: 20px 30px;
    border-radius: 10px;
    font-size: 16px;
    z-index: 9999;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.center-alert.success {
    background: #28a745;
}

.center-alert.error {
    background: #dc3545;
}
</style>

{{-- =========================
    AUTO HILANGKAN NOTIF
========================= --}}
<script>
setTimeout(function () {
    let alertBox = document.getElementById('centerAlert');
    if (alertBox) {
        alertBox.style.transition = "0.5s";
        alertBox.style.opacity = "0";
        setTimeout(() => alertBox.remove(), 500);
    }
}, 2000);
</script>

@endsection