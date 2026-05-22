<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Atk;
use App\Models\AtkAdjustment;
use App\Models\AtkAdjustmentItem;
use App\Models\AtkRequest;
use App\Models\AtkRequestItem;
use App\Models\Department;

class ATKController extends Controller
{

public function index() {

 $departments = Department::orderBy('name')->get(); // ambil semua department
  return view('facility.atk', compact('departments'));

}

public function create() {

  return view('facility.create-atk');

}

public function request(Request $request)
{
    $request->validate([
        'items'          => 'required|array|min:1',
        'items.*.atk_id' => 'required|exists:atks,id',
        'items.*.qty'    => 'required|integer|min:1',
        'notes'          => 'nullable|string|max:1000',
    ], [
        'items.required'          => 'Minimal satu item ATK harus dipilih.',
        'items.*.atk_id.required' => 'Item ATK wajib dipilih.',
        'items.*.atk_id.exists'   => 'Item ATK tidak ditemukan.',
        'items.*.qty.min'         => 'Qty minimal 1.',
    ]);

    // ── Generate request number ───────────────────────────
    $requestNumber = $this->generateRequestNumber();

    // ── Simpan ────────────────────────────────────────────
    DB::transaction(function () use ($request, $requestNumber) {
        $atkRequest = AtkRequest::create([
            'request_number' => $requestNumber,
            'department' => optional(Auth::user()->departments->first())->id ?? '—',
            'status'         => 'submitted',
            'note'           => $request->notes,
            'created_by'     => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            AtkRequestItem::create([
                'atk_request_id' => $atkRequest->id,
                'atk_id'         => $item['atk_id'],
                'qty'            => $item['qty'],
            ]);
        }
    });

    return response()->json([
        'message' => "Request {$requestNumber} berhasil disubmit dan menunggu persetujuan.",
    ], 201);
}

 // ─── Generate Request Number ──────────────────────────────
    private function generateRequestNumber(): string
{
    $prefix = 'ATK';
    $year   = now()->format('Y');
    $month  = (int) now()->format('m');

    // 🔥 mapping bulan ke romawi
    $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    $monthRoman = $romanMonths[$month];

    $last = AtkRequest::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderByDesc('id')
        ->value('request_number');

    $seq = 1;
    if ($last) {
        $parts = explode('-', $last);
        $seq   = (int) end($parts) + 1;
    }

    return sprintf('%s-%s-%s-%04d', $prefix, $year, $monthRoman, $seq);
}

public function dataSummary(Request $request)
{
    $query = AtkRequest::with('departemen')
        ->leftJoin('users as uc', 'uc.id', '=', 'atk_requests.created_by')
        ->leftJoin('users as ua', 'ua.id', '=', 'atk_requests.approved_by')
        ->leftJoin('users as ur', 'ur.id', '=', 'atk_requests.rejected_by')
        ->leftJoin('users as ud', 'ud.id', '=', 'atk_requests.distributed_by')
        ->leftJoin('users as urec', 'urec.id', '=', 'atk_requests.received_by')
        ->select([
            'atk_requests.id',
            'atk_requests.request_number',
            'atk_requests.department',
            'atk_requests.status',
            'atk_requests.note',
            'atk_requests.created_by',
            'atk_requests.created_at',
            'atk_requests.approved_at',
            'atk_requests.rejected_at',
            'atk_requests.distributed_at',
            'atk_requests.received_at',
            'uc.name   as created_by_name',
            'ua.name   as approved_by_name',
            'ur.name   as rejected_by_name',
            'ud.name   as distributed_by_name',
            'urec.name as received_by_name',
        ])
        ->when($request->request_number, fn($q) =>
            $q->where('atk_requests.request_number', 'like', '%' . $request->request_number . '%')
        )
        ->when($request->department, fn($q) =>
            $q->where('atk_requests.department', $request->department)
        )
        ->when($request->status, fn($q) =>
            $q->where('atk_requests.status', $request->status)
        )
        ->when($request->request_date, function ($q) use ($request) {
            if (str_contains($request->request_date, ' to ')) {
                [$start, $end] = explode(' to ', $request->request_date);
                $q->whereBetween('atk_requests.created_at', [
                    trim($start) . ' 00:00:00',
                    trim($end)   . ' 23:59:59',
                ]);
            } else {
                $q->whereDate('atk_requests.created_at', trim($request->request_date));
            }
        })
        ->orderByDesc('atk_requests.created_at');

    return DataTables::of($query)
   ->addColumn('action', function ($row) {

    $authUser  = auth()->user()->loadMissing('roles');
    $authId    = $authUser->id;

    $isCreator = $authId == $row->created_by;
    $isAdminGA = $authUser->roles->contains('name', 'Admin GA');
    $status    = strtolower($row->status);

    $dropdownId = 'dropdown-' . $row->id;
    $detail_url = route('facility.atk.detail', ['id' => $row->id]);
    $edit_url   = route('facility.atk.request.edit', ['id' => $row->id]);

    $actionButtons = '
    <div class="relative inline-block text-left">
      <button type="button"
       data-dropdown-id="' . $dropdownId . '"
        onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
        class="inline-flex justify-center w-full rounded-md px-2 py-1 bg-white text-sm text-gray-700 hover:bg-gray-50">
        <i data-feather="align-justify"></i>
      </button>

      <div id="' . $dropdownId . '" 
        class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
    ';

    // ✅ DEFAULT (selalu muncul)
    $actionButtons .= '
        <a href="' . $detail_url . '" class="block px-4 py-2 hover:bg-gray-100">
            <i data-feather="eye" class="w-4 h-4 inline mr-2"></i> Detail
        </a>

        <button onclick="modalExport(' . $row->id . ')" 
            class="w-full text-left px-4 py-2 text-gray-600 hover:bg-gray-100">
            <i data-feather="printer" class="w-4 h-4 inline mr-2"></i> Print
        </button>
    ';

    // 🔥 CREATOR (submitted)
    if ($status === 'submitted' && $isCreator) {
        $actionButtons .= '
            <a href="' . $edit_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i> Edit
            </a>

            <button onclick="cancelRequest(' . $row->id . ', \'' . $row->request_number . '\')" 
                class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">
                <i data-feather="x-circle" class="w-4 h-4 inline mr-2"></i> Cancel
            </button>
        ';
    }

    // 🔥 ADMIN GA (submitted)
    if ($status === 'submitted' && $isAdminGA) {
        $actionButtons .= '
            <button onclick="approveRequest(' . $row->id . ', \'' . $row->request_number . '\')"
                class="w-full text-left px-4 py-2 text-emerald-600 hover:bg-emerald-100">
                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i> Approve
            </button>

            <button onclick="rejectRequest(' . $row->id . ', \'' . $row->request_number . '\')" 
                class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">
                <i data-feather="x" class="w-4 h-4 inline mr-2"></i> Reject
            </button>
        ';
    }

    $actionButtons .= '</div></div>';

    return $actionButtons;
})

        ->editColumn('request_number', fn($row) =>
            '<span class="font-mono text-xs font-semibold text-gray-700">' . e($row->request_number) . '</span>'
        )

     ->editColumn('department', fn($row) =>
    '<span class="text-xs text-gray-600">'
    . e($row->departemen?->name ?? '—') .
    '</span>'
)

        ->editColumn('status', fn($row) => self::statusBadge($row->status))

        ->addColumn('created_by', fn($row) =>
            self::userCell($row->created_by_name)
        )

        ->editColumn('created_at', fn($row) =>
            self::dateCell($row->created_at)
        )

        ->addColumn('approved_by', fn($row) =>
            self::userCell($row->approved_by_name)
        )

        ->editColumn('approved_at', fn($row) =>
            self::dateCell($row->approved_at)
        )

        ->addColumn('rejected_by', fn($row) =>
            self::userCell($row->rejected_by_name)
        )

        ->editColumn('rejected_at', fn($row) =>
            self::dateCell($row->rejected_at)
        )

        ->rawColumns([
            'action', 'request_number', 'department', 'status',
            'created_by', 'created_at',
            'approved_by', 'approved_at',
            'rejected_by', 'rejected_at',
        ])

        ->make(true);
}

// ─── Shared Helpers (private static) ─────────────────────────────

private static function statusBadge(string $status): string
{
    $map = [
        'submitted'         => ['bg-gray-50 text-gray-600 border-gray-200',   'Submitted'],
        'approved'          => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Approved'],
        'rejected'          => ['bg-red-50 text-red-600 border-red-200',       'Rejected'],
        'distributed'       => ['bg-purple-50 text-purple-600 border-purple-200', 'Distributed'],
        'received'          => ['bg-teal-50 text-teal-700 border-teal-200',    'Received'],
    ];

    [$cls, $label] = $map[$status] ?? ['bg-gray-50 text-gray-500 border-gray-200', ucfirst($status)];

    return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium border ' . $cls . '">'
        . '<span class="w-1.5 h-1.5 rounded-full ' . str_replace('text-', 'bg-', explode(' ', $cls)[1]) . '"></span>'
        . e($label)
        . '</span>';
}

private static function userCell(?string $name): string
{
    if (!$name) return '<span class="text-gray-300">—</span>';

    return '
    <div class="flex items-center gap-1.5">
      <span class="text-xs text-gray-600">' . e($name) . '</span>
    </div>';
}

private static function dateCell(?string $datetime): string
{
    if (!$datetime) {
        return '<span class="text-gray-300">—</span>';
    }

    return '<span class="text-xs text-gray-600">'
         . \Carbon\Carbon::parse($datetime)
            ->locale('id')
            ->translatedFormat('d M Y H:i')
         . '</span>';
}

// Data JSON untuk tabel (dipanggil AJAX)
    public function dataStock()
{
    $atk = Atk::select(
            'atks.id',
            'atks.name',
            'atks.uom',
            'atks.min_stock',
            'atks.initial_stock',
            'atks.photo',
        )

        // ── TOTAL IN (Adjustment IN only) ─────────────────
        ->selectSub(function ($q) {
            $q->from('atk_adjustment_items')
              ->join('atk_adjustments', 'atk_adjustments.id', '=', 'atk_adjustment_items.atk_adjustment_id')
              ->whereColumn('atk_adjustment_items.atk_id', 'atks.id')
              ->whereRaw("LOWER(atk_adjustments.type) = 'in'")
              ->selectRaw('COALESCE(SUM(atk_adjustment_items.qty),0)');
        }, 'total_in')

        // ── TOTAL OUT (Adjustment OUT) ───────────────────
        ->selectSub(function ($q) {
            $q->from('atk_adjustment_items')
              ->join('atk_adjustments', 'atk_adjustments.id', '=', 'atk_adjustment_items.atk_adjustment_id')
              ->whereColumn('atk_adjustment_items.atk_id', 'atks.id')
              ->whereRaw("LOWER(atk_adjustments.type) = 'out'")
              ->selectRaw('COALESCE(SUM(atk_adjustment_items.qty),0)');
        }, 'total_out_adjustment')

        // ── TOTAL OUT (Request APPROVED) ─────────────────
        ->selectSub(function ($q) {
            $q->from('atk_request_items')
              ->join('atk_requests', 'atk_requests.id', '=', 'atk_request_items.atk_request_id')
              ->whereColumn('atk_request_items.atk_id', 'atks.id')
              ->whereRaw("LOWER(atk_requests.status) = 'approved'")
              ->selectRaw('COALESCE(SUM(atk_request_items.qty),0)');
        }, 'total_out_request')

        ->orderBy('name')
        ->get()

        ->map(function ($item) {

            $totalIn  = (int) $item->total_in;
            $outAdj   = (int) $item->total_out_adjustment;
            $outReq   = (int) $item->total_out_request;

            $totalOut = $outAdj + $outReq;

            return [
                'id'            => $item->id,
                'name'          => $item->name,
                'uom'           => $item->uom,
                'min_stock'     => $item->min_stock,
                'initial_stock' => (int) $item->initial_stock,

                'total_in'      => $totalIn,
                'total_out'     => $totalOut,

                // optional debug (kalau mau lihat breakdown)
                // 'total_out_adjustment' => $outAdj,
                // 'total_out_request'    => $outReq,

                'photo_url'     => $item->photo ? Storage::url($item->photo) : null,

                'balance' => (int) $item->initial_stock 
                           + $totalIn 
                           - $totalOut,
            ];
        });

    return response()->json(['data' => $atk]);
}

public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'initial_stock' => 'nullable|integer|min:0',
            'min_stock'     => 'nullable|integer|min:0',
            'uom'           => 'required|string|max:50',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2048 KB = 2 MB
        ], [
            'name.required'     => 'Nama ATK wajib diisi.',
            'photo.image'       => 'File harus berupa gambar.',
            'photo.mimes'       => 'Format foto harus JPG, PNG, atau WEBP.',
            'photo.max'         => 'Ukuran foto maksimal 2 MB.',
        ]);
// Handle upload foto
$photoName = null;

if ($request->hasFile('photo')) {
    $file = $request->file('photo');

    // Ambil nama asli file
    $photoName = $file->getClientOriginalName();

    // Path tujuan (hardcode sesuai request)
    $destinationPath = '/home/abimany3/public_html/atk/' . $request->id;

    // Pastikan folder ada
    if (!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }

    // Pindahkan file ke folder tujuan
    $file->move($destinationPath, $photoName);
}

        $atk = Atk::create([
            'name'          => $validated['name'],
            'initial_stock' => $validated['initial_stock'] ?? 0,
            'min_stock'     => $validated['min_stock'] ?? 0,
            'uom'           => $validated['uom'] ?? null,
            'photo'         => $photoName,
            'created_by'     => Auth::id(),
        ]);

        return response()->json([
            'message' => 'ATK berhasil ditambahkan.',
            'data'    => [
                'id'            => $atk->id,
                'name'          => $atk->name,
                'initial_stock' => $atk->initial_stock,
                'min_stock'     => $atk->min_stock,
                'uom'           => $atk->uom,
                'photo_url'     => $atk->photo ? Storage::url($atk->photo) : null,
            ],
        ], 201);
    }

    public function edit($id)
{
    $atk = Atk::findOrFail($id);

    return response()->json([
        'data' => [
            'id'            => $atk->id,
            'name'          => $atk->name,
            'uom'           => $atk->uom,
            'min_stock'     => $atk->min_stock,
            'initial_stock' => $atk->initial_stock,
            'photo_url'     => $atk->photo ? Storage::url($atk->photo) : null,
        ]
    ]);
}

public function update(Request $request, $id)
{
    $atk = Atk::findOrFail($id);

    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'initial_stock' => 'nullable|integer|min:0',
        'min_stock'     => 'nullable|integer|min:0',
        'uom'           => 'nullable|string|max:50',
        'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ], [
        'name.required' => 'Nama ATK wajib diisi.',
        'photo.image'   => 'File harus berupa gambar.',
        'photo.mimes'   => 'Format foto harus JPG, PNG, atau WEBP.',
        'photo.max'     => 'Ukuran foto maksimal 2 MB.',
    ]);

    // Handle foto baru
    if ($request->hasFile('photo')) {
        // Hapus foto lama jika ada
        if ($atk->photo && Storage::disk('public')->exists($atk->photo)) {
            Storage::disk('public')->delete($atk->photo);
        }
        $file      = $request->file('photo');
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $validated['photo'] = $file->storeAs('atk/photos', $filename, 'public');
    } else {
        // Jaga foto lama, jangan di-overwrite
        unset($validated['photo']);
    }

    $atk->update($validated);

    return response()->json([
        'message' => 'ATK berhasil diperbarui.',
        'data'    => [
            'id'            => $atk->id,
            'name'          => $atk->name,
            'uom'           => $atk->uom,
            'min_stock'     => $atk->min_stock,
            'initial_stock' => $atk->initial_stock,
            'photo_url'     => $atk->photo ? Storage::url($atk->photo) : null,
        ]
    ]);
}

public function destroy($id)
{
    $atk = Atk::findOrFail($id);

    // Hapus foto dari storage jika ada
    if ($atk->photo && Storage::disk('public')->exists($atk->photo)) {
        Storage::disk('public')->delete($atk->photo);
    }

    $atk->delete();

    return response()->json([
        'message' => 'ATK berhasil dihapus.',
    ]);
}

public function adjustment(Request $request)
{
    $request->validate([
        'type'            => 'required|in:in,out',
        'reason'          => 'nullable|string|max:255',
        'items'           => 'required|array|min:1',
        'items.*.atk_id'  => 'required|exists:atks,id',
        'items.*.qty'     => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($request) {

        // ✅ 1. Create Header
        $adjustment = AtkAdjustment::create([
            'type'   => $request->type,
            'reason' => $request->reason,
            'created_by' => auth()->id(), // optional tapi best practice
        ]);

        // ✅ 2. Insert Items
        foreach ($request->items as $item) {
            AtkAdjustmentItem::create([
                'atk_adjustment_id' => $adjustment->id,
                'atk_id'        => $item['atk_id'],
                'qty'           => $item['qty'],
            ]);
        }

        // (optional) 3. Update stock langsung di sini kalau mau
    });

    return response()->json([
        'message' => 'Adjustment stock berhasil disimpan.'
    ]);
}

public function movements(Request $request, $id)
{
    $atk = Atk::findOrFail($id);
    $openingStock = (int) ($atk->initial_stock ?? 0);

    // ── Adjustment movements ─────────────────────────────
    $adjustments = DB::table('atk_adjustment_items')
        ->join('atk_adjustments', 'atk_adjustments.id', '=', 'atk_adjustment_items.atk_adjustment_id')
        ->leftJoin('users as u', 'u.id', '=', 'atk_adjustments.created_by')
        ->where('atk_adjustment_items.atk_id', $id)
        ->select([
            'atk_adjustment_items.id',
            DB::raw("LOWER(atk_adjustments.type) as type"),
            'atk_adjustment_items.qty',
            'atk_adjustments.created_at',
            'atk_adjustments.reason',
            'u.name as distributed_by',
            DB::raw("NULL as received_by"),
            DB::raw("'adjustment' as source"),
            DB::raw("0 as priority"), // adjustment duluan jika timestamp sama
        ])
        ->get();

    // ── Request movements (ONLY APPROVED) ────────────────
    $requests = collect();

    if (Schema::hasTable('atk_request_items')) {
        $requests = DB::table('atk_request_items')
            ->join('atk_requests', 'atk_requests.id', '=', 'atk_request_items.atk_request_id')
            ->leftJoin('users as ud', 'ud.id', '=', 'atk_requests.approved_by')
            ->leftJoin('users as ur', 'ur.id', '=', 'atk_requests.created_by')
            ->where('atk_request_items.atk_id', $id)
            ->whereRaw("LOWER(atk_requests.status) = 'approved'")
            ->select([
                'atk_request_items.id',
                DB::raw("'out' as type"),
                'atk_request_items.qty',
                'atk_requests.approved_at as created_at', // pakai approved_at bukan created_at
                DB::raw("NULL as reason"),
                'ud.name as distributed_by',
                'ur.name as received_by',
                DB::raw("'request' as source"),
                DB::raw("1 as priority"), // request belakangan jika timestamp sama
            ])
            ->get();
    }

    // ── Merge & Sort ─────────────────────────────────────
    $rows = $adjustments
        ->concat($requests)
        ->sort(function ($a, $b) {
            $timeA = strtotime($a->created_at);
            $timeB = strtotime($b->created_at);

            if ($timeA !== $timeB) {
                return $timeA <=> $timeB;
            }

            // Timestamp sama → adjustment (priority=0) duluan dari request (priority=1)
            return $a->priority <=> $b->priority;
        })
        ->values();

    // ── Running Balance ───────────────────────────────────
    $runningBalance = $openingStock;

    $result = $rows->map(function ($row) use (&$runningBalance) {
        $qty  = (int) $row->qty;
        $type = strtolower($row->type);

        $stock_awal = $runningBalance;

        if ($type === 'in') {
            $runningBalance += $qty;
        } elseif ($type === 'out') {
            $runningBalance -= $qty;
        }

        return [
            'id'             => $row->id,
            'date'           => \Carbon\Carbon::parse($row->created_at)->format('Y-m-d'),
            'time'           => \Carbon\Carbon::parse($row->created_at)->format('H:i:s'),
            'type'           => strtoupper($type),
            'source'         => $row->source,
            'qty'            => $qty,
            'stock_awal'     => $stock_awal,
            'balance'        => $runningBalance,
            'reason'         => $row->reason ?? null,
            'distributed_by' => $row->distributed_by ?? null,
            'received_by'    => $row->received_by ?? null,
        ];
    });

    return response()->json([
        'data' => $result,
        'summary' => [
            'opening_balance' => $openingStock,
            'ending_balance'  => $runningBalance,
        ],
    ]);
}

public function movementsExport(Request $request, $id)
{
    $atk  = Atk::findOrFail($id);
    $res  = $this->movements($request, $id); // reuse method
    $rows = json_decode($res->getContent(), true)['data'] ?? [];

    // Filter
    if ($request->start_date) $rows = array_filter($rows, fn($r) => $r['date'] >= $request->start_date);
    if ($request->end_date)   $rows = array_filter($rows, fn($r) => $r['date'] <= $request->end_date);
    if ($request->type)       $rows = array_filter($rows, fn($r) => $r['type'] === $request->type);
    if ($request->source)     $rows = array_filter($rows, fn($r) => $r['source'] === $request->source);
    $rows = array_values($rows);

    // Rolling balance
    $initial = $atk->initial_stock ?? 0;
    $running = $initial;
    foreach ($rows as &$row) {
        $row['opening_stock'] = $running;
        $running = $row['type'] === 'in' ? $running + $row['qty'] : $running - $row['qty'];
        $row['balance'] = $running;
    }

    // Build CSV (tanpa library tambahan)
    $filename = 'movement_' . Str::slug($atk->name) . '_' . now()->format('Ymd_His') . '.csv';
    $headers  = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    return response()->streamDownload(function () use ($rows) {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['#', 'Tanggal', 'Type', 'Source', 'Stock Awal', 'Qty', 'Balance', 'Distributed By', 'Received By', 'Catatan']);
        foreach ($rows as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['date'],
                strtoupper($row['type']),
                ucfirst($row['source']),
                $row['opening_stock'],
                ($row['type'] === 'in' ? '+' : '-') . $row['qty'],
                $row['balance'],
                $row['distributed_by'] ?? '—',
                $row['received_by']    ?? '—',
                $row['reason']         ?? '—',
            ]);
        }
        fclose($out);
    }, $filename, $headers);
}

public function cancel($id)
{
    try {
        DB::beginTransaction();

        $request = AtkRequest::with('items')->findOrFail($id);

        // 🔒 Optional: permission
        if ($request->created_by !== Auth::id()) {
            return response()->json([
                'message' => 'Tidak punya akses.'
            ], 403);
        }

        // 🔥 Hapus detail dulu (jika relasi tidak cascade)
        $request->items()->delete();

        // 🔥 Hapus header
        $request->delete();

        DB::commit();

        return response()->json([
            'message' => 'Request berhasil dihapus.'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Gagal hapus request.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function approve($id)
{
    try {
        DB::beginTransaction();

        $request = AtkRequest::with('items')->findOrFail($id);

        // 🔒 Validasi status
        if ($request->status !== 'submitted') {
            return response()->json([
                'message' => 'Request tidak bisa di-approve.'
            ], 400);
        }

        // ✅ Update status
        $request->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Request berhasil di-approve.'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Gagal approve request.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function reject(Request $req, $id)
{
    $req->validate([
        'reason' => 'required|string|max:1000'
    ]);

    try {
        DB::beginTransaction();

        $request = AtkRequest::findOrFail($id);

        // 🔒 Validasi status
        if ($request->status !== 'submitted') {
            return response()->json([
                'message' => 'Request tidak bisa direject.'
            ], 400);
        }

        // ✅ Update status
        $request->update([
            'status'        => 'rejected',
            'rejected_by'   => Auth::id(),
            'rejected_at'   => now(),
            'rejected_reason' => $req->reason,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Request berhasil direject.'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Gagal reject request.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function editRequest($id)
{
    $atkRequest = AtkRequest::with('items')->findOrFail($id);

    // Pastikan hanya creator yang bisa edit, dan status masih submitted
    if ($atkRequest->created_by !== auth()->id() || strtolower($atkRequest->status) !== 'submitted') {
        abort(403, 'Tidak diizinkan.');
    }

    return view('facility.edit-atk', compact('atkRequest'));
}

public function updateRequest(Request $request, $id)
{
    $atkRequest = AtkRequest::with('items')->findOrFail($id);

    if ($atkRequest->created_by !== auth()->id() || strtolower($atkRequest->status) !== 'submitted') {
        return response()->json(['message' => 'Tidak diizinkan.'], 403);
    }

    // Hapus items lama, insert baru
    $atkRequest->items()->delete();
    foreach ($request->items as $item) {
        $atkRequest->items()->create([
            'atk_id' => $item['atk_id'],
            'qty'    => $item['qty'],
        ]);
    }

    $atkRequest->update(['note' => $request->notes]);

    return response()->json(['message' => 'Request berhasil diperbarui.']);
}

public function show($id)
{
    $atkRequest = AtkRequest::with([
        'items.atk',
        'createdBy',
        'approvedBy',
        'rejectedBy',
        'distributedBy',
        'receivedBy',
        'departemen',
    ])->findOrFail($id);

    auth()->user()->load('roles');

    return view('facility.detail-atk', compact('atkRequest'));
}

public function analyticsData()
{
    $currentYear = now()->year;

    // ── 1. Request per Department ─────────────────────────
    $byDepartment = AtkRequest::with('departemen')
        ->select('department', DB::raw('COUNT(*) as total'))
        ->groupBy('department')
        ->get()
        ->map(fn($r) => [
            'department' => $r->departemen?->name ?? 'Unknown',
            'total'      => $r->total,
        ]);

    // ── 2. Top 5 ATK yang sering direquest ───────────────
    $topAtk = AtkRequestItem::with('atk')
        ->select('atk_id', DB::raw('SUM(qty) as total_qty'), DB::raw('COUNT(*) as total_request'))
        ->groupBy('atk_id')
        ->orderByDesc('total_request')
        ->limit(5)
        ->get()
        ->map(fn($r) => [
            'name'          => $r->atk?->name ?? '—',
            'uom'           => $r->atk?->uom ?? '—',
            'photo_url'     => $r->atk?->photo ? Storage::url($r->atk->photo) : null,
            'total_request' => $r->total_request,
            'total_qty'     => $r->total_qty,
        ]);

    // ── 3. Request bulanan per status (current year) ─────
    $monthly = AtkRequest::select(
            DB::raw('MONTH(created_at) as month'),
            'status',
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy('month', 'status')
        ->get();

    $monthlyData = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthlyData[$m] = ['submitted' => 0, 'approved' => 0, 'rejected' => 0];
    }
    foreach ($monthly as $row) {
        $status = strtolower($row->status);
        if (isset($monthlyData[$row->month][$status])) {
            $monthlyData[$row->month][$status] = $row->total;
        }
    }

    return response()->json([
        'by_department' => $byDepartment,
        'top_atk'       => $topAtk,
        'monthly'       => $monthlyData,
        'year'          => $currentYear,
    ]);
}

}