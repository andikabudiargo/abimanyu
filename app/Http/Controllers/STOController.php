<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Sto;
use App\Models\StoItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Article;

class STOController extends Controller
{

public function index()
{
    return view('facility.sto');
}

  public function create()
{
    $warehouse = $this->userWarehouse();

    $canChooseWarehouse = is_null($warehouse); // 🔥 INI BARU

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $articles = Article::whereIn('article_type', $allowedTypes)
        ->select('id', 'article_code', 'description', 'unit', 'article_type')
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
        55        => 'Raw Material',
        44        => 'Finish Goods',
        94        => 'Werate',
        92        => 'WIP Buffing',
        96        => 'WIP Touch Up',
        95        => 'WIP Sanding',
        63        => 'OT',
        67        => null,
        53,2     => null, // 🔥 BOLEH PILIH SENDIRI
        default   => 'Raw Material',
    };
}

private function allowedWarehouses(): array
{
    $userId = Auth::id();

    // 🔥 User 67 hanya boleh Chemical & Consumable
    if ($userId == 67) {
        return ['Chemical', 'Consumable'];
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
        ];
    }

    // 🔒 User terkunci
    return [$this->userWarehouse()];
}


private function allowedArticleTypes(?string $warehouse): array
{
    // 🔥 kalau user boleh pilih gudang (warehouse = null)
    // jangan batasi tipe artikel
    if (is_null($warehouse)) {
        return ['RMP','RMNP','FG','CM1','CM2'];
    }

    return match ($warehouse) {
        'Raw Material'     => ['RMP','RMNP'],
        'Finish Goods'     => ['FG'],
        'WIP Buffing'      => ['FG'],
        'Werate'           => ['RMP','RMNP', 'FG'],
        'WIP Touch Up'     => ['FG'],
        'WIP Sanding'      => ['FG'],
        'OT'               => ['FG'],
        'Chemical'         => ['CM1'],
        'Consumable'       => ['CM2','RMP','RMNP'],
        default            => [],
    };
}

public function getArticlesByWarehouse(Request $request)
{
    // 🔐 Warehouse berdasarkan user login
    $userWarehouse = $this->userWarehouse();

    /**
     * RULE:
     * - Jika userWarehouse !== null → PAKSA pakai itu
     * - Jika null (boleh pilih sendiri) → pakai request
     */
    $warehouse = $userWarehouse ?? $request->warehouse;

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $articles = Article::whereIn('article_type', $allowedTypes)
        ->select('article_code', 'description', 'unit')
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

            $uom = $row['article_code'] === 'OTHER'
                ? $row['uom']
                : Article::where('article_code', $row['article_code'])->value('unit');

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
    5 => 'articles.unit',
    6 => 'stos.sto_number',   // ✅ FIX
    7 => 'users.name',
    8 => 'stos.created_at',
    9 => 'stos.note',
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
$qtyFormatted = $row->location === 'Chemical'
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
                $a->select('id', 'article_code', 'description', 'unit');
            }]);
        }
    ])->findOrFail($id);

    $items = $sto->items;

    $warehouse = optional($sto->items->first())->location ?? null;

    $canChooseWarehouse = is_null($warehouse); // 🔥 logika sama seperti create

    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $articles = Article::whereIn('article_type', $allowedTypes)
        ->select('id', 'article_code', 'description', 'unit', 'article_type')
        ->orderBy('description')
        ->get();

    $allowedWarehouses = $this->allowedWarehouses(); // 🔥 KIRIM KE VIEW

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
        ->limit(20)
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
    $page      = $request->get('page', 1);   // halaman, default 1
    $perPage   = 20;                          // jumlah item per load
    $offset    = ($page - 1) * $perPage;

    $warehouse = $this->userWarehouse();
    $allowedTypes = $this->allowedArticleTypes($warehouse);

    $query = Article::select('article_code', 'unit', 'description', 'article_type')
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


}