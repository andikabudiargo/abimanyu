<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Conversion;
use App\Models\ConversionValue;


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
            SUM(estimated_profit) as grand_total_profit,
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
        'grand_total_profit' => $summary->grand_total_profit ?? 0,
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
            MAX(sj.article_desc) as article_desc,
            SUM(sj.delivery_qty) as delivery_qty,
            MAX(sj.delivery_date) as last_delivery_date
        ")
        ->when($year, function($q) use ($year){
            $q->whereYear('sj.delivery_date',$year);
        })
        ->when($month, function($q) use ($month){
            $q->whereMonth('sj.delivery_date',$month);
        })
        ->groupBy('sj.customer','sj.article_code');

    $query = DB::query()->fromSub($baseQuery,'agg')

        ->leftJoin('basic_prices as mp', function ($join) {
    $join->on('mp.article_code','=','agg.article_code')
         ->where('mp.price_type','=','MATERIAL')
         ->whereRaw("
            mp.effective_date = (
                SELECT MAX(m2.effective_date)
                FROM basic_prices m2
                WHERE m2.article_code = agg.article_code
                AND m2.price_type = 'MATERIAL'
                AND m2.effective_date <= agg.last_delivery_date
            )
         ");
})

       ->leftJoin('basic_prices as sp', function ($join) {
    $join->on('sp.article_code','=','agg.article_code')
         ->where('sp.price_type','=','SERVICE')
         ->whereRaw("
            sp.effective_date = (
                SELECT MAX(s2.effective_date)
                FROM basic_prices s2
                WHERE s2.article_code = agg.article_code
                AND s2.price_type = 'SERVICE'
                AND s2.effective_date <= agg.last_delivery_date
            )
         ");
})

        ->leftJoin('conversion_values as cv', function ($join) {
            $join->whereRaw('cv.effective_date = (
                SELECT MAX(c2.effective_date)
                FROM conversion_values c2
                WHERE c2.effective_date <= agg.last_delivery_date
            )');
        })

        ->selectRaw("
            agg.customer,
            agg.article_code,
            agg.article_desc,
            agg.delivery_qty,

            IFNULL(mp.price,0) as material_price,
            IFNULL(sp.price,0) as service_price,

            (IFNULL(mp.price,0) + IFNULL(sp.price,0)) as price,

            IFNULL(cv.value,1) as conversion_value,

            CASE
                WHEN IFNULL(cv.value,0) > 0
                THEN (IFNULL(mp.price,0) + IFNULL(sp.price,0)) / cv.value
                ELSE 0
            END as fixed_conversion,

            ROUND(
                agg.delivery_qty *
                (
                    CASE
                        WHEN IFNULL(cv.value,0) > 0
                        THEN (IFNULL(mp.price,0) + IFNULL(sp.price,0)) / cv.value
                        ELSE 0
                    END
                ),2
            ) as conversion,

            ROUND(
                agg.delivery_qty *
                (IFNULL(mp.price,0) + IFNULL(sp.price,0)),2
            ) as grand_total
        ");

    $data = $query->get();

    // =======================
    // SUMMARY
    // =======================

    $summary = [
        'total_rows'        => $data->count(),
        'total_customers'   => $data->pluck('customer')->unique()->count(),
        'total_qty'         => $data->sum('delivery_qty'),
        'total_conversion'  => $data->sum('conversion'),
        'total_grand_total' => $data->sum('grand_total'),
    ];

    return response()->json([
        'data'    => $data,
        'summary' => $summary
    ]);
}

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'year'              => 'required|digits:4',
        'month'             => 'required|integer|min:1|max:12',
        'note'              => 'nullable|string',
        'total_qty'         => 'required|numeric|min:0',
        'total_conversion'  => 'required|numeric|min:0',
        'estimated_profit'  => 'required|numeric|min:0',
        'details'           => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {

        $year  = $request->year;
        $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);

        $last = Conversion::where('year', $year)
                    ->where('month', $request->month)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

        $sequence = $last ? ((int) substr($last->conversion_number, -4) + 1) : 1;
        $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $conversionNumber = "MKTC{$year}{$month}{$sequence}";

        $conversion = Conversion::create([
            'conversion_number' => $conversionNumber,
            'year'              => $year,
            'month'             => $request->month,
            'status'            => 'Draft',
            'total_qty'         => $request->total_qty,
            'total_conversion'  => $request->total_conversion,
            'estimated_profit'  => $request->estimated_profit,
            'created_by'        => Auth::id(),
            'note'              => $request->notes ?? '',
        ]);

        // Decode details
        $details = json_decode($request->details, true);

        foreach ($details as $item) {
            DB::table('conversion_details')->insert([
                'conversion_id'   => $conversion->id,
                'article_code'    => $item['article_code'],
                'delivery_qty'    => $item['delivery_qty'],
                'material_price'  => $item['material_price'],
                'service_price'   => $item['service_price'],
                'total_price'     => $item['price'],
                'conversion_value'=> $item['conversion_value'],
                'fixed_conversion'=> $item['fixed_conversion'],
                'conversion'      => $item['conversion'],
                'grand_total'     => $item['grand_total'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Conversion saved successfully.'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to save conversion.',
            'error'   => $e->getMessage()
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
        
        return view('marketing.price');
    }

     
    public function getPricingData(Request $request)
{
    $conversion = DB::table('conversion_values')
        ->where('effective_date','<=',date('Y-m-d'))
        ->orderByDesc('effective_date')
        ->value('value');

$query = DB::table('articles as a')
    ->selectRaw("
        a.article_code,
        a.description,
        c.name as supplier_name,

        avg_rm.average_raw_material_price,
        sj.selling_price,

        CASE
            WHEN ? > 0
            THEN avg_rm.average_raw_material_price / ?
            ELSE NULL
        END as rm_conversion,

        CASE
            WHEN ? > 0
            THEN sj.selling_price / ?
            ELSE NULL
        END as fg_conversion,

        CASE
            WHEN ? > 0
            THEN (sj.selling_price / ?) - (avg_rm.average_raw_material_price / ?)
            ELSE NULL
        END as matome
    ", [
        $conversion, $conversion,
        $conversion, $conversion,
        $conversion, $conversion, $conversion,
    ])

    ->leftJoin('customers as c', 'c.code', '=', 'a.supplier_code')

    /*
    |--------------------------------------------------------------------------
    | AVERAGE RAW MATERIAL PRICE
    | INNER JOIN avg_rm supaya artikel yang tidak ada di boms otomatis excluded
    |--------------------------------------------------------------------------
    */
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
                        @prev_price    := price,
                        @prev_article  := article_code
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

    /*
    |--------------------------------------------------------------------------
    | SELLING PRICE
    |--------------------------------------------------------------------------
    */
    ->leftJoin(DB::raw("
        (
            SELECT
                article_code,
                SUM(price + service_price) as selling_price
            FROM sj_temporary
            GROUP BY article_code
        ) sj
    "), 'sj.article_code', '=', 'a.article_code')

    ->whereRaw("a.article_type = 'FG'")
    ->whereRaw("a.status = 'active'");

    return datatables()->of($query)

        /*
        |--------------------------------------------------------------------------
        | AVERAGE RAW MATERIAL PRICE
        |--------------------------------------------------------------------------
        */
        ->editColumn('average_raw_material_price', function ($row) {
            if ($row->average_raw_material_price === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->average_raw_material_price, 0, ',', '.')
                . '</div>';
        })

        /*
        |--------------------------------------------------------------------------
        | SELLING PRICE
        |--------------------------------------------------------------------------
        */
        ->editColumn('selling_price', function ($row) {
            if ($row->selling_price === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->selling_price, 0, ',', '.')
                . '</div>';
        })

        /*
        |--------------------------------------------------------------------------
        | RM CONVERSION
        |--------------------------------------------------------------------------
        */
        ->editColumn('rm_conversion', function ($row) {
            if ($row->rm_conversion === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->rm_conversion, 2, ',', '.')
                . '</div>';
        })

        /*
        |--------------------------------------------------------------------------
        | FG CONVERSION
        |--------------------------------------------------------------------------
        */
        ->editColumn('fg_conversion', function ($row) {
            if ($row->fg_conversion === null) {
                return '<div class="text-end">-</div>';
            }
            return '<div class="text-end">'
                . number_format($row->fg_conversion, 2, ',', '.')
                . '</div>';
        })

        /*
        |--------------------------------------------------------------------------
        | MATOME (fg_conversion - rm_conversion)
        |--------------------------------------------------------------------------
        */
        ->editColumn('matome', function ($row) {
            if ($row->matome === null) {
                return '<div class="text-end">-</div>';
            }

            $class = $row->matome >= 0 ? 'text-green-600' : 'text-red-600';

            return '<div class="text-end font-bold ' . $class . '">'
                . number_format($row->matome, 2, ',', '.')
                . '</div>';
        })

        ->rawColumns([
            'average_raw_material_price',
            'selling_price',
            'rm_conversion',
            'fg_conversion',
            'matome',
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

public function storeBasicPrice(Request $request)
{
    $request->validate([
        'article_code'   => 'required|exists:articles,article_code',
        'effective_date' => 'required|date',
        'material_price' => 'nullable|numeric|min:0',
        'service_price'  => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {

        $now = now();

        // ======================
        // MATERIAL PRICE
        // ======================
        if($request->material_price !== null &&
           $request->material_price !== ''){

            DB::table('basic_prices')->insert([
                'article_code'  => $request->article_code,
                'price_type'    => 'MATERIAL',
                'price'         => $request->material_price,
                'effective_date'=> $request->effective_date,
                'created_by'        => Auth::id(),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        // ======================
        // SERVICE PRICE
        // ======================
        if($request->service_price !== null &&
           $request->service_price !== ''){

            DB::table('basic_prices')->insert([
                'article_code'  => $request->article_code,
                'price_type'    => 'SERVICE',
                'price'         => $request->service_price,
                'effective_date'=> $request->effective_date,
                'created_by'        => Auth::id(),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        DB::commit();

        return response()->json([
            'success'=>true,
            'message'=>'Basic price successfully created'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ],500);
    }
}

}
