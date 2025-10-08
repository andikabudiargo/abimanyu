<?php

namespace App\Http\Controllers;

use App\Models\GroupMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GroupMaterialController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('inventory.group-material');
    }

      public function select()
{
    $gom = GroupMaterial::select('id', 'code')
        ->get();

    return response()->json($gom);
}

     public function data(Request $request)
{
   $query = GroupMaterial::with(['user']);

    if ($request->inspection_number) {
        $query->where('inspection_number', 'like', '%' . $request->inspection_number . '%');
    }

    if ($request->inspection_post) {
        $query->where('inspection_post', $request->inspection_post);
    }

    return DataTables::of($query)
        ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $id;

    $actionButtons = '
    <div class="relative inline-block text-left">
      <button type="button"
        data-dropdown-id="' . $dropdownId . '"
        onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
        class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
        <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
        <a href="#" onclick="openEditModal(' . $id . ')" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
        </a>
         <a href="#" onclick="deleteRoom(' . $id . ')" class="block px-4 py-2 text-red-500 hover:bg-red-400 hover:text-white">
            <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
        </a>
      </div>
    </div>';

    return $actionButtons;
})

->addColumn('department', function ($row) {
    if ($row->user && $row->user->departments->isNotEmpty()) {
        return $row->user->departments->first()->name; // ambil 1 saja
    }
    return '-';
})

 ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })

        ->rawColumns(['action', 'department'])
        ->make(true);
}

     public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|max:50|unique:group_materials,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            // Simpan data ke database
            $group = GroupMaterial::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'created_by'  => auth()->id(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Group of Material succesfully saved.',
                'data' => $group
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}