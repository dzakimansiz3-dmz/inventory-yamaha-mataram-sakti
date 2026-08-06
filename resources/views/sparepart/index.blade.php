@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-3">Data Sparepart</h3>

    {{-- BUTTON AREA + FILTER STATUS --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        @if(auth()->user()->role == 'admin')
        <button class="btn btn-primary"
                data-toggle="modal"
                data-target="#modalTambah">
            + Tambah Sparepart
        </button>
        @endif

        {{-- FILTER STATUS --}}
        <div class="d-flex align-items-center ml-auto">
            <span class="font-weight-bold mr-2">Status:</span>

            <form method="GET" action="{{ route('sparepart.index') }}" class="mb-0">

                <select name="status"
                        class="form-control form-control-sm font-weight-bold"
                        onchange="this.form.submit()"
                        style="width: 160px;">

                    <option value="semua"
                        {{ request('status', 'semua') == 'semua' ? 'selected' : '' }}>
                        Semua
                    </option>

                    <option value="aktif"
                        {{ request('status') == 'aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option value="nonaktif"
                        {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
                        Non Aktif
                    </option>

                </select>

            </form>
        </div>

    </div>

    {{-- NOTIF --}}
    @if(session('message'))
        <div id="centerAlert" class="center-alert {{ session('type') }}">
            {{ session('message') }}
        </div>
    @endif

    {{-- TABLE --}}
    <table class="table table-bordered table-striped">
        <thead class="bg-dark text-white text-center">
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Kode Part</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                @if(auth()->user()->role == 'admin')
                <th>Aksi</th>
                @endif
            </tr>
        </thead>

        <tbody>
        @foreach($spareparts as $key => $item)
        <tr class="{{ $item->status == 'nonaktif' ? 'table-secondary text-muted' : '' }}">
            <td class="text-center">{{ $spareparts->firstItem() + $key }}</td>
            <td>{{ $item->nama_sparepart }}</td>
            <td class="text-center">{{ $item->kode_part }}</td>
            <td class="text-center">{{ $item->kategori }}</td>
            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
            
            <td class="text-center">
                <span class="badge {{ $item->stok <= 5 ? 'bg-danger' : 'bg-success' }}">
                    {{ number_format($item->stok, 0, ',', '.') }}
                </span>
            </td>
        
            {{-- STATUS --}}
            <td class="text-center">
                <span class="badge {{ $item->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($item->status) }}
                </span>
            </td>
        
            @if(auth()->user()->role == 'admin')
            <td class="text-center">
            
                {{-- EDIT --}}
                <button type="button"
                    class="btn btn-warning btn-sm btn-edit"
                    data-id="{{ $item->id_sparepart }}"
                    data-nama="{{ $item->nama_sparepart }}"
                    data-kode="{{ $item->kode_part }}"
                    data-kategori="{{ $item->kategori }}"
                    data-harga="{{ $item->harga }}"
                    {{ $item->status == 'nonaktif' ? 'disabled' : '' }}>
                    Edit
                </button>
            
                @if($item->status == 'aktif')
                    <form action="{{ route('sparepart.destroy', $item) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
            
                        <button class="btn btn-danger btn-sm">
                            Nonaktifkan
                        </button>
                    </form>
                @else
                    <form action="{{ route('sparepart.activate', $item->id_sparepart) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('PUT')
            
                        <button class="btn btn-success btn-sm">
                            Aktifkan
                        </button>
                    </form>
                @endif
            
            </td>
            @endif
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $spareparts->links() }}
    </div>

</div>

{{-- =========================
    MODAL TAMBAH
========================= --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('sparepart.store') }}">
                @csrf

                <div class="modal-header">
                    <h4>Tambah Sparepart</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Sparepart</label>
                        <input type="text" name="nama_sparepart" id="nama_sparepart"
                               value="{{ old('nama_sparepart') }}"
                               class="form-control @error('nama_sparepart') is-invalid @enderror"
                               required>
                        @error('nama_sparepart')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kode Part</label>
                    
                        <input type="text" name="kode_part" id="kode_part"
                               value="{{ old('kode_part') }}"
                               class="form-control @error('kode_part') is-invalid @enderror"
                               pattern="[A-Z]{3}-[0-9]{3}"
                               placeholder="Contoh: OLI-001"
                               required>
                    
                        @error('kode_part')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    
                        <small class="text-muted">
                            Format: 3 huruf - 3 angka (contoh: OLI-001)
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="" disabled selected>-- Kategori --</option>
                            <option value="Oli">Oli</option>
                            <option value="Rem">Rem</option>
                            <option value="Pengapian">Pengapian</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Filter">Filter</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="text" name="harga" id="harga"
                               value="{{ old('harga') }}"
                               class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- =========================
    MODAL EDIT
========================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h4>Edit Sparepart</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Sparepart</label>
                        <input type="text" name="nama_sparepart" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Kode Part</label>
                        <input type="text" name="kode_part" id="edit_kode"
                               class="form-control"
                               pattern="[A-Z]{3}-[0-9]{3}" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" id="edit_kategori" class="form-control" required>
                            <option value="Oli">Oli</option>
                            <option value="Rem">Rem</option>
                            <option value="Pengapian">Pengapian</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Filter">Filter</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="text" name="harga" id="edit_harga" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- =========================
    MODAL KONFIRMASI DELETE
========================= --}}
<div id="deleteModal" class="custom-modal">

    <div class="custom-modal-content">

        <h5 class="mb-3 text-center">⚠ Konfirmasi Hapus</h5>

        <p class="text-center">
            Apakah kamu yakin ingin menghapus data ini?
        </p>

        <div class="d-flex justify-content-center mt-4">

            <button class="btn btn-secondary mr-2" id="cancelDelete">
                Batal
            </button>

            <button class="btn btn-danger" id="confirmDelete">
                Hapus
            </button>

        </div>

    </div>

</div>

{{-- SCRIPT --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    
    // =========================
    // FORMAT INPUT TAMBAH
    // =========================
    const nama = document.getElementById('nama_sparepart');
    if (nama) {
        nama.addEventListener('input', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    }

    const kode = document.getElementById('kode_part');
    if (kode) {
        kode.addEventListener('input', function() {
            let val = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            let huruf = val.substring(0, 3);
            let angka = val.substring(3, 6);
            this.value = val.length > 3 ? huruf + '-' + angka : huruf;
        });
    }
    
    const harga = document.getElementById('harga');
    if (harga) {
        harga.addEventListener('input', function() {
            let angka = this.value.replace(/\D/g, '');
            this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    }
    
    // =========================
    // EDIT MODAL
    // =========================
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
    
            if (this.disabled) return;

            let id = this.dataset.id;
            let nama = this.dataset.nama;
            let kode = this.dataset.kode;
            let kategori = this.dataset.kategori;
            let harga = this.dataset.harga;

            // isi form
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_kode').value = kode;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_harga').value =
                harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // action route (AMAN)
            document.getElementById('formEdit').action = "{{ url('sparepart') }}/" + id;

            $('#modalEdit').modal('show');
        });
    });

    // =========================
    // FORMAT EDIT
    // =========================
    const editNama = document.getElementById('edit_nama');
    if (editNama) {
        editNama.addEventListener('input', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    }

    const editKode = document.getElementById('edit_kode');
    if (editKode) {
        editKode.addEventListener('input', function() {
            let val = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            let huruf = val.substring(0, 3);
            let angka = val.substring(3, 6);
            this.value = val.length > 3 ? huruf + '-' + angka : huruf;
        });
    }

    const editHarga = document.getElementById('edit_harga');
    if (editHarga) {
        editHarga.addEventListener('input', function() {
            let angka = this.value.replace(/\D/g, '');
            this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    }

    // =========================
    // AUTO OPEN MODAL JIKA ERROR
    // =========================
    @if($errors->any())
        $('#modalTambah').modal('show');
    @endif
});
</script>

{{-- STYLE --}}
<style>
.center-alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    z-index: 9999;
}
.success { background: green; }
.error { background: red; }

.custom-modal{
    display:none;
    position:fixed;
    z-index:9999;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}

.custom-modal-content{
    background:#fff;
    padding:25px;
    border-radius:10px;
    width:300px;
    text-align:center;
}
</style>

<script>
setTimeout(() => {
    let el = document.getElementById('centerAlert');
    if (el) el.remove();
}, 2000);
</script>

@endsection