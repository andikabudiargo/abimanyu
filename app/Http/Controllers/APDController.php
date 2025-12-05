<?php

    namespace App\Http\Controllers;
    
    
    use Illuminate\Http\Request;
    use App\Models\Employee;
    use App\Models\Apd;
    use App\Models\APDReturn;
    use App\Models\APDReturnItem;
    use App\Models\Department;
     use App\Models\APDAdjustment;
    use App\Models\APDAdjustmentItem;
    use App\Models\APDDistribution;
    use App\Models\APDDistributionItem;
    use Carbon\Carbon;
use App\Mail\APDReminderMail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;


class APDController extends Controller
{
    public function index(Request $request)
    {

          $typeEmployee = $request->input('typeEmployee');
       $employees = Employee::with(['positions', 'distributions'])
    ->when($typeEmployee == 1, function ($q) {
        $q->whereIn('employee_type', ['Tetap', 'Kontrak']);
    })
    ->when($typeEmployee == 2, function ($q) {
        $q->where('employee_type', 'Outsourcing');
    })
    ->where('employee_type', '!=', 'PKL') // jika tetap ingin PKL dikecualikan
    ->orderBy('nik', 'asc')
    ->get();



            $departments = Department::orderBy('id', 'asc')->get();

      $filterYear  = $request->input('tahun');
$filterMonth = $request->input('bulan');

if ($filterYear && $filterMonth) {
    $startDate = "{$filterYear}-{$filterMonth}-01";
    $endDate   = date("Y-m-t", strtotime($startDate));
} elseif ($filterYear) {
    $startDate = "{$filterYear}-01-01";
    $endDate   = "{$filterYear}-12-31";
} else {
    $startDate = null;
    $endDate   = null;
}

$apds = Apd::orderByRaw("
        CASE
            WHEN conditions = 'Baru' THEN 1
            WHEN conditions = 'Bekas' THEN 2
            WHEN conditions = 'Rusak' THEN 3
            ELSE 4
        END
    ")
    ->orderBy('name','asc')
    ->get();
$apds = $apds->map(function($apd) use ($startDate, $endDate, $filterYear, $filterMonth) {

    // --- Tentukan awal & akhir data APD untuk loop ---
    $firstDate = $apd->created_at 
        ?? APDAdjustmentItem::join('apd_adjustments','apd_adjustment_items.apd_adjustment_id','=','apd_adjustments.id')
            ->where('apd_adjustment_items.apd_id', $apd->id)
            ->min('apd_adjustments.adjustment_date');

    $lastDate = $endDate ?? now()->format('Y-m-d');

    // --- Hitung initial_stock_filtered sebelum filter ---
    if ($filterYear && $filterMonth) {
        $filterStart = date('Y-m-01', strtotime($startDate));
    } elseif ($filterYear && !$filterMonth) {
        $filterStart = date('Y-m-01', strtotime("$filterYear-01-01"));
    } else {
        $filterStart = $firstDate; // semua tahun → awal APD
    }

    $totalInBefore  = APDAdjustmentItem::join('apd_adjustments','apd_adjustment_items.apd_adjustment_id','=','apd_adjustments.id')
        ->where('apd_adjustment_items.apd_id', $apd->id)
        ->where('apd_adjustments.adjustment_type','IN')
        ->where('apd_adjustments.adjustment_date','<',$filterStart)
        ->sum('apd_adjustment_items.qty');

    $totalReturnBefore = APDReturnItem::join('apd_returns','apd_return_items.apd_return_id','=','apd_returns.id')
        ->where('apd_return_items.apd_id', $apd->id)
        ->where('apd_returns.return_date','<',$filterStart)
        ->sum('apd_return_items.qty');

    $totalOutBefore = APDAdjustmentItem::join('apd_adjustments','apd_adjustment_items.apd_adjustment_id','=','apd_adjustments.id')
        ->where('apd_adjustment_items.apd_id', $apd->id)
        ->where('apd_adjustments.adjustment_type','OUT')
        ->where('apd_adjustments.adjustment_date','<',$filterStart)
        ->sum('apd_adjustment_items.qty');

    $totalDistributionBefore = APDDistributionItem::join('apd_distributions','apd_distribution_items.apd_distribution_id','=','apd_distributions.id')
        ->where('apd_distribution_items.apd_id', $apd->id)
        ->where('apd_distributions.distribution_date','<',$filterStart)
        ->sum('apd_distribution_items.qty');

    $initialStock = $apd->initial_stock + $totalInBefore + $totalReturnBefore - $totalOutBefore - $totalDistributionBefore;
    $apd->initial_stock_filtered = $initialStock;

    // --- Hitung total in/out/return/distribution ---
    $totalIn = APDAdjustmentItem::join('apd_adjustments','apd_adjustment_items.apd_adjustment_id','=','apd_adjustments.id')
        ->where('apd_adjustment_items.apd_id', $apd->id)
        ->where('apd_adjustments.adjustment_type','IN')
        ->when($filterYear, fn($q) => $q->whereYear('apd_adjustments.adjustment_date', $filterYear))
        ->when($filterMonth, fn($q) => $q->whereMonth('apd_adjustments.adjustment_date', $filterMonth))
        ->sum('apd_adjustment_items.qty');

    $totalReturn = APDReturnItem::join('apd_returns','apd_return_items.apd_return_id','=','apd_returns.id')
        ->where('apd_return_items.apd_id', $apd->id)
        ->when($filterYear, fn($q) => $q->whereYear('apd_returns.return_date', $filterYear))
        ->when($filterMonth, fn($q) => $q->whereMonth('apd_returns.return_date', $filterMonth))
        ->sum('apd_return_items.qty');

    $totalOut = APDAdjustmentItem::join('apd_adjustments','apd_adjustment_items.apd_adjustment_id','=','apd_adjustments.id')
        ->where('apd_adjustment_items.apd_id', $apd->id)
        ->where('apd_adjustments.adjustment_type','OUT')
        ->when($filterYear, fn($q) => $q->whereYear('apd_adjustments.adjustment_date', $filterYear))
        ->when($filterMonth, fn($q) => $q->whereMonth('apd_adjustments.adjustment_date', $filterMonth))
        ->sum('apd_adjustment_items.qty');

    $totalDistribution = APDDistributionItem::join('apd_distributions','apd_distribution_items.apd_distribution_id','=','apd_distributions.id')
        ->where('apd_distribution_items.apd_id', $apd->id)
        ->when($filterYear, fn($q) => $q->whereYear('apd_distributions.distribution_date', $filterYear))
        ->when($filterMonth, fn($q) => $q->whereMonth('apd_distributions.distribution_date', $filterMonth))
        ->sum('apd_distribution_items.qty');

    $apd->total_in = $totalIn;
    $apd->total_return = $totalReturn;
    $apd->total_out = $totalOut;
    $apd->total_distribution = $totalDistribution;

    $apd->balance = $initialStock + $totalIn + $totalReturn - $totalOut - $totalDistribution;

    return $apd;
});










// ========== STEP 1: BUILD WARNINGS ==========
$warnings = [];

foreach ($employees as $emp) {
    foreach ($emp->distributions as $item) {

        // Pastikan distribusi dan APD lengkap
        if (!isset($item->distribution, $item->apd, $item->apd->lifetime)) {
            continue;
        }

        $distDateRaw = $item->distribution->distribution_date;

        // Lewati jika tanggal distribusi kosong atau format tidak valid
        try {
            $distDate = \Carbon\Carbon::parse($distDateRaw);
        } catch (\Exception $e) {
            continue; // skip data invalid
        }

        // Pastikan lifetime integer
        $lifetime = intval($item->apd->lifetime);

        // hitung tanggal penggantian
        $replaceDate = $distDate->copy()->addMonths($lifetime);

        // hitung sisa qty
        $sisaQty = ($item->qty ?? 0) - ($item->qty_return ?? 0);
        if ($sisaQty <= 0) continue; // sudah direturn semua → skip

        // hitung selisih bulan ke tanggal replace
        $diffMonths = now()->diffInMonths($replaceDate, false);

        // cek warning: expired atau mendekati expired (<= 2 bulan)
        if ($replaceDate->isPast() || $diffMonths <= 2) {
            $warnings[] = [
                'employee'    => $emp,
                'apd_id'      => $item->apd->id,
                'code'        => $item->apd->code,
                'apd'         => $item->apd,
                'qty'         => $sisaQty, // gunakan sisa qty
                'uom'         => $item->apd->uom ?? '-',
                'replaceDate' => $replaceDate->format('d M Y'),
                'isExpired'   => $replaceDate->isPast(),
                'distribution_date' => $distDate->format('d M Y'),
            ];
        }
    }
}


// ========== STEP 2: GROUP BY APD ==========

$groupedWarnings = collect($warnings)
    ->groupBy('apd_id')
    ->map(function ($items) use ($apds) {

        $first = $items->first(); 
        $apd   = $first['apd'];

        $apdName = $apd->name; // dipakai untuk cari APD lain yang satu nama

        // SUM: qty yang perlu diganti
        $qtyPergantian = collect($items)->sum('qty');

        /* ----------------------------------------------------
           HITUNG CURRENT STOCK (Baru + Bekas)
           ---------------------------------------------------- */

        // Balance APD Baru
        $stockBaru = $apds
            ->where('name', $apdName)
            ->where('conditions', 'Baru')
            ->sum('balance');

        // Balance APD Bekas
        $stockBekas = $apds
            ->where('name', $apdName)
            ->where('conditions', 'Bekas')
            ->sum('balance');

        // Total stok yang tersedia untuk dipakai
        $currentStock = $stockBaru + $stockBekas;

        // Hitung kebutuhan pembelian
        $perluPembelian = max($qtyPergantian - $stockBaru, 0);

        return [
            'apd_name'       => $apdName,
            'apd_code'       => $apd->code,
            'qtyPergantian'  => $qtyPergantian,
            'stockBaru'      => $stockBaru,
            'stockBekas'     => $stockBekas,
            'currentStock'   => $currentStock,
            'perluPembelian' => $perluPembelian,
        ];
    });


$raw = APDDistributionItem::with('apd')->get();

// DEBUG RAW
Log::info("RAW COUNT = " . $raw->count());
Log::info($raw->toArray());

$distributions = $raw
    ->groupBy('receiver') // group by employee
    ->map(function ($itemsPerReceiver) {

        return $itemsPerReceiver
            ->groupBy('apd_id') // group by APD
            ->map(function ($itemsPerApd) {

                $first = $itemsPerApd->first();

                return [
                    'apd_id'   => $first->apd_id,
                    'apd_name' => $first->apd->name ?? 'Unknown APD',
                    'uom'      => $first->apd->uom ?? '-',
                    'qty'      => $itemsPerApd->sum('qty'),
                ];
            })
            ->values(); // reset index
    })
    ->toArray();

return view('facility.apd', compact('employees', 'apds', 'distributions','warnings','groupedWarnings','departments','typeEmployee'));
    }

public function getEmployeeAPD($employee_id)
{
    // Ambil semua distribusi untuk receiver tertentu
    $apds = APDDistributionItem::with('apd')
        ->where('receiver', $employee_id)
        ->get()
        ->filter(fn($item) => ($item->qty - ($item->qty_return ?? 0)) > 0) // hanya sisa > 0
        ->map(fn($item) => [
            'apd_id'      => $item->apd_id,
            'apd_name'    => optional($item->apd)->name ?? 'Unknown APD',
            'uom'         => optional($item->apd)->uom ?? '-',
            'qty'         => $item->qty - ($item->qty_return ?? 0), // sisa APD
            'conditions'  => $item->conditions ?? ($item->apd->conditions ?? '-'), // kondisi per baris distribusi
        ])
        ->values(); // reset index array agar 0,1,2,...

    return response()->json($apds);
}

public function debugMovement()
{
    $apdIds = [11, 12]; // ganti sesuai kebutuhan

    foreach ($apdIds as $id) {

        $apd = DB::table('apds')->where('id', $id)->first();

        if (!$apd) continue;

        Log::info("=== DEBUG APD {$apd->id} - {$apd->name} ===");

        // ---  MONTH LIST  ----
        $months = [
            ['bulan' => 11, 'nama' => 'NOVEMBER'],
            ['bulan' => 12, 'nama' => 'DESEMBER'],
        ];

        // stock awal dari apds
        $currentStock = $apd->initial_stock;

        foreach ($months as $m) {

            $bulan = $m['bulan'];
            $namaBulan = $m['nama'];

            // ======================
            // IN (ADJUSTMENT)
            // ======================
            $inAdjustment = DB::table('apd_adjustment_items')
                ->join('apd_adjustments', 'apd_adjustments.id', 'apd_adjustment_items.apd_adjustment_id')
                ->where('apd_adjustment_items.apd_id', $id)
                ->where('apd_adjustments.adjustment_type', 'IN')
                ->whereMonth('apd_adjustments.adjustment_date', $bulan)
                ->sum('apd_adjustment_items.qty');

            // ======================
            // RETURN
            // ======================
            $returnQty = DB::table('apd_return_items')
                ->join('apd_returns', 'apd_returns.id', 'apd_return_items.apd_return_id')
                ->where('apd_return_items.apd_id', $id)
                ->whereMonth('apd_returns.return_date', $bulan)
                ->sum('apd_return_items.qty');

            // ======================
            // OUT (Distribution)
            // ======================
            $outDistribution = DB::table('apd_distribution_items')
                ->join('apd_distributions', 'apd_distributions.id', 'apd_distribution_items.apd_distribution_id')
                ->where('apd_distribution_items.apd_id', $id)
                ->whereMonth('apd_distributions.distribution_date', $bulan)
                ->sum('apd_distribution_items.qty');

            // ======================
            // OUT (Adjustment OUT)
            // ======================
            $outAdjustment = DB::table('apd_adjustment_items')
                ->join('apd_adjustments', 'apd_adjustments.id', 'apd_adjustment_items.apd_adjustment_id')
                ->where('apd_adjustment_items.apd_id', $id)
                ->where('apd_adjustments.adjustment_type', 'OUT')
                ->whereMonth('apd_adjustments.adjustment_date', $bulan)
                ->sum('apd_adjustment_items.qty');

            // ======================
            // TOTAL
            // ======================
            $inTotal = $inAdjustment + $returnQty;
            $outTotal = $outDistribution + $outAdjustment;
            $balance = $currentStock + $inTotal - $outTotal;

            Log::info("{$namaBulan} APD {$apd->id}", [
                'Stock awal'      => $currentStock,
                'IN (Adjustment)' => $inAdjustment,
                'RETURN'          => $returnQty,
                'OUT Dist'        => $outDistribution,
                'OUT Adjust'      => $outAdjustment,
                'Total IN'        => $inTotal,
                'Total OUT'       => $outTotal,
                'BALANCE'         => $balance
            ]);

            // update stock untuk bulan berikutnya
            $currentStock = $balance;
        }

        Log::info("=== END DEBUG APD {$apd->id} ===");
    }
}

public function getAPDReminder()
{
    $items = APDDistributionItem::with(['apd', 'receiverUser', 'distribution'])
        ->get();

    $result = [];

    foreach ($items as $item) {
        if (!$item->receiverUser || !$item->distribution) continue;

        // Ambil lifetime dari DB, default 12 jika null
        $lifetime = is_numeric($item->apd->lifetime) ? (int) $item->apd->lifetime : 12;

        $distribution_date = $item->distribution->distribution_date ?? null;

        // Hitung due date
        $due_date = $distribution_date ? \Carbon\Carbon::parse($distribution_date)->addMonths($lifetime) : null;

        $year = $due_date ? $due_date->format('Y') : null;
        $full_date = $due_date ? $due_date->format('Y-m-d') : null;

        // Hitung sisa APD
        $remaining = $item->qty - ($item->qty_return ?? 0);
        $status = $remaining > 0 ? "APD Masih Di Karyawan" : "APD Sudah Dikembalikan";

        // Masuk hanya jika qty belum dikembalikan
        if ($remaining > 0) {
            $yearKey = $year ?? 'Tidak Diketahui';

            $result[$yearKey][] = [
                'name'       => $item->receiverUser->name,
                'apd_name'   => $item->apd->name,
                'apd_icon'   => $item->apd->icon,
                'department' => $item->receiverUser->position->name ?? '-',
                'status'     => $status,
                'due'        => $full_date
            ];
        }
    }

    // Urutkan berdasarkan tahun ascending
    ksort($result);

    return response()->json($result);
}








    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'conditions' => 'required',
            'name' => 'required',
            'initial_stock' => 'required|numeric',
            'min_stock' => 'required|numeric',
            'uom' => 'required',
            'lifetime' => 'required|numeric',
            'icon' => 'required',
        ]);

        APD::create([
            'code' => $request->code,
            'conditions' => $request->conditions,
            'name' => $request->name,
            'initial_stock' => $request->initial_stock,
            'min_stock' => $request->min_stock,
            'uom' => $request->uom,
            'lifetime' => $request->lifetime,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'APD Baru berhasil ditambahkan.'
        ]);
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
    'icon' => 'required',
    'code' => 'required',
    'conditions' => 'required',
    'name' => 'required',
    'initial_stock' => 'required|numeric',
    'min_stock' => 'required|numeric',
    'uom' => 'required',
    'lifetime' => 'required|numeric',
]);

    Apd::where('id', $id)->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Data APD berhasil diperbarui'
    ]);
}

public function destroy($id)
{
    $apd = Apd::find($id);

    if (!$apd) {
        return response()->json([
            'message' => 'APD tidak ditemukan.'
        ], 404);
    }

    $apd->delete();

    return response()->json([
        'message' => 'APD berhasil dihapus.'
    ]);
}

public function getApdStock($id)
{
    $apd = Apd::select('apds.*')
        ->addSelect([
            // === TOTAL IN (Adjustment IN)
            'total_in' => DB::table('apd_adjustment_items')
                ->select(DB::raw('COALESCE(SUM(apd_adjustment_items.qty), 0)'))
                ->join('apd_adjustments', 'apd_adjustment_items.apd_adjustment_id', '=', 'apd_adjustments.id')
                ->whereColumn('apd_adjustment_items.apd_id', 'apds.id')
                ->where('apd_adjustments.adjustment_type', 'IN'),

            // === TOTAL OUT (Adjustment OUT)
            'total_out' => DB::table('apd_adjustment_items')
                ->select(DB::raw('COALESCE(SUM(apd_adjustment_items.qty), 0)'))
                ->join('apd_adjustments', 'apd_adjustment_items.apd_adjustment_id', '=', 'apd_adjustments.id')
                ->whereColumn('apd_adjustment_items.apd_id', 'apds.id')
                ->where('apd_adjustments.adjustment_type', 'OUT'),

            // === TOTAL DISTRIBUTED (keluar karena distribusi)
            'total_distributed' => DB::table('apd_distribution_items')
                ->select(DB::raw('COALESCE(SUM(apd_distribution_items.qty), 0)'))
                ->join('apd_distributions', 'apd_distribution_items.apd_distribution_id', '=', 'apd_distributions.id')
                ->whereColumn('apd_distribution_items.apd_id', 'apds.id'),

          'total_return' => DB::table('apd_return_items')
    ->select(DB::raw('COALESCE(SUM(apd_return_items.qty), 0)'))
    ->join('apd_returns', 'apd_return_items.apd_return_id', '=', 'apd_returns.id')
    ->join('apds as a', 'apd_return_items.apd_id', '=', 'a.id')
    ->whereColumn('a.name', 'apds.name')
    ->whereColumn('apd_return_items.conditions', 'apds.conditions')

        ])
        ->where('apds.id', $id)
        ->first();

    // Pastikan semua angka tidak null
    $initial = (int) ($apd->initial_stock ?? 0);
    $total_in = (int) ($apd->total_in ?? 0);
    $total_out = (int) ($apd->total_out ?? 0);
    $total_distributed = (int) ($apd->total_distributed ?? 0);
    $total_return = (int) ($apd->total_return ?? 0);

    // === Hitung saldo akhir termasuk return
    $balance = $initial + $total_in + $total_return - $total_out - $total_distributed;

    return response()->json([
        'stock' => $balance,
        'uom' => $apd->uom ?? '-',
        'debug' => [
            'initial_stock' => $initial,
            'total_in' => $total_in,
            'total_return' => $total_return,
            'total_out' => $total_out,
            'total_distributed' => $total_distributed,
            'balance' => $balance,
        ]
    ]);
}


      public function storeAdjustment(Request $request)
    {
        $request->validate([
            'adjustment_type' => 'required|in:IN,OUT',
            'adjustment_date' => 'required|date',
            'apd_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
        ]);

        $type = strtoupper($request->adjustment_type);
        $reference_number = $request->reference_number;
        $adjustment_date = $request->adjustment_date;
        $adjustment_reason = $request->adjustment_reason;
        $apd_ids = $request->apd_id;
        $qtys = $request->qty;

        // === Generate Nomor Transaksi ===
        $transaction_code = $this->generateTransactionNumber($type);

        DB::beginTransaction();
        try {
            // === Simpan Header ===
            $headerId = DB::table('apd_adjustments')->insertGetId([
                'transaction_code' => $transaction_code,
                'reference_number' => $reference_number,
                'adjustment_date' => $adjustment_date,
                'adjustment_type' => $type,
                'adjustment_reason' => $adjustment_reason,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // === Simpan Detail ===
            foreach ($apd_ids as $i => $apd_id) {
                $qty = (float) $qtys[$i] ?? 0;

                DB::table('apd_adjustment_items')->insert([
                    'apd_adjustment_id' => $headerId,
                    'apd_id' => $apd_id,
                    'qty' => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Stock APD berhasil disesuaikan.',
                'transaction_code' => $transaction_code
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // === Fungsi Generate Nomor Transaksi ===
    private function generateTransactionNumber($type)
    {
        $year = date('Y');
        $month = strtoupper(date('M')); // contoh: XI
        $prefix = "APD-{$type}-{$year}-{$month}";

        // Ambil nomor terakhir untuk bulan ini
        $last = DB::table('apd_adjustments')
            ->where('adjustment_type', $type)
            ->whereYear('adjustment_date', $year)
            ->whereMonth('adjustment_date', date('m'))
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($last && isset($last->transaction_code)) {
            $explode = explode('-', $last->transaction_code);
            $lastNumber = intval(end($explode));
        }

        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}-{$nextNumber}";
    }

 public function movement($apdId, Request $request)
{
    $apd = APD::findOrFail($apdId);

    $start = $request->start;
    $end   = $request->end;
    $type  = $request->type; // IN / OUT

    /* ============================================
       1. ADJUSTMENT
    ============================================ */
    $adjustments = DB::table('apd_adjustment_items as item')
        ->join('apd_adjustments as adj', 'item.apd_adjustment_id', '=', 'adj.id')
        ->select(
            'adj.adjustment_date as date',
            'adj.adjustment_type as type',
            'adj.transaction_code',
            'item.qty',
            DB::raw("'Adjustment' as source"),
            'adj.adjustment_reason as note',
            'adj.created_at',
            'item.id',
            DB::raw("NULL as giver"),
            DB::raw("NULL as receiver")
        )
        ->where('item.apd_id', $apdId);

    // FILTER date
    if ($start && $end) {
        $adjustments->whereBetween('adj.adjustment_date', [$start, $end]);
    }

    // FILTER type (IN/OUT)
    if ($type) {
        $adjustments->where('adj.adjustment_type', $type);
    }


    /* ============================================
       2. DISTRIBUTION (OUT)
    ============================================ */
    $distributions = DB::table('apd_distribution_items as item')
        ->join('apd_distributions as dist', 'item.apd_distribution_id', '=', 'dist.id')
        ->leftJoin('users as u', 'dist.created_by', '=', 'u.id')
        ->leftJoin('employees as e', 'item.receiver', '=', 'e.id')
        ->select(
            'dist.distribution_date as date',
            DB::raw("'OUT' as type"),
            'dist.distribution_number as transaction_code',
            'item.qty',
            DB::raw("'Distribution' as source"),
            'dist.note',
            'dist.created_at',
            'item.id',
            'u.name as giver',
            'e.name as receiver'
        )
        ->where('item.apd_id', $apdId);

    // FILTER date
    if ($start && $end) {
        $distributions->whereBetween('dist.distribution_date', [$start, $end]);
    }

    // FILTER type
    if ($type === "OUT") {
        // OK, OUT ketemu
    } elseif ($type === "IN") {
        // Distribution adalah OUT → jangan tampilkan
        $distributions->whereRaw("1=0");
    }


    /* ============================================
       3. RETURN (IN)
    ============================================ */
    $rawReturns = DB::table('apd_return_items as item')
        ->join('apd_returns as r', 'item.apd_return_id', '=', 'r.id')
        ->leftJoin('employees as e', 'item.returned_from', '=', 'e.id')
        ->leftJoin('users as u', 'r.created_by', '=', 'u.id')
        ->whereIn('item.apd_id', function ($q) use ($apd) {
            $q->select('id')->from('apds')->where('name', $apd->name);
        })
        ->select(
            'item.apd_id',
            'item.conditions',
            'item.qty',
            'r.return_date as date',
            'r.return_number as transaction_code',
            DB::raw("'IN' as type"),
            DB::raw("'Return' as source"),
            'r.note',
            'r.created_at',
            'item.id',
            'e.name as giver',
            'u.name as receiver'
        );

    // FILTER date
    if ($start && $end) {
        $rawReturns->whereBetween('r.return_date', [$start, $end]);
    }

    // FILTER type (Return = IN)
    if ($type === "OUT") {
        // Return adalah IN → sembunyikan
        $rawReturns->whereRaw("1=0");
    }


    $rawReturns = $rawReturns->get();


    /* =====================================================
       FILTER RETURN AGAR MASUK APD + KONDISI YANG SAMA
    ====================================================== */
    $returns = collect();

    foreach ($rawReturns as $row) {
        $targetApdId = DB::table('apds')
            ->where('name', $apd->name)
            ->where('conditions', $row->conditions)
            ->value('id');

        if ($targetApdId != $apdId) {
            continue;
        }

        $returns->push($row);
    }


    /* =====================================================
       UNION ADJUSTMENT + DISTRIBUTION
    ====================================================== */
    $union = $adjustments->unionAll($distributions)->get();

    // gabungkan dengan return
    $allMovements = $union->merge($returns);

    // sort
    $allMovements = $allMovements->sortBy([
        ['date', 'asc'],
        ['id', 'asc'],
    ])->values();


    /* =====================================================
       HITUNG BALANCE
    ====================================================== */
    $balance = $apd->initial_stock ?? 0;

    $movements = $allMovements->map(function ($item) use (&$balance) {

        $qtySigned = $item->type === 'IN' ? $item->qty : -$item->qty;
        $rowBalance = $balance + $qtySigned;

        $previousBalance = $balance;
        $balance = $rowBalance;

        return [
            'date'       => $item->date,
            'type'       => $item->type,
            'transaction_code' => $item->transaction_code,
            'source'     => $item->source,
            'item_id'    => $item->id,
            'initial_stock' => $previousBalance,
            'qty'        => $item->qty,
            'balance'    => $rowBalance,
            'note'       => $item->note,
            'created_at' => $item->created_at,
            'giver'      => $item->giver,
            'receiver'   => $item->receiver,
        ];
    });

    return response()->json($movements);
}

public function globalMovement($apdId, Request $request)
{
    $apd = APD::findOrFail($apdId);

    $start = $request->start;
    $end   = $request->end;
    $type  = $request->type; // IN / OUT

    /* ============================================
       1. ADJUSTMENT
    ============================================ */
    $adjustments = DB::table('apd_adjustment_items as item')
        ->join('apd_adjustments as adj', 'item.apd_adjustment_id', '=', 'adj.id')
        ->select(
            'adj.adjustment_date as date',
            'adj.adjustment_type as type',
            'adj.transaction_code',
            'item.qty',
            DB::raw("'Adjustment' as source"),
            'adj.adjustment_reason as note',
            'adj.created_at',
            'item.id',
            DB::raw("NULL as giver"),
            DB::raw("NULL as receiver")
        )
        ->where('item.apd_id', $apdId);

    // FILTER date
    if ($start && $end) {
        $adjustments->whereBetween('adj.adjustment_date', [$start, $end]);
    }

    // FILTER type (IN/OUT)
    if ($type) {
        $adjustments->where('adj.adjustment_type', $type);
    }


    /* ============================================
       2. DISTRIBUTION (OUT)
    ============================================ */
    $distributions = DB::table('apd_distribution_items as item')
        ->join('apd_distributions as dist', 'item.apd_distribution_id', '=', 'dist.id')
        ->leftJoin('users as u', 'dist.created_by', '=', 'u.id')
        ->leftJoin('employees as e', 'item.receiver', '=', 'e.id')
        ->select(
            'dist.distribution_date as date',
            DB::raw("'OUT' as type"),
            'dist.distribution_number as transaction_code',
            'item.qty',
            DB::raw("'Distribution' as source"),
            'dist.note',
            'dist.created_at',
            'item.id',
            'u.name as giver',
            'e.name as receiver'
        )
        ->where('item.apd_id', $apdId);

    // FILTER date
    if ($start && $end) {
        $distributions->whereBetween('dist.distribution_date', [$start, $end]);
    }

    // FILTER type
    if ($type === "OUT") {
        // OK, OUT ketemu
    } elseif ($type === "IN") {
        // Distribution adalah OUT → jangan tampilkan
        $distributions->whereRaw("1=0");
    }


    /* ============================================
       3. RETURN (IN)
    ============================================ */
    $rawReturns = DB::table('apd_return_items as item')
        ->join('apd_returns as r', 'item.apd_return_id', '=', 'r.id')
        ->leftJoin('employees as e', 'item.returned_from', '=', 'e.id')
        ->leftJoin('users as u', 'r.created_by', '=', 'u.id')
        ->whereIn('item.apd_id', function ($q) use ($apd) {
            $q->select('id')->from('apds')->where('name', $apd->name);
        })
        ->select(
            'item.apd_id',
            'item.conditions',
            'item.qty',
            'r.return_date as date',
            'r.return_number as transaction_code',
            DB::raw("'IN' as type"),
            DB::raw("'Return' as source"),
            'r.note',
            'r.created_at',
            'item.id',
            'e.name as giver',
            'u.name as receiver'
        );

    // FILTER date
    if ($start && $end) {
        $rawReturns->whereBetween('r.return_date', [$start, $end]);
    }

    // FILTER type (Return = IN)
    if ($type === "OUT") {
        // Return adalah IN → sembunyikan
        $rawReturns->whereRaw("1=0");
    }


    $rawReturns = $rawReturns->get();


    /* =====================================================
       FILTER RETURN AGAR MASUK APD + KONDISI YANG SAMA
    ====================================================== */
    $returns = collect();

    foreach ($rawReturns as $row) {
        $targetApdId = DB::table('apds')
            ->where('name', $apd->name)
            ->where('conditions', $row->conditions)
            ->value('id');

        if ($targetApdId != $apdId) {
            continue;
        }

        $returns->push($row);
    }


    /* =====================================================
       UNION ADJUSTMENT + DISTRIBUTION
    ====================================================== */
    $union = $adjustments->unionAll($distributions)->get();

    // gabungkan dengan return
    $allMovements = $union->merge($returns);

    // sort
    $allMovements = $allMovements->sortBy([
        ['date', 'asc'],
        ['id', 'asc'],
    ])->values();


    /* =====================================================
       HITUNG BALANCE
    ====================================================== */
    $balance = $apd->initial_stock ?? 0;

    $movements = $allMovements->map(function ($item) use (&$balance) {

        $qtySigned = $item->type === 'IN' ? $item->qty : -$item->qty;
        $rowBalance = $balance + $qtySigned;

        $previousBalance = $balance;
        $balance = $rowBalance;

       return [
    'date'       => $item->date,
    'type'       => $item->type,
    'transaction_code' => $item->transaction_code,
    'source'     => $item->source,
    'item_id'    => $item->id,
    'initial_stock' => $previousBalance,
    'qty'        => $item->qty,
    'balance'    => $rowBalance,
    'note'       => $item->note,
    'created_at' => $item->created_at,
    'giver'      => $item->giver,
    'receiver'   => $item->receiver,
];

    });

    return response()->json([
    "draw" => intval($request->draw),
    "recordsTotal" => $movements->count(),
    "recordsFiltered" => $movements->count(),
    "data" => $movements
]);

}

public function getAvailableYears()
{
    // Ambil tahun paling awal & paling akhir dari distribusi
    $minDist = DB::table('apd_distributions')->min(DB::raw('YEAR(distribution_date)'));
    $maxDist = DB::table('apd_distributions')->max(DB::raw('YEAR(distribution_date)'));

    // Ambil tahun paling awal & paling akhir dari return
    $minRet = DB::table('apd_returns')->min(DB::raw('YEAR(return_date)'));
    $maxRet = DB::table('apd_returns')->max(DB::raw('YEAR(return_date)'));

    // Tentukan batas tahun final
    $minYear = min($minDist, $minRet);
    $maxYear = max($maxDist, $maxRet);

    // Safety jika database kosong
    if (!$minYear || !$maxYear) {
        $current = date("Y");
        return response()->json([$current]);
    }

    // Generate daftar tahun
    $years = range($maxYear, $minYear); // urut dari paling baru ke paling lama

    return response()->json($years);
}


public function yearlyChart(Request $request)
{
    $year = $request->year ?? date('Y');

    // --- DISTRIBUTION (OUT) ---
    $distributed = DB::table('apd_distribution_items AS item')
        ->join('apd_distributions AS dist', 'item.apd_distribution_id', '=', 'dist.id')
        ->select(
            DB::raw('MONTH(dist.distribution_date) as month'),
            DB::raw('SUM(item.qty) as total')
        )
        ->whereYear('dist.distribution_date', $year)
        ->groupBy('month')
        ->pluck('total', 'month');

    // --- RETURN (IN) ---
    $returned = DB::table('apd_return_items AS item')
        ->join('apd_returns AS r', 'item.apd_return_id', '=', 'r.id')
        ->select(
            DB::raw('MONTH(r.return_date) as month'),
            DB::raw('SUM(item.qty) as total')
        )
        ->whereYear('r.return_date', $year)
        ->groupBy('month')
        ->pluck('total', 'month');

    // Siapkan array bulan 1–12 bernilai 0
    $distributedData = array_fill(1, 12, 0);
    $returnedData    = array_fill(1, 12, 0);

    // Masukkan data per bulan
    foreach ($distributed as $month => $total) {
        $distributedData[$month] = $total;
    }

    foreach ($returned as $month => $total) {
        $returnedData[$month] = $total;
    }

    return response()->json([
        'year' => $year,
        'distributed' => array_values($distributedData),
        'returned'    => array_values($returnedData),
    ]);
}





 public function storeDistribution(Request $request)
    {
        $request->validate([
            'distribution_date' => 'required|date',
            'note' => 'nullable|string',
            'apd_id' => 'required|array',
            'qty' => 'required|array',
        ]);

        // ===========================
        // 🔹 Generate Nomor Distribusi
        // ===========================
        $month = strtoupper($this->toRoman(Carbon::now()->month));
        $year = Carbon::now()->year;

        $last = APDDistribution::whereYear('distribution_date', $year)
            ->whereMonth('distribution_date', Carbon::now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $last ? intval(substr($last->distribution_number, -4)) + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $distributionNumber = "DIS-APD-{$year}-{$month}-{$formattedNumber}";

        // ===========================
        // 🔹 Simpan Distribusi
        // ===========================
        $distribution = APDDistribution::create([
            'distribution_number' => $distributionNumber,
            'distribution_date' => $request->distribution_date,
            'note' => $request->note,
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ===========================
        // 🔹 Simpan Item Distribusi
        // ===========================
        foreach ($request->apd_id as $i => $apdId) {
            if (!$apdId) continue;

            APDDistributionItem::create([
                'apd_distribution_id' => $distribution->id,
                'apd_id' => $apdId,
                'qty' => $request->qty[$i] ?? 0,
                'receiver' => $request->receiver[$i] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Distribusi APD berhasil disimpan!',
            'distribution_number' => $distributionNumber,
        ]);
    }

    // ===========================
    // 🔹 Fungsi bantu konversi bulan → angka Romawi
    // ===========================
    private function toRoman($month)
    {
        $romans = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return $romans[$month] ?? '';
    }

public function storeReturn(Request $request)
{
    $request->validate([
        "return_date"   => "required|date",
        "returned_from" => "required|array",
        "apd_id"        => "required|array",
        "qty"           => "required|array",
        "conditions"    => "required|array",
    ]);

    DB::beginTransaction();

    try {
        // 1️⃣ Generate return number
        $returnNumber = $this->generateReturnNumber();

        // 2️⃣ Insert ke apd_returns
        $return = APDReturn::create([
            'return_number' => $returnNumber,
            'return_date'   => $request->return_date,
            'note'          => $request->note ?? null,
            'created_by'    => Auth::id(),
        ]);

        // 3️⃣ Loop semua item yang direturn
        foreach ($request->apd_id as $i => $apdId) {
            $qty = (int) $request->qty[$i];
            $condition = $request->conditions[$i];
            $employeeId = $request->returned_from[$i];

            // Simpan ke apd_return_items
            APDReturnItem::create([
                'apd_return_id' => $return->id,
                'apd_id'        => $apdId,
                'qty'           => $qty,
                'returned_from' => $employeeId,
                'conditions'    => $condition, // optional, kalau mau simpan kondisi
            ]);

          $distributionItems = DB::table('apd_distribution_items as di')
    ->join('apds as a', 'di.apd_id', '=', 'a.id')
    ->where('di.apd_id', $apdId)
    ->where('di.receiver', $employeeId)
    ->whereRaw('di.qty - di.qty_return > 0')
    ->orderBy('di.id', 'asc')
    ->select('di.*') // ambil semua kolom distribusi
    ->get();

$remainingQty = $qty;
foreach ($distributionItems as $distItem) {
    $available = $distItem->qty - $distItem->qty_return;
    $toReturn = min($remainingQty, $available);

    DB::table('apd_distribution_items')
        ->where('id', $distItem->id)
        ->increment('qty_return', $toReturn);

    $remainingQty -= $toReturn;
    if ($remainingQty <= 0) break;
}


        }

        DB::commit();

        return response()->json([
            'status'        => 'success',
            'message'       => 'APD return berhasil disimpan!',
            'return_number' => $returnNumber,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menyimpan return: ' . $e->getMessage()
        ], 500);
    }
}




private function generateReturnNumber()
{
    $month = Carbon::now()->month; // tetap angka untuk query
    $year = Carbon::now()->year;

    // Cari nomor terakhir bulan ini
    $last = APDReturn::whereYear('return_date', $year)
        ->whereMonth('return_date', $month) // harus angka
        ->orderBy('id', 'desc')
        ->first();

    if (!$last) {
        $nextNumber = '0001';
    } else {
        // format terakhir contoh: APDRTN-2025-XI-0001
        $lastNumber = intval(substr($last->return_number, -4));
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // Konversi bulan ke Romawi untuk nomor
    $monthRoman = strtoupper($this->toRoman($month));

    return "APDRTN-{$year}-{$monthRoman}-{$nextNumber}";
}
public function sendAPDReminderEmail()
{
    $now = Carbon::now();

    $items = APDDistributionItem::with(['apd', 'receiverUser', 'distribution'])->get();
    $reminderItems = [];

    foreach ($items as $item) {
        if (!$item->receiverUser || !$item->distribution) continue;

        $lifetime = is_numeric($item->apd->lifetime) && $item->apd->lifetime > 0
                    ? (int)$item->apd->lifetime
                    : 12; // default 12 bulan

        $distribution_date = $item->distribution->distribution_date;
        if (!$distribution_date) continue;

        $due_date = Carbon::parse($distribution_date)->addMonths($lifetime);
        $reminder_date = $due_date->copy()->subMonths(2);

        // cek apakah hari ini waktunya kirim reminder
        if ($now->toDateString() === $reminder_date->toDateString()) {
            $remaining = $item->qty - ($item->qty_return ?? 0);
            if ($remaining <= 0) continue; // sudah dikembalikan, skip

            $reminderItems[] = [
                'name' => $item->receiverUser->name,
                'department' => $item->receiverUser->position->name ?? '-',
                'apd_name' => $item->apd->name,
                'due' => $due_date->format('Y-m-d'),
                'status' => "APD Masih Di Karyawan",
            ];
        }
    }

    if (count($reminderItems) > 0) {
        Mail::to('it2@asnusantara.co.id')->send(new APDReminderMail($reminderItems));
    }

    return response()->json(['success' => true, 'count' => count($reminderItems)]);
}


public function getAPDRecommendation()
{
    // Ambil APD yang kondisi BARU atau BEKAS (rusak tidak diambil)
    $apds = Apd::whereIn('conditions', ['Baru', 'Bekas'])
        ->orderBy('name')
        ->get();

    if ($apds->isEmpty()) {
        return response()->json([]);
    }

    $grouped = [];

    // SATUKAN APD BERDASARKAN NAMA (Baru + Bekas)
    foreach ($apds as $apd) {

        $key = $apd->name;

        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'name' => $apd->name,
                'code' => $apd->code,
                'lifetime' => $apd->lifetime,

                'min_stock' => $apd->min_stock ?? 0,

                'initial_stock' => 0,
                'adjust' => 0,
                'distributed' => 0,
                'returned' => 0,

                'count_baru' => 0,
                'count_bekas' => 0
            ];
        }

        // Hitung jumlah item berdasarkan kondisi
        if ($apd->conditions === "Baru") {
            $grouped[$key]['count_baru']++;
        } elseif ($apd->conditions === "Bekas") {
            $grouped[$key]['count_bekas']++;
        }

        // Stok awal → ditambahkan semua karena Baru & Bekas
        $grouped[$key]['initial_stock'] += $apd->initial_stock ?? 0;


        /** ============================================
         * 1. ADJUSTMENT (IN/OUT)
         * =============================================*/
        $adjust = APDAdjustmentItem::join(
                'apd_adjustments',
                'apd_adjustments.id',
                '=',
                'apd_adjustment_items.apd_adjustment_id'
            )
            ->where('apd_adjustment_items.apd_id', $apd->id)
            ->selectRaw("
                SUM(
                    CASE
                        WHEN apd_adjustments.adjustment_type = 'IN' THEN apd_adjustment_items.qty
                        WHEN apd_adjustments.adjustment_type = 'OUT' THEN -apd_adjustment_items.qty
                        ELSE 0
                    END
                ) AS total_adjust
            ")
            ->value('total_adjust');

        $grouped[$key]['adjust'] += ($adjust ?? 0);


        /** ============================================
         * 2. DISTRIBUSI OUT
         * =============================================*/
        $distributed = APDDistributionItem::where('apd_id', $apd->id)->sum('qty');
        $grouped[$key]['distributed'] += $distributed;


        /** ============================================
         * 3. RETURN (hanya yang punya return_date)
         * =============================================*/
        $returned = APDReturnItem::join(
                'apd_returns',
                'apd_returns.id',
                '=',
                'apd_return_items.apd_return_id'
            )
            ->where('apd_return_items.apd_id', $apd->id)
            ->whereNotNull('apd_returns.return_date')
            ->sum('apd_return_items.qty');

        $grouped[$key]['returned'] += $returned;
    }


    /** ==========================================================================
     *  PROSES PER APD GROUP (Baru + Bekas)
     * ==========================================================================*/
    $final = [];

   foreach ($grouped as $name => $g) {

    // ID APD baru + bekas yang namanya sama
    $apdIds = Apd::where('name', $name)
        ->whereIn('conditions', ['Baru', 'Bekas'])
        ->pluck('id');

    $currentStock = $g['initial_stock'] + $g['adjust'] - $g['distributed'] + $g['returned'];

    /** ============================================
     * 4. DUE IN 2 MONTHS
     * =============================================*/
    $dueItems = APDDistributionItem::join(
            'apd_distributions',
            'apd_distributions.id',
            '=',
            'apd_distribution_items.apd_distribution_id'
        )
        ->whereIn('apd_distribution_items.apd_id', $apdIds)
        ->get();

    $dueCount = 0;

    foreach ($dueItems as $dist) {
        $dueDate = Carbon::parse($dist->distribution_date)
            ->addMonths($g['lifetime']);

        if (now()->diffInDays($dueDate, false) <= 60) {
            $dueCount += $dist->qty;
        }
    }

    /** ============================================
     * 5. USAGE 3 MONTHS
     * =============================================*/
    $usage3 = APDDistributionItem::join(
            'apd_distributions',
            'apd_distributions.id',
            '=',
            'apd_distribution_items.apd_distribution_id'
        )
        ->whereIn('apd_distribution_items.apd_id', $apdIds)
        ->where('apd_distributions.distribution_date', '>=', now()->subMonths(3))
        ->sum('apd_distribution_items.qty');

    $avgUsage = $usage3 / 3;

    /** ============================================
     * 6. BUFFER
     * =============================================*/
    $buffer = ceil(($dueCount + $avgUsage) * 0.10);

    /** ============================================
     * 7. TOTAL NEED
     * =============================================*/
    $totalNeed = $dueCount + $avgUsage + $buffer;

    /** ============================================
     * 8. RECOMMENDED PURCHASE
     * =============================================*/
    $recommendedPurchase = max(0, ceil(($totalNeed + $g['min_stock']) - $currentStock));

    if ($recommendedPurchase <= 0) {
        continue;
    }

    $final[] = [
        'apd' => $name,
        'code' => $g['code'],
        'lifetime' => $g['lifetime'],

        'baru' => $g['count_baru'],
        'bekas' => $g['count_bekas'],

        'current_stock' => $currentStock,
        'due_in_2_months' => $dueCount,
        'usage_3_months' => $usage3,
        'avg_usage' => round($avgUsage, 2),

        'total_need' => ceil($totalNeed),
        'recommendation' => $recommendedPurchase,

        'message' => "Perlu pembelian {$recommendedPurchase} pcs"
    ];
}

    return response()->json($final);
}

public function data(Request $request)
{
    // ===========================
    // Gabungkan semua sumber
    // ===========================

    // Adjustment (IN/OUT tergantung adjustment_type)
    $adjustments = APDAdjustment::select(
        'adjustment_date as date',
        'adjustment_type as type',      // IN atau OUT
        'transaction_code as transaction_number',
        'adjustment_reason as note',
        DB::raw("'Adjustment' as source")
    );

    // Distribution (selalu OUT)
    $distributions = APDDistribution::select(
        'distribution_date as date',
        DB::raw("'OUT' as type"),
        'distribution_number as transaction_number',
        'note',
        DB::raw("'Distribution' as source")
    );

    // Return (selalu IN)
    $returns = APDReturn::select(
        'return_date as date',
        DB::raw("'IN' as type"),
        'return_number as transaction_number',
        'note',
        DB::raw("'Return' as source")
    );

    // Merge semua menggunakan unionAll supaya tetap query builder
    $allTransactions = $adjustments
        ->unionAll($distributions)
        ->unionAll($returns);

    // ===========================
   return DataTables::of($allTransactions)
    ->addColumn('action', function ($row) {
        $dropdownId = $row->source . '-' . $row->transaction_number;

        return <<<HTML
<div class="relative inline-block text-left group">
    <button type="button"
        data-dropdown-id="{$dropdownId}"
        onclick="toggleDropdown('{$dropdownId}', event)"
        class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
        <i data-feather="align-justify"></i>
    </button>
    <div id="{$dropdownId}" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
        <div class="py-1 text-sm text-gray-700">
            <a href="" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
            </a>
            <button 
                type="button" 
                class="btn-delete-apd w-full text-left px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
                data-number="{$row->transaction_number}">
                <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
            </button>
        </div>
    </div>
</div>
HTML;
    })
    ->editColumn('type', function($row){
        $color = $row->type === 'IN' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
        return "<span class='{$color}'>".$row->type."</span>";
    })
    ->rawColumns(['action', 'type'])
    ->make(true);
}


}
