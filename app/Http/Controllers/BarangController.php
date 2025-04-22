<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\LevelModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{

    public function index(): View
    {
        $page = (object) ['title' => 'Daftar barang yang terdaftar dalam sistem.'];
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $activeMenu = 'barang';
        $kategori = Kategori::all();


        return view('barang.index', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }

    public function list(Request $request): JsonResponse
    {
        $barangs = Barang::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')->with('kategori');
        if ($request->kategori_id) {
            $barangs->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                // $btn = '<a href="' . url( '/barang/' . $barang->barang_id) . '" class="btn btn-info btnsm">Detail</a> ';
                // $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // public function create(): View
    // {
    //     $page = (object) ['title' => 'Tambah barang.'];
    //     $breadcrumb = (object) [
    //         'title' => 'Daftar Barang',
    //         'list' => ['Home', 'Barang', 'Add']
    //     ];

    //     $activeMenu = 'barang';
    //     $kategori = Kategori::all();

    //     return view('barang.create', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    // }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
    //         'barang_nama' => 'required|string|max:100',
    //         'harga_beli' => 'required|integer',
    //         'harga_jual' => 'required|integer',
    //         'kategori_id' => 'required|integer'
    //     ]);

    //     Barang::create([
    //         'barang_kode' => $request->barang_kode,
    //         'barang_nama' => $request->barang_nama,
    //         'harga_beli' => $request->harga_beli,
    //         'harga_jual' => $request->harga_jual,
    //         'kategori_id' => $request->kategori_id
    //     ]);

    //     return redirect(route('barang.index'))->with('success', 'Data barang berhasil disimpan');
    // }
    public function show(string $id)
    {
        $barang = Barang::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Barang'
        ];

        $activeMenu = 'barang';

        return view('barang.show', compact('breadcrumb', 'page', 'barang', 'activeMenu'));
    }
    // public function edit(string $id)
    // {
    //     $barang = Barang::find($id);
    //     $kategori = Kategori::all();
    //     $breadcrumb = (object)[
    //         'title' => 'Edit Barang',
    //         'list' => ['Home', 'Barang', 'Edit']
    //     ];

    //     $page = (object)[
    //         'title' => 'Edit Barang'
    //     ];

    //     $activeMenu = 'barang';

    //     return view('barang.edit', compact('breadcrumb', 'page', 'barang', 'activeMenu', 'kategori'));
    // }

    // public function update(Request $request, string $id)
    // {
    //     $request->validate([
    //         'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode, ' . $id . ',barang_id',
    //         'barang_nama' => 'required|string|max:100',
    //         'harga_beli' => 'required|integer',
    //         'harga_jual' => 'required|integer',
    //         'kategori_id' => 'required|integer'
    //     ]);

    //     Barang::find($id)->update([
    //         'barang_kode' => $request->barang_kode,
    //         'barang_nama' => $request->barang_nama,
    //         'harga_beli' => $request->harga_beli,
    //         'harga_jual' => $request->harga_jual,
    //         'kategori_id' => $request->kategori_id
    //     ]);

    //     return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    // }
    // public function destroy(string $id)
    // {
    //     $check = Barang::find($id);
    //     if (!$check) {
    //         return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
    //     }
    //     try {
    //         Barang::destroy($id);
    //         return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
    //     } catch (QueryException $e) {
    //         return redirect('/barang')->with('error', 'Data barang gagal dihapys karena masih terdapat tabel lain yang terkait dengan data ini');
    //     }
    // }

    public function create_ajax()
    {
        $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();

        return view('barang.create_ajax')->with('kategori', $kategori);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|integer',
                'harga_jual' => 'required|integer',
                'kategori_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            Barang::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan.'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $barang = Barang::find($id);
        $kategori = Kategori::select('kategori_id', 'kategori_nama')->get();

        return view('barang.edit_ajax', compact('barang', 'kategori'));
    }
    public function update_ajax(Request $request, $id)
    { // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode, ' . $id . ',barang_id',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|integer',
                'harga_jual' => 'required|integer',
                'kategori_id' => 'required|integer'
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
            $check = Barang::find($id);
            if ($check) {
                // if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request
                //     $request->request->remove('password');
                // }
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
        $barang = Barang::find($id);

        return view('barang.confirm_ajax', compact('barang'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = Barang::find($id);
            if ($barang) {
                try {
                    $barang->delete();
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

    public function import()
    {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [ // validasi file harus xls atau xlsx, max 1MB
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Validasi Gagal', 'msgField' => $validator->errors()]);
            }
            $file = $request->file('file_barang'); // ambil file dari request
            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
            $data = $sheet->toArray(null, false, true, true); // ambil data excel
            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $insert[] = ['kategori_id' => $value['A'], 'barang_kode' => $value['B'], 'barang_nama' => $value['C'], 'harga_beli' => $value['D'], 'harga_jual' => $value['E'], 'created_at' => now(),];
                    }
                }
                if (count($insert) > 0) { // insert data ke database, jika data sudah ada, maka diabaikan
                    Barang::insertOrIgnore($insert);
                }
                return response()->json(['status' => true, 'message' => 'Data berhasil diimport']);
            } else {
                return response()->json(['status' => false, 'message' => 'Tidak ada data yang diimport']);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        // ambil data barang yang akan diexport

        $barang = Barang::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();

        // load library excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Harga Beli');
        $sheet->setCellValue('E1', 'Harga Jual');
        $sheet->setCellValue('F1', 'Kategori');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke-2
        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->barang_kode);
            $sheet->setCellValue('C' . $baris, $value->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->harga_beli);
            $sheet->setCellValue('E' . $baris, $value->harga_jual);
            $sheet->setCellValue('F' . $baris, $value->kategori->kategori_nama); // ambil nama kategori
            $baris++;
            $no++;
        }

        // set column size
        foreach(range('A', 'F') as $columnID){
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Barang');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Barang ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf(){
        $barang = Barang::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->with('kategori')
            ->get();
        $pdf = Pdf::loadview('barang.export_pdf', compact('barang'));
        $pdf->setPaper('a4','portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream('Data Barang'. date('Y-m-d H:i:s' . ' .pdf'));
    }
}
