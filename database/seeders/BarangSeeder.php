<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1,
                'kategori_id' => 1,
                'barang_kode' => 'ABC1234567',
                'barang_nama' => 'Barang A',
                'harga_beli' => 20000,
                'harga_jual' => 50000,

            ],
            [
                'barang_id' => 2,
                'kategori_id' => 1,
                'barang_kode' => 'DEF1234567',
                'barang_nama' => 'Barang B',
                'harga_beli' => 25000,
                'harga_jual' => 55000,

            ],
            [
                'barang_id' => 3,
                'kategori_id' => 1,
                'barang_kode' => 'GHI1234567',
                'barang_nama' => 'Barang C',
                'harga_beli' => 30000,
                'harga_jual' => 60000,

            ],
            [
                'barang_id' => 4,
                'kategori_id' => 2,
                'barang_kode' => 'JKL1234567',
                'barang_nama' => 'Barang D',
                'harga_beli' => 15000,
                'harga_jual' => 40000,

            ],
            [
                'barang_id' => 5,
                'kategori_id' => 2,
                'barang_kode' => 'MNO1234567',
                'barang_nama' => 'Barang E',
                'harga_beli' => 18000,
                'harga_jual' => 45000,

            ],
            [
                'barang_id' => 6,
                'kategori_id' => 2,
                'barang_kode' => 'PQR1234567',
                'barang_nama' => 'Barang F',
                'harga_beli' => 22000,
                'harga_jual' => 48000,

            ],
            [
                'barang_id' => 7,
                'kategori_id' => 3,
                'barang_kode' => 'STU1234567',
                'barang_nama' => 'Barang G',
                'harga_beli' => 27000,
                'harga_jual' => 57000,

            ],
            [
                'barang_id' => 8,
                'kategori_id' => 3,
                'barang_kode' => 'VWX1234567',
                'barang_nama' => 'Barang H',
                'harga_beli' => 19000,
                'harga_jual' => 46000,

            ],
            [
                'barang_id' => 9,
                'kategori_id' => 3,
                'barang_kode' => 'YZA1234567',
                'barang_nama' => 'Barang I',
                'harga_beli' => 26000,
                'harga_jual' => 58000,

            ],
            [
                'barang_id' => 10,
                'kategori_id' => 1,
                'barang_kode' => 'BCD1234567',
                'barang_nama' => 'Barang J',
                'harga_beli' => 23000,
                'harga_jual' => 53000,

            ],
            [
                'barang_id' => 11,
                'kategori_id' => 1,
                'barang_kode' => 'EFG1234567',
                'barang_nama' => 'Barang K',
                'harga_beli' => 29000,
                'harga_jual' => 59000,

            ],
            [
                'barang_id' => 12,
                'kategori_id' => 2,
                'barang_kode' => 'HIJ1234567',
                'barang_nama' => 'Barang L',
                'harga_beli' => 24000,
                'harga_jual' => 52000,

            ],
            [
                'barang_id' => 13,
                'kategori_id' => 2,
                'barang_kode' => 'KLM1234567',
                'barang_nama' => 'Barang M',
                'harga_beli' => 17000,
                'harga_jual' => 47000,

            ],
            [
                'barang_id' => 14,
                'kategori_id' => 3,
                'barang_kode' => 'NOP1234567',
                'barang_nama' => 'Barang N',
                'harga_beli' => 21000,
                'harga_jual' => 51000,
            ],
            [
                'barang_id' => 15,
                'kategori_id' => 3,
                'barang_kode' => 'QRS1234567',
                'barang_nama' => 'Barang O',
                'harga_beli' => 28000,
                'harga_jual' => 60000,
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
