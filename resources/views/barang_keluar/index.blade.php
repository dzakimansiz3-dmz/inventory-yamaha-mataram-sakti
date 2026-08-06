@extends('layouts.app')

@section('content')

<div class="container">

    {{-- JUDUL --}}
    <h3 class="mb-2">Barang Keluar</h3>

    {{-- BARIS: BUTTON KIRI + STATUS KANAN --}}
    <div class="d-flex align-items-center mb-3">

        {{-- BUTTON --}}
        @if(auth()->user()->role == 'admin')
        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalTambah">
            + Barang Keluar
        </button>
        @endif

        {{-- STATUS (KANAN) --}}
        <div class="d-flex align-items-center ml-auto">
            <span class="font-weight-bold mr-2">Status:</span>

            <form method="GET" action="{{ route('barang-keluar.index') }}" class="mb-0">
                <select name="status"
                        class="form-control form-control-sm"
                        onchange="this.form.submit()"
                        style="width: 140px;">

                    <option value="semua" {{ ($status ?? 'semua') == 'semua' ? 'selected' : '' }}>
                        Semua
                    </option>

                    <option value="valid" {{ ($status ?? '') == 'valid' ? 'selected' : '' }}>
                        Valid
                    </option>

                    <option value="dibatalkan" {{ ($status ?? '') == 'dibatalkan' ? 'selected' : '' }}>
                        Dibatalkan
                    </option>

                </select>
            </form>
        </div>

    </div>

    <table class="table table-bordered">
        <thead class="bg-dark text-white text-center">
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Status</th>
                @if(auth()->user()->role == 'admin')
                <th>Aksi</th>
                @endif
                
            </tr>
        </thead>

        <tbody>
            @foreach($data as $key => $item)
            <tr class="
                {{ $item->is_late ? 'table-warning' : '' }}
                {{ $item->status == 'dibatalkan' ? 'table-secondary text-muted' : '' }}
            ">
                <td class="text-center">{{ $data->firstItem() + $key }}</td>
            
                <td>{{ $item->sparepart->nama_sparepart }}</td>
            
                <td class="text-center">{{ number_format($item->jumlah,0,',','.') }}</td>
            
                <td class="text-center">
                    <span class="{{ $item->is_late ? 'text-danger font-weight-bold' : '' }}">
                        {{ $item->tanggal }}
                    </span>
            
                    @if($item->is_late)
                        <span class="badge bg-warning text-dark ml-2">
                            Terlambat
                        </span>
                    @endif
                </td>
            
                <td class="text-center">
                    @if($item->keterangan)
                        <span class="text-muted">
                            {{ $item->keterangan }}
                        </span>
                    @endif
                </td>
            
                <td class="text-center">
                    <span class="badge {{ $item->status == 'valid' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $item->status == 'valid' ? 'Valid' : 'Dibatalkan' }}
                    </span>
                </td>
                
                @if(auth()->user()->role == 'admin')
                <td class="text-center">
                
                    @if($item->status == 'valid')
                        <form action="{{ route('barang-keluar.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                
                            <button type="button"
                                    class="btn btn-warning btn-sm btn-batalkan">
                                Batalkan
                            </button>
                        </form>
                    @else
                        <span class="text-muted">Tidak tersedia</span>
                    @endif
                
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $data->links() }}
    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('barang-keluar.store') }}">
                @csrf

                <div class="modal-header">
                    <h4>Tambah Barang Keluar</h4>
                </div>


                <div class="modal-body">
                    <div class="form-group">
                        <label>Sparepart</label>
                        <select name="sparepart_id" class="form-control" required>
                            <option value="">-- Pilih Sparepart--</option>
                            @foreach($spareparts as $sp)
                                <option value="{{ $sp->id_sparepart }}">
                                    {{ $sp->nama_sparepart }} (Stok: {{ $sp->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                    
                        <input type="text" id="jumlah_display"
                               class="form-control"
                               value="{{ old('jumlah') }}"
                               placeholder="Masukkan jumlah"
                               required>
                    
                        <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah') }}">
                    
                        <div class="invalid-feedback" id="jumlahError">
                            Jumlah wajib diisi
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal"
                            class="form-control"
                            value="{{ old('tanggal') }}"
                            onclick="this.showPicker()"
                            onfocus="this.showPicker()"
                            required>
                    </div>
                    
                    <div class="text-warning mt-1" id="warningTanggal" style="display:none;">
                        ⚠ Input lebih dari 30 hari yang lalu
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <select name="keterangan" class="form-control">
                            <option value="">-- Tidak ada / Normal --</option>
                            <option value="Lupa input" {{ old('keterangan')=='Lupa input'?'selected':'' }}>Lupa input</option>
                            <option value="Salah input sebelumnya" {{ old('keterangan')=='Salah input sebelumnya'?'selected':'' }}>Salah input sebelumnya</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">Simpan</button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    
        // =========================
        // ELEMENT
        // =========================
        const form = document.querySelector('#modalTambah form');
        const display = document.getElementById('jumlah_display');
        const hidden = document.getElementById('jumlah');
        const error = document.getElementById('jumlahError');
    
        const tanggal = document.getElementById('tanggal');
        const warning = document.getElementById('warningTanggal');
        const keterangan = document.querySelector('[name="keterangan"]');
    
        if (!tanggal || !keterangan) return;
    
        // =========================
        // FORMAT RIBUAN
        // =========================
        display.addEventListener('input', function () {
            let angka = this.value.replace(/\D/g, '');

            // ❗ cegah 0
            if (angka === "0") {
                angka = "";
            }

            hidden.value = angka;
            this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    
        // =========================
        // SET DEFAULT TANGGAL (HARI INI)
        // =========================
        const now = new Date();

        const today =
            now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');

        tanggal.setAttribute('max', today);
        tanggal.value = today;
    
        // =========================
        // FUNCTION UTAMA
        // =========================
        function updateKeteranganState() {

            const now = new Date();

            const today =
                now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0');

            // Hari ini
            if (tanggal.value === today) {

                keterangan.value = "";
                keterangan.disabled = true;
                keterangan.required = false;

                warning.style.display = 'none';

            } else {

                keterangan.disabled = false;
                keterangan.required = true;

                let selected = new Date(tanggal.value);
                let diffTime = now - selected;
                let diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                warning.style.display = diffDays > 30 ? 'block' : 'none';
            }
        }
    
        // =========================
        // INIT (PERTAMA LOAD)
        // =========================
        updateKeteranganState();
    
        // =========================
        // EVENT
        // =========================
        tanggal.addEventListener('change', updateKeteranganState);
    
        // =========================
        // VALIDASI SUBMIT
        form.addEventListener('submit', function (e) {

            // VALIDASI JUMLAH
            if (!hidden.value || hidden.value < 1) {
                e.preventDefault();

                display.classList.add('is-invalid');
                error.innerText = "Jumlah minimal 1";
                error.style.display = 'block';
                return;
            }

            // VALIDASI KETERANGAN
            if (!keterangan.disabled && !keterangan.value) {
                e.preventDefault();
                alert('Keterangan wajib diisi jika tanggal bukan hari ini');
                return;
            }

            display.classList.remove('is-invalid');
            error.style.display = 'none';
        });
    
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.btn-batalkan').forEach(button => {

        button.addEventListener('click', function () {

            let form = this.closest('form');

            Swal.fire({
                title: 'Batalkan Data?',
                html: `
                    ⚠ Data ini akan dibatalkan dan stok akan dikembalikan.
                    <br><br>
                    Lanjutkan?
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });

    });

});
</script>


{{-- STYLE --}}
<style>
.confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    
    z-index: 9999999999 !important; /* lebih tinggi dari bootstrap */
    
    display: flex;
    justify-content: center;
    align-items: center;
    
    opacity: 0;
    visibility: hidden;
    transition: 0.2s ease;
}
    
.confirm-overlay.show {
    display: flex !important;
    opacity: 1;
    visibility: visible;
}
    
.confirm-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    min-width: 250px;
}
    
.center-alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    
    padding: 20px 30px;
    border-radius: 10px;
    color: white;
    
    z-index: 999999999; /* pastikan di atas semua */
    min-width: 250px;
    text-align: center;
}
    
    
.modal {
    z-index: 1050;
}
    
.modal-backdrop {
    z-index: 1040;
}
    
    
/* warna */
.success { background: green; }
.warning { background: orange; }
.error   { background: red; }
    
</style>

<script>
setTimeout(() => {
    document.querySelectorAll('.alert-box').forEach(el => el.remove());
}, 2000);
</script>

@if(session('open_modal'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#modalTambah').modal('show');
    });
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", function () {

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            html: `{!! session('error') !!}`,
            confirmButtonColor: '#d33'
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            confirmButtonColor: '#28a745'
        });
    @endif

});
</script>

<script>
$('#modalTambah').on('hidden.bs.modal', function () {

    this.querySelector('form').reset();

    let tanggal = document.getElementById('tanggal');

    const now = new Date();

    const today =
        now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    tanggal.value = today;

    document.getElementById('jumlah_display').value = '';
    document.getElementById('jumlah').value = '';

});
</script>

@endsection