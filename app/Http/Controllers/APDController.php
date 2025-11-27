<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Apd;
use App\Models\Department;
use App\Models\APDAdjustmenT;
use App\Models\APDAdjustmentItem;
use App\Models\APDDistribution;
use App\Models\APDDistributionitem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class APDController extends Controller
{
    public function index(Request $request)
    {

          $typeEmployee = $request->input('typeEmployee');
       $employees = Employee::with(['departments', 'distributions'])
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


            $apds = Apd::select('apds.*')
    ->addSelect([
        // 🔹 Total IN dari adjustment
        'total_in' => APDAdjustmentItem::select(DB::raw('SUM(qty)'))
            ->join('apd_adjustments', 'apd_adjustment_items.apd_adjustment_id', '=', 'apd_adjustments.id')
            ->whereColumn('apd_adjustment_items.apd_id', 'apds.id')
            ->where('apd_adjustments.adjustment_type', 'IN'),

        // 🔹 Total OUT dari adjustment
        'total_out' => APDAdjustmentItem::select(DB::raw('SUM(qty)'))
            ->join('apd_adjustments', 'apd_adjustment_items.apd_adjustment_id', '=', 'apd_adjustments.id')
            ->whereColumn('apd_adjustment_items.apd_id', 'apds.id')
            ->where('apd_adjustments.adjustment_type', 'OUT'),

        // 🔹 Total OUT dari distribusi
        'total_distribution' => APDDistributionItem::select(DB::raw('SUM(qty)'))
            ->whereColumn('apd_distribution_items.apd_id', 'apds.id'),

    ])
    ->orderByRaw("CASE 
    WHEN conditions = 'Baru' THEN 1
    WHEN conditions = 'Bekas' THEN 2
    WHEN conditions = 'Rusak' THEN 3
    ELSE 4
END")
->orderBy('name', 'asc')


    ->get()
    ->map(function ($apd) {
        // Rumus lengkap balance:
        // (stok awal + total IN + total RETURN) - (total OUT + total DISTRIBUSI)
        $apd->balance = 
            (($apd->initial_stock ?? 0)
            + ($apd->total_in ?? 0))
            - (($apd->total_out ?? 0)
            + ($apd->total_distribution ?? 0));

        return $apd;
    });


          // ========== STEP 1: BUILD WARNINGS ==========
$warnings = [];

foreach ($employees as $emp) {
    foreach ($emp->distributions as $item) {

        if (!isset($item->distribution->distribution_date, $item->apd->lifetime)) {
            continue;
        }

        $distDate = \Carbon\Carbon::parse($item->distribution->distribution_date);
        $replaceDate = $distDate->addMonths($item->apd->lifetime);

        $diffMonths = now()->diffInMonths($replaceDate, false);

        if ($replaceDate->isPast() || $diffMonths <= 2) {
            $warnings[] = [
                'employee'    => $emp,
                'apd_id'      => $item->apd->id,   // FIX 1
                'code'         => $item->apd->code,
                'apd'         => $item->apd,
                'qty'         => $item->qty,
                'uom'         => $item->apd->uom,
                'replaceDate' => $replaceDate->format('d M Y'),
                'isExpired'   => $replaceDate->isPast()
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

        $qtyPergantian = collect($items)->sum('qty');

        // FIX 2: stock dari perhitungan balance
        $currentStock = $apds->firstWhere('id', $apd->id)->balance ?? 0;

        return [
            'apd_name'       => $apd->name,
            'apd_code'       => $apd->code,
            'qtyPergantian'  => $qtyPergantian,
            'currentStock'   => $currentStock,
            'perluPembelian' => max($qtyPergantian - $currentStock, 0),
        ];
    });





 $distributions = APDDistributionItem::with(['apd'])
    ->get()
    ->groupBy('receiver') // group by receiver id
    ->map(function ($items) {
        return $items->map(function ($i) {
            return [
                'apd_id' => $i->apd?->id ?? $i->apd_id,
                'apd_name' => $i->apd?->name ?? 'Unknown APD',
                'uom' => $i->apd?->uom ?? '-',
            ];
        })->values();
    })
    ->mapWithKeys(function ($items, $key) {
        return [(string)$key => $items]; // key = receiver id
    })
    ->toArray();


Log::info('=== DISTRIBUTIONS RAW ===', APDDistributionItem::with('apd')->get()->toArray());
Log::info('=== DISTRIBUTIONS GROUPED ===', $distributions);




return view('facility.apd', compact('employees', 'apds', 'distributions','warnings','groupedWarnings','departments','typeEmployee'));
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
        ])
        ->where('apds.id', $id)
        ->first();

    // Pastikan semua angka tidak null
    $initial = (int) ($apd->initial_stock ?? 0);
    $total_in = (int) ($apd->total_in ?? 0);
    $total_out = (int) ($apd->total_out ?? 0);
    $total_distributed = (int) ($apd->total_distributed ?? 0);

    // === Hitung saldo akhir
    $balance = $initial + $total_in - $total_out - $total_distributed;

    return response()->json([
        'stock' => $balance,
        'uom' => $apd->uom ?? '-',
        'debug' => [
            'initial_stock' => $initial,
            'total_in' => $total_in,
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

   public function movement($apdId)
{
    $apd = APD::findOrFail($apdId);

    // ======================
    // 1️⃣ DATA ADJUSTMENT
    // ======================
    $adjustments = DB::table('apd_adjustment_items as item')
        ->join('apd_adjustments as adj', 'item.apd_adjustment_id', '=', 'adj.id')
        ->where('item.apd_id', $apdId)
        ->select(
            'adj.adjustment_date as date',
            'adj.adjustment_type as type',
            'adj.transaction_code',
            'item.qty',
            DB::raw("'Adjustment' as source"),
            'adj.adjustment_reason as note',
            'adj.created_at'
        );

    // ======================
    // 2️⃣ DATA DISTRIBUTION
    // ======================
    $distributions = DB::table('apd_distribution_items as item')
        ->join('apd_distributions as dist', 'item.apd_distribution_id', '=', 'dist.id')
        ->where('item.apd_id', $apdId)
        ->select(
            'dist.distribution_date as date',
            DB::raw("'OUT' as type"),
            'dist.distribution_number as transaction_code',
            'item.qty',
            DB::raw("'Distribution' as source"),
            'dist.note',
            'dist.created_at'
        );

    // Gabungkan semua sumber
    $allMovements = $adjustments
        ->unionAll($distributions)
        ->orderBy('date', 'asc')
        ->get();

    // ======================
    // 4️⃣ HITUNG BALANCE
    // ======================
    $balance = $apd->initial_stock ?? 0;

    $movements = $allMovements->map(function ($item) use (&$balance) {
        // Tanda qty
        $qtySigned = $item->type === 'IN' ? $item->qty : -$item->qty;
        $rowBalance = $balance + $qtySigned;

        $row = [
            'date' => $item->date,
            'type' => $item->type,
            'transaction_code' => $item->transaction_code,
            'source' => $item->source,
            'initial_stock' => $balance,
            'qty' => $item->qty,
            'balance' => $rowBalance,
            'note' => $item->note,
            'created_at' => $item->created_at,
        ];

        $balance = $rowBalance;
        return $row;
    });

    return response()->json($movements);
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



}
