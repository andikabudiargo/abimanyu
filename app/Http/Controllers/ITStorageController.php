<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\BookingRoom;
use App\Models\CancelBookingRoom;
use App\Mail\BookingRoomRequest;
use App\Mail\BookingRoomApproved;
use App\Mail\BookingRoomCancelled;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ITStorageController extends Controller
{
    public function index()
    {
        return view('it.storage');
    }

    public function select()
{
    $storage = Storage::select('id', 'name')
        ->get();

    return response()->json($storage);
}

      public function data(Request $request)
{
    $query = Storage::query();

    if ($request->filled('code')) {
        $query->where('code', 'like', '%' . $request->code . '%');
    }
    if ($request->filled('capacity')) {
        $query->where('capacity', $request->capacity);
    }
    if ($request->filled('location')) {
        $query->where('location', $request->location);
    }
   if ($request->has('equipment') && $request->equipment != '') {
    $equipment = $request->equipment; // misal "TV/Monitor"
    $query->whereRaw("JSON_CONTAINS(equipment, ?)", [json_encode($equipment)]);
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



       ->editColumn('status', function ($row) {
    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    // Antisipasi division by zero
    $capacity = $row->capacity > 0 ? $row->capacity : 1;
    $percentage = ($row->used / $capacity) * 100;

    if ($row->used == 0) {
        return '<span class="bg-gray-500 ' . $commonClasses . '">Empty</span>';
    } elseif ($percentage < 80) {
        return '<span class="bg-green-500 ' . $commonClasses . '">Normal</span>';
    } elseif ($percentage >= 80 && $percentage < 100) {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Warning</span>';
    } elseif ($percentage >= 100) {
        return '<span class="bg-red-500 ' . $commonClasses . '">Full</span>';
    }
})

->editColumn('name', function ($row) {
    $capacity = $row->capacity > 0 ? $row->capacity : 1;
    $used = $row->used;
    $free = max($capacity - $used, 0);
    $percentage = ($used / $capacity) * 100;

    // Tentukan warna
    if ($used == 0) {
        $color = 'bg-gray-500';
    } elseif ($percentage < 80) {
        $color = 'bg-green-500';
    } elseif ($percentage >= 80 && $percentage < 100) {
        $color = 'bg-yellow-500';
    } else {
        $color = 'bg-red-500';
    }

  return '
  <div class="w-full">
    <!-- Nama Storage -->
    <div class="text-sm font-semibold text-gray-800 mb-1">
      '.$row->name.'
    </div>

    <!-- Icon + Progress Bar -->
    <div class="flex items-center gap-2 mb-1">
        <!-- Icon -->
        <div class="flex-shrink-0 text-gray-600">
            <i data-feather="hard-drive" class="w-5 h-5"></i>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 h-6 relative">
            <div class="'.$color.' h-6" style="width:'.$percentage.'%"></div>
            <span class="absolute right-1 top-1 text-xs font-semibold text-gray-800">
                '.$used.' GB Used
            </span>
        </div>
    </div>

    <!-- Free of Total -->
    <div class="text-xs text-gray-600">
      '.$free.' GB free of '.$capacity.' GB
    </div>
  </div>
';


})

->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })


        ->rawColumns(['action','status','name'])
        ->make(true);
}


    public function store(Request $request)
    {
        $request->validate([
            'storage_type' => 'required|string',
            'category' => 'required|string',
            'name' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'used' => 'nullable|integer|min:0',
           
        ]);

        $storage = Storage::create($request->all());
        return response()->json($storage, 201);
    }

    public function show(Storage $storage)
    {
        return response()->json($storage);
    }

    public function update(Request $request, Storage $storage)
    {
        $request->validate([
            'storage_type' => 'sometimes|string',
            'category' => 'sometimes|string',
            'name' => 'sometimes|string',
            'capacity' => 'sometimes|integer|min:1',
            'used' => 'sometimes|integer|min:0',

        ]);

        $storage->update($request->all());
        return response()->json($storage);
    }

    public function destroy(Storage $storage)
    {
        $storage->delete();
        return response()->json(null, 204);
    }
}