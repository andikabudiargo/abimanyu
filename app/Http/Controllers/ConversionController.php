<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Conversion;
use App\Models\ConversionValue;
use App\Models\Customer;
use App\Models\Article;


class ConversionController extends Controller
{
     public function index()
    {
        
        return view('marketing.conversion');
    }

    public function data(Request $request)
{
    $query = Conversion::all();

    return datatables()->of($query)

 ->addColumn('action', function ($query) {
        $id = $query->id;
        $number = $query->conversion_number;
        $dropdownId = 'dropdown-' . $query->id;

        return '
        <div class="relative inline-block text-left">
            <button type="button"
                data-dropdown-id="' . $dropdownId . '"
                onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
                class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                <i data-feather="align-justify"></i>
            </button>
            <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">
                <div class="py-1 text-sm text-gray-700">
                    <a href="" class="block px-4 py-2 hover:bg-gray-100">
                        <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
                    </a>
                      <a href="" class="block px-4 py-2 hover:bg-gray-100">
                        <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
                    </a>
                    <button 
                        type="button" 
                        class="w-full text-left px-4 py-2 text-red-500 hover:bg-red-500 hover:text-white"
                        data-url=""
                        data-id="'. $id . '"
                        data-number="'. $number . '">
                        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>';
    })


        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
         ->addColumn('created_by', function ($row) {
    return $row->createdBy ? $row->createdBy->name : '-';
})
        
       
        ->rawColumns(['created_by','action'])
        ->make(true);
}

public function conversionChart(Request $request)
{
    $year = $request->year ?? date('Y');

    /*
    |--------------------------------------------------------------------------
    | 1. MONTHLY DATA (UNTUK CHART)
    |--------------------------------------------------------------------------
    */
    $monthly = DB::table('conversions')
        ->select(
            'month',
            DB::raw('SUM(total_conversion) as total_conversion')
        )
        ->where('year', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    $months = [];
    $totals = [];

    for ($i = 1; $i <= 12; $i++) {
        $months[] = date('M', mktime(0,0,0,$i,1));
        $totals[] = $monthly[$i]->total_conversion ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. GRAND TOTAL METRICS
    |--------------------------------------------------------------------------
    */

    $summary = DB::table('conversions')
        ->selectRaw('
            SUM(total_conversion) as grand_total_conversion,
            SUM(total_qty) as grand_total_qty
        ')
        ->where('year', $year)
        ->first();

    /*
    |--------------------------------------------------------------------------
    | 3. TOTAL CUSTOMER DISTINCT
    |--------------------------------------------------------------------------
    */

    $totalCustomer = DB::table('conversion_details as cd')
        ->join('conversions as c', 'cd.conversion_id', '=', 'c.id')
        ->join('articles as a', 'cd.article_code', '=', 'a.article_code')
        ->where('c.year', $year)
        ->distinct('a.supplier_name')
        ->count('a.supplier_name');

    return response()->json([
        'months' => $months,
        'totals' => $totals,

        'grand_total_conversion' => $summary->grand_total_conversion ?? 0,
        'grand_total_qty' => $summary->grand_total_qty ?? 0,
        'total_customer' => $totalCustomer
    ]);
}

    public function create()
    {
        
        return view('marketing.create-conversion');
    }

    public function getConversion(Request $request)
{
    $year  = $request->year;
    $month = $request->month;

   $baseQuery = DB::table('sj_temporary as sj')
    ->selectRaw("
        sj.customer,
        sj.article_code,
        SUM(sj.delivery_qty)      as delivery_qty,
        GROUP_CONCAT(DISTINCT sj.delivery_date ORDER BY sj.delivery_date ASC) as delivery_dates,
        MAX(sj.delivery_date)     as last_delivery_date,
        MIN(sj.delivery_date)     as first_delivery_date
    ")
    ->when($year, function ($q) use ($year) {
        $q->whereYear('sj.delivery_date', $year);
    })
    ->when($month, function ($q) use ($month) {
        $q->whereMonth('sj.delivery_date', $month);
    })
    
    ->groupBy('sj.customer', 'sj.article_code');

    $query = DB::query()->fromSub($baseQuery, 'agg')

        ->leftJoin('articles as ar', 'ar.article_code', '=', 'agg.article_code')
        ->leftJoin('customers as cu', 'cu.name', '=', 'agg.customer')
        ->leftJoin('basic_prices as bp', 'bp.article_code', '=', 'agg.article_code')

        ->selectRaw("
            agg.customer,
            cu.name as customer_name,
            agg.article_code,
            ar.description as article_desc,
            agg.delivery_qty,
            agg.first_delivery_date,
    agg.last_delivery_date,

            CASE
                WHEN bp.matome IS NULL THEN NULL
                ELSE bp.matome
            END as matome,

            CASE
                WHEN bp.matome IS NULL THEN NULL
                ELSE ROUND(agg.delivery_qty * bp.matome, 2)
            END as conversion,

            CASE
                WHEN bp.matome IS NULL
                THEN 'Belum disync — jalankan Sync Pricing terlebih dahulu'
                ELSE NULL
            END as fallback_note
        ")
        ->when($request->matome_filter === 'with', function ($q) {
        $q->whereNotNull('bp.matome');
    })
    ->when($request->matome_filter === 'without', function ($q) {
        $q->whereNull('bp.matome');
    });

    $data = $query->get();

    // =======================
    // SUMMARY
    // =======================
    $summary = [
        'total_rows'          => $data->count(),
        'total_customers'     => $data->pluck('customer')->unique()->count(),
        'total_qty'           => $data->sum('delivery_qty'),
        'total_conversion'    => $data->sum('conversion'),
        'total_no_matome'     => $data->whereNull('conversion')->count(),
        'articles_no_matome'  => $data->whereNull('conversion')
                                    ->pluck('article_code')
                                    ->unique()
                                    ->values(),
    ];

    return response()->json([
        'data'    => $data,
        'summary' => $summary,
    ]);
}

public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year'             => 'required|digits:4',
            'month'            => 'required|integer|min:1|max:12',
            'note'             => 'nullable|string',
            'total_qty'        => 'required|numeric|min:0',
            'total_conversion' => 'required|numeric|min:0',
            'details'          => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {

            $year  = $request->year;
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Generate conversion number
            $last = Conversion::where('year', $year)
                ->where('month', $request->month)
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $sequence = $last ? ((int) substr($last->conversion_number, -4) + 1) : 1;
            $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $conversionNumber = "MKTC{$year}{$month}{$sequence}";

            // Simpan header
            $conversion = Conversion::create([
                'conversion_number' => $conversionNumber,
                'year'              => $year,
                'month'             => $request->month,
                'status'            => 'Draft',
                'total_qty'         => $request->total_qty,
                'total_conversion'  => $request->total_conversion,
                'created_by'        => Auth::id(),
                'note'              => $request->note ?? '',
            ]);

            // Decode dan filter hanya detail yang punya nilai matome valid
            $details = collect(json_decode($request->details, true))
                ->filter(fn($item) =>
                    !is_null($item['matome'])       &&
                    !is_null($item['conversion'])
                )
                ->values();

            // Jika setelah filter tidak ada yang valid sama sekali
            if ($details->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada artikel dengan matome valid. Lakukan Sync Pricing terlebih dahulu.',
                ], 422);
            }

            // Insert detail
            $insertData = $details->map(fn($item) => [
                'conversion_id'    => $conversion->id,
                'article_code'     => $item['article_code'],
                'delivery_qty'     => $item['delivery_qty'],
                'matome'           => $item['matome'],
                'conversion' => $item['conversion'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ])->toArray();

            DB::table('conversion_details')->insert($insertData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conversion saved successfully.',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save conversion.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



 public function indexSetting()
    {
        
        return view('marketing.setting');
    }

    public function storeConversionValue(Request $request)
{
    $request->validate([
        'value'              => 'required|string|max:100',
        'effective_date'     => 'required|date',
    ]);

    $setting = ConversionValue::create([
        'value'             => $request->value,
        'effective_date'    => $request->effective_date,
        'created_by'        => Auth::id(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Conversion Value Succesfully Saved.',
        'data'    => $setting
    ]);
}

public function dataConversionValue(Request $request)
{
    $value = ConversionValue::select([
        'id', 'value','effective_date','created_by', 'created_at'
    ]);

    return datatables()->of($value)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
         ->addColumn('created_by', function ($row) {
    return $row->createdBy ? $row->createdBy->name : '-';
})
        
       
        ->rawColumns(['created_by'])
        ->make(true);
}

  public function indexPrice()
    {
         $customers = Customer::orderBy('name')->get();

        $articles = Article::whereIn('article_type', ['FG'])
        ->orderBy('description')
        ->get();
        return view('marketing.price',compact('customers','articles'));
    }

     
    public function getPricingData(Request $request)
{
    $query = DB::table('articles as a')
        ->select(
            'a.article_code',
            'a.description',
            'c.name as supplier_name',
            'bp.purchase_price as average_raw_material_price',
            'bp.selling_price',
            'bp.rm_conversion',
            'bp.fg_conversion',
            'bp.matome',
            'bp.last_calculated_at'
        )
        ->leftJoin('customers as c', 'c.code', '=', 'a.supplier_code')
        ->leftJoin('basic_prices as bp', 'bp.article_code', '=', 'a.article_code')
        ->whereRaw("a.article_type = 'FG'")
        ->whereRaw("a.status = 'active'")
        ->when($request->search['value'] ?? null, function ($q) use ($request) {
    $search = $request->search['value'];
    $q->where(function ($q) use ($search) {
        $q->where('a.article_code', 'like', "%$search%")
          ->orWhere('a.description', 'like', "%$search%");
    });
})
        // Filter customer — cari berdasarkan nama customer
        ->when($request->customer, function ($q) use ($request) {
            $q->where('c.name', $request->customer);
        })
        // Filter article — cari berdasarkan article_code
        ->when($request->article, function ($q) use ($request) {
            $q->where('a.article_code', $request->article);
        })
        ->when($request->only_matome, function ($q) {
    $q->whereNotNull('bp.matome');
});

    return datatables()->of($query)

        ->editColumn('average_raw_material_price', function ($row) {
            if ($row->average_raw_material_price === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->average_raw_material_price, 0, ',', '.')
                . '</div>';
        })

        ->editColumn('selling_price', function ($row) {
            if ($row->selling_price === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->selling_price, 0, ',', '.')
                . '</div>';
        })

        ->editColumn('rm_conversion', function ($row) {
            if ($row->rm_conversion === null) {
                return '<div class="text-end">-</div>';
            }
             return '<div class="text-end">'
        . rtrim(rtrim(number_format($row->rm_conversion, 16, ',', '.'), '0'), ',')
        . '</div>';
        })

        ->editColumn('fg_conversion', function ($row) {
            if ($row->fg_conversion === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
        . rtrim(rtrim(number_format($row->fg_conversion, 16, ',', '.'), '0'), ',')
        . '</div>';
        })

        ->editColumn('matome', function ($row) {
            if ($row->matome === null) {
                return '<div class="text-end">-</div>';
            }
            $class = $row->matome >= 0 ? 'text-green-600' : 'text-red-600';
             return '<div class="text-end font-bold ' . $class . '">'
        . rtrim(rtrim(number_format($row->matome, 16, ',', '.'), '0'), ',')
        . '</div>';
        })

        ->editColumn('last_calculated_at', function ($row) {
            if (!$row->last_calculated_at) {
                return '<div class="text-center text-gray-400 text-xs">Belum ada data</div>';
            }
            return '<div class="text-center text-xs text-gray-500">'
                . \Carbon\Carbon::parse($row->last_calculated_at)->format('d/m/Y H:i')
                . '</div>';
        })

        ->rawColumns([
            'average_raw_material_price',
            'selling_price',
            'rm_conversion',
            'fg_conversion',
            'matome',
            'last_calculated_at',
        ])

        ->make(true);
}

public function getArticles()
{
    $articles = DB::table('articles')
        ->select(
            'article_code',
            'description',
            'supplier_name'
        )
        ->where('article_type', 'FG')
        ->where('status', 'active')
        ->orderBy('article_code')
        ->get();

    return response()->json($articles);
}

public function syncPricing(Request $request)
{
    try {
        $conversion = DB::table('conversion_values')
            ->where('effective_date', '<=', date('Y-m-d'))
            ->orderByDesc('effective_date')
            ->value('value');

        $conv = $conversion > 0 ? (float) $conversion : null;

        $rows = DB::table('articles as a')
            ->selectRaw("
                a.article_code,
                avg_rm.average_raw_material_price,
                sj.selling_price,
                CASE WHEN ? > 0 THEN avg_rm.average_raw_material_price / ? ELSE NULL END as rm_conversion,
                CASE WHEN ? > 0 THEN sj.selling_price / ? ELSE NULL END as fg_conversion,
                CASE WHEN ? > 0 THEN (sj.selling_price - avg_rm.average_raw_material_price) / ? ELSE NULL END as matome,
? as conversion_value_used
            ", [$conv, $conv, $conv, $conv, $conv, $conv, $conv])

            ->join(DB::raw("
                (
                    SELECT
                        b.article_fg,
                        AVG(seg.segment_avg) as average_raw_material_price
                    FROM boms b
                    INNER JOIN (
                        SELECT
                            article_code,
                            grp,
                            AVG(price) as segment_avg
                        FROM (
                            SELECT
                                article_code,
                                price,
                                @grp := IF(
                                    @prev_price <> price OR @prev_article <> article_code,
                                    @grp + 1,
                                    @grp
                                ) as grp,
                                @prev_price   := price,
                                @prev_article := article_code
                            FROM lpb_temporary
                            CROSS JOIN (
                                SELECT @grp := 0, @prev_price := NULL, @prev_article := NULL
                            ) init
                            ORDER BY article_code, id
                        ) flagged
                        GROUP BY article_code, grp
                    ) seg ON seg.article_code = b.article_rm
                    GROUP BY b.article_fg
                ) avg_rm
            "), 'avg_rm.article_fg', '=', 'a.article_code', 'inner')

          ->leftJoin(DB::raw("
    (
        SELECT article_code, AVG(price + service_price) as selling_price
        FROM sj_temporary
        GROUP BY article_code
    ) sj
"), 'sj.article_code', '=', 'a.article_code')

            ->whereRaw("a.article_type = 'FG'")
            ->whereRaw("a.status = 'active'")
            ->get();

        $synced  = 0;
        $skipped = [];

        // Mapping label kolom untuk pesan yang lebih informatif
        $requiredFields = [
            'average_raw_material_price' => 'Raw Material Price (tidak ada data di LPB)',
            'selling_price'              => 'Selling Price (tidak ada data di SJ)',
            'rm_conversion'              => 'RM Conversion (average_raw_material_price kosong)',
            'fg_conversion'              => 'FG Conversion (selling_price kosong)',
            'matome'                     => 'Matome (fg_conversion atau rm_conversion kosong)',
            'conversion_value_used'      => 'Conversion Value (belum ada nilai konversi aktif)',
        ];

        foreach ($rows as $row) {

            // Cek kolom mana yang null
            $missingFields = [];
            foreach ($requiredFields as $field => $reason) {
                if (is_null($row->$field)) {
                    $missingFields[$field] = $reason;
                }
            }

            // Skip jika ada kolom yang kosong
            if (!empty($missingFields)) {
                $skipped[] = [
                    'article_code' => $row->article_code,
                    'reasons'      => array_values($missingFields),
                ];
                continue;
            }

            DB::table('basic_prices')->updateOrInsert(
                ['article_code' => $row->article_code],
                [
                    'purchase_price'        => $row->average_raw_material_price,
                    'selling_price'         => $row->selling_price,
                    'rm_conversion'         => $row->rm_conversion,
                    'fg_conversion'         => $row->fg_conversion,
                    'matome'                => $row->matome,
                    'conversion_value_used' => $row->conversion_value_used,
                    'last_calculated_at'    => now(),
                ]
            );

            $synced++;
        }

        return response()->json([
            'success'      => true,
            'message'      => $synced . ' artikel berhasil disync.',
            'synced_count' => $synced,
            'skip_count'   => count($skipped),
            'skipped'      => $skipped,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal sync: ' . $e->getMessage(),
        ], 500);
    }
}





}
