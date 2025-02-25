<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_kode' => 'SUP001',
                'supplier_nama' => 'PT Supplier Pertama',
                'supplier_alamat' => 'Jl. Raya No. 1, Jakarta',
            ],
            [
                'supplier_kode' => 'SUP002',
                'supplier_nama' => 'CV Supplier Kedua',
                'supplier_alamat' => 'Jl. Alternatif No. 2, Bandung',
            ],
            [
                'supplier_kode' => 'SUP003',
                'supplier_nama' => 'UD Supplier Ketiga',
                'supplier_alamat' => 'Jl. Utama No. 3, Surabaya',
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
