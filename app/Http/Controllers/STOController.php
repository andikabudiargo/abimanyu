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




<?php

public function exportReview(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | 1. TENTUKAN PERIODE FILTER
    |    - Jika user id=53 dan ada filter → pakai filter
    |    - Selain itu → pakai periode STO terbaru
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

    // Periode yang difilter (bulan aktif)
    if (auth()->id() == 53 && $request->filled('periode')) {
        // Ubah format dari "2026/02" → "2026-02"
        $periodeAktif = str_replace('/', '-', $request->input('periode'));
    } else {
        // Default: periode terbaru
        $periodeAktif = end($allPeriodes);
    }

    // Periode sebelumnya = satu bulan sebelum periodeAktif
    $periodeAktifCarbon  = \Carbon\Carbon::createFromFormat('Y-m', $periodeAktif);
    $periodeSebelumnya   = $periodeAktifCarbon->copy()->subMonth()->format('Y-m');

    // Pastikan periodeSebelumnya ada di data; jika tidak ada, set null
    $hasPeriodeSebelumnya = in_array($periodeSebelumnya, $allPeriodes);

    /*
    |--------------------------------------------------------------------------
    | 2. QUERY DATA STO — HANYA 2 PERIODE
    |    (periodeSebelumnya + periodeAktif)
    |--------------------------------------------------------------------------
    */
    $periodeFilter = array_filter(
        [$periodeSebelumnya, $periodeAktif],
        fn($p) => in_array($p, $allPeriodes)
    );

    // Build IN clause placeholders
    $placeholders = implode(',', array_fill(0, count($periodeFilter), '?'));

    $rows = DB::select("
/* =====================================================
   1. DATA YANG MATCH BOM (NORMAL)
===================================================== */
SELECT
    REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') AS periode,
    bh.article_rm     AS rm_code,
    bh.article_rm_desc AS rm_desc,
    bh.article_fg     AS fg_code,
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
    OR
    (si.location!='Raw Material' AND si.article_code = bh.article_fg)
)

LEFT JOIN articles a ON a.article_code = bh.article_fg

WHERE bh.bom_code IS NOT NULL
  AND REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') IN ({$placeholders})

GROUP BY
    periode, bh.bom_code, bh.article_rm, bh.article_rm_desc,
    bh.article_fg, bh.article_fg_desc, a.unit

UNION ALL

/* =====================================================
   2. DATA TANPA BOM → OTHER
===================================================== */
SELECT
    REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') AS periode,
    'OTHER' AS rm_code,
    si.other_name  AS rm_desc,
    'OTHER' AS fg_code,
    si.other_name  AS fg_desc,
    'PCS'   AS uom,

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
  AND si.other_name IS NOT NULL
  AND si.other_name <> ''
  AND REPLACE(SUBSTRING(s.sto_number,1,7), '/', '-') IN ({$placeholders})

GROUP BY
    periode, si.other_name

ORDER BY
    sort_group ASC, rm_code, fg_code
", array_merge($periodeFilter, $periodeFilter));

    /*
    |--------------------------------------------------------------------------
    | 2B. QUERY BELI — hanya periode aktif
    |--------------------------------------------------------------------------
    */
    $beliRows = DB::select("
SELECT
    lt.article_code AS rm_code,
    SUM(lt.qty)     AS qty_beli
FROM lpb_temporary lt
WHERE DATE_FORMAT(lt.do_date, '%Y-%m') = ?
GROUP BY lt.article_code
", [$periodeAktif]);

    $beliIndex = [];
    foreach ($beliRows as $br) {
        $beliIndex[$br->rm_code] = $br->qty_beli;
    }

    /*
    |--------------------------------------------------------------------------
    | 2C. QUERY TF IN — hanya periode aktif
    |--------------------------------------------------------------------------
    */
    $tfinRows = DB::select("
SELECT
    it.article_code AS rm_code,
    SUM(it.qty)     AS qty_tfin
FROM in_temporary it
WHERE DATE_FORMAT(it.date, '%Y-%m') = ?
GROUP BY it.article_code
", [$periodeAktif]);

    $tfinIndex = [];
    foreach ($tfinRows as $tr) {
        $tfinIndex[$tr->rm_code] = $tr->qty_tfin;
    }

    /*
    |--------------------------------------------------------------------------
    | 2D. QUERY KIRIM — hanya periode aktif
    |--------------------------------------------------------------------------
    */
    $kirimRows = DB::select("
SELECT
    st.article_code         AS fg_code,
    SUM(st.delivery_qty)    AS qty_kirim
FROM sj_temporary st
WHERE DATE_FORMAT(st.delivery_date, '%Y-%m') = ?
GROUP BY st.article_code
", [$periodeAktif]);

    $kirimIndex = [];
    foreach ($kirimRows as $kr) {
        $kirimIndex[$kr->fg_code] = $kr->qty_kirim;
    }

    /*
    |--------------------------------------------------------------------------
    | 3. PIVOT DATA STO
    |    $data[key]['info']          = [rm_code, rm_desc, fg_code, fg_desc, uom]
    |    $data[key]['periode'][p]    = row object
    |--------------------------------------------------------------------------
    */
    $data = [];

    foreach ($rows as $r) {
        $key = ($r->rm_code === 'OTHER')
            ? 'OTHER|' . $r->rm_desc
            : $r->rm_code . '|' . $r->fg_code;

        $data[$key]['info']              = [$r->rm_code, $r->rm_desc, $r->fg_code, $r->fg_desc, $r->uom];
        $data[$key]['periode'][$r->periode] = $r;
    }

    ksort($data, SORT_NATURAL);

    /*
    |--------------------------------------------------------------------------
    | 3B. RM GROUPS & STOCK STO PER PERIODE
    |--------------------------------------------------------------------------
    */
    $rmGroups = [];
    foreach ($data as $key => $item) {
        $rmGroups[$item['info'][0]][] = $key;
    }

    // Helper: hitung total stock STO (semua lokasi) untuk satu rmCode + periode
    $calcStockSto = function (string $rmCode, string $periode) use ($data, $rmGroups) {
        $total     = 0;
        $rmSeen    = false;
        foreach ($rmGroups[$rmCode] as $key) {
            $d      = $data[$key]['periode'][$periode] ?? null;
            $rmQty  = (!$rmSeen) ? ($d->qty_rm ?? 0) : 0;
            $rmSeen = true;
            $total += $rmQty
                    + ($d->qty_buff   ?? 0)
                    + ($d->qty_sand   ?? 0)
                    + ($d->qty_touch  ?? 0)
                    + ($d->qty_werate ?? 0)
                    + ($d->qty_fg     ?? 0)
                    + ($d->qty_ot     ?? 0);
        }
        return $total;
    };

    /*
    |--------------------------------------------------------------------------
    | 3C. STOCK ADMIN PER RM
    |
    |    STOCK ADMIN = Total STO Sebelumnya + BELI + TF IN − KIRIM
    |                  (semua nilai periode aktif kecuali "STO sebelumnya")
    |--------------------------------------------------------------------------
    */
    $stockAdminByRM = [];
    foreach ($rmGroups as $rmCode => $keys) {

        $totalStoSebelumnya = $hasPeriodeSebelumnya
            ? $calcStockSto($rmCode, $periodeSebelumnya)
            : 0;

        $beli  = ($rmCode !== 'OTHER') ? ($beliIndex[$rmCode]  ?? 0) : 0;
        $tfin  = ($rmCode !== 'OTHER') ? ($tfinIndex[$rmCode]  ?? 0) : 0;

        $kirim = 0;
        foreach ($keys as $key) {
            $fgCode = $data[$key]['info'][2];
            $kirim += ($fgCode !== 'OTHER') ? ($kirimIndex[$fgCode] ?? 0) : 0;
        }

        $stockAdminByRM[$rmCode] = $totalStoSebelumnya + $beli + $tfin - $kirim;
    }

    /*
    |--------------------------------------------------------------------------
    | 4. CREATE EXCEL
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
    | 5. HEADER TITLE (baris 1)
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
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
    ]);

    $sheet->fromArray(['RM Code', 'RM Description', 'FG Code', 'FG Description', 'UOM'], null, 'A2');
    $color('A2:E3', '000000');

    /*
    |--------------------------------------------------------------------------
    | 5B. HEADER KOLOM PER PERIODE
    |
    |    === BLOK STO SEBELUMNYA (8 kolom) ===
    |    [0] RM
    |    [1] WIP Buffing   \
    |    [2] WIP Sanding    > WIP (merge row 2)
    |    [3] WIP Touch Up  /
    |    [4] WIP Werate   /
    |    [5] FG
    |    [6] OT
    |    [7] TOTAL STO SEBELUMNYA  ← merge row 2-3
    |
    |    === BLOK PERIODE AKTIF (12 kolom) ===
    |    [0] RM
    |    [1-4] WIP (Buffing, Sanding, Touch Up, Werate)
    |    [5] FG
    |    [6] OT
    |    [7] STOCK STO AKTIF      ← merge row 2-3
    |    [8] BELI                 ← merge row 2-3
    |    [9] TF IN                ← merge row 2-3
    |    [10] KIRIM               ← merge row 2-3
    |    [11] STOCK ADMIN         ← merge row 2-3
    |    [12] SELISIH             ← merge row 2-3
    |--------------------------------------------------------------------------
    */
    $bulanNama = [
        '01' => 'JANUARI',  '02' => 'FEBRUARI', '03' => 'MARET',
        '04' => 'APRIL',    '05' => 'MEI',       '06' => 'JUNI',
        '07' => 'JULI',     '08' => 'AGUSTUS',   '09' => 'SEPTEMBER',
        '10' => 'OKTOBER',  '11' => 'NOVEMBER',  '12' => 'DESEMBER',
    ];

    $colStart = 6; // kolom F (index 6)

    // ── BLOK STO SEBELUMNYA ──────────────────────────────────────────────────
    $colSebelumnyaStart = $colStart;
    $colCountSebel      = 8;

    $yearSebel  = substr($periodeSebelumnya, 0, 4);
    $monthSebel = substr($periodeSebelumnya, 5, 2);
    $titleSebel = "STO " . ($bulanNama[$monthSebel] ?? $monthSebel) . " {$yearSebel}";

    $sS = Coordinate::stringFromColumnIndex($colSebelumnyaStart);
    $sE = Coordinate::stringFromColumnIndex($colSebelumnyaStart + $colCountSebel - 1);
    $sheet->mergeCells("{$sS}1:{$sE}1");
    $sheet->setCellValue("{$sS}1", $titleSebel);
    $color("{$sS}1:{$sE}1", '0369A1'); // biru tua

    // Sub-header STO Sebelumnya
    $cs = $colSebelumnyaStart;

    // RM
    $cRM = Coordinate::stringFromColumnIndex($cs);
    $sheet->mergeCells($cRM . '2:' . $cRM . '3');
    $sheet->setCellValue($cRM . '2', 'RM');
    $color($cRM . '2:' . $cRM . '3', '2563EB');

    // WIP
    $wipS = Coordinate::stringFromColumnIndex($cs + 1);
    $wipE = Coordinate::stringFromColumnIndex($cs + 4);
    $sheet->mergeCells($wipS . '2:' . $wipE . '2');
    $sheet->setCellValue($wipS . '2', 'WIP');
    $color($wipS . '2:' . $wipE . '3', 'FACC15', '000000');
    foreach (['Buffing', 'Sanding', 'Touch Up', 'Werate'] as $i => $sub) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs + 1 + $i) . '3', $sub);
    }

    // FG
    $cFG = Coordinate::stringFromColumnIndex($cs + 5);
    $sheet->mergeCells($cFG . '2:' . $cFG . '3');
    $sheet->setCellValue($cFG . '2', 'FG');
    $color($cFG . '2:' . $cFG . '3', '16A34A');

    // OT
    $cOT = Coordinate::stringFromColumnIndex($cs + 6);
    $sheet->mergeCells($cOT . '2:' . $cOT . '3');
    $sheet->setCellValue($cOT . '2', 'OT');
    $color($cOT . '2:' . $cOT . '3', '9CA3AF', '000000');

    // TOTAL STO SEBELUMNYA
    $cTotSebel = Coordinate::stringFromColumnIndex($cs + 7);
    $sheet->mergeCells($cTotSebel . '2:' . $cTotSebel . '3');
    $sheet->setCellValue($cTotSebel . '2', 'TOTAL STO');
    $color($cTotSebel . '2:' . $cTotSebel . '3', 'DC2626');

    // ── BLOK PERIODE AKTIF ───────────────────────────────────────────────────
    $colAktifStart = $colSebelumnyaStart + $colCountSebel;
    $colCountAktif = 13;

    $yearAktif  = substr($periodeAktif, 0, 4);
    $monthAktif = substr($periodeAktif, 5, 2);
    $titleAktif = "STO " . ($bulanNama[$monthAktif] ?? $monthAktif) . " {$yearAktif}";

    $aS = Coordinate::stringFromColumnIndex($colAktifStart);
    $aE = Coordinate::stringFromColumnIndex($colAktifStart + $colCountAktif - 1);
    $sheet->mergeCells("{$aS}1:{$aE}1");
    $sheet->setCellValue("{$aS}1", $titleAktif);
    $color("{$aS}1:{$aE}1", 'F97316');

    $ca = $colAktifStart;

    $cRMa = Coordinate::stringFromColumnIndex($ca);
    $sheet->mergeCells($cRMa . '2:' . $cRMa . '3');
    $sheet->setCellValue($cRMa . '2', 'RM');
    $color($cRMa . '2:' . $cRMa . '3', '2563EB');

    $wipSa = Coordinate::stringFromColumnIndex($ca + 1);
    $wipEa = Coordinate::stringFromColumnIndex($ca + 4);
    $sheet->mergeCells($wipSa . '2:' . $wipEa . '2');
    $sheet->setCellValue($wipSa . '2', 'WIP');
    $color($wipSa . '2:' . $wipEa . '3', 'FACC15', '000000');
    foreach (['Buffing', 'Sanding', 'Touch Up', 'Werate'] as $i => $sub) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca + 1 + $i) . '3', $sub);
    }

    $cFGa = Coordinate::stringFromColumnIndex($ca + 5);
    $sheet->mergeCells($cFGa . '2:' . $cFGa . '3');
    $sheet->setCellValue($cFGa . '2', 'FG');
    $color($cFGa . '2:' . $cFGa . '3', '16A34A');

    $cOTa = Coordinate::stringFromColumnIndex($ca + 6);
    $sheet->mergeCells($cOTa . '2:' . $cOTa . '3');
    $sheet->setCellValue($cOTa . '2', 'OT');
    $color($cOTa . '2:' . $cOTa . '3', '9CA3AF', '000000');

    $cStockStoA   = Coordinate::stringFromColumnIndex($ca + 7);
    $cBeliA       = Coordinate::stringFromColumnIndex($ca + 8);
    $cTfinA       = Coordinate::stringFromColumnIndex($ca + 9);
    $cKirimA      = Coordinate::stringFromColumnIndex($ca + 10);
    $cStockAdminA = Coordinate::stringFromColumnIndex($ca + 11);
    $cSelisihA    = Coordinate::stringFromColumnIndex($ca + 12);

    foreach ([
        [$cStockStoA,   'STOCK STO',   'DC2626'],
        [$cBeliA,       'BELI',        '7C3AED'],
        [$cTfinA,       'TF IN',       'BE185D'],
        [$cKirimA,      'KIRIM',       '0891B2'],
        [$cStockAdminA, 'STOCK ADMIN', '92400E'],
        [$cSelisihA,    'SELISIH',     '065F46'],
    ] as [$c, $label, $bg]) {
        $sheet->mergeCells($c . '2:' . $c . '3');
        $sheet->setCellValue($c . '2', $label);
        $color($c . '2:' . $c . '3', $bg);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. INSERT DATA
    |--------------------------------------------------------------------------
    */
    $rowIndex = 4;

    // Pass 1: hitung posisi & size tiap rm group
    $rmGroupRows = [];
    $tempRow     = 4;
    $tempLastRM  = null;
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
    $lastRM    = null;
    $rmSeenSto = []; // track apakah rm sudah ditulis qty_rm-nya (untuk STO sebelumnya)

    foreach ($data as $key => $item) {

        $rmCode = $item['info'][0];
        $rmDesc = $item['info'][1];
        $fgCode = $item['info'][2];

        $isSameRM  = ($rmCode !== 'OTHER' && $rmCode === $lastRM);
        $groupInfo = $rmGroupRows[$rmCode] ?? null;
        $groupSize = $groupInfo ? $groupInfo['count'] : 1;
        $groupStart= $groupInfo ? $groupInfo['start'] : $rowIndex;
        $groupEnd  = $groupStart + $groupSize - 1;

        // ── Kolom A-E ──────────────────────────────────────────────────────
        $sheet->setCellValue("A{$rowIndex}", $isSameRM ? '' : $rmCode);
        $sheet->setCellValue("B{$rowIndex}", $isSameRM ? '' : $rmDesc);
        $sheet->setCellValue("C{$rowIndex}", $item['info'][2]);
        $sheet->setCellValue("D{$rowIndex}", $item['info'][3]);
        $sheet->setCellValue("E{$rowIndex}", $item['info'][4]);

        if ($groupSize > 1 && !$isSameRM) {
            $sheet->mergeCells("A{$groupStart}:A{$groupEnd}");
            $sheet->mergeCells("B{$groupStart}:B{$groupEnd}");
        }

        // ── BLOK STO SEBELUMNYA ────────────────────────────────────────────
        $dSebel   = $hasPeriodeSebelumnya ? ($item['periode'][$periodeSebelumnya] ?? null) : null;
        $isFirstRM = !isset($rmSeenSto[$rmCode]);

        $rmQtySebel = $isFirstRM ? ($dSebel->qty_rm ?? 0) : 0;
        $buffSebel  = $dSebel->qty_buff   ?? 0;
        $sandSebel  = $dSebel->qty_sand   ?? 0;
        $touchSebel = $dSebel->qty_touch  ?? 0;
        $werSebel   = $dSebel->qty_werate ?? 0;
        $fgSebel    = $dSebel->qty_fg     ?? 0;
        $otSebel    = $dSebel->qty_ot     ?? 0;

        $cs2 = $colSebelumnyaStart;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2)     . $rowIndex, $rmQtySebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 1) . $rowIndex, $buffSebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 2) . $rowIndex, $sandSebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 3) . $rowIndex, $touchSebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 4) . $rowIndex, $werSebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 5) . $rowIndex, $fgSebel);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($cs2 + 6) . $rowIndex, $otSebel);

        // TOTAL STO SEBELUMNYA — merge per group RM, hanya tulis di baris pertama
        $cTotSebelCol = Coordinate::stringFromColumnIndex($cs2 + 7);
        if (!$isSameRM) {
            $totalStockStoSebel = $hasPeriodeSebelumnya
                ? $calcStockSto($rmCode, $periodeSebelumnya)
                : 0;
            $sheet->setCellValue("{$cTotSebelCol}{$rowIndex}", $totalStockStoSebel);
            if ($groupSize > 1) {
                $sheet->mergeCells("{$cTotSebelCol}{$groupStart}:{$cTotSebelCol}{$groupEnd}");
            }
        }

        $rmSeenSto[$rmCode] = true;

        // ── BLOK PERIODE AKTIF ─────────────────────────────────────────────
        $dAktif   = $item['periode'][$periodeAktif] ?? null;

        $rmQtyAktif  = $isSameRM ? 0 : ($dAktif->qty_rm     ?? 0);
        $buffAktif   = $dAktif->qty_buff   ?? 0;
        $sandAktif   = $dAktif->qty_sand   ?? 0;
        $touchAktif  = $dAktif->qty_touch  ?? 0;
        $werAktif    = $dAktif->qty_werate ?? 0;
        $fgAktif     = $dAktif->qty_fg     ?? 0;
        $otAktif     = $dAktif->qty_ot     ?? 0;

        $ca2 = $colAktifStart;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2)     . $rowIndex, $rmQtyAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 1) . $rowIndex, $buffAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 2) . $rowIndex, $sandAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 3) . $rowIndex, $touchAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 4) . $rowIndex, $werAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 5) . $rowIndex, $fgAktif);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($ca2 + 6) . $rowIndex, $otAktif);

        // STOCK STO AKTIF, BELI, TF IN, KIRIM, STOCK ADMIN, SELISIH
        // → hanya baris pertama per RM group, di-merge
        if (!$isSameRM) {
            $totalStockStoAktif = $calcStockSto($rmCode, $periodeAktif);
            $totalStockAdmin    = $stockAdminByRM[$rmCode] ?? 0;
            $totalSelisih       = $totalStockStoAktif - $totalStockAdmin;

            $totalBeli  = ($rmCode !== 'OTHER') ? ($beliIndex[$rmCode]  ?? 0) : 0;
            $totalTfin  = ($rmCode !== 'OTHER') ? ($tfinIndex[$rmCode]  ?? 0) : 0;
            $totalKirim = 0;
            foreach (($rmGroups[$rmCode] ?? []) as $gKey) {
                $gFgCode    = $data[$gKey]['info'][2];
                $totalKirim += ($gFgCode !== 'OTHER') ? ($kirimIndex[$gFgCode] ?? 0) : 0;
            }

            $sheet->setCellValue("{$cStockStoA}{$rowIndex}",   $totalStockStoAktif);
            $sheet->setCellValue("{$cBeliA}{$rowIndex}",       $totalBeli);
            $sheet->setCellValue("{$cTfinA}{$rowIndex}",       $totalTfin);
            $sheet->setCellValue("{$cKirimA}{$rowIndex}",      $totalKirim);
            $sheet->setCellValue("{$cStockAdminA}{$rowIndex}", $totalStockAdmin);
            $sheet->setCellValue("{$cSelisihA}{$rowIndex}",    $totalSelisih);

            if ($groupSize > 1) {
                foreach ([$cStockStoA, $cBeliA, $cTfinA, $cKirimA, $cStockAdminA, $cSelisihA] as $mc) {
                    $sheet->mergeCells("{$mc}{$groupStart}:{$mc}{$groupEnd}");
                }
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

    $sheet->setCellValue("A{$totalRow}", "TOTAL");
    $sheet->mergeCells("A{$totalRow}:E{$totalRow}");

    $totalColCount = $colCountSebel + $colCountAktif;
    for ($i = 0; $i < $totalColCount; $i++) {
        $c = Coordinate::stringFromColumnIndex($colStart + $i);
        $sheet->setCellValue(
            "{$c}{$totalRow}",
            "=SUM({$c}{$firstDataRow}:{$c}{$lastDataRow})"
        );
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
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color'       => ['rgb' => '000000'],
            ],
        ],
    ]);

    $sheet->getStyle("A4:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle("C4:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // Warna SELISIH: merah jika negatif (kurang), hijau jika positif/nol
    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
        $val = $sheet->getCell("{$cSelisihA}{$r}")->getValue();
        if (is_numeric($val)) {
            $bg = ($val < 0) ? 'FEE2E2' : 'D1FAE5'; // merah muda / hijau muda
            $sheet->getStyle("{$cSelisihA}{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'font' => ['color' => ['rgb' => ($val < 0) ? 'DC2626' : '065F46']],
            ]);
        }
    }

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

}