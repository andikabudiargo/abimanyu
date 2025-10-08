<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionDefect;
use App\Models\Supplier;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InspectionController extends Controller
{
      public function index() {
    $suppliers = Supplier::orderBy('name')->get();
    $articles = Article::whereIn('article_type', ['RMP', 'RMNP', 'FG'])
                       ->orderBy('description')
                       ->get();

    return view('qc.daily-inspection', compact('suppliers', 'articles'));
}


     public function data(Request $request)
{
    $query = Inspection::with(['user', 'article', 'supplier']);

    if ($request->inspection_number) {
        $query->where('inspection_number', 'like', '%' . $request->inspection_number . '%');
    }

    if ($request->inspection_post) {
        $query->where('inspection_post', $request->inspection_post);
    }

    if ($request->inspection_date) {
        [$start, $end] = explode(' to ', $request->inspection_date);
        $query->whereBetween('inspection_date', [$start, $end]);
    }

   if ($request->supplier_code) {
    $query->whereHas('supplier', function ($q) use ($request) {
        $q->where('code', $request->supplier_code);
    });
}


if ($request->part_name) {
    $query->whereHas('article', function ($q) use ($request) {
        $q->where('description', 'like', '%' . $request->part_name . '%');
    });
}

$query->orderBy('created_at', 'desc');

 return DataTables::of($query)
    // Tambahkan kolom berwarna untuk inspection_post
    ->editColumn('inspection_post', function ($row) {
        $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

        return match ($row->inspection_post) {
            'Incoming' => '<span class="bg-yellow-500 '.$commonClasses.'">Incoming</span>',
            'Unloading' => '<span class="bg-red-500 '.$commonClasses.'">Unloading</span>',
            'Buffing' => '<span class="bg-blue-500 '.$commonClasses.'">Buffing</span>',
            'Touch Up' => '<span class="bg-green-500 '.$commonClasses.'">Touch Up</span>',
            'Final' => '<span class="bg-teal-400 '.$commonClasses.'">Final</span>',
            default => '-'
        };
    })

    ->editColumn('part_name', function ($row) {
        $code = $row->part_name;
        $name = optional($row->article)->description ?? '-';
        return "<span class='font-semibold text-gray-800'>{$code}</span><br><span class='text-sm text-gray-500'>{$name}</span>";
    })

    ->editColumn('user_id', function ($row) {
        return optional($row->user)->name ?? '-';
    })

    ->editColumn('total_check', fn($row) => '<span class="font-semibold text-gray-800">'.$row->total_check.'</span>')
    ->editColumn('total_ok', fn($row) => '<span class="text-green-600 font-semibold">'.$row->total_ok.'</span>')
    ->editColumn('total_ok_repair', fn($row) => '<span class="text-yellow-600 font-semibold">'.$row->total_ok_repair.'</span>')
    ->editColumn('total_ng', fn($row) => '<span class="text-red-600 font-semibold">'.$row->total_ng.'</span>')

   // Tambahkan kolom persentase
->addColumn('pass_rate', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $passRate = ($row->total_ok / $totalCheck) * 100;
    return '<span class="text-green-600 font-semibold">' . number_format($passRate, 0) . '%</span>';
})

->addColumn('ok_repair_rate', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $okRepairRate = ($row->total_ok_repair / $totalCheck) * 100;
    return '<span class="text-yellow-600 font-semibold">' . number_format($okRepairRate, 0) . '%</span>';
})

->addColumn('ng_rate', function ($row) {
    $totalCheck = $row->total_check ?: 1;
    $ngRate = ($row->total_ng / $totalCheck) * 100;
    return '<span class="text-red-600 font-semibold">' . number_format($ngRate, 0) . '%</span>';
})

 ->editColumn('inspection_number', function ($row) {
    $colorClass = '';
    switch ($row->inspection_post) {
        case 'Incoming':
            $colorClass = 'bg-yellow-500';
            break;
        case 'Unloading':
            $colorClass = 'bg-red-500';
            break;
        case 'Buffing':
            $colorClass = 'bg-blue-500';
            break;
        case 'Touch Up':
            $colorClass = 'bg-green-500';
            break;
        case 'Final':
            $colorClass = 'bg-teal-400';
            break;
        default:
            $colorClass = 'bg-gray-300';
    }

    return '<span class="' . $colorClass . ' text-white text-xs font-medium px-2 py-1 rounded">' . $row->inspection_number . '</span>';
})


    ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i'))

    ->addColumn('action', function ($row) {
        $id = $row->id;
        $number = $row->inspection_number;
        $dropdownId = 'dropdown-' . $row->id;
        $detail_url = route('qc.inspections.show', ['id' => $row->id]);
        $delete_url = route('qc.inspections.destroy', $row->id);

        return '
        <div class="relative inline-block text-left">
            <button type="button"
                data-dropdown-id="' . $dropdownId . '"
                onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
                class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                <i data-feather="align-justify"></i>
            </button>
            <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
                <div class="py-1 text-sm text-gray-700">
                    <a href="' . $detail_url . '" class="block px-4 py-2 hover:bg-gray-100">
                        <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
                    </a>
                    <button 
                        type="button" 
                        class="btn-delete-inspection w-full text-left px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
                        data-url="' . $delete_url . '"
                        data-id="'. $id . '"
                        data-number="'. $number . '">
                        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>';
    })

    ->rawColumns([
        'inspection_number', 'inspection_post', 'part_name', 'user_id', 'total_check', 
        'total_ok', 'total_ok_repair', 'total_ng', 
        'pass_rate', 'ng_rate', 'ok_repair_rate', 'action'
    ])
    ->make(true);


}

     public function unloading() {
        return view('qc.unloading');
    }

     public function create() {
         $suppliers = Supplier::orderBy('name')->get();
        return view('qc.create-daily-inspection', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspection_post' => 'required|string',
            'part_name' => 'required|string',
            'total_check' => 'required|integer',
            'check_method' => 'nullable|string',
            'defects' => 'nullable|array',
            'defects.*.defect' => 'required|string',
            'defects.*.qty' => 'required|integer',
            'defects.*.ok_repair' => 'nullable|integer',
            'defects.*.note_defect' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       $inspection_number = $this->generateInspectionNumber($request->inspection_post, $request->shift, $request->inspection_date ?? now()->toDateString());


        $inspection = Inspection::create([
            'inspection_number' => $inspection_number, // ✅ tambahkan ini
            'user_id' => Auth::id(),
            'shift' => $request->shift,
            'inspection_date' => $request->inspection_date ?? now()->toDateString(),
            'part_name' => $request->part_name,
            'supplier_code' => $request->supplier_code,
            'inspection_post' => $request->inspection_post,
            'check_method' => $request->check_method,
            'note' => $request->note,
            'qty_received' => $request->qty_received,
            'total_check' => $request->total_check,
            'total_ok' => $request->total_ok ?? 0,
            'total_ok_repair' => $request->total_ok_repair ?? 0,
            'total_ng' => $request->total_ng ?? 0,
        ]);

   // Simpan defects jika ada
if ($request->defect_id) {
    foreach ($request->defect_id as $i => $defectId) {
        if ($defectId && $request->qty[$i]) {
            InspectionDefect::create([
                'inspection_id' => $inspection->id,
                'defect_id' => $defectId,
                'qty' => $request->qty[$i],
                'ok_repair' => $request->ok_repair[$i] ?? 0,
                'note_defect' => $request->note[$i] ?? null,
            ]);
        }
    }
}

        return response()->json(['message' => 'Inspection saved successfully']);
    }

    function generateInspectionNumber($inspection_post, $shift, $inspection_date)
{
    $prefix = 'QC';
    $romawiBulan = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];

    $postMapping = [
        'INCOMING'  => 'I',
        'UNLOADING' => 'U',
        'BUFFING'   => 'B',
        'TOUCH UP'  => 'T',
        'FINAL'     => 'F'
    ];

    $shiftMapping = [
        'Shift 1' => 'S1',
        'Shift 2' => 'S2',
        'Shift 3' => 'S3'
    ];

    $inspection_post = strtoupper(trim($inspection_post));
    $code = $postMapping[$inspection_post] ?? 'X';
    $shiftCode = $shiftMapping[$shift] ?? strtoupper($shift); // fallback jika shift tidak dikenal

    $bulan = (int) date('m', strtotime($inspection_date));
    $tahun = date('Y', strtotime($inspection_date));
    $bulanRomawi = $romawiBulan[$bulan];

    // Hitung jumlah existing inspection pada hari dan shift yang sama
    $count = Inspection::where('inspection_date', $inspection_date)
        ->where('inspection_post', $inspection_post)
        ->where('shift', $shift)
        ->count();

    $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return "{$prefix}{$code}-ASN-{$tahun}-{$bulanRomawi}-{$shiftCode}-{$sequence}";
}

public function getInspectionNumbers(Request $request)
{

    \Log::info('Incoming getInspectionNumbers request', $request->all()); // cek request masuk
    $year = $request->input('year');
    $month = $request->input('month');
    $supplier = $request->input('supplier_code');
    $articleCode = $request->input('article_code');

     \Log::info("Filter: year=$year, month=$month, supplier=$supplier, article=$articleCode");

   $inspections = Inspection::with(['inspection_defects.defect'])
    ->select('id', 'inspection_number', 'inspection_date', 'qty_received', 'total_ok', 'total_check', 'total_ng', 'total_ok_repair')
    ->where('inspection_post', 'Incoming')
    ->whereYear('inspection_date', $year)
    ->whereMonth('inspection_date', $month)
    ->where('supplier_code', $supplier)
    ->when($articleCode, function ($query) use ($articleCode) {
        $query->where('part_name', $articleCode); // hanya filter kalau diisi
    })
    ->orderByDesc('inspection_date')
    ->get();

    return response()->json($inspections);
}


 public function show($id)
{
   $inspection = Inspection::with('supplier','article', 'inspection_defects')->findOrFail($id);
    return view('qc.detail-daily-inspection', compact('inspection'));
}

public function destroy($id)
{
    $inspection = Inspection::findOrFail($id);
    $inspection->delete();

    return redirect()->route('qc.inspections.index')->with('success', 'Inspection berhasil dihapus');
}





}
