<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

use function Laravel\Prompts\error;

class LevelController extends Controller
{


    public function index(): View
    {
        $page = (object) ['title' => 'Daftar level yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];

        $activeMenu = 'level';

        return view('level.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request): JsonResponse
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');
        // if ($request->level_id) {
        //     $users->where('level_id', $request->level_id);
        // }
        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                // $btn = '<a href="' . url( '/level/' . $level->level_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . route('level.edit_ajax', ['id' => $level->level_id]) . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(): View
    {
        $page = (object) ['title' => 'Tambah level.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level', 'Add']
        ];

        $activeMenu = 'level';

        return view('level.create', compact('breadcrumb', 'page', 'activeMenu'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|min:3|unique:m_level,level_kode',
            'level_nama' => 'required|string|max:100',
        ]);

        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }
    public function show(string $id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list' => ['Home', 'Level', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Level'
        ];

        $activeMenu = 'level';

        return view('level.show', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }
    public function edit(string $id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object)[
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit Level'
        ];

        $activeMenu = 'level';

        return view('level.edit', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'level_kode' => 'required|string|min:3|unique:m_level,level_kode, ' . $id . ',level_id',
            'level_nama' => 'required|string|max:100',
        ]);

        LevelModel::find($id)->update([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level')->with('success', 'Data level berhasil diubah');
    }
    public function destroy(string $id)
    {
        $check = LevelModel::find($id);
        if (!$check) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }
        try {
            LevelModel::destroy($id);
            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (QueryException $e) {
            return redirect('/level')->with('error', 'Data level gagal dihapys karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        /**
         * dipake kalau ada foreign key
         */
        // $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            LevelModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan.'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $level = LevelModel::find($id);

        /**
         * dipake kalau ada foreign key
         */
        // $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('level.edit_ajax', compact('level'));
    }
    public function update_ajax(Request $request, $id)
    { // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|max:20|unique:m_level,level_kode,' . $id . ',level_id',
                'level_nama' => 'required|max:100',
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
            $check = LevelModel::find($id);
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
        $level = LevelModel::find($id);

        return view('level.confirm_ajax', compact('level'));
    }

    public function delete_ajax(Request $request, $id)
    {


        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);
            if ($level) {
                try{

                    $level->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                }
                catch(QueryException $e){
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
