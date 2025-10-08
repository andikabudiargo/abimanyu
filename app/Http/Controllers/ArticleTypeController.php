<?php

namespace App\Http\Controllers;

use App\Models\ArticleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ArticleTypeController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('inventory.article-type');
    }

    public function getData(Request $request)
{
   $type = ArticleType::select([
        'article_types.id',
        'article_types.code',
        'article_types.name',
        'warehouses.name as warehouse_name',
        'article_types.note',
        'article_types.created_at',
        'article_types.created_by'
    ])
    ->leftJoin('warehouses', 'article_types.warehouse_id', '=', 'warehouses.id');

    return datatables()->of($type)
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
         <a href="javascript:void(0)" class="block px-4 py-2 hover:bg-gray-100 editTypeBtn" data-id="' . $id . '">
         <i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <a href="javascript:void(0)" class="block px-4 py-2 hover:bg-red-500 hover:text-gray-100 text-red-700 deleteTypeBtn" data-id="' . $id . '">
  <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Hapus
</a>
        </div>
      </div>
    </div>';
})
        ->addColumn('warehouse_id', fn($row) => $row->warehouse_name ?? '-')
        ->rawColumns(['action'])
        ->make(true);
}

    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'name'     => 'required|string|max:100',
        'code'     => 'required|string|max:10',
        'warehouse_id' => 'nullable|exists:warehouses,id',
        'note'     => 'nullable|string|max:255',
    ]);

    // Simpan ke database
    $type = ArticleType::create([
        'code'       => $request->code,
        'name'       => $request->name,
        'warehouse_id' => $request->warehouse_id,
        'note'       => $request->note,
        'created_by' => Auth::id(), // jika kamu pakai sistem login
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Article Type berhasil disimpan.',
        'data'    => $type
    ]);
}

public function select()
{
    $type = ArticleType::select('id', 'code', 'name')->get();
    return response()->json($type);
}

public function show($id)
{
    $type = ArticleType::select('id', 'code', 'name', 'warehouse_id', 'note')
        ->findOrFail($id);

    return response()->json($type);
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'note' => 'nullable|string',
        'warehouse_id' => 'nullable|exists:warehouses,id',
    ]);

    $articleType = ArticleType::findOrFail($id);
    $articleType->name = $validated['name'];
    $articleType->note = $validated['note'];
    $articleType->warehouse_id = $validated['warehouse_id'];
    $articleType->save();

    return response()->json(['message' => 'Article Type updated successfully']);
}

public function destroy($id)
{
    $type = ArticleType::findOrFail($id);
    $type->delete();

    return response()->json(['message' => 'Data berhasil dihapus']);
}



}
