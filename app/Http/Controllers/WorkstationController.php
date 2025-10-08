<?php

namespace App\Http\Controllers;

use App\Models\Workstation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkstationController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('production.workstation');
    }

      public function select()
{
    $gom = Workstation::select('id', 'code')
        ->get();

    return response()->json($gom);
}

     public function data(Request $request)
{
   $query = Workstation::with(['pics']);

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

->addColumn('pic', function ($row) {
        return optional($row->pics)->name ?? '-';
})

 ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })

        ->rawColumns(['action', 'pic'])
        ->make(true);
}

     public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'code' => 'required|string|max:50|unique:workstations,code',
            'plant' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'pic' => 'required|numeric',
        ]);

        try {
            // Simpan data ke database
            $work = Workstation::create([
                'code' => $request->code,
                'plant' => $request->plant,
                'name' => $request->name,
                'pic' => $request->pic,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Workstation succesfully saved.',
                'data' => $work
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}