<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Stok;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenjualanController extends Controller
{
    public function index()
    {
        $page = (object) ['title' => 'Daftar penjualan yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }
    public function list(Request $request): JsonResponse
    {
        $penjualans = Penjualan::select('penjualan_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal', 'user_id')->with('user');
        // if ($request->kategori_id) {
        //     $penjualans->where('kategori_id', $request->kategori_id);
        // }

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                // $btn = '<a href="' . url( '/barang/' . $barang->barang_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function create_ajax()
    {
        // $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();
        $barang = Barang::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')->with('kategori')->get();
        $stok = Stok::select('stok_id', 'barang_id', 'stok_tanggal', 'stok_jumlah')->with('barang')->get();
        return view('penjualan.create_ajax', compact('barang', 'stok'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // 'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
                'pembeli' => 'required|string|max:1000',
                // 'harga_beli' => 'required|integer',
                // 'harga_jual' => 'required|integer',
                // 'kategori_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }


            // Penjualan::create($request->all());

            $penjualan = new Penjualan();
            $penjualan->pembeli = $request->pembeli;
            $penjualan->user_id = Auth::user()->user_id;
            $penjualan->pembeli = $request->pembeli;
            $penjualan->penjualan_kode = $request->penjualan_kode;
            $penjualan->penjualan_tanggal = $request->penjualan_tanggal;
            $penjualan->save();


            for($i = 0; $i < count($request->barang_id); $i++) {
                $detail = new DetailPenjualan();
                $detail->penjualan_id = $penjualan->penjualan_id;
                $detail->barang_id = $request->barang_id[$i];
                $detail->harga = $request->harga[$i];
                $detail->jumlah = $request->jumlah[$i];
                $detail->save();
            }


            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan.'
            ]);
        }
        redirect('/');
    }
}
