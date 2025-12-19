<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Sto;
use App\Models\StoItem;
use Illuminate\Support\Facades\DB;

use App\Models\Article;


class STOController extends Controller
{
   public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('facility.sto');
    }

   public function create()
{
    $warehouse     = $this->userWarehouse();
    $allowedTypes  = $this->allowedArticleTypes($warehouse);

    $articles = Article::whereIn('article_type', $allowedTypes)
        ->select('id', 'article_code', 'description', 'unit', 'article_type')
        ->orderBy('description')
        ->get();

          // ambil STO number yang sudah ada di database
    $usedStoNumbers = \DB::table('stos')
        ->pluck('sto_number')
        ->toArray();

        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('facility.create-sto', compact(
        'warehouse',
        'articles',
        'usedStoNumbers'
    ));
}

     private function userWarehouse()
{
    $userId = Auth::id();

    return match ($userId) {
        55        => 'Raw Material',
        64        => 'Finish Goods',
        92        => 'Work In Progress',
        86, 63    => 'OT',
        67        => 'Chemical',
        94        => 'Consumable',
        default   => 'Raw Material',
    };
}

private function allowedArticleTypes(string $warehouse): array
{
    return match ($warehouse) {
        'Raw Material'     => ['RMP','RMNP'],
        'Finish Goods'     => ['FG'],
        'Work In Progress' => ['RMP','RMNP', 'FG', 'CM1', 'CM2'],
        'OT'               => ['RMP','RMNP', 'FG', 'CM1', 'CM2'],
        'Chemical'         => ['CM1'],
        'Consumable'       => ['CM2'],
        default            => [],
    };
}

public function store(Request $request)
{
    // =========================
    // VALIDATION
    // =========================
    $validator = \Validator::make($request->all(), [
         'sto_number'              => 'required|string|unique:stos,sto_number',
        'articles'                  => 'required|array',
        'articles.*.article_code'     => 'required|exists:articles,article_code',
        'articles.*.qty'            => 'required|numeric|min:0',
        'articles.*.location'       => 'required|string',
        'note'                      => 'nullable|string',
    ]);

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
        $sto = Sto::create([
            'sto_number' => $request->sto_number,
            'warehouse'  => $this->userWarehouse(),
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

            $sto->items()->create([
                'article_code' => $row['article_code'],
                'qty'        => $row['qty'],
                'location'   => $row['location'],
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
        ], 200);

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
    $columns = [
        0 => 'sto_items.id',
        1 => 'sto_items.location',
        2 => 'articles.description',
        3 => 'sto_items.qty',
        4 => 'articles.unit',
        5 => 'stos.sto_number',
        6 => 'stos.note',
    ];

    $totalData = DB::table('sto_items')->count();
    $totalFiltered = $totalData;

    $limit  = $request->length;
    $start  = $request->start;
    $order  = $columns[$request->input('order.0.column')];
    $dir    = $request->input('order.0.dir');

   $query = DB::table('sto_items')
    ->join('stos', 'stos.id', '=', 'sto_items.sto_id')
    ->join('articles', 'articles.article_code', '=', 'sto_items.article_code')
    ->select(
        'sto_items.id as sto_item_id',   // kalau nanti mau edit item
        'sto_items.sto_id',              // 🔥 INI KUNCI UTAMA
        'sto_items.location',
        'sto_items.article_code',
        'articles.description as part_name',
        'sto_items.qty',
        'articles.unit',
        'stos.sto_number',
        'stos.note'
    );


        // =====================
    // 🔍 FILTERS
    // =====================
   // FILTERS
if ($request->filled('location')) {
    $query->where('sto_items.location', $request->location);
}

if ($request->filled('article')) {
    $query->where('sto_items.article_code', 'like', '%'.$request->article.'%');
}

if ($request->filled('sto_number')) {
    $query->where('stos.sto_number', 'like', '%'.$request->sto_number.'%');
}

    // 🔍 SEARCH
    if (!empty($request->search['value'])) {
        $search = $request->search['value'];

       $query->where(function ($q) use ($search) {
    $q->where('sto_items.article_code', 'LIKE', "%{$search}%") // ✅
      ->orWhere('articles.description', 'LIKE', "%{$search}%")
      ->orWhere('stos.sto_number', 'LIKE', "%{$search}%")
      ->orWhere('sto_items.location', 'LIKE', "%{$search}%");
});


        $totalFiltered = $query->count();
    }

   $query->orderBy($order, $dir);

// 🔥 JIKA BUKAN SHOW ALL
if ($limit != -1) {
    $query->offset($start)->limit($limit);
}

$data = $query->get();


    $result = [];
    foreach ($data as $row) {
      $result[] = [
    'DT_RowAttr' => [
        'data-id' => $row->sto_id, // 🔥 PAKE sto_id
        'class'   => 'sto-row cursor-pointer hover:bg-blue-50'
    ],
    'location'     => $row->location,
    'article_code' => $row->article_code,
    'part_name'    => $row->part_name,
    'qty'          => $row->qty,
    'unit'         => $row->unit,
    'sto_number'   => $row->sto_number,
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

    return view('facility.edit-sto', [
        'sto'       => $sto,
        'items'     => $sto->items,
        'articles'  => Article::select('id', 'article_code', 'description', 'unit')
                              ->orderBy('description')
                              ->get(),
        'warehouse' => optional($sto->items->first())->location ?? '',
    ]);
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
    $search = $request->get('q');

    $allowedTypes = [
        'RMP',
        'RMNP',
        'CM1',
        'CM2',
        'FG',
    ];

    $articles = Article::select('article_code', 'description', 'article_type')
        ->whereIn('article_type', $allowedTypes)
        ->when($search, function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('article_code', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->orderBy('article_code')
        ->get();

    return response()->json([
        'results' => $articles->map(function ($a) {
            return [
                'id'   => $a->article_code,
                'text' => "{$a->article_code} - {$a->description}",
            ];
        }),
    ]);
}


}
