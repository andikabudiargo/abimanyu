<?php

namespace App\Http\Controllers;

use App\Models\BackupPlan;
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

class ITBackupScheduleController extends Controller
{
    public function index()
    {
        return view('it.backup-schedule');
    }

    public function select()
{
    $plan = BackupPlan::with('sources','targets')
        ->get()
        ->map(function ($item) {
            return [
                'id'     => $item->id,
                'job_name' => $item->job_name,
                'source' => $item->sources->name ?? '-',   // tampilkan nama storage
                'target' => $item->targets->name ?? '-',   // tampilkan nama storage
                'backup_type' => $item->backup_type ?? '-',   // tampilkan nama storage
            ];
        });

    return response()->json($plan);
}

    public function data(Request $request)
{
    $query = BackupPlan::with(['sources','targets','users']);

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

        ->editColumn('source', fn($row) => optional($row->sources)->name)
        ->editColumn('target', fn($row) => optional($row->targets)->name)
        ->editColumn('pic', fn($row) => optional($row->users)->name)
        ->editColumn('retention_policy', fn($row) => $row->retention_policy ? $row->retention_policy . ' Day' : '-')
        ->editColumn('updated_at', fn($row) => $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '-')


        ->rawColumns(['action'])
        ->make(true);
}

     public function store(Request $request)
    {
        $request->validate([
            'job_name' => 'required|string',
            'backup_type' => 'required|string',
            'source' => 'required|integer|min:1',
            'target' => 'required|integer|min:1',
            'frequency' => 'required|string',
            'retention_policy' => 'required|string',
            'pic' => 'required|integer|min:1',
           
        ]);

        $backupplan = BackupPlan::create($request->all());
        return response()->json($backupplan, 201);
    }
}