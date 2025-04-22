<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Stok;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    public function index()
    {
        $page = (object) ['title' => 'Daftar riwayat stok yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $activeMenu = 'stok';
        return view('stok.index', compact('breadcrumb', 'page', 'activeMenu'));
    }
    public function list(Request $request): JsonResponse
    {
        $stoks = Stok::select('stok_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')->with('barang', 'user')->orderBy('stok_tanggal', 'desc');
        // if ($request->kategori_id) {
        //     $stoks->where('kategori_id', $request->kategori_id);
        // }
        return DataTables::of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                // $btn = '<a href="' . url( '/supplier/' . $supplier->level_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/supplier/' . $supplier->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->level_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\'' . route('stok.show', ['id' => $stok->stok_id]) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                // $btn .= '<button onclick="modalAction(\'' . route('stok.edit_ajax', ['id' => $stok->stok_id]) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('stok.delete_ajax', ['id' => $stok->stok_id]) . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function show($id){
        $stok = Stok::find($id);
        return view('stok.show', compact('stok'));
    }
    public function create_ajax()
    {
        $barang = Barang::select('barang_id', 'barang_nama')->get();
        $stok = Stok::all();
        $sa = 1;


        return view('stok.create_ajax', compact('barang', 'sa', 'stok'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'stok_jumlah' => 'required|integer',
                'user_id' => 'required|integer',
                'barang_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            Stok::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan.'
            ]);
        }
        redirect('/');
    }

    // public function edit_ajax(string $id)
    // {
    //     $barang = Barang::select('barang_id', 'barang_nama')->get();

    //     $stok = Stok::find($id);
    //     // $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();

    //     return view('stok.edit_ajax', compact('barang', 'stok'));
    // }
    // public function update_ajax(Request $request, $id)
    // { // cek apakah request dari ajax
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode, ' . $id . ',barang_id',
    //             'barang_nama' => 'required|string|max:100',
    //             'harga_beli' => 'required|integer',
    //             'harga_jual' => 'required|integer',
    //             'kategori_id' => 'required|integer'
    //         ];
    //         // use Illuminate\Support\Facades\Validator;
    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false, // respon json, true: berhasil, false: gagal
    //                 'message' => 'Validasi gagal.',
    //                 'msgField' => $validator->errors() // menunjukkan field mana yang error
    //             ]);
    //         }
    //         $check = Barang::find($id);
    //         if ($check) {
    //             // if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request
    //             //     $request->request->remove('password');
    //             // }
    //             $check->update($request->all());
    //             return response()->json(['status' => true, 'message' => 'Data berhasil diupdate']);
    //         } else {
    //             return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    //         }
    //     }
    //     return redirect('/');
    // }

    public function confirm_ajax(string $id)
    {
        $stok = Stok::find($id);

        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = Stok::find($id);
            if ($stok) {
                try {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }
}
