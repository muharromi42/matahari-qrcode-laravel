<?php

namespace App\Http\Controllers;

use App\Models\barangModel;
use App\Models\qrCodeModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query_data = new qrCodeModel();
            // $data = barangModel::with(['merk', 'jenisBarang', 'satuan'])->get();
            $data = $query_data::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // $editButton = '  <a class="btn btn-primary" href="' . route('barang.edit', $row->id) . '">Edit</a> ';
                    $deleteButton = '
                <form action="' . route('qrcode.destroy', $row->id) . '"
                method="POST" style="display:inline;"  class="delete-form">
                ' . csrf_field() . method_field('DELETE') . '
                <button type="submit" class="btn btn-danger delete-button">Delete</button>
                </form>
            ';
                    return $deleteButton;
                    // return $editButton . ' ' . $deleteButton;
                })
                // fungsi mengubah id jadi nama item
                ->editColumn('id_barang', function ($row) {
                    return $row->barang ? $row->barang->nama_barang : 'N/A';
                })
                ->editColumn('id_merk', function ($row) {
                    return $row->merk ? $row->merk->merk : 'N/A';
                })
                ->editColumn('id_jenisbarang', function ($row) {
                    return $row->jenis_barang ? $row->jenis_barang->kategori : 'N/A';
                })
                ->editColumn('id_satuan', function ($row) {
                    return $row->satuan ? $row->satuan->satuan : 'N/A';
                })
                ->rawColumns(['action'])->make(true);
        }
        $barangs = barangModel::all();
        // $qrcodes = qrCodeModel::all();
        return view('qrcodes.index', compact('barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:tbl_barang,id_barang',
        ]);

        $barang = barangModel::find($request->id_barang);

        // dd($barang);

        $qrCodeData = json_encode([
            'kode_barang' => $barang->kode_barang,
        ]);
        // dd($qrCodeData);
        $qrCodeImage = QrCodeGenerator::format('png')->size(100)->generate($qrCodeData);
        $qrcodebase64 = base64_encode($qrCodeImage);

        qrCodeModel::create([
            'id_barang' => $barang->id_barang,
            'kode_barang' => $barang->kode_barang,
            'qr_code_data' => $qrcodebase64,
        ]);

        // dd($qrCodeImage);

        return redirect()->back()->with('success', 'QR Code berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $qrcode = qrCodeModel::findOrFail($id);
        $qrcode->delete();

        return redirect()->route('qrcode.index')->with('success', 'data barang berhasil di hapus');
    }
}
