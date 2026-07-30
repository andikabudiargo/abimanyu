<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Defect;

class DefectController extends Controller
{
     public function index() {
        return view('qc.master-defect');
    }

       public function indexv2() {
        return view('qc.master-defect-v2');
    }

     public function store(Request $request)
    {
        $request->validate([
            'defect'          => 'required|string|max:255',
            'raw_material'    => 'nullable|boolean',
            'description'     => 'nullable|string|max:255',
            'inspection_post' => 'nullable|in:Incoming,Unloading,Buffing,Touch Up,Final,Outgoing',
            'category'        => 'nullable|in:NG,NC,Both',
            'status'          => 'nullable|in:active,inactive'
        ]);

      $code = $this->generateDefectCode($request->category, $request->inspection_post);


        $defect = Defect::create([
        'code'               => $code,
        'defect'             => $request->defect,
        'raw_material'       => $request->has('raw_material'), // fix: pastikan masuk ke DB
        'description'        => $request->description,
        'inspection_post'    => $request->inspection_post,
        'status'             => $request->status ?? 'active',
        'category'           => $request->category,
        ]);

        return response()->json([
            'message' => 'Defect created successfully',
            'data'    => $defect
        ], 201);
    }

      public function storev2(Request $request)
    {
        $request->validate([
            'defect'          => 'required|string|max:255',
            'description'     => 'nullable|string|max:255',
            'inspection_post' => 'nullable|in:Incoming,Unloading,Buffing,Touch Up,Final,Outgoing',
            'status'          => 'nullable|in:active,inactive'
        ]);

      $code = $this->generateDefectCodeV2($request->inspection_post);


        $defect = Defect::create([
        'code'               => $code,
        'defect'             => $request->defect,
        'description'        => $request->description,
        'inspection_post'    => $request->inspection_post,
        'status'             => $request->status ?? 'active',
        ]);

        return response()->json([
            'message' => 'Defect created successfully',
            'data'    => $defect
        ], 201);
    }

    private function generateDefectCode($category, $inspectionPost)
{
    $postMap = [
        'Incoming'  => 'I',
        'Unloading' => 'U',
        'Buffing'   => 'B',
        'Touch Up'  => 'T',
        'Final'     => 'F',
        'Outgoing'     => 'O',
    ];

    $categoryCode = strtoupper(substr($category, 0, 2)); // NG, NC, BO
    $postCode = $postMap[$inspectionPost] ?? 'X'; // fallback jika tidak dikenali

    $prefix = $categoryCode . $postCode;

    $count = Defect::where('code', 'LIKE', $prefix . '%')->count();
    $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return $prefix . $number; // tanpa tanda "-"
}

  private function generateDefectCodeV2($inspectionPost)
{
    $postMap = [
        'Incoming'  => 'I',
        'Unloading' => 'U',
        'Buffing'   => 'B',
        'Touch Up'  => 'T',
        'Final'     => 'F',
        'Outgoing'     => 'O',
    ];

    $postCode = $postMap[$inspectionPost] ?? 'X'; // fallback jika tidak dikenali

    $prefix = $postCode;

    $count = Defect::where('code', 'LIKE', $prefix . '%')->count();
    $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return $prefix . $number; // tanpa tanda "-"
}


public function data(Request $request)
{
    $query = Defect::query();

    return datatables()->of($query)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
         ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })
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
          <a href="" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-500 hover:text-gray-300"><i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete</button>
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
->editColumn('raw_material', function ($row) {
    if ($row->raw_material === true) {
        return '<span class="text-green-500 text-xs font-medium px-2.5 py-0.5 rounded">Yes</span>';
    } elseif ($row->raw_material === false) {
        return '<span class="text-red-500 text-xs font-medium px-2.5 py-0.5 rounded">No</span>';
    }
    return $row->raw_material;
})
        ->rawColumns(['action','status','raw_material'])
        ->make(true);
}

public function datav2(Request $request)
{
    $query = Defect::query()
        ->whereNull('category'); // hanya ambil yang category kosong / null

    return datatables()->of($query)
        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
        ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })
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
                  <a href="" class="block px-4 py-2 hover:bg-gray-100">
                    <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
                  </a>
                  <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-500 hover:text-gray-300">
                      <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete
                    </button>
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

        ->editColumn('raw_material', function ($row) {
            if ($row->raw_material === true) {
                return '<span class="text-green-500 text-xs font-medium px-2.5 py-0.5 rounded">Yes</span>';
            } elseif ($row->raw_material === false) {
                return '<span class="text-red-500 text-xs font-medium px-2.5 py-0.5 rounded">No</span>';
            }

            return $row->raw_material;
        })

        ->rawColumns(['action', 'status', 'raw_material'])
        ->make(true);
}

public function getByInspectionPost($post)
{
    $defects = \App\Models\Defect::whereRaw(
            'LOWER(inspection_post) = ?',
            [strtolower($post)]
        )
        ->whereNull('category')
        ->select('id', 'defect', 'category')
        ->orderBy('defect', 'ASC')
        ->get();

    return response()->json($defects);
}








}
