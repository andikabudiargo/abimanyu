<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Sto;
use App\Models\StoItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Jenssegers\Agent\Agent;
use App\Models\Article;

class STOController extends Controller
{

public function index()
{
    return view('facility.sto');
}

public function indexv2()
{
    return view('facility.sto-v2');
}

public function referencePrint(Request $request)
{
    $warehouse = $request->query('warehouse');

    $masters = \App\Models\StoReferenceMaster::where('warehouse', $warehouse)
        ->where('is_active', 1)
        ->orderBy('page')
        ->with(['items.article'])
        ->get();

    return response()->json($masters);
}

  public function create()
{
     $agent = new Agent();

    $warehouse = $this->userWarehouse();

    $canChooseWarehouse = is_null($warehouse); // 🔥 INI BARU

    $allowedTypes = $this->allowedArticleTypes($warehouse);

   
    // 🔹 Bangun query dulu
    $query = Article::whereIn('article_type', $allowedTypes);

    // ✅ Filter khusus Werate → supplier_code WJI
    if ($warehouse === 'Werate') {
        $query->where('supplier_code', 'LIKE', '%WJI%');
    }

    $articles = $query->select('id', 'article_code', 'description', 'unit', 'article_type', 'min_package')
        ->orderBy('description')
        ->get();

    $usedStoNumbers = \DB::table('stos')
        ->pluck('sto_number')
        ->toArray();

        $allowedWarehouses = $this->allowedWarehouses();

    return view('facility.create-sto', compact(
        'warehouse',
        'canChooseWarehouse',
         'allowedWarehouses', // 🔥 KIRIM KE VIEW
        'articles',
        'usedStoNumbers'
    ));
}

public function createv2()
{
     $agent = new Agent();

    $warehouse = $this->userWarehouse();

    $canChooseWarehouse = is_null($warehouse); // 🔥 INI BARU

    $allowedTypes = $this->allowedArticleTypes($warehouse);

   
    // 🔹 Bangun query dulu
    $query = Article::whereIn('article_type', $allowedTypes);

    // ✅ Filter khusus Werate → supplier_code WJI
    if ($warehouse === 'Werate') {
        $query->where('supplier_code', 'LIKE', '%WJI%');
    }

    $articles = $query->select('id', 'article_code', 'description', 'unit', 'article_type', 'min_package')
        ->orderBy('description')
        ->get();

    $usedStoNumbers = \DB::table('stos')
        ->pluck('sto_number')
        ->toArray();

        $allowedWarehouses = $this->allowedWarehouses();

    return view('facility.create-sto', compact(
        'warehouse',
        'canChooseWarehouse',
         'allowedWarehouses', // 🔥 KIRIM KE VIEW
        'articles',
        'usedStoNumbers'
    ));
}

     private function userWarehouse()
{
    $userId = Auth::id();

    return match ($userId) {
        69        => 'Raw Material',
        85        => 'Werate',
        43,47     => 'WIP Buffing',
        44,45,88  => 'WIP Touch Up',
        108,126   => 'WIP Sanding',
        63, 129, 2   => 'Consumable',
        67, 54    => 'Chemical',
        45        => 'Finish Goods',
        44        => 'OT',
        53 => null, // 🔥 BOLEH PILIH SENDIRI
        default   => 'Raw Material',
    };
}

private function allowedWarehouses(): array
{
    $userId = Auth::id();

    //$mapping = [
        //92  => ['Dead Stock CM1', 'OT'],
   // ];

    if (isset($mapping[$userId])) {
        return $mapping[$userId];
    }

    // 🔥 User bebas pilih
    if (is_null($this->userWarehouse())) {
        return [
            'Raw Material',
            'Finish Goods',
            'OT',
            'Chemical',
            'Consumable',
            'WIP Sanding',
            'WIP Buffing',
            'Werate',
            'WIP Touch Up',
            'Dead Stock CM1',
        ];
    }

    // 🔒 User terkunci
    return [$this->userWarehouse()];
}


private function allowedArticleTypes(?string $warehouse): array
{
    if (is_null($warehouse)) {
        return ['RMP','RMNP','FG','CM1','CM2'];
    }

    return match ($warehouse) {
        'Raw Material'     => ['RMP','RMNP'],
        'Finish Goods'     => ['FG'],
        'WIP Buffing'      => ['FG'],
        'Werate'           => ['RMP', 'RMNP', 'FG'],
        'WIP Touch Up'     => ['FG'],
        'WIP Sanding'      => ['FG'],
        'OT'               => ['FG'],
        'Chemical'         => ['CM1'],
        'Consumable'       => ['CM2', 'CM3', 'RMP','RMNP'],
        'Dead Stock CM1'   => ['CM1'],
        default            => [],
    };
}

public function getArticlesByWarehouse(Request $request)
{
    $userWarehouse = $this->userWarehouse();

    $warehouse = $userWarehouse ?? $request->warehouse;

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $query = Article::whereIn('article_type', $allowedTypes);

    // ✅ Khusus Werate → filter supplier_code WJI
    if ($warehouse === 'Werate') {
        $query->where('supplier_code', 'LIKE', '%WJI%');
    }

    $articles = $query->select('article_code', 'description', 'unit','min_package')
        ->orderBy('description')
        ->get();

    return response()->json($articles);
}


public function store(Request $request)
{
    // =========================
    // VALIDATION
    // =========================
    $validator = \Validator::make($request->all(), [
        'sto_number'                => 'nullable|string|unique:stos,sto_number',
        'area'                      => 'nullable|string',
        'shelf'                     => 'nullable|string',
        'articles'                  => 'required|array|min:1',
        'articles.*.article_code'   => 'required|string',
        'articles.*.qty'            => 'required|numeric|min:0',
        'articles.*.uom'            => 'nullable|string',
        'articles.*.kondisi'        => 'nullable|string',
        'articles.*.location'       => 'required|string',
        'note'                      => 'nullable|string',
    ]);

    // VALIDASI OTHER
    $validator->after(function ($validator) use ($request) {
        foreach ($request->articles as $i => $row) {
            if (($row['article_code'] ?? null) === 'OTHER') {

                if (empty($row['other_name'])) {
                    $validator->errors()->add("articles.$i.other_name", "Nama part wajib diisi untuk OTHER");
                }

                if (empty($row['uom'])) {
                    $validator->errors()->add("articles.$i.uom", "UOM wajib diisi untuk OTHER");
                }
            }
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {

        $userId   = auth()->id();
        $warehouse = $this->userWarehouse();

        // =========================
        // HANDLE WAREHOUSE NULL
        // =========================
        if ($warehouse === null) {
            $firstLocation = $request->articles[0]['location'] ?? null;

            if (!$firstLocation) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Location belum dipilih',
                ], 422);
            }

            $warehouse = str_starts_with($firstLocation, 'WIP')
                ? 'Work In Progress'
                : $firstLocation;
        }

        $location = strtolower($request->articles[0]['location'] ?? '');
        $isChemical   = str_contains($location, 'chemical');
        $isConsumable = str_contains($location, 'consumable');

        $sto = null;

        // =========================
        // CHEMICAL / CONSUMABLE RULE
        // =========================
        if ($isChemical || $isConsumable) {

            $existingSto = Sto::where('warehouse', $warehouse)
                ->where('area', $request->area)
                ->where('shelves', $request->shelf)
                ->whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->first();

            if ($existingSto) {

                // user sudah terdaftar
                if (
                    $existingSto->created_by == $userId ||
                    $existingSto->created_by_2 == $userId
                ) {
                    $sto = $existingSto;
                }

                // slot ke-2 kosong
                elseif (!$existingSto->created_by_2) {

                    $existingSto->update([
                        'created_by_2' => $userId
                    ]);

                    $sto = $existingSto;
                }

                // sudah 2 user
                else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'STO ini sudah digunakan oleh 2 user'
                    ], 422);
                }

            } else {

                // create baru
                $sto = Sto::create([
                    'sto_number' => $request->sto_number ?? null,
                    'warehouse'  => $warehouse,
                    'area'       => $request->area,
                    'shelves'    => $request->shelf,
                    'note'       => $request->note,
                    'created_by' => $userId,
                ]);
            }

        } else {

            // =========================
            // NON CHEMICAL
            // =========================
            $sto = Sto::create([
                'sto_number' => $request->sto_number ?? null,
                'warehouse'  => $warehouse,
                'area'       => $request->area,
                'shelves'    => $request->shelf,
                'note'       => $request->note,
                'created_by' => $userId,
            ]);
        }

        // =========================
        // AUTO GENERATE NUMBER
        // =========================
        if (($isChemical || $isConsumable) && !$sto->sto_number) {

            $year  = $sto->created_at->format('Y');
            $month = $sto->created_at->format('m');

            $start = $isChemical ? 1000 : 2000;
            $end   = $isChemical ? 1999 : 2999;

            $lastSto = Sto::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereBetween(
                    DB::raw("CAST(SUBSTRING_INDEX(sto_number, '/', -1) AS UNSIGNED)"),
                    [$start, $end]
                )
                ->lockForUpdate()
                ->orderByDesc('sto_number')
                ->first();

            $nextIndex = $lastSto
                ? ((int) explode('/', $lastSto->sto_number)[2] + 1)
                : $start;

            if ($nextIndex > $end) {
                throw new \Exception('Index STO habis');
            }

            $sto->update([
                'sto_number' => sprintf('%s/%s/%04d', $year, $month, $nextIndex)
            ]);
        }

    // =========================
// CREATE / UPDATE ITEMS (UPSERT)
// =========================
$itemCount = 0;

// tentukan apakah user ke-2
$isSecondUser = $sto->created_by_2 == $userId;

foreach ($request->articles as $row) {

    if (empty($row['article_code']) || !isset($row['qty'])) {
        continue;
    }

    $isOverrideLocation =
        str_contains($row['location'], 'Chemical') ||
        str_contains($row['location'], 'Dead Stock CM1');

    $uom = ($row['article_code'] === 'OTHER' || $isOverrideLocation)
        ? $row['uom']
        : Article::where('article_code', $row['article_code'])->value('unit');

    // =========================
    // FIND EXISTING ITEM
    // =========================
   $rowLocation = strtolower($row['location']);
$isChemOrCons = str_contains($rowLocation, 'chemical') || str_contains($rowLocation, 'consumable');

$existingItem = null;

if (!$isChemOrCons) {
    $existingItem = $sto->items()
        ->where('article_code', $row['article_code'])
        ->where('location', $row['location'])
        ->where('uom', $uom)
        ->when($row['article_code'] === 'OTHER', function ($q) use ($row) {
            $q->where('other_name', $row['other_name']);
        })
        ->lockForUpdate()
        ->first();
}

    if ($existingItem) {

        // =========================
        // UPDATE EXISTING
        // =========================
        if ($isSecondUser) {

            $existingItem->update([
                'qty_2' => ($existingItem->qty_2 ?? 0) + $row['qty']
            ]);

        } else {

            $existingItem->update([
                'qty' => ($existingItem->qty ?? 0) + $row['qty']
            ]);
        }

    } else {

        // =========================
        // CREATE NEW
        // =========================
        $sto->items()->create([
            'article_code' => $row['article_code'],
            'other_name'   => $row['article_code'] === 'OTHER' ? $row['other_name'] : null,
            'uom'          => $uom,
            'qty'          => $isSecondUser ? 0 : $row['qty'],
            'qty_2'        => $isSecondUser ? $row['qty'] : null,
            'kondisi'     => $row['kondisi'],
            'location'     => $row['location'],
        ]);
    }

    $itemCount++;
}

if ($itemCount === 0) {
    DB::rollBack();
    return response()->json([
        'success' => false,
        'message' => 'Tidak ada item STO yang valid',
    ], 400);
}

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'STO berhasil disimpan',
            'data' => [
                'sto_id'     => $sto->id,
                'sto_number' => $sto->sto_number,
                'warehouse'  => $sto->warehouse,
                'item_count' => $itemCount,
            ]
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function checkAreaShelf(Request $request)
{
    $request->validate([
        'area'  => 'required|string',
        'shelf' => 'required|string',
    ]);

    $userId = auth()->id();

    // =========================
    // CARI STO BERDASARKAN AREA + SHELF
    // =========================
    $sto = Sto::where('area', $request->area)
        ->where('shelves', $request->shelf)
        ->first();

    // =========================
    // DEFAULT RESPONSE
    // =========================
    $response = [
        'can_input' => true,
        'reason'    => null,
    ];

    if ($sto) {

        // =========================
        // ❌ USER SUDAH PERNAH INPUT
        // =========================
        if (
            $sto->created_by == $userId ||
            $sto->created_by_2 == $userId
        ) {
            return response()->json([
                'can_input' => false,
                'reason'    => 'already_joined',
                'message'   => 'Anda sudah pernah input di area & shelf ini'
            ]);
        }

        // =========================
        // ❌ SLOT SUDAH 2 USER
        // =========================
        if ($sto->created_by && $sto->created_by_2) {
            return response()->json([
                'can_input' => false,
                'reason'    => 'slot_full',
                'message'   => 'Maksimal 2 user untuk area & shelf ini'
            ]);
        }

        // =========================
        // ✅ MASIH BISA JOIN (USER KE-2)
        // =========================
        return response()->json([
            'can_input' => true,
            'reason'    => 'available_as_second_user',
        ]);
    }

    // =========================
    // ✅ BELUM ADA STO → BEBAS
    // =========================
    return response()->json([
        'can_input' => true,
        'reason'    => 'new_sto',
    ]);
}


public function datatables(Request $request)
{
    $userId = Auth::id();
    $superUsers = [53, 2]; // <-- tambahin di sini kalau nambah lagi
    $isSuperUser = in_array($userId, $superUsers);
 
    $columns = [
        0  => null,                    // action
        1  => 'sto_items.location',
        2  => 'stos.area',
        3  => 'stos.shelves',
        4  => 'sto_items.article_code',
        5  => 'articles.description',
        6  => 'sto_items.qty_display', // kolom virtual (alias)
        7  => 'articles.min_package',
        8  => 'articles.unit',
        9  => 'status',                // alias dari select
        10  => 'stos.sto_number',
        11 => 'u1.name',    
        11 => 'u2.name',           // pakai alias u1
        12 => 'stos.created_at',
        13 => 'stos.note',
    ];
 
    $warehouse = $this->userWarehouse();
    $limit     = $request->length;
    $start     = $request->start;
 
    // =====================================================================
    // BASE QUERY
    // =====================================================================
    $query = DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->leftJoin('articles', 'articles.article_code', '=', 'sto_items.article_code')
        ->leftJoin('users as u1', 'u1.id', '=', 'stos.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'stos.created_by_2')
        ->select(
              'stos.area',        // ✅ TAMBAH INI
    'stos.shelves',     // ✅ TAMBAH INI
            'sto_items.id as sto_item_id',
            'sto_items.sto_id',
            'sto_items.location',
            'sto_items.article_code',
 
            // Nama part: OTHER pakai other_name, sisanya pakai description artikel
            DB::raw("
                CASE
                    WHEN sto_items.article_code = 'OTHER'
                    THEN sto_items.other_name
                    ELSE articles.description
                END as part_name
            "),
 
            // qty hanya milik auth sendiri
         DB::raw("
CASE
    WHEN " . ($isSuperUser ? 1 : 0) . " = 1
        THEN COALESCE(sto_items.qty, sto_items.qty_2, 0)

    WHEN {$userId} = stos.created_by
        THEN COALESCE(sto_items.qty, 0)

    WHEN {$userId} = stos.created_by_2
        THEN COALESCE(sto_items.qty_2, 0)

    ELSE 0
END as qty_display
"),
'sto_items.qty',    // qty user 1
'sto_items.qty_2',  // qty user 2
            'articles.min_package',
 
            DB::raw("
                CASE
                    WHEN sto_items.article_code = 'OTHER'
                    THEN sto_items.uom
                    ELSE articles.unit
                END as unit
            "),
 
            // ✅ STATUS 3 KONDISI — hanya untuk Chemical / Consumable
            // Logika:
            //   NOT COMPLETE → salah satu qty atau qty_2 masih NULL
            //   NOT MATCH    → keduanya sudah diisi tapi nilainya berbeda
            //   MATCH        → keduanya sudah diisi dan nilainya sama
            DB::raw("
                CASE
                    WHEN LOWER(sto_items.location) LIKE '%chemical%'
                      OR LOWER(sto_items.location) LIKE '%consumable%'
                    THEN
                        CASE
                            WHEN sto_items.qty IS NULL OR sto_items.qty_2 IS NULL
                                THEN 'NOT COMPLETE'
                            WHEN sto_items.qty = sto_items.qty_2
                                THEN 'MATCH'
                            ELSE 'NOT MATCH'
                        END
                    ELSE NULL
                END as status
            "),
 
            'stos.sto_number',
 
            // Nama created_by sesuai auth — u1 jika user pertama, u2 jika user kedua
           // ✅ BARU — superuser lihat kedua nama, non-superuser lihat nama sendiri
DB::raw("
    CASE
        WHEN " . ($isSuperUser ? 1 : 0) . " = 1
            THEN u1.name
        WHEN stos.created_by   = {$userId} THEN u1.name
        WHEN stos.created_by_2 = {$userId} THEN u2.name
        ELSE NULL
    END as created_by
"),

// Tambah kolom ini khusus superuser (u2.name selalu diambil)
DB::raw("
    CASE
        WHEN " . ($isSuperUser ? 1 : 0) . " = 1
            THEN u2.name
        ELSE NULL
    END as created_by_2_name
"),
 
            'stos.created_at',
            'stos.note'
        );
 
    // =====================================================================
    // ✅ KUNCI UTAMA ANTI-CONTEK: user hanya bisa lihat STO miliknya
    //
    // Masalah: Chemical/Consumable bisa punya 2 user dalam 1 STO (created_by
    // dan created_by_2). Tanpa filter ini, semua user bisa melihat baris
    // yang sama, termasuk qty user lain.
    //
    // Solusi: tampilkan baris HANYA jika auth user terdaftar sebagai
    // created_by ATAU created_by_2 di STO tersebut. Jika user tidak
    // terdaftar di STO itu, baris tidak akan muncul sama sekali.
    //
    // Efeknya:
    //   - User A (created_by)   → lihat qty, status, dan namanya sendiri
    //   - User B (created_by_2) → lihat qty_2, status, dan namanya sendiri
    //   - User C (tidak terdaftar di STO ini) → baris tidak muncul sama sekali
    // =====================================================================
   if (!$isSuperUser) {
    $query->where(function ($q) use ($userId) {
        $q->where('stos.created_by', $userId)
          ->orWhere('stos.created_by_2', $userId);
    });
}
 
    // =====================================================================
    // FILTER BULAN (default bulan berjalan)
    // =====================================================================
    $defaultMonth = '2026/05';
 
    $selectedMonth = $request->filled('sto_month')
        ? $request->sto_month
        : $defaultMonth;
 
    $query->where('stos.sto_number', 'like', $selectedMonth . '/%');
 
    // =====================================================================
    // FILTER WAREHOUSE (user terkunci ke warehouse tertentu)
    // =====================================================================
   if (!$isSuperUser && !is_null($warehouse)) {
    $query->where('sto_items.location', $warehouse);
}
 
    // =====================================================================
    // FILTER REQUEST TAMBAHAN
    // =====================================================================
 
    // Filter location — hanya aktif jika user bebas pilih warehouse
    if ($request->filled('location') && is_null($warehouse)) {
        $query->where('sto_items.location', $request->location);
    }
 
    // Filter article (code atau other_name)
    if ($request->filled('article')) {
        $query->where(function ($q) use ($request) {
            $q->where('sto_items.article_code', 'like', "%{$request->article}%")
              ->orWhere('sto_items.other_name', 'like', "%{$request->article}%");
        });
    }
 
    // Filter sto_number
    if ($request->filled('sto_number')) {
        $query->where('stos.sto_number', 'like', '%' . $request->sto_number . '%');
    }

    // Filter status (computed, bukan dari DB)
if ($request->filled('status')) {
    $status = $request->status;

    $query->where(function ($q) use ($status) {
        if ($status === 'MATCH') {
            $q->whereNotNull('sto_items.qty')
              ->whereNotNull('sto_items.qty_2')
              ->whereColumn('sto_items.qty', '=', 'sto_items.qty_2');
        }

        if ($status === 'NOT MATCH') {
            $q->whereNotNull('sto_items.qty')
              ->whereNotNull('sto_items.qty_2')
              ->whereColumn('sto_items.qty', '!=', 'sto_items.qty_2');
        }

        if ($status === 'NOT COMPLETE') {
            $q->where(function ($sub) {
                $sub->whereNull('sto_items.qty')
                    ->orWhereNull('sto_items.qty_2');
            });
        }
    });
}
 
    // =====================================================================
    // GLOBAL SEARCH
    // =====================================================================
    if (!empty($request->search['value'])) {
        $search = $request->search['value'];
 
        $query->where(function ($q) use ($search) {
            $q->where('sto_items.article_code', 'LIKE', "%{$search}%")
              ->orWhere('articles.description',  'LIKE', "%{$search}%")
              ->orWhere('stos.sto_number',        'LIKE', "%{$search}%")
              ->orWhere('sto_items.location',     'LIKE', "%{$search}%")
              ->orWhere('u1.name',                'LIKE', "%{$search}%")
              ->orWhere('u2.name',                'LIKE', "%{$search}%")
              ->orWhere('sto_items.other_name',   'LIKE', "%{$search}%");
        });
    }
 
    // =====================================================================
    // TOTAL RECORDS
    // ✅ Pakai query yang sama persis dengan filter ownership di atas
    //    agar angka recordsTotal / recordsFiltered akurat
    // =====================================================================
    $totalDataQuery = DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->leftJoin('articles', 'articles.article_code', '=', 'sto_items.article_code')
        ->where('stos.sto_number', 'like', $selectedMonth . '/%')
        ->where(function ($q) use ($userId) {
            $q->where('stos.created_by', $userId)
              ->orWhere('stos.created_by_2', $userId);
        });
 
   if (!$isSuperUser && !is_null($warehouse)) {
    $totalDataQuery->where('sto_items.location', $warehouse);
}
 
    $totalData = $totalFiltered = $totalDataQuery->count();
 
    // =====================================================================
    // ORDERING & PAGINATION
    // =====================================================================
    $orderColumnIndex = $request->input('order.0.column');
    $orderDir         = $request->input('order.0.dir', 'desc');
 
    if (
        isset($columns[$orderColumnIndex]) &&
        $columns[$orderColumnIndex] !== null
    ) {
        $query->orderBy($columns[$orderColumnIndex], $orderDir);
    } else {
        $query->orderBy('stos.sto_number', 'desc');
    }
 
    if ($limit != -1) {
        $query->offset($start)->limit($limit);
    }
 
    $data = $query->get();
 
    // =====================================================================
    // FORMAT DATA UNTUK RESPONSE
    // =====================================================================
    $result = [];
 
    foreach ($data as $row) {
 
        $dropdownId = 'dropdown-' . $row->sto_item_id;
        $editUrl    = route('facility.sto.edit', ['id' => $row->sto_id]);
 
        $twoDecimalLocations = ['Chemical', 'Dead Stock CM1', 'Consumable'];
 
        $qtyFormatted = in_array($row->location, $twoDecimalLocations)
    ? number_format($row->qty_display, 2)
    : number_format($row->qty_display, 0);

// ✅ Override qty untuk superuser → pakai qty (user 1)
if ($isSuperUser) {
    $qty1 = $row->qty !== null
        ? (in_array($row->location, $twoDecimalLocations) ? number_format($row->qty, 2) : number_format($row->qty, 0))
        : '-';
    $qty2 = $row->qty_2 !== null
        ? (in_array($row->location, $twoDecimalLocations) ? number_format($row->qty_2, 2) : number_format($row->qty_2, 0))
        : '-';
}
 
        // Badge status 3 kondisi
        $statusBadge = null;
        if (!is_null($row->status)) {
            $statusBadge = match ($row->status) {
                'MATCH'        => '<span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-semibold">MATCH</span>',
                'NOT MATCH'    => '<span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded font-semibold">NOT MATCH</span>',
                'NOT COMPLETE' => '<span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded font-semibold">NOT COMPLETE</span>',
                default        => null,
            };
        }
 
        $editButton = '
            <a href="' . $editUrl . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
        ';
 
        $result[] = [
            'DT_RowAttr' => [
                'data-id' => $row->sto_id,
                'class'   => 'sto-row cursor-pointer hover:bg-blue-50',
            ],
 
            'action' => '
                <div class="relative inline-block text-left">
                  <button type="button"
                    onclick="toggleDropdown(\'' . $dropdownId . '\')"
                    class="inline-flex justify-center w-full px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i data-feather="align-justify"></i>
                  </button>
                  <div id="' . $dropdownId . '"
                    class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1 text-sm text-gray-700">
                      ' . $editButton . '
                      <button onclick="deleteSTO(' . $row->sto_id . ')"
                        class="w-full text-red-500 text-left px-4 py-2 hover:bg-red-500 hover:text-gray-100">
                        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                      </button>
                    </div>
                  </div>
                </div>',
 
            'location'     => $row->location,
            'area' => $row->area,
            'shelves' => $row->shelves,
            'article_code' => $row->article_code,
            'part_name'    => $row->part_name,
            'qty'      => $isSuperUser ? $qty1 : $qtyFormatted,  // ← superuser: qty = qty1
...($isSuperUser ? [
    'qty_1' => $qty1,
    'qty_2' => $qty2,
] : []),
            'min_package'  => $row->min_package,
            'unit'         => $row->unit,
            'status'       => $statusBadge,
            'sto_number'   => $row->sto_number,
            'created_by'   => $row->created_by,
'created_by_2' => $isSuperUser ? ($row->created_by_2_name ?? '-') : null,
            'created_at'   => $row->created_at,
            'note'         => $row->note,
        ];
    }
 
    return response()->json([
        'draw'            => intval($request->draw),
        'recordsTotal'    => $totalData,
        'recordsFiltered' => $totalFiltered,
        'data'            => $result,
    ]);
}



public function edit($id)
{
    $sto = Sto::with([
        'items' => function ($q) {
            $q->with(['article' => function ($a) {
                $a->select('id', 'article_code', 'description', 'unit', 'min_package');
            }]);
        }
    ])->findOrFail($id);

    $items = $sto->items;

    $warehouse = optional($items->first())->location ?? null;

    $canChooseWarehouse = is_null($warehouse);

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    // 🔥 Ambil artikel yang sudah dipakai
    $usedArticleCodes = $items->pluck('article_code')->toArray();

    $query = Article::whereIn('article_type', $allowedTypes)
        ->orWhereIn('article_code', $usedArticleCodes);

    // ✅ Werate filter
    if ($warehouse === 'Werate') {
        $query->where(function ($q) use ($usedArticleCodes) {
            $q->where('supplier_code', 'LIKE', '%WJI%')
              ->orWhereIn('article_code', $usedArticleCodes);
        });
    }

    $articles = $query
        ->select('id', 'article_code', 'description', 'unit', 'article_type', 'min_package')
        ->orderBy('description')
        ->get();

    $allowedWarehouses = $this->allowedWarehouses();

    return view('facility.edit-sto', compact(
        'sto',
        'items',
        'articles',
        'warehouse',
        'canChooseWarehouse',
        'allowedWarehouses'
    ));
}



public function update(Request $request, $id)
{
    DB::transaction(function () use ($request, $id) {

        $sto = Sto::findOrFail($id);
        $sto->note = $request->note;
        $sto->save();

        // reset items
        StoItem::where('sto_id', $sto->id)->delete();

        foreach ($request->articles as $row) {
            StoItem::create([
                'sto_id'       => $sto->id,
                'article_code' => $row['article_code'],
                'qty'          => $row['qty'],
                'uom'          => $row['uom'] ?? null,           // 🔹 UOM
                'other_name'   => $row['other_name'] ?? null,   // 🔹 OTHER name
                'location'     => $row['location'],
            ]);
        }
    });

    return response()->json([
        'message' => 'STO berhasil diperbarui'
    ]);
}


public function selectSto(Request $request)
{
    $search = $request->get('q');

    $stos = Sto::select('id', 'sto_number')
        ->when($search, function ($q) use ($search) {
            $q->where('sto_number', 'like', "%{$search}%");
        })
        ->orderBy('sto_number', 'desc')
        ->get();

    return response()->json([
        'results' => $stos->map(function ($sto) {
            return [
                'id'   => $sto->sto_number, // ⬅ dipakai untuk filter
                'text' => $sto->sto_number,
            ];
        }),
    ]);
}

public function selectArticle(Request $request)
{
    $search    = $request->get('q');
    $page      = $request->get('page', 1);
    $perPage   = 20;
    $offset    = ($page - 1) * $perPage;

    $warehouse = $this->userWarehouse() ?? $request->warehouse;
    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $isReferenceWarehouse = in_array(strtolower($warehouse), ['chemical', 'consumable']);

    $query = Article::query()
        ->select(
            'articles.article_code',
            'articles.unit as default_unit',
            'articles.description',
            'articles.article_type',
            'articles.min_package',
            DB::raw('COALESCE(sto_reference_items.uom, articles.unit) as unit') // 🔥 override
        )
        ->leftJoin('sto_reference_items', function ($join) use ($warehouse, $isReferenceWarehouse) {
            if ($isReferenceWarehouse) {
                $join->on('sto_reference_items.article_code', '=', 'articles.article_code')
                     ->where('sto_reference_items.warehouse', $warehouse);
            }
        })
        ->when(!empty($allowedTypes), fn($q) => 
            $q->whereIn('articles.article_type', $allowedTypes)
        )
        ->when($search, fn($q) => 
            $q->where(function ($qq) use ($search) {
                $qq->where('articles.article_code', 'like', "%{$search}%")
                   ->orWhere('articles.description', 'like', "%{$search}%");
            })
        )
        ->orderBy('articles.article_code');

    $totalCount = $query->count();

    $articles = $query->offset($offset)
                      ->limit($perPage)
                      ->get();

    return response()->json([
        'results' => $articles->map(fn($a) => [
            'id'   => $a->article_code,
            'text' => "{$a->article_code} - {$a->description}",
            'unit' => $a->unit, // 🔥 sudah auto override
            'min_package' => $a->min_package,
        ]),
        'pagination' => [
            'more' => $offset + $perPage < $totalCount
        ]
    ]);
}





public function destroy($id)
{
    $sto = STO::findOrFail($id);

    // Hapus semua item terkait
    $sto->items()->delete(); 

    // Hapus header STO
    $sto->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'STO berhasil dihapus'
    ]);
}



public function exportReport()
{
    /*
    |--------------------------------------------------------------------------
    | 1. AMBIL PERIODE STO
    |--------------------------------------------------------------------------
    */
    $periodes = DB::table('stos')
        ->selectRaw("DISTINCT SUBSTRING(sto_number,1,7) as periode")
        ->orderBy('periode')
        ->pluck('periode')
        ->toArray();

    if(empty($periodes)){
        abort(404,'Tidak ada data STO');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. QUERY DATA
    |--------------------------------------------------------------------------
    */
    $rows = DB::select("
/* =====================================================
   1. DATA YANG MATCH BOM (NORMAL)
===================================================== */
SELECT
    SUBSTRING(s.sto_number,1,7) AS periode,

    bh.article_rm AS rm_code,
    bh.article_rm_desc AS rm_desc,
    bh.article_fg AS fg_code,
    bh.article_fg_desc AS fg_desc,
    COALESCE(a.unit,'PCS') AS uom,

    SUM(CASE WHEN si.location='Raw Material' THEN si.qty ELSE 0 END) qty_rm,
    SUM(CASE WHEN si.location='WIP Buffing' THEN si.qty ELSE 0 END) qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding' THEN si.qty ELSE 0 END) qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) qty_touch,
    SUM(CASE WHEN si.location='Werate' THEN si.qty ELSE 0 END) qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) qty_fg,
    SUM(CASE WHEN si.location='OT' THEN si.qty ELSE 0 END) qty_ot,

    1 AS sort_group

FROM stos s
JOIN sto_items si ON si.sto_id = s.id

LEFT JOIN (
    SELECT
        code AS bom_code,
        article_rm,
        article_rm_desc,
        article_fg,
        article_fg_desc
    FROM boms
    GROUP BY
        code,
        article_rm,
        article_rm_desc,
        article_fg,
        article_fg_desc
) bh
ON (
    (si.location='Raw Material' AND si.article_code = bh.article_rm)
    OR
    (si.location!='Raw Material' AND si.article_code = bh.article_fg)
)

LEFT JOIN articles a
    ON a.article_code = bh.article_fg

WHERE bh.bom_code IS NOT NULL

GROUP BY
    periode,
    bh.bom_code,
    bh.article_rm,
    bh.article_rm_desc,
    bh.article_fg,
    bh.article_fg_desc,
    a.unit


UNION ALL


/* =====================================================
   2. DATA TANPA BOM → OTHER
===================================================== */
SELECT
    SUBSTRING(s.sto_number,1,7) AS periode,

    'OTHER' AS rm_code,
    si.other_name AS rm_desc,
    'OTHER' AS fg_code,
    si.other_name AS fg_desc,
    'PCS' AS uom,

    SUM(CASE WHEN si.location='Raw Material' THEN si.qty ELSE 0 END) qty_rm,
    SUM(CASE WHEN si.location='WIP Buffing' THEN si.qty ELSE 0 END) qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding' THEN si.qty ELSE 0 END) qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) qty_touch,
    SUM(CASE WHEN si.location='Werate' THEN si.qty ELSE 0 END) qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) qty_fg,
    SUM(CASE WHEN si.location='OT' THEN si.qty ELSE 0 END) qty_ot,

    0 AS sort_group

FROM stos s
JOIN sto_items si ON si.sto_id = s.id

LEFT JOIN boms b
    ON si.article_code IN (b.article_rm, b.article_fg)

WHERE b.code IS NULL
AND si.other_name IS NOT NULL
AND si.other_name <> ''

GROUP BY
    periode,
    si.other_name


ORDER BY
    sort_group ASC,
    rm_code,
    fg_code
");

    /*
    |--------------------------------------------------------------------------
    | 3. PIVOT DATA
    |--------------------------------------------------------------------------
    */
    $data = [];

    foreach($rows as $r){
       if($r->rm_code === 'OTHER'){
    $key = 'OTHER|'.$r->rm_desc; // pakai nama other
}else{
    $key = $r->rm_code.'|'.$r->fg_code;
}

        $data[$key]['info'] = [
            $r->rm_code,
            $r->rm_desc,
            $r->fg_code,
            $r->fg_desc,
            $r->uom
        ];

        $data[$key]['periode'][$r->periode] = $r;
    }

    /*
|--------------------------------------------------------------------------
| SORT DATA AGAR GROUP RM STABIL
|--------------------------------------------------------------------------
*/
ksort($data, SORT_NATURAL);

    /*
    |--------------------------------------------------------------------------
    | 4. CREATE EXCEL
    |--------------------------------------------------------------------------
    */
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* helper warna */
    $color=function($range,$bg,$font='FFFFFF') use ($sheet){
        $sheet->getStyle($range)->applyFromArray([
            'font'=>['bold'=>true,'color'=>['rgb'=>$font]],
            'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'=>[
                'fillType'=>Fill::FILL_SOLID,
                'startColor'=>['rgb'=>$bg]
            ]
        ]);
    };

    /*
|--------------------------------------------------------------------------
| HEADER TITLE BILL OF MATERIAL
|--------------------------------------------------------------------------
*/
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'BILL OF MATERIAL');

$sheet->getStyle('A1:E1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '111827'] // abu gelap elegan
    ]
]);

    /* header static */
    $sheet->fromArray(
        ['RM Code','RM Description','FG Code','FG Description','UOM'],
        null,
        'A2'
    );

    $color('A2:E3','000000');

    /*
    |--------------------------------------------------------------------------
    | 5. HEADER DINAMIS PER PERIODE
    |--------------------------------------------------------------------------
    */
    $startColIndex = 6; // F

    $bulanNama = [
        '01'=>'JANUARI','02'=>'FEBRUARI','03'=>'MARET','04'=>'APRIL',
        '05'=>'MEI','06'=>'JUNI','07'=>'JULI','08'=>'AGUSTUS',
        '09'=>'SEPTEMBER','10'=>'OKTOBER','11'=>'NOVEMBER','12'=>'DESEMBER'
    ];

    foreach($periodes as $periode){

        $year = substr($periode,0,4);
        $month = substr($periode,5,2);

        $title = "STO ".$bulanNama[$month]." ".$year;

        $start = Coordinate::stringFromColumnIndex($startColIndex);
        $end   = Coordinate::stringFromColumnIndex($startColIndex+7);

        /* TITLE */
        $sheet->mergeCells("$start"."1:$end"."1");
        $sheet->setCellValue("$start"."1",$title);
        $color("$start"."1:$end"."1",'F97316');

       /* ================================
   GROUP HEADER (MERGED CLEAN)
================================ */

/* RAW MATERIAL (merge vertical) */
$colRM = Coordinate::stringFromColumnIndex($startColIndex);
$sheet->mergeCells($colRM.'2:'.$colRM.'3');
$sheet->setCellValue($colRM.'2','RM');

/* WIP (merge horizontal) */
$wipStart = Coordinate::stringFromColumnIndex($startColIndex+1);
$wipEnd   = Coordinate::stringFromColumnIndex($startColIndex+4);

$sheet->mergeCells($wipStart.'2:'.$wipEnd.'2');
$sheet->setCellValue($wipStart.'2','WIP');

/* FINISH GOODS (merge vertical) */
$colFG = Coordinate::stringFromColumnIndex($startColIndex+5);
$sheet->mergeCells($colFG.'2:'.$colFG.'3');
$sheet->setCellValue($colFG.'2','FG');

/* OT (merge vertical) */
$colOT = Coordinate::stringFromColumnIndex($startColIndex+6);
$sheet->mergeCells($colOT.'2:'.$colOT.'3');
$sheet->setCellValue($colOT.'2','OT');

/* TOTAL (merge vertical) */
$colTotal = Coordinate::stringFromColumnIndex($startColIndex+7);
$sheet->mergeCells($colTotal.'2:'.$colTotal.'3');
$sheet->setCellValue($colTotal.'2','TOTAL');

        /* SUB HEADER */
        $sub=['','Buffing','Sanding','Touch Up','Werate','','',''];

        for($i=0;$i<8;$i++){
            $sheet->setCellValue(
                Coordinate::stringFromColumnIndex($startColIndex+$i).'3',
                $sub[$i]
            );
        }

        /* WARNA */
        $color("$start"."2:$start"."3",'2563EB');
        $color(
            Coordinate::stringFromColumnIndex($startColIndex+1).'2:'.
            Coordinate::stringFromColumnIndex($startColIndex+4).'3',
            'FACC15','000000'
        );
        $color(Coordinate::stringFromColumnIndex($startColIndex+5).'2:'.
               Coordinate::stringFromColumnIndex($startColIndex+5).'3','16A34A');

        $color(Coordinate::stringFromColumnIndex($startColIndex+6).'2:'.
               Coordinate::stringFromColumnIndex($startColIndex+6).'3','9CA3AF','000000');

        $color(Coordinate::stringFromColumnIndex($startColIndex+7).'2:'.
               Coordinate::stringFromColumnIndex($startColIndex+7).'3','DC2626');

        $startColIndex += 8;
    }

    /*
    |--------------------------------------------------------------------------
    | 6. INSERT DATA
    |--------------------------------------------------------------------------
    */
  $rowIndex = 4;
$lastRM   = null;

foreach($data as $item){

    $rmCode = $item['info'][0];
    $rmDesc = $item['info'][1];

    /*
    |--------------------------------------------------
    | RM HANYA MUNCUL DI BARIS PERTAMA GROUP
    |--------------------------------------------------
    */
   $isSameRM = ($rmCode !== 'OTHER' && $rmCode === $lastRM);

    $rowInfo = [
        $isSameRM ? '' : $rmCode,
        $isSameRM ? '' : $rmDesc,
        $item['info'][2], // FG Code
        $item['info'][3], // FG Desc
        $item['info'][4], // UOM
    ];

    $sheet->fromArray($rowInfo,null,"A{$rowIndex}");

    /*
    |--------------------------------------------------
    | INSERT QTY PER PERIODE
    |--------------------------------------------------
    */
    $col = 6;

    foreach($periodes as $periode){

        $d = $item['periode'][$periode] ?? null;

        /*
        | qty RM hanya di baris pertama RM
        */
        $rm    = $isSameRM ? 0 : ($d->qty_rm ?? 0);

        $buff  = $d->qty_buff ?? 0;
        $sand  = $d->qty_sand ?? 0;
        $touch = $d->qty_touch ?? 0;
        $wer   = $d->qty_werate ?? 0;
        $fg    = $d->qty_fg ?? 0;
        $ot    = $d->qty_ot ?? 0;

        $total = $rm + $buff + $sand + $touch + $wer + $fg + $ot;

        $sheet->fromArray(
            [$rm,$buff,$sand,$touch,$wer,$fg,$ot,$total],
            null,
            Coordinate::stringFromColumnIndex($col).$rowIndex
        );

        $col += 8;
    }

    $lastRM = $rmCode;
    $rowIndex++;
}
     /*
    |--------------------------------------------------------------------------
    | 8. TOTAL ROW
    |--------------------------------------------------------------------------
    */
    $totalRow=$rowIndex+1;

    $sheet->setCellValue("A{$totalRow}","TOTAL");
    $sheet->mergeCells("A{$totalRow}:E{$totalRow}");

    $col=6;

    foreach($periodes as $periode){

        $firstDataRow=4;
        $lastDataRow=$rowIndex-1;

        for($i=0;$i<8;$i++){

            $c=Coordinate::stringFromColumnIndex($col+$i);

            $sheet->setCellValue(
                "{$c}{$totalRow}",
                "=SUM({$c}{$firstDataRow}:{$c}{$lastDataRow})"
            );
        }

        $col+=8;
    }


    /*
    |--------------------------------------------------------------------------
    | AUTO WIDTH (FIX ERROR SEBELUMNYA)
    |--------------------------------------------------------------------------
    */
    $highestColumnIndex = Coordinate::columnIndexFromString(
        $sheet->getHighestColumn()
    );

    for($i=1;$i<=$highestColumnIndex;$i++){
        $sheet->getColumnDimension(
            Coordinate::stringFromColumnIndex($i)
        )->setAutoSize(true);
    }

    $sheet->freezePane('F4');

    /*
|--------------------------------------------------------------------------
| 7. BORDER + VERTICAL ALIGN CENTER
|--------------------------------------------------------------------------
*/

/* cari batas akhir data */
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

/* range dari A1 sampai cell terakhir */
$fullRange = "A1:{$highestColumn}{$highestRow}";

/* APPLY BORDER + ALIGNMENT */
$sheet->getStyle($fullRange)->applyFromArray([
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
]);
    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD
    |--------------------------------------------------------------------------
    */
    $fileName='STO_REPORT_'.now()->format('Y-m-d').'.xlsx';

    return new StreamedResponse(function() use($spreadsheet){
        (new Xlsx($spreadsheet))->save('php://output');
    },200,[
        "Content-Type"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "Content-Disposition"=>"attachment; filename=\"$fileName\"",
        "Cache-Control"=>"max-age=0",
    ]);
}







public function exportReview(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | 1. TENTUKAN PERIODE FILTER
    |--------------------------------------------------------------------------
    */
    $allPeriodes = DB::table('stos')
        ->selectRaw("DISTINCT REPLACE(SUBSTRING(sto_number,1,7), '/', '-') as periode")
        ->orderBy('periode')
        ->pluck('periode')
        ->toArray();

    if (empty($allPeriodes)) {
        abort(404, 'Tidak ada data STO');
    }

    if (auth()->id() == 53 && $request->filled('periode')) {
        $periodeAktif = str_replace('/', '-', $request->input('periode'));
    } else {
        $periodeAktif = end($allPeriodes);
    }

   $periodeAktifCarbon = \Carbon\Carbon::createFromFormat('Y-m-d', $periodeAktif . '-01');
    $periodeSebelumnya    = $periodeAktifCarbon->copy()->subMonth()->format('Y-m');
    $hasPeriodeSebelumnya = in_array($periodeSebelumnya, $allPeriodes);

    /*
    |--------------------------------------------------------------------------
    | 2. QUERY DATA STO
    |--------------------------------------------------------------------------
    */
    $periodeFilter = array_values(array_filter(
        [$periodeSebelumnya, $periodeAktif],
        fn($p) => in_array($p, $allPeriodes)
    ));

    $placeholders = implode(',', array_fill(0, count($periodeFilter), '?'));

    $rows = DB::select("
SELECT
    REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') AS periode,
    bh.article_rm      AS rm_code,
    bh.article_rm_desc AS rm_desc,
    bh.article_fg      AS fg_code,
    bh.article_fg_desc AS fg_desc,
    COALESCE(a.unit,'PCS') AS uom,
    SUM(CASE WHEN si.location='Raw Material' THEN si.qty ELSE 0 END) AS qty_rm,
    SUM(CASE WHEN si.location='WIP Buffing'  THEN si.qty ELSE 0 END) AS qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding'  THEN si.qty ELSE 0 END) AS qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) AS qty_touch,
    SUM(CASE WHEN si.location='Werate'       THEN si.qty ELSE 0 END) AS qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) AS qty_fg,
    SUM(CASE WHEN si.location='OT'           THEN si.qty ELSE 0 END) AS qty_ot,
    1 AS sort_group
FROM stos s
JOIN sto_items si ON si.sto_id = s.id
LEFT JOIN (
    SELECT code AS bom_code, article_rm, article_rm_desc, article_fg, article_fg_desc
    FROM boms
    GROUP BY code, article_rm, article_rm_desc, article_fg, article_fg_desc
) bh ON (
    (si.location='Raw Material' AND si.article_code = bh.article_rm)
    OR (si.location!='Raw Material' AND si.article_code = bh.article_fg)
)
LEFT JOIN articles a ON a.article_code = bh.article_fg
WHERE bh.bom_code IS NOT NULL
  AND REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') IN ({$placeholders})
GROUP BY periode, bh.bom_code, bh.article_rm, bh.article_rm_desc, bh.article_fg, bh.article_fg_desc, a.unit

UNION ALL

SELECT
    REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') AS periode,
    'OTHER' AS rm_code, si.other_name AS rm_desc,
    'OTHER' AS fg_code, si.other_name AS fg_desc, 'PCS' AS uom,
    SUM(CASE WHEN si.location='Raw Material' THEN si.qty ELSE 0 END) AS qty_rm,
    SUM(CASE WHEN si.location='WIP Buffing'  THEN si.qty ELSE 0 END) AS qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding'  THEN si.qty ELSE 0 END) AS qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) AS qty_touch,
    SUM(CASE WHEN si.location='Werate'       THEN si.qty ELSE 0 END) AS qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) AS qty_fg,
    SUM(CASE WHEN si.location='OT'           THEN si.qty ELSE 0 END) AS qty_ot,
    0 AS sort_group
FROM stos s
JOIN sto_items si ON si.sto_id = s.id
LEFT JOIN boms b ON si.article_code IN (b.article_rm, b.article_fg)
WHERE b.code IS NULL
  AND si.other_name IS NOT NULL AND si.other_name <> ''
  AND REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') IN ({$placeholders})
GROUP BY periode, si.other_name
ORDER BY sort_group ASC, rm_code, fg_code
", array_merge($periodeFilter, $periodeFilter));

    /*
    |--------------------------------------------------------------------------
    | 2B-2D. BELI / TF IN / KIRIM — periode aktif saja
    |--------------------------------------------------------------------------
    */
    $beliRows = DB::select(
        "SELECT lt.article_code AS rm_code, SUM(lt.qty) AS qty_beli
         FROM lpb_temporary lt WHERE DATE_FORMAT(lt.do_date, '%Y-%m') = ?
         GROUP BY lt.article_code",
        [$periodeAktif]
    );
    $beliIndex = [];
    foreach ($beliRows as $br) { $beliIndex[$br->rm_code] = $br->qty_beli; }

    $tfinRows = DB::select(
        "SELECT it.article_code AS rm_code, SUM(it.qty) AS qty_tfin
         FROM in_temporary it WHERE DATE_FORMAT(it.date, '%Y-%m') = ?
         GROUP BY it.article_code",
        [$periodeAktif]
    );
    $tfinIndex = [];
    foreach ($tfinRows as $tr) { $tfinIndex[$tr->rm_code] = $tr->qty_tfin; }

    $kirimRows = DB::select(
        "SELECT st.article_code AS fg_code, SUM(st.delivery_qty) AS qty_kirim
         FROM sj_temporary st WHERE DATE_FORMAT(st.delivery_date, '%Y-%m') = ?
         GROUP BY st.article_code",
        [$periodeAktif]
    );
    $kirimIndex = [];
    foreach ($kirimRows as $kr) { $kirimIndex[$kr->fg_code] = $kr->qty_kirim; }

    /*
    |--------------------------------------------------------------------------
    | 3. PIVOT DATA STO
    |--------------------------------------------------------------------------
    */
    $data = [];
    foreach ($rows as $r) {
        $key = ($r->rm_code === 'OTHER') ? 'OTHER|' . $r->rm_desc : $r->rm_code . '|' . $r->fg_code;
        $data[$key]['info']                 = [$r->rm_code, $r->rm_desc, $r->fg_code, $r->fg_desc, $r->uom];
        $data[$key]['periode'][$r->periode] = $r;
    }
    ksort($data, SORT_NATURAL);

    /*
    |--------------------------------------------------------------------------
    | 3B. RM GROUPS + HELPER CALC
    |--------------------------------------------------------------------------
    */
    $rmGroups = [];
    foreach ($data as $key => $item) {
        $rmGroups[$item['info'][0]][] = $key;
    }

    $calcStockSto = function (string $rmCode, string $periode) use ($data, $rmGroups) {
        $total = 0; $rmSeen = false;
        foreach ($rmGroups[$rmCode] as $key) {
            $d = $data[$key]['periode'][$periode] ?? null;
            $total += (!$rmSeen ? ($d->qty_rm ?? 0) : 0)
                    + ($d->qty_buff ?? 0) + ($d->qty_sand ?? 0)
                    + ($d->qty_touch ?? 0) + ($d->qty_werate ?? 0)
                    + ($d->qty_fg ?? 0) + ($d->qty_ot ?? 0);
            $rmSeen = true;
        }
        return $total;
    };

    /*
    |--------------------------------------------------------------------------
    | 3C. PRE-COMPUTE BELI / TFIN / KIRIM / STOCK ADMIN PER RM
    |--------------------------------------------------------------------------
    */
    $beliByRM = $tfinByRM = $kirimByRM = $stockAdminByRM = [];

    foreach ($rmGroups as $rmCode => $keys) {
        $stoSebel = $hasPeriodeSebelumnya ? $calcStockSto($rmCode, $periodeSebelumnya) : 0;
        $beli  = ($rmCode !== 'OTHER') ? ($beliIndex[$rmCode] ?? 0) : 0;
        $tfin  = ($rmCode !== 'OTHER') ? ($tfinIndex[$rmCode] ?? 0) : 0;
        $kirim = 0;
        foreach ($keys as $key) {
            $fgCode = $data[$key]['info'][2];
            $kirim += ($fgCode !== 'OTHER') ? ($kirimIndex[$fgCode] ?? 0) : 0;
        }
        $beliByRM[$rmCode]       = $beli;
        $tfinByRM[$rmCode]       = $tfin;
        $kirimByRM[$rmCode]      = $kirim;
        $stockAdminByRM[$rmCode] = $stoSebel + $beli + $tfin - $kirim;
    }

    /*
    |--------------------------------------------------------------------------
    | 4. BUAT EXCEL
    |--------------------------------------------------------------------------
    */
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();

    $color = function (string $range, string $bg, string $font = 'FFFFFF') use ($sheet) {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $font]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
        ]);
    };

    /*
    |--------------------------------------------------------------------------
    | 5. HEADER BARIS 1 — Judul tabel (A-E)
    |--------------------------------------------------------------------------
    */
    $sheet->mergeCells('A1:E1');
    $sheet->setCellValue('A1', 'BILL OF MATERIAL');
    $sheet->getStyle('A1:E1')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
    ]);
    $sheet->fromArray(['RM Code', 'RM Description', 'FG Code', 'FG Description', 'UOM'], null, 'A2');
    $color('A2:E3', '000000');

    /*
    |--------------------------------------------------------------------------
    | 5B. DEFINISI OFFSET KOLOM
    |
    |  BASE = 6 (kolom F)
    |
    |  ┌─────────────────────────────────────────────────────────────────────┐
    |  │  BLOK A  │  BLOK B   │       BLOK C        │       BLOK D          │
    |  │ STO SBEL │  MUTASI   │     STO AKTIF        │   PERBANDINGAN        │
    |  ├──────────┼───────────┼──────────────────────┼───────────────────────┤
    |  │+0  RM    │+8  BELI   │+12 RM                │+20 STOCK ADMIN        │
    |  │+1  Buff  │+9  TF IN  │+13 WIP Buffing       │+21 SELISIH            │
    |  │+2  Sand  │+10 KIRIM  │+14 WIP Sanding       │                       │
    |  │+3  Touch │+11 SADMIN │+15 WIP Touch Up      │                       │
    |  │+4  Wer   │           │+16 WIP Werate        │                       │
    |  │+5  FG    │           │+17 FG                │                       │
    |  │+6  OT    │           │+18 OT                │                       │
    |  │+7  TOTAL │           │+19 STOCK STO         │                       │
    |  └──────────┴───────────┴──────────────────────┴───────────────────────┘
    |--------------------------------------------------------------------------
    */
    $BASE = 6;

    $OFF = [
        // BLOK A — STO Sebelumnya (8 kolom)
        'A_RM'      =>  0, 'A_BUFF'  =>  1, 'A_SAND'  =>  2, 'A_TOUCH' =>  3,
        'A_WER'     =>  4, 'A_FG'    =>  5, 'A_OT'    =>  6, 'A_TOTAL' =>  7,
        // BLOK B — Mutasi periode aktif (4 kolom)
        'B_BELI'    =>  8, 'B_TFIN'  =>  9, 'B_KIRIM' => 10, 'B_SADMIN' => 11,
        // BLOK C — STO Aktif (8 kolom)
        'C_RM'      => 12, 'C_BUFF'  => 13, 'C_SAND'  => 14, 'C_TOUCH' => 15,
        'C_WER'     => 16, 'C_FG'    => 17, 'C_OT'    => 18, 'C_STOCKSTO' => 19,
        // BLOK D — Perbandingan (2 kolom)
        'D_SADMIN'  => 20, 'D_SELISIH' => 21,
    ];

    // Konversi offset → string kolom Excel
    $col = fn(int $off): string => Coordinate::stringFromColumnIndex($BASE + $off);

    /*
    |--------------------------------------------------------------------------
    | 5C. HEADER BARIS 1 — Judul blok
    |--------------------------------------------------------------------------
    */
    $bulanNama = [
        '01'=>'JANUARI','02'=>'FEBRUARI','03'=>'MARET','04'=>'APRIL',
        '05'=>'MEI','06'=>'JUNI','07'=>'JULI','08'=>'AGUSTUS',
        '09'=>'SEPTEMBER','10'=>'OKTOBER','11'=>'NOVEMBER','12'=>'DESEMBER',
    ];

    $titleSebel  = 'STO '   . ($bulanNama[substr($periodeSebelumnya,5,2)] ?? '') . ' ' . substr($periodeSebelumnya,0,4);
    $titleMutasi = 'MUTASI ' . ($bulanNama[substr($periodeAktif,5,2)]     ?? '') . ' ' . substr($periodeAktif,0,4);
    $titleAktif  = 'STO '   . ($bulanNama[substr($periodeAktif,5,2)]     ?? '') . ' ' . substr($periodeAktif,0,4);

    // BLOK A (offset 0-7)
    $sheet->mergeCells($col($OFF['A_RM'])    . '1:' . $col($OFF['A_TOTAL'])   . '1');
    $sheet->setCellValue($col($OFF['A_RM'])  . '1', $titleSebel);
    $color($col($OFF['A_RM']) . '1:' . $col($OFF['A_TOTAL']) . '1', '0369A1');

    // BLOK B (offset 8-11)
    $sheet->mergeCells($col($OFF['B_BELI'])  . '1:' . $col($OFF['B_SADMIN']) . '1');
    $sheet->setCellValue($col($OFF['B_BELI']) . '1', $titleMutasi);
    $color($col($OFF['B_BELI']) . '1:' . $col($OFF['B_SADMIN']) . '1', '4C1D95');

    // BLOK C (offset 12-19)
    $sheet->mergeCells($col($OFF['C_RM'])    . '1:' . $col($OFF['C_STOCKSTO']) . '1');
    $sheet->setCellValue($col($OFF['C_RM'])  . '1', $titleAktif);
    $color($col($OFF['C_RM']) . '1:' . $col($OFF['C_STOCKSTO']) . '1', 'F97316');

    // BLOK D (offset 20-21)
    $sheet->mergeCells($col($OFF['D_SADMIN']) . '1:' . $col($OFF['D_SELISIH']) . '1');
    $sheet->setCellValue($col($OFF['D_SADMIN']) . '1', 'PERBANDINGAN');
    $color($col($OFF['D_SADMIN']) . '1:' . $col($OFF['D_SELISIH']) . '1', '065F46');

    /*
    |--------------------------------------------------------------------------
    | 5D. HEADER BARIS 2-3 — Sub-header tiap blok
    |--------------------------------------------------------------------------
    */

    // ── BLOK A ───────────────────────────────────────────────────────────────
    $sheet->mergeCells($col($OFF['A_RM'])   . '2:' . $col($OFF['A_RM'])    . '3');
    $sheet->setCellValue($col($OFF['A_RM']) . '2', 'RM');
    $color($col($OFF['A_RM']) . '2:' . $col($OFF['A_RM']) . '3', '2563EB');

    $sheet->mergeCells($col($OFF['A_BUFF']) . '2:' . $col($OFF['A_WER'])   . '2');
    $sheet->setCellValue($col($OFF['A_BUFF']) . '2', 'WIP');
    $color($col($OFF['A_BUFF']) . '2:' . $col($OFF['A_WER']) . '3', 'FACC15', '000000');
    foreach (['Buffing','Sanding','Touch Up','Werate'] as $i => $sub) {
        $sheet->setCellValue($col($OFF['A_BUFF'] + $i) . '3', $sub);
    }

    $sheet->mergeCells($col($OFF['A_FG'])    . '2:' . $col($OFF['A_FG'])   . '3');
    $sheet->setCellValue($col($OFF['A_FG'])  . '2', 'FG');
    $color($col($OFF['A_FG']) . '2:' . $col($OFF['A_FG']) . '3', '16A34A');

    $sheet->mergeCells($col($OFF['A_OT'])    . '2:' . $col($OFF['A_OT'])   . '3');
    $sheet->setCellValue($col($OFF['A_OT'])  . '2', 'OT');
    $color($col($OFF['A_OT']) . '2:' . $col($OFF['A_OT']) . '3', '9CA3AF', '000000');

    $sheet->mergeCells($col($OFF['A_TOTAL']) . '2:' . $col($OFF['A_TOTAL']) . '3');
    $sheet->setCellValue($col($OFF['A_TOTAL']) . '2', 'TOTAL STO');
    $color($col($OFF['A_TOTAL']) . '2:' . $col($OFF['A_TOTAL']) . '3', 'DC2626');

    // ── BLOK B ───────────────────────────────────────────────────────────────
    foreach ([
        [$OFF['B_BELI'],   'BELI',        '7C3AED'],
        [$OFF['B_TFIN'],   'TF IN',       'BE185D'],
        [$OFF['B_KIRIM'],  'KIRIM',       '0891B2'],
        [$OFF['B_SADMIN'], 'STOCK ADMIN', '92400E'],
    ] as [$off, $label, $bg]) {
        $sheet->mergeCells($col($off) . '2:' . $col($off) . '3');
        $sheet->setCellValue($col($off) . '2', $label);
        $color($col($off) . '2:' . $col($off) . '3', $bg);
    }

    // ── BLOK C ───────────────────────────────────────────────────────────────
    $sheet->mergeCells($col($OFF['C_RM'])      . '2:' . $col($OFF['C_RM'])      . '3');
    $sheet->setCellValue($col($OFF['C_RM'])    . '2', 'RM');
    $color($col($OFF['C_RM']) . '2:' . $col($OFF['C_RM']) . '3', '2563EB');

    $sheet->mergeCells($col($OFF['C_BUFF'])    . '2:' . $col($OFF['C_WER'])     . '2');
    $sheet->setCellValue($col($OFF['C_BUFF'])  . '2', 'WIP');
    $color($col($OFF['C_BUFF']) . '2:' . $col($OFF['C_WER']) . '3', 'FACC15', '000000');
    foreach (['Buffing','Sanding','Touch Up','Werate'] as $i => $sub) {
        $sheet->setCellValue($col($OFF['C_BUFF'] + $i) . '3', $sub);
    }

    $sheet->mergeCells($col($OFF['C_FG'])      . '2:' . $col($OFF['C_FG'])      . '3');
    $sheet->setCellValue($col($OFF['C_FG'])    . '2', 'FG');
    $color($col($OFF['C_FG']) . '2:' . $col($OFF['C_FG']) . '3', '16A34A');

    $sheet->mergeCells($col($OFF['C_OT'])      . '2:' . $col($OFF['C_OT'])      . '3');
    $sheet->setCellValue($col($OFF['C_OT'])    . '2', 'OT');
    $color($col($OFF['C_OT']) . '2:' . $col($OFF['C_OT']) . '3', '9CA3AF', '000000');

    $sheet->mergeCells($col($OFF['C_STOCKSTO']) . '2:' . $col($OFF['C_STOCKSTO']) . '3');
    $sheet->setCellValue($col($OFF['C_STOCKSTO']) . '2', 'STOCK STO');
    $color($col($OFF['C_STOCKSTO']) . '2:' . $col($OFF['C_STOCKSTO']) . '3', 'DC2626');

    // ── BLOK D ───────────────────────────────────────────────────────────────
    $sheet->mergeCells($col($OFF['D_SADMIN'])  . '2:' . $col($OFF['D_SADMIN'])  . '3');
    $sheet->setCellValue($col($OFF['D_SADMIN']) . '2', 'STOCK ADMIN');
    $color($col($OFF['D_SADMIN']) . '2:' . $col($OFF['D_SADMIN']) . '3', '92400E');

    $sheet->mergeCells($col($OFF['D_SELISIH']) . '2:' . $col($OFF['D_SELISIH']) . '3');
    $sheet->setCellValue($col($OFF['D_SELISIH']) . '2', 'SELISIH');
    $color($col($OFF['D_SELISIH']) . '2:' . $col($OFF['D_SELISIH']) . '3', '065F46');

    /*
    |--------------------------------------------------------------------------
    | 6. INSERT DATA
    |--------------------------------------------------------------------------
    */

    // Pass 1: hitung posisi & jumlah baris tiap RM group
    $rmGroupRows = [];
    $tempRow = 4; $tempLastRM = null;
    foreach ($data as $key => $item) {
        $rmCode = $item['info'][0];
        if ($rmCode !== 'OTHER' && $rmCode === $tempLastRM) {
            $rmGroupRows[$rmCode]['count']++;
        } else {
            $rmGroupRows[$rmCode] = ['start' => $tempRow, 'count' => 1];
        }
        $tempLastRM = $rmCode;
        $tempRow++;
    }

    // Pass 2: tulis data
    $rowIndex = 4; $lastRM = null; $rmSeenSto = [];

    foreach ($data as $key => $item) {

        $rmCode = $item['info'][0];
        $isSameRM   = ($rmCode !== 'OTHER' && $rmCode === $lastRM);
        $groupInfo  = $rmGroupRows[$rmCode] ?? null;
        $groupSize  = $groupInfo ? $groupInfo['count'] : 1;
        $groupStart = $groupInfo ? $groupInfo['start'] : $rowIndex;
        $groupEnd   = $groupStart + $groupSize - 1;
        $isFirstRM  = !isset($rmSeenSto[$rmCode]);

        // ── A-E: identitas RM/FG ─────────────────────────────────────────────
        $sheet->setCellValue("A{$rowIndex}", $isSameRM ? '' : $rmCode);
        $sheet->setCellValue("B{$rowIndex}", $isSameRM ? '' : $item['info'][1]);
        $sheet->setCellValue("C{$rowIndex}", $item['info'][2]);
        $sheet->setCellValue("D{$rowIndex}", $item['info'][3]);
        $sheet->setCellValue("E{$rowIndex}", $item['info'][4]);

        if ($groupSize > 1 && !$isSameRM) {
            $sheet->mergeCells("A{$groupStart}:A{$groupEnd}");
            $sheet->mergeCells("B{$groupStart}:B{$groupEnd}");
        }

        // ── BLOK A: STO Sebelumnya ───────────────────────────────────────────
        $dS = $hasPeriodeSebelumnya ? ($item['periode'][$periodeSebelumnya] ?? null) : null;

        $sheet->setCellValue($col($OFF['A_RM'])    . $rowIndex, $isFirstRM ? ($dS->qty_rm    ?? 0) : 0);
        $sheet->setCellValue($col($OFF['A_BUFF'])  . $rowIndex, $dS->qty_buff   ?? 0);
        $sheet->setCellValue($col($OFF['A_SAND'])  . $rowIndex, $dS->qty_sand   ?? 0);
        $sheet->setCellValue($col($OFF['A_TOUCH']) . $rowIndex, $dS->qty_touch  ?? 0);
        $sheet->setCellValue($col($OFF['A_WER'])   . $rowIndex, $dS->qty_werate ?? 0);
        $sheet->setCellValue($col($OFF['A_FG'])    . $rowIndex, $dS->qty_fg     ?? 0);
        $sheet->setCellValue($col($OFF['A_OT'])    . $rowIndex, $dS->qty_ot     ?? 0);

        // TOTAL STO Sebelumnya (merge per group, tulis di baris pertama)
        if ($isFirstRM) {
            $totalStoSebel = $hasPeriodeSebelumnya ? $calcStockSto($rmCode, $periodeSebelumnya) : 0;
            $sheet->setCellValue($col($OFF['A_TOTAL']) . $rowIndex, $totalStoSebel);
            if ($groupSize > 1) {
                $sheet->mergeCells($col($OFF['A_TOTAL']) . $groupStart . ':' . $col($OFF['A_TOTAL']) . $groupEnd);
            }
        }

        // ── BLOK B: Mutasi (merge per group, tulis di baris pertama) ─────────
        if ($isFirstRM) {
            $sheet->setCellValue($col($OFF['B_BELI'])   . $rowIndex, $beliByRM[$rmCode]      ?? 0);
            $sheet->setCellValue($col($OFF['B_TFIN'])   . $rowIndex, $tfinByRM[$rmCode]      ?? 0);
            $sheet->setCellValue($col($OFF['B_KIRIM'])  . $rowIndex, $kirimByRM[$rmCode]     ?? 0);
            $sheet->setCellValue($col($OFF['B_SADMIN']) . $rowIndex, $stockAdminByRM[$rmCode] ?? 0);

            if ($groupSize > 1) {
                foreach ([$OFF['B_BELI'], $OFF['B_TFIN'], $OFF['B_KIRIM'], $OFF['B_SADMIN']] as $off) {
                    $sheet->mergeCells($col($off) . $groupStart . ':' . $col($off) . $groupEnd);
                }
            }
        }

        $rmSeenSto[$rmCode] = true;

        // ── BLOK C: STO Aktif ────────────────────────────────────────────────
        $dA = $item['periode'][$periodeAktif] ?? null;

        $sheet->setCellValue($col($OFF['C_RM'])    . $rowIndex, $isSameRM ? 0 : ($dA->qty_rm ?? 0));
        $sheet->setCellValue($col($OFF['C_BUFF'])  . $rowIndex, $dA->qty_buff   ?? 0);
        $sheet->setCellValue($col($OFF['C_SAND'])  . $rowIndex, $dA->qty_sand   ?? 0);
        $sheet->setCellValue($col($OFF['C_TOUCH']) . $rowIndex, $dA->qty_touch  ?? 0);
        $sheet->setCellValue($col($OFF['C_WER'])   . $rowIndex, $dA->qty_werate ?? 0);
        $sheet->setCellValue($col($OFF['C_FG'])    . $rowIndex, $dA->qty_fg     ?? 0);
        $sheet->setCellValue($col($OFF['C_OT'])    . $rowIndex, $dA->qty_ot     ?? 0);

        // STOCK STO Aktif (merge per group, tulis di baris pertama)
        if (!$isSameRM) {
            $totalStoAktif = $calcStockSto($rmCode, $periodeAktif);
            $sheet->setCellValue($col($OFF['C_STOCKSTO']) . $rowIndex, $totalStoAktif);
            if ($groupSize > 1) {
                $sheet->mergeCells($col($OFF['C_STOCKSTO']) . $groupStart . ':' . $col($OFF['C_STOCKSTO']) . $groupEnd);
            }
        }

        // ── BLOK D: Perbandingan (merge per group, tulis di baris pertama) ───
        if (!$isSameRM) {
            $stockAdmin = $stockAdminByRM[$rmCode] ?? 0;
            $stoAktif   = $calcStockSto($rmCode, $periodeAktif);
            $selisih    = $stoAktif - $stockAdmin;

            $sheet->setCellValue($col($OFF['D_SADMIN'])  . $rowIndex, $stockAdmin);
            $sheet->setCellValue($col($OFF['D_SELISIH']) . $rowIndex, $selisih);

            if ($groupSize > 1) {
                $sheet->mergeCells($col($OFF['D_SADMIN'])  . $groupStart . ':' . $col($OFF['D_SADMIN'])  . $groupEnd);
                $sheet->mergeCells($col($OFF['D_SELISIH']) . $groupStart . ':' . $col($OFF['D_SELISIH']) . $groupEnd);
            }
        }

        $lastRM = $rmCode;
        $rowIndex++;
    }

    /*
    |--------------------------------------------------------------------------
    | 7. TOTAL ROW
    |--------------------------------------------------------------------------
    */
    $totalRow     = $rowIndex + 1;
    $firstDataRow = 4;
    $lastDataRow  = $rowIndex - 1;

    $sheet->setCellValue("A{$totalRow}", 'TOTAL');
    $sheet->mergeCells("A{$totalRow}:E{$totalRow}");

    for ($i = 0; $i <= 21; $i++) {
        $c = $col($i);
        $sheet->setCellValue("{$c}{$totalRow}", "=SUM({$c}{$firstDataRow}:{$c}{$lastDataRow})");
    }

    $highestCol = $sheet->getHighestColumn();
    $sheet->getStyle("A{$totalRow}:{$highestCol}{$totalRow}")->applyFromArray([
        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 8. GLOBAL STYLING
    |--------------------------------------------------------------------------
    */
    $highestRow    = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
        'alignment' => [
            'vertical'   => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'wrapText'   => false,
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        ],
    ]);

    $sheet->getStyle("A4:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("C4:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // Warna kondisional SELISIH
    // < 0  → merah  (stok fisik kurang dari admin)
    // > 0  → kuning (stok fisik lebih dari admin)
    // = 0  → hijau  (balance sempurna)
    $colSelisih = $col($OFF['D_SELISIH']);
    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
        $val = $sheet->getCell("{$colSelisih}{$r}")->getValue();
        if (!is_numeric($val)) continue;

        if ($val < 0) {
            $bg   = 'FEE2E2'; // merah muda
            $font = 'DC2626'; // merah tua
        } elseif ($val > 0) {
            $bg   = 'FEF9C3'; // kuning muda
            $font = '854D0E'; // kuning tua / amber
        } else {
            $bg   = 'D1FAE5'; // hijau muda
            $font = '065F46'; // hijau tua
        }

        $sheet->getStyle("{$colSelisih}{$r}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'font' => ['bold' => true, 'color' => ['rgb' => $font]],
        ]);
    }

    // Warna STOCK ADMIN Blok D — highlight kuning muda agar mudah dibedakan
    $colSadmin2 = $col($OFF['D_SADMIN']);
    $sheet->getStyle("{$colSadmin2}{$firstDataRow}:{$colSadmin2}{$lastDataRow}")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        'font' => ['color' => ['rgb' => '92400E']],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 9. AUTO WIDTH + FREEZE
    |--------------------------------------------------------------------------
    */
    $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    for ($i = 1; $i <= $highestColumnIndex; $i++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
    }

    $sheet->freezePane('F4');

    /*
    |--------------------------------------------------------------------------
    | 10. DOWNLOAD
    |--------------------------------------------------------------------------
    */
    $fileName = 'STO_REVIEW_' . str_replace('-', '', $periodeAktif) . '_' . now()->format('Ymd') . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {
        (new Xlsx($spreadsheet))->save('php://output');
    }, 200, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
        'Cache-Control'       => 'max-age=0',
    ]);
}

public function getReferenceAreas(Request $request)
{
    $warehouse = $request->input('warehouse');

    if (!$warehouse) {
        return response()->json(['areas' => []]);
    }

    $masters = \App\Models\StoReferenceMaster::where('warehouse', $warehouse)
        ->where('is_active', 1)
        ->select('id', 'area', 'shelves')
        ->orderBy('area')
        ->get();

    // ✅ Ambil semua shelves yang sudah all_saved di warehouse ini bulan ini
    $savedShelvesInWarehouse = \DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->where('stos.warehouse', $warehouse)
        ->whereYear('stos.created_at', now()->year)
        ->whereMonth('stos.created_at', now()->month)
         ->where(function ($q) use ($userId) {
        $q->where('stos.created_by', $userId)
          ->orWhere('stos.created_by_2', $userId);
    })
        ->select('stos.area', 'stos.shelves')
        ->distinct()
        ->get()
        ->groupBy('area')
        ->map(fn($rows) => $rows->pluck('shelves')->toArray());

    // Group per area, cek apakah SEMUA shelf di area tersebut sudah tersimpan
    $areas = $masters
        ->groupBy('area')
        ->map(function ($shelvesInArea, $area) use ($savedShelvesInWarehouse) {
            $allShelvesInArea  = $shelvesInArea->pluck('shelves')->toArray();
            $savedShelves      = $savedShelvesInWarehouse->get($area, []);
            // all_saved = semua shelf di referensi sudah ada di sto_items
            $allSaved = count($allShelvesInArea) > 0
                && count(array_diff($allShelvesInArea, $savedShelves)) === 0;

            return [
                'area'      => $area,
                'all_saved' => $allSaved,
            ];
        })
        ->values();

    return response()->json(['areas' => $areas]);
}

// GET /facility/sto/reference/items-by-area?warehouse=Chemical&area=Line+1
public function getReferenceItemsByArea(Request $request)
{
    $warehouse = $request->input('warehouse');
    $area      = $request->input('area');

    if (!$warehouse || !$area) {
        return response()->json(['items' => [], 'shelves' => []]);
    }

    $masters = \App\Models\StoReferenceMaster::where('warehouse', $warehouse)
        ->where('area', $area)
        ->where('is_active', 1)
        ->with(['items.article'])
        ->orderBy('shelves')
        ->get();

    // ✅ Kumpulkan semua article_code yang sudah tersimpan di SELURUH AREA ini
    //    (bukan per shelf, tapi per area — agar item yang ada di 2 shelf
    //    tidak muncul lagi jika salah satunya sudah tersimpan)
    $savedCodesInArea = \DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->where('stos.warehouse', $warehouse)
        ->where('stos.area', $area)                    // ← filter per area
        ->whereYear('stos.created_at', now()->year)
        ->whereMonth('stos.created_at', now()->month)
         ->where(function ($q) use ($userId) {
        $q->where('stos.created_by', $userId)
          ->orWhere('stos.created_by_2', $userId);
    })
        ->pluck('sto_items.article_code')
        ->unique()
        ->toArray();

    // Tetap pertahankan per-shelf untuk keperluan all_saved & shelf view
    $savedItemsByShelf = \DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->where('stos.warehouse', $warehouse)
        ->where('stos.area', $area)
        ->whereYear('stos.created_at', now()->year)
        ->whereMonth('stos.created_at', now()->month)
          ->where(function ($q) use ($userId) {
        $q->where('stos.created_by', $userId)
          ->orWhere('stos.created_by_2', $userId);
    })
        ->select('sto_items.article_code', 'stos.shelves')
        ->get()
        ->groupBy('shelves');

    $shelves = [];
    foreach ($masters as $master) {
        $savedCodesInShelf = collect($savedItemsByShelf->get($master->shelves, []))
            ->pluck('article_code')
            ->toArray();

        $items = $master->items->map(function ($item) use ($savedCodesInArea) {
            return [
                'article_code'  => $item->article_code,
                'description'   => optional($item->article)->description,
                'unit'          => optional($item->article)->unit,
                'min_package'   => optional($item->article)->min_package,
                // ✅ already_saved berdasarkan seluruh area, bukan hanya shelf ini
                'already_saved' => in_array($item->article_code, $savedCodesInArea),
            ];
        });

        $allSaved = $items->every(fn($i) => $i['already_saved']);

        $shelves[] = [
            'id'        => $master->id,
            'shelves'   => $master->shelves,
            'items'     => $items,
            'all_saved' => $allSaved,
        ];
    }

    $allItems = collect($shelves)
        ->flatMap(fn($s) => $s['items'])
        ->unique('article_code')
        ->values();

    return response()->json([
        'shelves'   => $shelves,
        'all_items' => $allItems,
    ]);
}
 
 
/**
 * GET /facility/sto/reference/shelves?warehouse=Chemical&area=Line+1
 *
 * Mengembalikan daftar shelf (beserta id master-nya) untuk
 * kombinasi warehouse + area tertentu.
 */
public function getReferenceShelves(Request $request)
{
    $warehouse = $request->input('warehouse');
    $area      = $request->input('area');
 
    if (!$warehouse || !$area) {
        return response()->json(['shelves' => []]);
    }
 
    $shelves = \App\Models\StoReferenceMaster::where('warehouse', $warehouse)
        ->where('area', $area)
        ->where('is_active', 1)
        ->select('id', 'shelves')
        ->orderBy('shelves')
        ->get();
 
    return response()->json(['shelves' => $shelves]);
}
 
 
/**
 * GET /facility/sto/reference/items?master_id=1
 *
 * Mengembalikan article_code + detail artikel dari sto_reference_items
 * untuk master_id tertentu.
 */
public function getReferenceItems(Request $request)
{
    $masterId = $request->input('master_id');
 
    if (!$masterId) {
        return response()->json(['items' => []]);
    }
 
    // JOIN ke tabel articles untuk ambil description, unit, min_package
    $items = \DB::table('sto_reference_items as ri')
        ->leftJoin('articles as a', 'a.article_code', '=', 'ri.article_code')
        ->where('ri.sto_reference_id', $masterId)
        ->select(
            'ri.id',
            'ri.article_code',
            'ri.qty',
            'a.id as article_id',
            'a.description',
            'a.unit',
            'a.min_package'
        )
        ->orderBy('a.description')
        ->get();
 
    return response()->json(['items' => $items]);
}

}