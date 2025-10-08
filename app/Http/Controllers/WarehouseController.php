<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WarehouseController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('ppic.warehouse');
    }

    public function getData(Request $request)
{
    $warehouses = Warehouse::select([
        'id', 'code', 'name', 'type', 'pic', 'status', 'capacity', 'note', 'created_at', 'created_by'
    ]);

    return datatables()->of($warehouses)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    return '
    <div class="relative inline-block text-left">
      <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
       <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1 text-sm text-gray-700">
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail</a>
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-500 hover:text-gray-300"><i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Hapus</button>
          </form>
        </div>
      </div>
    </div>';
})

->editColumn('status', function ($row) {
    if ($row->status === 'active') {
        return '<span class="bg-green-500 text-gray-100 text-xs font-medium px-2.5 py-0.5 rounded">Active</span>';
    } elseif ($row->status === 'inactive') {
        return '<span class="bg-red-500 text-gray-100 text-xs font-medium px-2.5 py-0.5 rounded">Inactive</span>';
    }
    return $row->status;
})
        ->rawColumns(['action','status'])
        ->make(true);
}

private function generateWarehouseCode($type)
{
    // Mapping tipe ke kode singkatan
    $typeMap = [
        'Plant' => 'PT',
        'Warehouse' => 'WH',
        'Transit Area' => 'TA',
        'Department' => 'DT',
    ];

    $typeCode = $typeMap[$type] ?? 'XX'; // fallback untuk type tak dikenal

    // Hitung jumlah data yang sudah ada untuk tipe ini
    $last = DB::table('warehouses')
        ->where('type', $type)
        ->orderBy('id', 'desc')
        ->value('code');

    $number = 1;

    if ($last) {
        // Ambil nomor urut terakhir dari kode (misal dari: LOC-ASN-WH-0005)
        $segments = explode('-', $last);
        $lastNumber = end($segments);
        if (is_numeric($lastNumber)) {
            $number = intval($lastNumber) + 1;
        }
    }

    $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);
    return "LOC-ASN-$typeCode-$formattedNumber";
}

    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'name'     => 'required|string|max:100',
        'type'     => 'required|string|in:Plant,Warehouse,Transit Area,Department',
        'pic'      => 'required|string|max:100',
        'status'   => 'nullable|in:active,inactive',
        'capacity' => 'nullable|string|max:20',
        'note'     => 'nullable|string|max:255',
    ]);

     $code = $this->generateWarehouseCode($request->type);

    // Simpan ke database
    $warehouse = Warehouse::create([
        'code' => $code,
        'name'       => $request->name,
        'type'       => $request->type,
        'pic'        => $request->pic,
        'status'     => $request->status ?? 'active',
        'capacity'   => $request->capacity,
        'note'       => $request->note,
        'created_by' => Auth::id(), // jika kamu pakai sistem login
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Warehouse berhasil disimpan.',
        'data'    => $warehouse
    ]);
}
public function getActiveWarehouses()
{
    $warehouses = Warehouse::where('status', 'active')
        ->select('id', 'name')
        ->get();

    return response()->json($warehouses);
}

}
