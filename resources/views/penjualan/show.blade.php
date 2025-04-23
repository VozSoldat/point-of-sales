{{-- <form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah"> --}}
{{-- @csrf --}}
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tambah Data Penjualan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Kasir</label>
                <input type="text" class="form-control" value="{{ $penjualan->user->nama }}" readonly>
            </div>
            <div class="form-group">
                <label>Nama Pembeli</label>
                <input type="text" name="pembeli" id="pembeli" class="form-control" required
                    value="{{ $penjualan->pembeli }}" readonly="readonly">
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input value="{{ $penjualan->penjualan_tanggal }}" type="text" name="penjualan_tanggal"
                    class="form-control" required readonly="readonly">
            </div>
            <div class="form-group">
                <label>Kode Penjualan</label>
                <input value="{{ $penjualan->penjualan_kode }}" type="text" name="penjualan_kode"
                    class="form-control" required readonly="readonly">
            </div>
            <div class="form-group">
                <label>Kode Penjualan</label>
                <input value="{{ $penjualan->penjualan_kode }}" type="text" name="penjualan_kode"
                    class="form-control" required readonly="readonly">
            </div>

            <div class="form-group" id="barang-container">
                @foreach ($penjualan->detail_penjualan as $item)
                    <div class="form-row align-items-end mt-3">
                        <div class="col-md-4">
                            <label for="barang_id[]">Nama Barang</label>
                            <input value="{{ $item->barang->barang_nama }}" type="text" name="barang_id[]"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="harga[]">Harga Barang</label>
                            <input value="{{ $item->barang->harga_jual }}" type="number" name="harga[]"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="jumlah[]">Jumlah</label>
                            <input value="{{ $item->jumlah }}" type="number" name="jumlah[]" class="form-control"
                                placeholder="Masukkan jumlah" readonly="readonly">
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
</div>
{{-- </form> --}}
{{-- <script>
$(document).ready(function() {
    $("#form-tambah").validate({
        rules: {
            // kategori_id: {
            //     required: true,
            //     number: true
            // },
            pembeli: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
        },
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#modal-master').modal(
                            'hide'); // Konsisten dengan ID modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataUser.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    const hargaData =
        @json($barang); // pastikan ini array of object dengan barang_id dan harga_jual
    const stokData =
        @json($stok); // pastikan ini sudah berisi semua data stok (dengan barang_id dan stok_tanggal)

    // Event listener untuk perubahan pilihan barang di semua baris
    $('#barang-container').on('change', 'select[name="barang_id[]"]', function() {
        const selectedId = $(this).val();
        const row = $(this).closest('.form-row');

        // Ambil data harga berdasarkan barang_id
        const barang = hargaData.find(item => item.barang_id == selectedId);
        const harga = barang ? barang.harga_jual : '';
        row.find('input[name="harga[]"]').val(harga);

        // Ambil stok terbaru berdasarkan barang_id
        // const stokTerkait = stokData.filter(s => s.barang_id == selectedId);
        // stokTerkait.sort((a, b) => new Date(b.stok_tanggal) - new Date(a.stok_tanggal));
        // const stokTerbaru = stokTerkait[0];
        // const stok = stokTerbaru ? stokTerbaru.stok_jumlah : '';

        // Filter data berdasarkan barang_id
        const filtered = stokData.filter(item => item.barang_id == selectedId);

        // Hitung jumlah entry dan opname
        const stok = filtered.reduce((sum, item) => sum + Number(item.stok_jumlah), 0);
        // const totalEntry = filtered.reduce((sum, item) => sum + Number(item.), 0);
        // const totalOpname = filtered.reduce((sum, item) => sum + Number(item.opname), 0);

        row.find('input[name="stok[]"]').val(stok);
    });


    // templating field group
    const template = `
    <div class="form-row align-items-end mt-3">
<div class="col-md-4">
    <select name="barang_id[]" class="form-control">
        <option value="">- Pilih Barang -</option>
        @foreach ($barang as $l)
            <option value="{{ $l->barang_id }}">{{ $l->barang_nama }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <input type="number" name="harga[]" class="form-control" readonly>
</div>
<div class="col-md-2">
    <input type="number" name="stok[]" class="form-control" readonly>
</div>
<div class="col-md-2">
    <input type="number" name="jumlah[]" class="form-control" placeholder="Masukkan jumlah">
</div>
<div class="col-md-1 d-flex">
    <button type="button" class="btn btn-danger btn-sm mt-auto remove-row">✕</button>
</div>
</div>
    `;

    // Event tombol tambah
    $('#tambah-barang').on('click', function() {
        $('#barang-container').append(template);
    });

    // hapus baris saat tombol .remove-row diklik
    $('#barang-container').on('click', '.remove-row', function() {
        $(this).closest('.form-row').remove();
    });

});
</script> --}}
