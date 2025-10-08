<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\TransferIn;
use App\Models\TransferInItems;
use App\Models\TransferOutItem;
use App\Models\Receiving;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ArticleController extends Controller
{
    public function index()
    {
        return view('inventory.article');
    }

    public function select()
{
    $storage = Article::select('id', 'article_code', 'description')
        ->where('description', 'like', '%Hardisk%')
        ->get();

    return response()->json($storage);
}


    public function data(Request $request)
{
    $query = Article::with('supplier');

    if ($request->filled('article_code')) {
        $query->where('article_code', 'like', '%' . $request->article_code . '%');
    }

    if ($request->filled('article_type')) {
        $query->where('article_type', 'like', '%' . $request->article_type . '%');
    }

    if ($request->filled('supplier')) {
        $query->where('supplier', 'like', '%' . $request->supplier . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', 'like', '%' . $request->status . '%');
    }

    return DataTables::of($query)
    ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    return '
    <div class="relative inline-block text-left">
      <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
       <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1 text-sm text-gray-700">
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail</a>
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-500 hover:text-gray-300"><i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Hapus</button>
          </form>
        </div>
      </div>
    </div>';
})

->editColumn('status', function ($row) {
    if ($row->status === 'active') {
        return '<span class="bg-green-500 text-gray-100 text-xs font-medium px-2.5 py-0.5 rounded">Active</span>';
    } elseif ($row->status === 'inactive') {
        return '<span class="bg-red-500 text-gray-100 text-xs font-medium px-2.5 py-0.5 rounded">Inactive</span>';
    }
    return $row->status;
})

        ->editColumn('qr_code_path', function ($row) {
            return '<img src="'.asset('storage/'.$row->qr_code_path).'" width="50">';
        })
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->rawColumns(['action', 'qr_code_path', 'status'])
        ->make(true);
}

    public function create()
    {
        
        return view('inventory.create-article');
    }

    public function store(Request $request)
{
    $request->validate([
        'articles' => 'required|array',
        'articles.*.supplier_code' => 'required|string',
        'articles.*.items' => 'required|array',
        'articles.*.items.*.article_type' => 'required|string',
        'articles.*.items.*.description' => 'required|string',
        'articles.*.items.*.unit' => 'required|string',
        'articles.*.items.*.color' => 'nullable|string',
        'articles.*.items.*.model' => 'nullable|string',
    ]);

    foreach ($request->articles as $group) {
        $supplier = $group['supplier_code'];
        foreach ($group['items'] as $item) {
            $code = 'ART' . strtoupper(Str::random(6));
            $articleData = [
                'article_code' => $code,
                'article_type' => $item['article_type'],
                'description' => $item['description'],
                'unit' => $item['unit'],
                'color' => $item['color'] ?? '',
                'model' => $item['model'] ?? '',
                'supplier_code' => $supplier,
                'status' => 'active',
                'note' => null,
            ];

            $qrPath = $this->generateQr($articleData);
            $articleData['qr_code_path'] = $qrPath;

            Article::create($articleData);
        }
    }

    return redirect()->route('inventory.article.index')->with('success', 'Artikel berhasil disimpan.');
}


   public function template()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'article_code',
        'article_type',
        'supplier_code',
        'description',
        'color',
        'model',
        'unit',
        'safety_stock',
        'min_package'
    ];

   $col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}


    $filename = 'template_article.xlsx';
    $writer = new Xlsx($spreadsheet);
    $tempFile = tempnam(sys_get_temp_dir(), $filename);
    $writer->save($tempFile);

    return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
}


   public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,xlsx,xls',
    ]);

    $file = $request->file('csv_file');
    $extension = $file->getClientOriginalExtension();
    $mappedRows = [];

    $expectedHeaders = [
        'article_code', 'article_type', 'supplier_code',
        'description', 'color', 'model', 'unit',
        'safety_stock', 'min_package'
    ];

    if ($extension === 'csv') {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        $rawHeader = fgetcsv($handle);

        $header = array_map(fn($h) => strtolower(trim(str_replace([' ', '/'], '_', $h))), $rawHeader);

        if ($header !== array_map(fn($h) => str_replace([' ', '/'], '_', $h), $expectedHeaders)) {
            return redirect()->back()->with('error', 'Header CSV tidak sesuai template.');
        }

        while ($row = fgetcsv($handle)) {
            if (count($row) !== count($header)) continue;
            $mappedRows[] = array_combine($header, $row);
        }
        fclose($handle);
    } else {
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file)->getActiveSheet();
        $rows = $sheet->toArray();
        $headerRow = $rows[0];
        unset($rows[0]);

        $header = array_map(fn($h) => strtolower(trim(str_replace([' ', '/'], '_', $h))), $headerRow);

        if ($header !== array_map(fn($h) => str_replace([' ', '/'], '_', $h), $expectedHeaders)) {
            return redirect()->back()->with('error', 'Header Excel tidak sesuai template.');
        }

        foreach ($rows as $row) {
            if (count($row) !== count($header)) continue;
            $mappedRows[] = array_combine($header, $row);
        }
    }

    $inserted = 0;
    foreach ($mappedRows as $data) {
        if (empty($data['article_code']) || empty($data['description'])) continue;

        if (Article::where('article_code', $data['article_code'])->exists()) continue;

        $qrPath = $this->generateQr($data['article_code']);

       Article::create([
    'article_code'   => $data['article_code'],
    'article_type'   => $data['article_type'] ?? '',
    'description'    => $data['description'],
    'supplier_code'  => $data['supplier_code'] ?? '',
    'unit'           => $data['unit'] ?? '',
    'color'          => $data['color'] ?? '',
    'model'          => $data['model'] ?? '',
    'safety_stock'   => is_numeric($data['safety_stock']) ? (int) $data['safety_stock'] : null,
    'min_package'    => is_numeric($data['min_package']) ? (int) $data['min_package'] : null,
    'qr_code_path'   => $qrPath,
    'status'         => 'active',
    'note'           => null,
]);


        $inserted++;
    }

    return redirect()->back()->with('success', "$inserted artikel berhasil diimport.");
}



 private function generateQr($articleCode)
{
    $qrContent = $articleCode;

    $qrCode = QrCode::create($qrContent)
        ->setSize(300)
        ->setMargin(10);

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    $folder = storage_path('app/public/qrcodes');
    if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
    }

    $filePath = $folder . '/' . $articleCode . '.png';
    file_put_contents($filePath, $result->getString());

    return 'qrcodes/' . $articleCode . '.png';
}


public function search(Request $request)
{
    $q = $request->query('q');

    $query = Article::with('supplier');

    if (!empty($q)) {
        $query->where(function ($subquery) use ($q) {
            $subquery->where('article_code', 'LIKE', "%{$q}%")
                     ->orWhere('description', 'LIKE', "%{$q}%");
        });
    }

    $results = $query->limit(10)->get();

    $mapped = $results->map(function ($article) {
        return [
            'article_code'   => $article->article_code,
            'description'    => $article->description,
            'unit'           => $article->unit,
            'supplier_name'  => optional($article->supplier)->name ?? '-',
            'supplier_code'  => $article->supplier_code
        ];
    });

    return response()->json($mapped);
}



     public function dropdown()
    {
        return DB::table('articles')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['article_code', 'description']);
    }

   public function find($code)
{
    // === Transfer In ===
    $transfer = TransferIn::with(['supplier', 'items.article'])
    ->where('code', $code)
    ->first();

if ($transfer) {
    return response()->json([
        'type' => 'transfer_in',
        'code' => $transfer->code,
        'supplier_name' => optional($transfer->supplier)->name,
        'supplier_code' => $transfer->supplier_code,
        'items' => $transfer->items->map(function ($item) use ($transfer) {
            $articleCode = $item->article->article_code;
             // Ambil transfer out terakhir untuk item ini
    $lastTransferOut = \App\Models\TransferOutItem::where('transfer_in_code', $transfer->code)
                        ->orderBy('created_at', 'desc')
                        ->first();
            

            return [
                'id'              => $item->id,
                'article_code'    => $articleCode,
                'description'     => $item->article->description,
                'uom'             => $item->article->unit,
                'min_package'     => $item->article->min_package,
               'qty' => max(0, ($item->qty + $item->qty_return) - $item->qty_used),
                'destination_id'  => $item->article->type->warehouse_id ?? null,
                'destination_name'=> optional($item->article->type->warehouse)->name,
                'qty_used'         => $lastTransferOut ? $lastTransferOut->qty : 0, // <-- last qty used
            ];
        }),
    ]);
}

$item = TransferInItems::with(['article', 'transferIn'])
    ->where('code', $code) // code dari transfer_in_items
    ->first();

$lastQtyOut = null;

if ($item && $item->transferIn) {
    $lastQtyOut = \App\Models\TransferOutItem::where('transfer_in_code', $item->transferIn->code)
                    ->where('article_code', $item->article_code)
                    ->orderBy('created_at', 'desc')
                    ->first();
}

if ($item) {
    return response()->json([
        'type'           => 'transfer_in_item',
        'code'           => $item->code, // code item, bukan header
        'transfer_in_code'  => optional($item->transferIn)->code, // <<== tambahkan ini
        'supplier_name'  => optional($item->article->supplier)->name,
        'supplier_code'  => optional($item->transferIn)->supplier_code,
        'article_code'   => $item->article->article_code,
        'description'    => $item->article->description,
        'uom'            => $item->article->unit,
        'min_package'    => $item->article->min_package,
        'qty'            => $item->qty - $item->qty_used, // sisa stok
        'destination_id' => $item->article->type->warehouse_id ?? null,
        'destination_name' => optional($item->article->type->warehouse)->name,
        'qty_used'         => $lastQtyOut ? $lastQtyOut->qty : 0, // <-- last qty used per article
    ]);
}




    // === LPB ===
    $lpb = Receiving::with(['supplier', 'items.article'])
        ->where('receiving_number', $code)
        ->first();

    if ($lpb) {
        return response()->json([
            'type' => 'lpb',
            'code' => $lpb->receiving_number,
            'supplier_name' => optional($lpb->supplier)->name,
            'supplier_code' => $lpb->supplier_code,
            'items' => $lpb->items->map(function ($item) use ($lpb) {
                $articleCode = $item->article->article_code;
              
                return [
                    'id'            => $item->id, // ✅ kirim id asli dari DB
                    'article_code' => $articleCode,
                    'description'  => $item->article->description,
                    'uom'          => $item->article->unit,
                    'min_package'  => $item->article->min_package,
                    'qty'          => $item->qty_received - $item->qty_used,
                    'destination_id'   => $item->article->type->warehouse_id ?? null,
                    'destination_name' => optional($item->article->type->warehouse)->name,
                    'qty_out'          => $item->qty_used   ,
                ];
            }),
        ]);
    }

    // === Artikel biasa ===
    $article = Article::with(['supplier', 'type.warehouse'])
        ->where('article_code', $code)
        ->first();

    if ($article) {
        return response()->json([
            'type' => 'article',
            'article_code' => $article->article_code,
            'description'  => $article->description,
            'supplier_name' => optional($article->supplier)->name,
            'supplier_code' => optional($article->supplier)->code,
            'uom'          => $article->unit,
            'min_package'  => $article->min_package,
            'qty'          => 1,
            'destination_id'   => $article->type->warehouse_id ?? null,
            'destination_name' => optional($article->type->warehouse)->name,
        ]);
    }

    return response()->json(['message' => 'Not Found'], 404);
}



    public function getByInspectionPost(Request $request)
{
    $post = $request->get('post');
    $supplierCode = $request->get('supplier');
    $term = $request->get('term');

    $query = Article::with('supplier');

    if ($post === 'Incoming') {
        $query->whereIn('article_type', ['RMP', 'RMNP']);
    } else {
        $query->where('article_type', 'FG');
    }

    if ($supplierCode) {
        $query->where('supplier_code', $supplierCode);
    }

    if ($term) {
        $query->where(function ($q) use ($term) {
            $q->where('article_code', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    $articles = $query->orderBy('description')->get([
        'id', 'article_code', 'description', 'supplier_code'
    ]);

    return response()->json($articles);
}


public function getBySupplier($supplierCode)
{
    $articles = Article::where('supplier_code', $supplierCode)
        ->whereIn('article_type', ['RMNP', 'RMP'])
        ->orderBy('description')
        ->get(['id', 'article_code', 'description']);

    return response()->json($articles);
}



}
