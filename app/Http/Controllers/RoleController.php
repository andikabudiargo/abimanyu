<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
     public function index() {
        return view('setting.role');
    }

     public function data(Request $request)
{
    $role = Role::select([
        'id', 'name','created_at', 'updated_at'
    ]);

    return datatables()->of($role)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
         ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })
        ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    return '
    <div class="relative inline-block text-left">
      <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
       <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1 text-sm text-gray-700">
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail</a>
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-500 hover:text-gray-100"><i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Hapus</button>
          </form>
        </div>
      </div>
    </div>';
})
        ->rawColumns(['action'])
        ->make(true);
}


    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100',
    ]);

    $role = Role::create([
        'name' => $validated['name'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Role created successfully.',
        'data' => $role
    ]);
}

}
