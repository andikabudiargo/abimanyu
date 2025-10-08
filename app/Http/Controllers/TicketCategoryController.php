<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Department;
use App\Models\Category;

class TicketCategoryController extends Controller
{
     public function index() {
         $departments = Department::all();
         
    return view('it.category-ticket', compact('departments'));
    }

   public function store(Request $request)
{
    // Validasi input tanpa 'code' karena akan dibuat otomatis
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id',
        'user_id' => 'required|exists:users,id',
        'description' => 'required|string',
    ]);

    // Ambil kode terakhir dari tabel categories
    $lastCode = Category::max('code');

    // Jika belum ada data, mulai dari 001
    if (!$lastCode) {
        $newCode = '001';
    } else {
        // Tambah 1 dari kode terakhir dan format 3 digit
        $newCode = str_pad((int)$lastCode + 1, 3, '0', STR_PAD_LEFT);
    }

    // Masukkan kode baru dan created_by
    $validated['code'] = $newCode;
    $validated['created_by'] = Auth::user()->name ?? 'System';

    // Simpan data
    Category::create($validated);

    return response()->json(['message' => 'Category created successfully', 'code' => $newCode]);
}


    public function data()
{
    $query = Category::with(['department', 'user']);


    return DataTables::of($query)
    ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    // ✅ Buat URL Detail, Edit, Delete sesuai route yang benar
            $detailUrl = '#';    // route detail
            $editUrl = '#';                                           // route edit (belum ada, placeholder)
            $deleteUrl ='#'; // route delete

            $form = '
                <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="w-full text-left text-red-500 px-4 py-2 hover:bg-red-500 hover:text-white">
                        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                    </button>
                </form>';

            return '
                <div class="relative inline-block text-left">
                  <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                   <i data-feather="align-justify"></i>
                  </button>
                  <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1 text-sm text-gray-700">
                      <a href="' . $detailUrl . '" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail</a>
                      <a href="' . $editUrl . '" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
                      ' . $form . '
                    </div>
                  </div>
                </div>';
        })
        ->editColumn('department.name', function ($row) {
        return $row->department->name ?? '-';
        })
        ->editColumn('user.name', function ($row) {
        return $row->user->name ?? '-';
        })
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action'])
        ->make(true);
}

public function getCategoryDropdown()
{
    $categories = Category::with('department')->get();

    $grouped = $categories->groupBy(function ($item) {
        return $item->department->name ?? 'Unknown Department';
    });

    $result = [];

    foreach ($grouped as $deptName => $items) {
         $sortedItems = $items->sortBy('code');
        $result[] = [
            'label' => $deptName,
            'options' =>  $sortedItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'description' => $item->description,
                ];
            })->values()->toArray(), // values() untuk reset index array
        ];
    }

    return response()->json($result);
}


}
