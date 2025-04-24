<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Stok;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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

                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id) . '\')" class="btn btn-info btn-sm">Detail</button> ';
                // $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function show($id)
    {
        $penjualan = Penjualan::find($id);
        return view('penjualan.show', compact('penjualan'));
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
                'pembeli' => 'required|string|max:100',
                // 'barang_id.*' => 'required|integer',
                // 'jumlah.*' => 'required|integer',
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




            for ($i = 0; $i < count($request->barang_id); $i++) {
                $detail = new DetailPenjualan();
                $detail->penjualan_id = $penjualan->penjualan_id;
                $detail->barang_id = $request->barang_id[$i];
                $detail->harga = $request->harga[$i];
                $detail->jumlah = $request->jumlah[$i];
                $detail->save();

                Stok::create([
                    'barang_id' => $request->barang_id[$i],
                    'user_id' => Auth::user()->user_id,
                    'stok_tanggal' => $request->penjualan_tanggal,
                    'stok_jumlah' => ($request->jumlah[$i] * (-1)),
                    'keterangan' => 'Penjualan barang ' . $request->barang_id[$i] . ',penjualan:' . $penjualan->penjualan_id,
                ]);
            }


            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan.'
            ]);
        }
        redirect('/');
    }
    public function confirm_ajax(string $id)
    {
        $penjualan = Penjualan::find($id);

        return view('penjualan.confirm_ajax', compact('penjualan'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = Penjualan::find($id);
            if ($penjualan) {
                try {
                    $penjualan->detail_penjualan()->delete();
                    $penjualan->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } catch (QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
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
    public function export_excel()
    {
        $semua_penjualan = Penjualan::with('detail_penjualan')->with('user')->orderBy('penjualan_tanggal', 'asc')->get();
        $penjualan = [];

        foreach ($semua_penjualan as $data) {
            foreach ($data->detail_penjualan as $item) {
                $penjualan[] = [
                    'nama' => $data->user->nama,
                    'pembeli' => $data->pembeli,
                    'penjualan_kode' => $data->penjualan_kode,
                    'tgl_penjualan' => $data->penjualan_tanggal,
                    'barang' => $item->barang->barang_nama,
                    'harga' => $item->harga,
                    'jumlah' => $item->jumlah,
                    'total' => $item->jumlah * $item->harga,
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Stok');
        $sheet->fromArray(['Nama', 'Pembeli', 'Kode Penjualan', 'Tanggal', 'Barang', 'Harga', 'Jumlah', 'Total'], null, 'A1');
        $row = 2;
        $start_merge = $row;
        foreach ($penjualan as $index => $item) {
            $sheet->setCellValue("A{$row}", $item['nama']);
            $sheet->setCellValue("B{$row}", $item['pembeli']);
            $sheet->setCellValue("C{$row}", $item['penjualan_kode']);
            $sheet->setCellValue("D{$row}", $item['tgl_penjualan']);
            $sheet->setCellValue("E{$row}", $item['barang']);
            $sheet->setCellValue("F{$row}", $item['harga']);
            $sheet->setCellValue("G{$row}", $item['jumlah']);
            $sheet->setCellValue("H{$row}", $item['total']);
            $next = $penjualan[$index + 1]['penjualan_kode'] ?? null;
            if ($item['penjualan_kode'] !== $next) {
                if ($start_merge !== $row) {
                    $sheet->mergeCells("A{$start_merge}:A{$row}");
                    $sheet->mergeCells("B{$start_merge}:B{$row}");
                    $sheet->mergeCells("C{$start_merge}:C{$row}");
                    $sheet->getStyle("A{$start_merge}:C{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("A{$start_merge}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $start_merge = $row + 1;
            }

            $row++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx' . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }
    public function export_pdf()
    {
        $penjualan_semua = Penjualan::with('detail_penjualan')->with('user')->orderBy('penjualan_tanggal', 'asc')->get();

        $rekap = [];
        foreach ($penjualan_semua as $data) {
            foreach ($data->detail_penjualan as $item) {
                $rekap[] = [
                    'nama' => $data->user->nama,
                    'pembeli' => $data->pembeli,
                    'penjualan_kode' => $data->penjualan_kode,
                    'tgl_penjualan' => $data->penjualan_tanggal,
                    'barang' => $item->barang->barang_nama,
                    'harga' => $item->harga,
                    'jumlah' => $item->jumlah,
                    'total' => $item->jumlah * $item->harga,
                ];
            }
        }

        $pdf = Pdf::loadView('penjualan.export_pdf', ['rekap' => $rekap]);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(["isRemoteEnabled"], true);
        $pdf->render();
        return $pdf->stream('Data_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
