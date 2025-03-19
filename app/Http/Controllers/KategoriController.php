<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Kategori;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{

    public function index(): View
    {
        $page = (object) ['title' => 'Daftar kategori yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $activeMenu = 'kategori';

        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request): JsonResponse
    {
        $kategoris = Kategori::select('kategori_id', 'kategori_kode', 'kategori_nama');

        return DataTables::of($kategoris)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                // $btn = '<a href="' . url( '/kategori/' . $kategori->level_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/kategori/' . $kategori->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->level_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('kategori.edit_ajax', ['id' => $kategori->kategori_id]) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(): View
    {
        $page = (object) ['title' => 'Tambah kategori.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori', 'Add']
        ];

        $activeMenu = 'kategori';

        return view('kategori.create', compact('breadcrumb', 'page', 'activeMenu'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ]);

        Kategori::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return redirect(route('kategori.index'))->with('success', 'Data kategori berhasil disimpan');
    }
    public function show(string $id)
    {
        $kategori = Kategori::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Kategori'
        ];

        $activeMenu = 'kategori';

        return view('kategori.show', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }
    public function edit(string $id)
    {
        $kategori = Kategori::find($id);

        $breadcrumb = (object)[
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit Kategori'
        ];

        $activeMenu = 'kategori';

        return view('kategori.edit', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode, ' . $id . ',kategori_id',
            'kategori_nama' => 'required|string|max:100',
        ]);

        Kategori::find($id)->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama,
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }
    public function destroy(string $id)
    {
        $check = Kategori::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }
        try {
            Kategori::destroy($id);
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (QueryException $e) {
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapys karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
    public function create_ajax()
    {
        /**
         * dipake kalau ada foreign key
         */
        // $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('kategori.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
                'kategori_nama' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            Kategori::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan.'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $kategori = Kategori::find($id);

        /**
         * dipake kalau ada foreign key
         */
        // $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('kategori.edit_ajax', compact('kategori'));
    }
    public function update_ajax(Request $request, $id)
    { // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|max:20|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                'kategori_nama' => 'required|max:100',
            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }
            $check = Kategori::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json(['status' => true, 'message' => 'Data berhasil diupdate']);
            } else {
                return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $kategori = Kategori::find($id);

        return view('kategori.confirm_ajax', compact('kategori'));
    }

    public function delete_ajax(Request $request, $id)
    {


        if ($request->ajax() || $request->wantsJson()) {
            $kategori = Kategori::find($id);
            if ($kategori) {
                try {

                    $kategori->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terdapat data lain yang terkait dengan data ini'
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
