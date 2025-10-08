<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        return view('facility.room');
    }

     public function data(Request $request)
{
    $query = Room::query();

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



        ->addColumn('capacity', function ($row) {
    return $row->capacity . ' Person';
})

        ->rawColumns(['action','capacity'])
        ->make(true);
}

    public function store(Request $request)
    {
        // ✅ Validasi input
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'capacity'  => 'nullable|integer',
            'equipment' => 'nullable|array',
            'location'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Generate kode ruangan otomatis (ROOM001, ROOM002, dst)
        $lastRoom = Room::orderBy('id', 'desc')->first();
        $nextNumber = $lastRoom ? ((int) substr($lastRoom->code, 4)) + 1 : 1;
        $code = 'ROOM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // ✅ Simpan data
        $room = Room::create([
            'code'      => $code,
            'name'      => $request->name,
            'capacity'  => $request->capacity,
            'equipment' => $request->equipment, // otomatis tersimpan sebagai JSON
            'location'  => $request->location,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Room created successfully!',
            'data'    => $room
        ]);
    }

    public function edit($id)
{
    $room = Room::findOrFail($id);

    return response()->json([
        'code' => $room->code,
        'name' => $room->name,
        'capacity' => $room->capacity,
        'location' => $room->location,
         'equipment' => is_array($room->equipment) ? $room->equipment : json_decode($room->equipment ?? '[]'),
    ]);
}

public function update(Request $request, $id)
{
    $room = Room::findOrFail($id);
    $room->update([
        'name' => $request->name,
        'capacity' => $request->capacity,
        'location' => $request->location,
        'equipment' => $request->equipment, // simpan sebagai array atau json
    ]);

    return response()->json(['message' => 'Room updated successfully']);
}

public function destroy($id)
{
    $room = Room::findOrFail($id);
    $room->delete();

    return response()->json(['message' => 'Room deleted successfully']);
}

public function select()
{
    $rooms = Room::select('id', 'name')->orderBy('name')->get();
    return response()->json($rooms);
}


}
