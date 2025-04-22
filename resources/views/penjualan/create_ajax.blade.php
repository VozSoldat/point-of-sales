<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah"> @csrf <div id="modal-master"
        class="modal-dialog modal-lg" role="document">
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
                    <input value="{{ Auth::user()->user_id }}" type="hidden" name="user_id" id="user_id"
                        class="form-control" required>
                    <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group"> <label>Nama Pembeli</label> <input value="" type="text" name="pembeli"
                        id="pembeli" class="form-control" required> <small id="error-pembeli"
                        class="error-text form-text text-danger"></small>
                </div>
                <input type="text" name="penjualan_tanggal" id="penjualan_tanggal" value="{{ now() }}"
                    hidden="hidden">
                <input type="text" name="penjualan_kode" id="penjualan_kode" hidden="hidden"
                    value="{{ now() . rand(1, 9) }}">

                <div class="form-group" id="barang-container">
                    <div class="form-row align-items-end mt-3">
                        <div class="col-md-4">
                            <label for="barang_id[]">Nama Barang</label>
                            <select name="barang_id[]" class="form-control">
                                <option value="">- Pilih Barang -</option>
                                @foreach ($barang as $l)
                                    <option value="{{ $l->barang_id }}">{{ $l->barang_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="harga[]">Harga Barang</label>
                            <input type="number" name="harga[]" class="form-control" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="stok[]">Stok</label>
                            <input type="number" name="stok[]" class="form-control" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="jumlah[]">Jumlah</label>
                            <input type="number" name="jumlah[]" class="form-control" placeholder="Masukkan jumlah">
                        </div>
                        <div class="col-md-1 d-flex">
                            <button type="button" class="btn btn-danger btn-sm mt-auto remove-row">✕</button>
                        </div>
                    </div>


                </div>
                <button type="button" class="btn btn-primary mt-3" id="tambah-barang">Tambah Barang</button>
            </div>
            <div class="modal-footer"> <button type="button" data-dismiss="modal"
                    class="btn btn-warning">Batal</button> <button type="submit"
                    class="btn btn-primary">Simpan</button> </div>
        </div>
    </div>
</form>
<script>
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
            const stokTerkait = stokData.filter(s => s.barang_id == selectedId);
            stokTerkait.sort((a, b) => new Date(b.stok_tanggal) - new Date(a.stok_tanggal));
            const stokTerbaru = stokTerkait[0];
            const stok = stokTerbaru ? stokTerbaru.stok_jumlah : '';

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
</script>
