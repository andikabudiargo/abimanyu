<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\BackupLog;
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

class ITBackupController extends Controller
{
    public function index()
    {
        return view('it.backup');
    }

    public function store(Request $request)
{
    $request->validate([
        'backup_plan_id' => 'required|integer|min:1',
        'backup_date'    => 'required|date',
        'status'         => 'required|string',
        'start_time'     => 'required|date_format:H:i',
        'end_time'       => 'required|date_format:H:i',
        'final_size'     => 'required|numeric|between:0,999999.99',
        'evidence'       => 'required|image|mimes:jpg,jpeg,png|max:5120', // max 2MB
        'remark'         => 'nullable|string',
    ]);

    $evidencePath = null;
    if ($request->hasFile('evidence')) {
        $file = $request->file('evidence');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('backup_evidence', $filename, 'public');
        $evidencePath = $path;
    }

    $backupLog = BackupLog::create([
        'backup_plan_id' => $request->backup_plan_id,
        'status' => $request->status,
        'backup_date' => $request->backup_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'final_size' => $request->final_size,
        'evidence' => $evidencePath,
        'remark' => $request->remark,
        'created_by' => Auth::id(),
    ]);

    return response()->json($backupLog, 201);
}

public function data(Request $request)
{
    $query = BackupLog::with(['plans','users']);

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

        ->editColumn('backup_plan_id', fn($row) => optional($row->plans)->job_name)
        ->editColumn('created_by', fn($row) => optional($row->users)->name)
        ->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-')
        ->editColumn('updated_at', fn($row) => $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '-')


        ->rawColumns(['action'])
        ->make(true);
}

}