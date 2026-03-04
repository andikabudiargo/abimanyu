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

     private function userWarehouse()
{
    $userId = Auth::id();

    return match ($userId) {
        69        => 'Raw Material',
        88        => 'Werate',
        108,92    => 'WIP Buffing',
        44,45     => 'WIP Touch Up',
        85, 71    => 'WIP Sanding',
        99        => 'Consumable',
        67        => 'Chemical',
        68        => 'Finish Goods',
        53,2  => null, // 🔥 BOLEH PILIH SENDIRI
        default   => 'Raw Material',
    };
}

private function allowedWarehouses(): array
{
  $userId = Auth::id();

//if ($userId == 92) {
  //  return ['Dead Stock CM1', 'OT', 'WIP Buffing'];
//}

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
        'WIP Buffing'      => ['RMP','RMNP', 'FG'],
        'Werate'           => ['RMP','RMNP', 'FG'],
        'WIP Touch Up'     => ['RMP','RMNP', 'FG'],
        'WIP Sanding'      => ['RMP','RMNP', 'FG'],
        'OT'               => ['FG'],
        'Chemical'         => ['CM1'],
        'Consumable'       => ['CM2','RMP','RMNP'],
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


public function getStoByWarehouse(Request $request)
{
    $warehouse = $request->warehouse;

    $year  = 2026;
    $month = '02';

    $stoRange = [
        'Dead Stock CM1' => [1, 500],
        'Chemical'     => [1000, 1999],
        'Consumable'   => [2000, 2999],
        'Raw Material' => [3000, 3999],
        'WIP Buffing'  => [4000, 4999],
        'WIP Sanding'  => [5000, 5999],
        'WIP Touch Up' => [6000, 6999],
        'Finish Goods' => [7000, 7999],
        'OT'           => [8000, 8999],
        'Werate'       => [9000, 9999],
    ];

    $start = 1;
    $end   = 2000;

    if (isset($stoRange[$warehouse])) {
        [$start, $end] = $stoRange[$warehouse];
    }

    $usedStoNumbers = DB::table('stos')
        ->pluck('sto_number')
        ->toArray();

    $options = '';

    for ($i = $start; $i <= $end; $i++) {

        $number = str_pad($i, 4, '0', STR_PAD_LEFT);
        $val = "{$year}/{$month}/{$number}";

        if (!in_array($val, $usedStoNumbers)) {
            $options .= "<option value='{$val}'>{$val}</option>";
        }
    }

    return response()->json([
        'html' => $options
    ]);
}


public function store(Request $request)
{
    // =========================
    // VALIDATION
    // =========================
    $validator = \Validator::make($request->all(), [
        'sto_number'                => 'required|string|unique:stos,sto_number',
        'articles'                  => 'required|array|min:1',
        'articles.*.article_code'   => 'required|string',
        'articles.*.qty'            => 'required|numeric|min:0',
        'articles.*.uom'            => 'nullable|string',
        'articles.*.location'       => 'required|string',
        'note'                      => 'nullable|string',
    ]);

    // 🔥 VALIDASI KHUSUS OTHER
    $validator->after(function ($validator) use ($request) {
        foreach ($request->articles as $i => $row) {

            if (($row['article_code'] ?? null) === 'OTHER') {

                if (empty($row['other_name'])) {
                    $validator->errors()->add(
                        "articles.$i.other_name",
                        "Nama part wajib diisi untuk OTHER"
                    );
                }

                if (empty($row['uom'])) {
                    $validator->errors()->add(
                        "articles.$i.uom",
                        "UOM wajib diisi untuk OTHER"
                    );
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
        // =========================
        // CREATE STO HEADER
        // =========================
        $warehouse = $this->userWarehouse();

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

        $sto = Sto::create([
            'sto_number' => $request->sto_number,
            'warehouse'  => $warehouse,
            'note'       => $request->note,
            'created_by' => auth()->id(),
        ]);

        // =========================
        // CREATE STO ITEMS
        // =========================
        $itemCount = 0;

        foreach ($request->articles as $row) {

            if (empty($row['article_code']) || empty($row['qty'])) {
                continue;
            }

            $isOverrideLocation =
    str_contains($row['location'], 'Chemical') ||
    str_contains($row['location'], 'Dead Stock CM1');

if ($row['article_code'] === 'OTHER' || $isOverrideLocation) {

    // pakai UOM dari input user
    $uom = $row['uom'];

} else {

    // default ambil dari master
    $uom = Article::where(
        'article_code',
        $row['article_code']
    )->value('unit');
}

            $sto->items()->create([
                'article_code' => $row['article_code'],
                'other_name'   => $row['article_code'] === 'OTHER'
                                    ? $row['other_name']
                                    : null,
                'uom'          => $uom,
                'qty'          => $row['qty'],
                'location'     => $row['location'],
            ]);

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
            'data'    => [
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
            'message' => 'Terjadi kesalahan saat menyimpan STO',
        ], 500);
    }
}


public function datatables(Request $request)
{
    $userId = Auth::id();

   $columns = [
    0 => null, // action (tidak sortable)
    1 => 'sto_items.location',
    2 => 'sto_items.article_code',
    3 => 'articles.description',
    4 => 'sto_items.qty',
    5 => 'articles.min_package',
    6 => 'articles.unit',
    7 => 'stos.sto_number',   // ✅ FIX
    8 => 'users.name',
    9 => 'stos.created_at',
    10 => 'stos.note',
];


    // 🔥 AMBIL MAPPING USER
    $warehouse     = $this->userWarehouse();
    $limit  = $request->length;
    $start  = $request->start;
    $order  = $columns[$request->input('order.0.column')];
    $dir    = $request->input('order.0.dir');

    // =====================
    // 🔹 BASE QUERY
    // =====================
    $query = DB::table('sto_items')
        ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
        ->leftJoin('articles', 'articles.article_code', '=', 'sto_items.article_code')
        ->leftJoin('users', 'users.id', '=', 'stos.created_by')
       ->select(
    'sto_items.id as sto_item_id',
    'sto_items.sto_id',
    'sto_items.location',
    'sto_items.article_code',

    DB::raw("
      CASE
        WHEN sto_items.article_code = 'OTHER'
        THEN sto_items.other_name
        ELSE articles.description
      END as part_name
    "),

    'sto_items.qty',
     'articles.min_package',

    DB::raw("
      CASE
        WHEN sto_items.article_code = 'OTHER'
        THEN sto_items.uom
        ELSE articles.unit
      END as unit
    "),

    'stos.sto_number',
    'users.name as created_by',
    'stos.created_at',
    'stos.note'
       );

       // =====================
// 📅 FILTER STO MONTH (TAMBAH DI SINI)
// =====================
$defaultMonth = '2026/02';

$selectedMonth = $request->filled('sto_month')
    ? $request->sto_month
    : $defaultMonth;

$query->where('stos.sto_number', 'like', $selectedMonth . '/%');
    // =====================
    // 🔐 FILTER KHUSUS USER 67
if ($userId == 67) {
    $query->whereIn('sto_items.location', ['Chemical', 'Consumable']);
}

    if (!is_null($warehouse)) {
        $query->where('sto_items.location', $warehouse);
    }

    // =====================
    // 🔍 FILTER REQUEST (AMAN)
    // =====================
    if ($request->filled('location') && is_null($warehouse)) {
        // hanya user yg boleh pilih gudang
        $query->where('sto_items.location', $request->location);
    }

    if ($request->filled('article')) {
    $query->where(function ($q) use ($request) {
        $q->where('sto_items.article_code', 'like', "%{$request->article}%")
          ->orWhere('sto_items.other_name', 'like', "%{$request->article}%");
    });
}


    if ($request->filled('sto_number')) {
        $query->where('stos.sto_number', 'like', '%'.$request->sto_number.'%');
    }

    // =====================
    // 🔍 SEARCH
    // =====================
    if (!empty($request->search['value'])) {
        $search = $request->search['value'];

        $query->where(function ($q) use ($search) {
            $q->where('sto_items.article_code', 'LIKE', "%{$search}%")
              ->orWhere('articles.description', 'LIKE', "%{$search}%")
              ->orWhere('stos.sto_number', 'LIKE', "%{$search}%")
              ->orWhere('sto_items.location', 'LIKE', "%{$search}%")
              ->orWhere('users.name', 'LIKE', "%{$search}%")
              ->orWhere('sto_items.other_name', 'LIKE', "%{$search}%");

        });
    }

    // =====================
    // 🔢 TOTAL DATA (WAJIB SESUAI USER)
    // =====================
    $totalDataQuery = DB::table('sto_items')
        ->join('articles', 'articles.article_code', '=', 'sto_items.article_code');

        $totalDataQuery->join('stos', 'stos.id', '=', 'sto_items.sto_id')
               ->where('stos.sto_number', 'like', $selectedMonth . '/%');

    if (!is_null($warehouse)) {
        $totalDataQuery->where('sto_items.location', $warehouse);
    }

    $totalData = $totalFiltered = $totalDataQuery->count();

  // =====================
// 📊 ORDER & PAGINATION (FINAL)
// =====================
$orderColumnIndex = $request->input('order.0.column');
$orderDir         = $request->input('order.0.dir', 'desc');

if (
    isset($columns[$orderColumnIndex]) &&
    $columns[$orderColumnIndex] !== null
) {
    // sorting dari header DataTables
    $query->orderBy($columns[$orderColumnIndex], $orderDir);
} else {
    // fallback default
    $query->orderBy('stos.sto_number', 'desc');
}

if ($limit != -1) {
    $query->offset($start)->limit($limit);
}

$data = $query->get();


    // =====================
    // 📦 FORMAT DATA
    // =====================
  $result = [];

 
foreach ($data as $row) {
    // 🔥 ID unik untuk dropdown
    $dropdownId = 'dropdown-' . $row->sto_item_id;
$editUrl = route('facility.sto.edit', ['id' => $row->sto_id]);
$twoDecimalLocations = ['Chemical', 'Dead Stock CM1', 'Consumable'];

$qtyFormatted = in_array($row->location, $twoDecimalLocations)
    ? number_format($row->qty, 2)
    : number_format($row->qty, 0);

    $result[] = [
        'DT_RowAttr' => [
            'data-id' => $row->sto_id,
            'class'   => 'sto-row cursor-pointer hover:bg-blue-50'
        ],
        // 🔹 ACTION DROPDOWN DI AWAL
        'action' => '
        <div class="relative inline-block text-left">
          <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
           <i data-feather="align-justify"></i>
          </button>
          <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
            <div class="py-1 text-sm text-gray-700">
             <a href="' . $editUrl . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
              </a>
             <button onclick="deleteSTO(' . $row->sto_id . ')" class="w-full text-red-500 text-left px-4 py-2 hover:bg-red-500 hover:text-gray-100">
    <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
</button>
            </div>
          </div>
        </div>',
        // 🔹 DATA LAINNYA
        'location'     => $row->location,
        'article_code' => $row->article_code,
        'part_name'    => $row->part_name,
        'qty'          => $qtyFormatted, // ✅ FIX DI SINI
        'min_package' => $row->min_package,
        'unit'         => $row->unit,
        'sto_number'   => $row->sto_number,
        'created_by'   => $row->created_by,
        'created_at'   => $row->created_at,
        'note'         => $row->note,
    ];
}


    return response()->json([
        "draw"            => intval($request->draw),
        "recordsTotal"    => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data"            => $result
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

    // Gunakan warehouse dari user dulu, jika tidak ada pakai request
    $warehouse = $this->userWarehouse() ?? $request->warehouse;

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $query = Article::select('article_code', 'unit', 'description', 'article_type', 'min_package')
        ->when(!empty($allowedTypes), fn($q) => $q->whereIn('article_type', $allowedTypes))
        ->when($search, fn($q) => $q->where(fn($qq) => 
            $qq->where('article_code', 'like', "%{$search}%")
               ->orWhere('description', 'like', "%{$search}%")
        ))
        ->orderBy('article_code');

    $totalCount = $query->count();

    $articles = $query->offset($offset)
                      ->limit($perPage)
                      ->get();

    return response()->json([
        'results' => $articles->map(fn($a) => [
            'id'   => $a->article_code,
            'text' => "{$a->article_code} - {$a->description}",
            'unit' => $a->unit,
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



<?php

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

    if (empty($periodes)) {
        abort(404, 'Tidak ada data STO');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. QUERY DATA STO (NORMAL + OTHER)
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
    SUM(CASE WHEN si.location='WIP Buffing'  THEN si.qty ELSE 0 END) qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding'  THEN si.qty ELSE 0 END) qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) qty_touch,
    SUM(CASE WHEN si.location='Werate'       THEN si.qty ELSE 0 END) qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) qty_fg,
    SUM(CASE WHEN si.location='OT'           THEN si.qty ELSE 0 END) qty_ot,

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
    GROUP BY code, article_rm, article_rm_desc, article_fg, article_fg_desc
) bh ON (
    (si.location='Raw Material' AND si.article_code = bh.article_rm)
    OR
    (si.location!='Raw Material' AND si.article_code = bh.article_fg)
)

LEFT JOIN articles a ON a.article_code = bh.article_fg

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
    SUM(CASE WHEN si.location='WIP Buffing'  THEN si.qty ELSE 0 END) qty_buff,
    SUM(CASE WHEN si.location='WIP Sanding'  THEN si.qty ELSE 0 END) qty_sand,
    SUM(CASE WHEN si.location='WIP Touch Up' THEN si.qty ELSE 0 END) qty_touch,
    SUM(CASE WHEN si.location='Werate'       THEN si.qty ELSE 0 END) qty_werate,
    SUM(CASE WHEN si.location='Finish Goods' THEN si.qty ELSE 0 END) qty_fg,
    SUM(CASE WHEN si.location='OT'           THEN si.qty ELSE 0 END) qty_ot,

    0 AS sort_group

FROM stos s
JOIN sto_items si ON si.sto_id = s.id

LEFT JOIN boms b ON si.article_code IN (b.article_rm, b.article_fg)

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
    | 2B. QUERY DATA BELI (lpb_temporary)
    |--------------------------------------------------------------------------
    */
    $beliRows = DB::select("
SELECT
    DATE_FORMAT(lt.do_date, '%Y-%m') AS periode,
    lt.article_code AS rm_code,
    SUM(lt.qty)     AS qty_beli
FROM lpb_temporary lt
GROUP BY
    DATE_FORMAT(lt.do_date, '%Y-%m'),
    lt.article_code
");

    // Index by rm_code saja — 1 RM bisa punya banyak FG di BOM
    // sehingga qty tidak ikut terduplikasi
    $beliIndex = [];
    foreach ($beliRows as $br) {
        $beliIndex[$br->rm_code][$br->periode] = ($beliIndex[$br->rm_code][$br->periode] ?? 0) + $br->qty_beli;
    }

    /*
    |--------------------------------------------------------------------------
    | 2C. QUERY DATA KIRIM (sj_temporary)
    |--------------------------------------------------------------------------
    */
    $kirimRows = DB::select("
SELECT
    DATE_FORMAT(st.delivery_date, '%Y-%m') AS periode,
    st.article_code AS fg_code,
    SUM(st.delivery_qty) AS qty_kirim
FROM sj_temporary st
GROUP BY
    DATE_FORMAT(st.delivery_date, '%Y-%m'),
    st.article_code
");

    // Index by fg_code saja — 1 FG bisa punya banyak RM di BOM
    // sehingga qty tidak ikut terduplikasi
    $kirimIndex = [];
    foreach ($kirimRows as $kr) {
        $kirimIndex[$kr->fg_code][$kr->periode] = ($kirimIndex[$kr->fg_code][$kr->periode] ?? 0) + $kr->qty_kirim;
    }

    /*
    |--------------------------------------------------------------------------
    | 3. PIVOT DATA STO
    |--------------------------------------------------------------------------
    */
    $data = [];

    foreach ($rows as $r) {
        if ($r->rm_code === 'OTHER') {
            $key = 'OTHER|' . $r->rm_desc;
        } else {
            $key = $r->rm_code . '|' . $r->fg_code;
        }

        $data[$key]['info'] = [
            $r->rm_code,
            $r->rm_desc,
            $r->fg_code,
            $r->fg_desc,
            $r->uom,
        ];

        $data[$key]['periode'][$r->periode] = $r;
    }

    ksort($data, SORT_NATURAL);

    /*
    |--------------------------------------------------------------------------
    | 4. CREATE EXCEL
    |--------------------------------------------------------------------------
    */
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();

    $color = function ($range, $bg, $font = 'FFFFFF') use ($sheet) {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $font]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $bg],
            ],
        ]);
    };

    /*
    |--------------------------------------------------------------------------
    | HEADER TITLE
    |--------------------------------------------------------------------------
    */
    $sheet->mergeCells('A1:E1');
    $sheet->setCellValue('A1', 'BILL OF MATERIAL');
    $sheet->getStyle('A1:E1')->applyFromArray([
        'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '111827'],
        ],
    ]);

    $sheet->fromArray(
        ['RM Code', 'RM Description', 'FG Code', 'FG Description', 'UOM'],
        null,
        'A2'
    );
    $color('A2:E3', '000000');

    /*
    |--------------------------------------------------------------------------
    | 5. HEADER DINAMIS PER PERIODE
    |
    |    Struktur 12 kolom per periode:
    |    [0] RM
    |    [1] Buffing
    |    [2] Sanding
    |    [3] Touch Up
    |    [4] Werate
    |    [5] FG
    |    [6] OT
    |    [7] STOCK STO   ← total STO
    |    [8] BELI
    |    [9] KIRIM
    |    [10] STOCK ADMIN ← KIRIM - BELI
    |    [11] SELISIH     ← STOCK STO - STOCK ADMIN  (BARU)
    |--------------------------------------------------------------------------
    */
    $startColIndex = 6; // kolom F
    $colCount      = 12; // total kolom per periode

    $bulanNama = [
        '01' => 'JANUARI',  '02' => 'FEBRUARI', '03' => 'MARET',
        '04' => 'APRIL',    '05' => 'MEI',       '06' => 'JUNI',
        '07' => 'JULI',     '08' => 'AGUSTUS',   '09' => 'SEPTEMBER',
        '10' => 'OKTOBER',  '11' => 'NOVEMBER',  '12' => 'DESEMBER',
    ];

    $periodeColMap = [];

    foreach ($periodes as $periode) {

        $year  = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);
        $title = "STO " . $bulanNama[$month] . " " . $year;

        $start = Coordinate::stringFromColumnIndex($startColIndex);
        $end   = Coordinate::stringFromColumnIndex($startColIndex + $colCount - 1);

        /* TITLE */
        $sheet->mergeCells("{$start}1:{$end}1");
        $sheet->setCellValue("{$start}1", $title);
        $color("{$start}1:{$end}1", 'F97316');

        /* ── GROUP HEADER row 2 ── */

        // RM
        $colRM = Coordinate::stringFromColumnIndex($startColIndex);
        $sheet->mergeCells($colRM . '2:' . $colRM . '3');
        $sheet->setCellValue($colRM . '2', 'RM');

        // WIP
        $wipStart = Coordinate::stringFromColumnIndex($startColIndex + 1);
        $wipEnd   = Coordinate::stringFromColumnIndex($startColIndex + 4);
        $sheet->mergeCells($wipStart . '2:' . $wipEnd . '2');
        $sheet->setCellValue($wipStart . '2', 'WIP');

        // FG
        $colFG = Coordinate::stringFromColumnIndex($startColIndex + 5);
        $sheet->mergeCells($colFG . '2:' . $colFG . '3');
        $sheet->setCellValue($colFG . '2', 'FG');

        // OT
        $colOT = Coordinate::stringFromColumnIndex($startColIndex + 6);
        $sheet->mergeCells($colOT . '2:' . $colOT . '3');
        $sheet->setCellValue($colOT . '2', 'OT');

        // STOCK STO
        $colStockSto = Coordinate::stringFromColumnIndex($startColIndex + 7);
        $sheet->mergeCells($colStockSto . '2:' . $colStockSto . '3');
        $sheet->setCellValue($colStockSto . '2', 'STOCK STO');

        // BELI
        $colBeli = Coordinate::stringFromColumnIndex($startColIndex + 8);
        $sheet->mergeCells($colBeli . '2:' . $colBeli . '3');
        $sheet->setCellValue($colBeli . '2', 'BELI');

        // KIRIM
        $colKirim = Coordinate::stringFromColumnIndex($startColIndex + 9);
        $sheet->mergeCells($colKirim . '2:' . $colKirim . '3');
        $sheet->setCellValue($colKirim . '2', 'KIRIM');

        // STOCK ADMIN
        $colStockAdmin = Coordinate::stringFromColumnIndex($startColIndex + 10);
        $sheet->mergeCells($colStockAdmin . '2:' . $colStockAdmin . '3');
        $sheet->setCellValue($colStockAdmin . '2', 'STOCK ADMIN');

        // SELISIH (STOCK STO - STOCK ADMIN)  ← BARU
        $colSelisih = Coordinate::stringFromColumnIndex($startColIndex + 11);
        $sheet->mergeCells($colSelisih . '2:' . $colSelisih . '3');
        $sheet->setCellValue($colSelisih . '2', 'SELISIH');

        /* SUB HEADER WIP row 3 */
        $wipSubs = ['Buffing', 'Sanding', 'Touch Up', 'Werate'];
        for ($i = 0; $i < 4; $i++) {
            $sheet->setCellValue(
                Coordinate::stringFromColumnIndex($startColIndex + 1 + $i) . '3',
                $wipSubs[$i]
            );
        }

        /* ── WARNA ── */
        $color($colRM       . '2:' . $colRM       . '3', '2563EB');           // biru
        $color($wipStart    . '2:' . $wipEnd       . '3', 'FACC15', '000000'); // kuning
        $color($colFG       . '2:' . $colFG        . '3', '16A34A');           // hijau
        $color($colOT       . '2:' . $colOT        . '3', '9CA3AF', '000000'); // abu
        $color($colStockSto . '2:' . $colStockSto  . '3', 'DC2626');           // merah
        $color($colBeli     . '2:' . $colBeli      . '3', '7C3AED');           // ungu
        $color($colKirim    . '2:' . $colKirim     . '3', '0891B2');           // teal
        $color($colStockAdmin . '2:' . $colStockAdmin . '3', '92400E');        // coklat
        $color($colSelisih  . '2:' . $colSelisih   . '3', '065F46');           // hijau tua

        $periodeColMap[$periode] = $startColIndex;
        $startColIndex += $colCount;
    }

    /*
    |--------------------------------------------------------------------------
    | 6. INSERT DATA
    |--------------------------------------------------------------------------
    */
    $rowIndex = 4;
    $lastRM   = null;

    foreach ($data as $key => $item) {

        $rmCode = $item['info'][0];
        $rmDesc = $item['info'][1];
        $fgCode = $item['info'][2];

        $isSameRM = ($rmCode !== 'OTHER' && $rmCode === $lastRM);

        $sheet->fromArray([
            $isSameRM ? '' : $rmCode,
            $isSameRM ? '' : $rmDesc,
            $item['info'][2],
            $item['info'][3],
            $item['info'][4],
        ], null, "A{$rowIndex}");

        foreach ($periodes as $periode) {

            $col = $periodeColMap[$periode];
            $d   = $item['periode'][$periode] ?? null;

            $rm    = $isSameRM ? 0 : ($d->qty_rm     ?? 0);
            $buff  = $d->qty_buff   ?? 0;
            $sand  = $d->qty_sand   ?? 0;
            $touch = $d->qty_touch  ?? 0;
            $wer   = $d->qty_werate ?? 0;
            $fg    = $d->qty_fg     ?? 0;
            $ot    = $d->qty_ot     ?? 0;

            $stockSto = $rm + $buff + $sand + $touch + $wer + $fg + $ot;

            // BELI: lookup by rm_code saja
            $qtyBeli  = ($rmCode !== 'OTHER' && isset($beliIndex[$rmCode][$periode]))
                        ? $beliIndex[$rmCode][$periode] : 0;

            // KIRIM: lookup by fg_code saja
            $qtyKirim = ($fgCode !== 'OTHER' && isset($kirimIndex[$fgCode][$periode]))
                        ? $kirimIndex[$fgCode][$periode] : 0;

            $stockAdmin = $qtyKirim - $qtyBeli;          // STOCK ADMIN
            $selisih    = $stockSto  - $stockAdmin;       // SELISIH ← BARU

            $sheet->fromArray(
                [$rm, $buff, $sand, $touch, $wer, $fg, $ot,
                 $stockSto, $qtyBeli, $qtyKirim, $stockAdmin, $selisih],
                null,
                Coordinate::stringFromColumnIndex($col) . $rowIndex
            );
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

    $sheet->setCellValue("A{$totalRow}", "TOTAL");
    $sheet->mergeCells("A{$totalRow}:E{$totalRow}");

    foreach ($periodes as $periode) {
        $col = $periodeColMap[$periode];
        for ($i = 0; $i < $colCount; $i++) {
            $c = Coordinate::stringFromColumnIndex($col + $i);
            $sheet->setCellValue(
                "{$c}{$totalRow}",
                "=SUM({$c}{$firstDataRow}:{$c}{$lastDataRow})"
            );
        }
    }

    $highestCol = $sheet->getHighestColumn();
    $sheet->getStyle("A{$totalRow}:{$highestCol}{$totalRow}")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '111827'],
        ],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 8. AUTO WIDTH
    |--------------------------------------------------------------------------
    */
    $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    for ($i = 1; $i <= $highestColumnIndex; $i++) {
        $sheet->getColumnDimension(
            Coordinate::stringFromColumnIndex($i)
        )->setAutoSize(true);
    }

    $sheet->freezePane('F4');

    /*
    |--------------------------------------------------------------------------
    | 9. BORDER + VERTICAL ALIGN CENTER
    |--------------------------------------------------------------------------
    */
    $highestRow    = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color'       => ['rgb' => '000000'],
            ],
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 10. DOWNLOAD
    |--------------------------------------------------------------------------
    */
    $fileName = 'STO_REPORT_' . now()->format('Y-m-d') . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {
        (new Xlsx($spreadsheet))->save('php://output');
    }, 200, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
        'Cache-Control'       => 'max-age=0',
    ]);
}


}