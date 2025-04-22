{{-- <form action="{{ route('stok.ajax') }}" method="POST" id="form-tambah"> @csrf --}}
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Stok</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Author</label>
                <input value="{{ $stok->user->name }}" type="hidden" name="user_id" id="user_id" class="form-control"
                    required>
                <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly>
            </div>
            <div class="form-group">
                <label>Barang</label>
                <input type="text" class="form-control" value="{{ $stok->barang->barang_nama }}" readonly="readonly">
            </div>
            <div class="form-group">
                <label>Jumlah</label><br>
                <input value="{{ $stok->stok_jumlah }}" type="number" name="stok_jumlah" id="stok_jumlah"
                    class="form-control" readonly="readonly">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control"
                    value="{{ $stok->keterangan }}" readonly="readonly">
            </div>
        </div>
        <input hidden="hidden" type="text" name="stok_tanggal" id="stok_tanggal" value="{{ now() }}">
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Tutup</button>
            {{-- <button type="submit" class="btn btn-primary">Simpan</button> --}}
        </div>
    </div>
</div>
{{-- </form> --}}
<script></script>
