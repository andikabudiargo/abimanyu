<?php

namespace App\Http\Controllers;

 use App\Models\Article;
 use Illuminate\Http\Request;

class ProductKnowledgeController extends Controller
{
     public function index() {
        return view('ppic.product-knowledge');
    }

    public function search(Request $request)
{
    $q = $request->query('q');

    $query = Article::with([
        'supplier:id,code,name',
        'customer:id,code,name',
        'type:id,code,name',
        'images:id,article_code,file_name'
    ])
    ->select([
        'id',
        'article_code',
        'description',
        'article_type',
        'min_package',
        'unit',
        'status',
        'supplier_code'
    ])

      // ✅ EXCLUDE ARTICLE TYPE GA & PT
   ->whereNotIn('article_type', ['GA', 'PT'])

    // ✅ HANYA ARTICLE ACTIVE
    ->where('status', 'active');   // ← TAMBAHAN INI

    // 🔎 SEARCH
    if (!empty($q)) {
        $query->where(function ($subquery) use ($q) {
            $subquery->where('article_code', 'LIKE', "%{$q}%")
                     ->orWhere('description', 'LIKE', "%{$q}%");
        });
    }

    $articles = $query
        ->orderBy('article_code')
        ->get();

   return response()->json([
    'success' => true,
    'data' => $articles->map(function ($item) {

        $partnerName = $item->article_type == 'FG'
            ? optional($item->customer)->name
            : optional($item->supplier)->name;


         $articleCode = trim($item->article_code);

// pastikan ada slash di akhir basePath
$basePath = rtrim(asset('article_image/' . $articleCode), '/') . '/';

$images = $item->images->map(function ($img) use ($basePath) {
    return $basePath . $img->file_name;
})->values()->toArray();

        return [
            'id'             => $item->id, // ⭐ penting
            'code'           => $item->article_code,
            'description'    => $item->description,
            'article_type'   => optional($item->type)->name,
            'min_package'    => $item->min_package,
            'unit'           => $item->unit,
            'partner_name'   => $partnerName,
            'images'         => $images
        ];
    })
]);
}

}