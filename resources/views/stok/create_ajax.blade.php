<form action="{{ route('stok.ajax') }}" method="POST" id="form-tambah"> @csrf <div id="modal-master"
        class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Author</label>
                    <input value="{{ Auth::user()->user_id }}" type="hidden" name="user_id" id="user_id"
                        class="form-control" required>
                    <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Barang</label>
                    <select name="barang_id" id="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach ($barang as $l)
                            <option value="{{ $l->barang_id }}">{{ $l->barang_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-barang_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Jumlah</label><br>
                    <small>Gunakan bilangan negatif untuk opname.</small>
                    <input value="" type="number" name="stok_jumlah" id="stok_jumlah" class="form-control"
                        required>
                    <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <input hidden="hidden" type="text" name="stok_tanggal" id="stok_tanggal" value="{{ now() }}">
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
                kategori_id: {
                    required: true,
                    number: true
                },
                barang_kode: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                },
                barang_nama: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                harga_jual: {
                    required: true,
                    number: true
                },
                harga_beli: {
                    required: true,
                    number: true
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
        
        // Saat barang dipilih
        // gak jadi dipake karena menyesuaikan proses t_stok yang diajarkan pak zawa
        // $('#barang_id').on('change', function() {
        //     let barangId = $(this).val();
        //     let stok = @json($stok);
        //     let filteredStok = stok.filter(x => x.barang_id == barangId);
        //     filteredStok.sort((a, b) => new Date(b.stok_tanggal) - new Date(a.stok_tanggal));
        //     let jumlahLama = filteredStok.length > 0 ? filteredStok[0].stok_jumlah : 0;
        //     $('#stok_jumlah').val(jumlahLama);
        // });

    });
</script>
