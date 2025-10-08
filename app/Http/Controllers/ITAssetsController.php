<?php

namespace App\Http\Controllers;

use App\Models\ITAsset;
use App\Models\BookingRoom;
use App\Models\CancelBookingRoom;
use App\Mail\BookingRoomRequest;
use App\Mail\BookingRoomApproved;
use App\Mail\BookingRoomCancelled;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ITAssetsController extends Controller
{
    public function index()
    {
        return view('it.assets');
    }

     public function create()
    {
        return view('it.create-assets');
    }

    
  public function data(Request $request)
{
   $query = ITAsset::query()
    ->orderByRaw("
        CASE 
            WHEN status = 'Loaned' THEN 0
            WHEN status = 'Available' THEN 1
            WHEN status = 'Lost' THEN 2
            WHEN status = 'Disposed' THEN 3
            ELSE 4
        END
    ")
    ->orderByRaw("
        CASE
            WHEN conditions = 'Good' THEN 0
            WHEN conditions = 'Broken but still usable' THEN 1
            WHEN conditions = 'Damaged and can\\'t be used' THEN 2
            ELSE 3
        END
    ")
    ->get();


  

    return DataTables::of($query)
     ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;

    $user = Auth::user();
    $userRoles = $user->roles->pluck('name');
    $userDepartments = $user->departments->pluck('name');
    $isOwner = $row->request_by === Auth::id();
    $detail_url = route('it.ticket.show', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $edit_url = route('it.ticket.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row
    $ticketNumber = $row->ticket_number ?? 'Unknown';


$actionButtons = '
<div class="relative inline-block text-left">
  <button type="button"
    data-dropdown-id="' . $dropdownId . '"
    onclick="toggleDropdown(\'' . $dropdownId . '\', event)"
    class="inline-flex justify-center w-full rounded-md shadow-sm px-2 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
    <i data-feather=\'align-justify\'></i>
  </button>
  <div id="' . $dropdownId . '" class="dropdown-menu hidden absolute right-0 mt-2 z-50 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 text-sm text-gray-700">';


 $actionButtons .= '
            <a href="' . $detail_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>Detail
            </a>
            <a href="' . $edit_url . '" class="block px-4 py-2 hover:bg-gray-100">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>Edit
            </a>
             <button type="button" onclick="confirmDelete(' . $id . ')" class="block w-full text-left px-4 py-2 hover:bg-red-100 text-red-700">
        <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i> Delete
    </button>';

    $actionButtons .= '</div></div></div>';

    return $actionButtons;
})

   ->addColumn('asset_number', function ($row) {
    $photoUrl = $row->photo 
        ? asset($row->photo) 
        : asset('images/no-image.png'); // fallback jika tidak ada foto

    return '
        <div class="flex items-center space-x-3 px-2 py-1">
            <div class="relative w-14 aspect-square">
                <img src="'.$photoUrl.'" 
                    alt="Foto Aset"
                    class="w-full h-full p-2 rounded-xl object-cover border border-gray-200 shadow-md transition-transform duration-300 hover:scale-105 hover:shadow-lg">
                <div class="absolute inset-0 rounded-xl bg-gradient-to-tr from-gray-100 to-transparent opacity-20"></div>
            </div>
            <div class="leading-tight">
             <span class="block text-gray-400 text-xs">Asset Number</span>
                <span class="block text-gray-800 font-semibold text-sm">'.$row->asset_number.'</span>
            </div>
        </div>
    ';
})


// Hitung umur aset dari purchase_date sampai sekarang
// Hitung umur aset dari purchase_date sampai sekarang
->addColumn('lifetime', function ($row) {
    if (!$row->purchase_date) {
        return '-';
    }

    $purchaseDate = Carbon::parse($row->purchase_date);
    $now = Carbon::now();

    // Hitung selisih total hari
    $days = $purchaseDate->diffInDays($now);

    // Kalau kurang dari 30 hari → tampilkan hari saja
    if ($days < 30) {
        return (int)$days . ' day' . ($days != 1 ? 's' : '');
    }

    // Hitung selisih tahun dan bulan (integer saja)
    $years = (int)$purchaseDate->diffInYears($now);
    $months = (int)$purchaseDate->copy()->addYears($years)->diffInMonths($now);

    $age = '';
    if ($years > 0) {
        $age .= $years . ' year' . ($years > 1 ? 's ' : ' ');
    }

    if ($months > 0) {
        $age .= $months . ' month' . ($months > 1 ? 's' : '');
    }

    return trim($age);
})

->addColumn('warranty', function ($row) {
    if (!$row->purchase_date || !$row->warranty) {
        return '-';
    }

    $purchaseDate = Carbon::parse($row->purchase_date);
    $now = Carbon::now();

    // Hitung tanggal garansi berakhir
    $warrantyEnd = $purchaseDate->copy()->addMonths($row->warranty);

    // Jika garansi sudah habis
    if ($now->greaterThan($warrantyEnd)) {
        return '<span class="text-red-500 font-semibold">Expired</span>';
    }

    // Hitung sisa waktu garansi
    $remainingDays = $now->diffInDays($warrantyEnd);

    // Kalau kurang dari 30 hari → tampilkan hari
    if ($remainingDays < 30) {
        return '<span class="text-green-500 font-semibold">' . 
            $remainingDays . ' day' . ($remainingDays != 1 ? 's' : '') . ' remaining</span>';
    }

    // Jika lebih dari 30 hari → tampilkan dalam bulan
    $remainingMonths = floor($remainingDays / 30);
    return '<span class="text-green-500 font-semibold">' . 
        $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '') . ' remaining</span>';
})

->addColumn('conditions', function ($row) {
    $condition = strtolower($row->conditions ?? '');

    switch ($condition) {
        case 'good':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9h14l-2-9M10 21a1 1 0 11-2 0 1 1 0 012 0zm8 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>';
            $bgColor = 'bg-green-100';
            $textColor = 'text-green-700';
            break;
        case 'broken but still usable':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            $bgColor = 'bg-yellow-100';
            $textColor = 'text-yellow-700';
            break;
        case 'damaged and can\'t be used':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" /></svg>';
            $bgColor = 'bg-red-100';
            $textColor = 'text-red-700';
            break;
        default:
            $icon = '';
            $bgColor = 'bg-gray-100';
            $textColor = 'text-gray-700';
    }

    // Tambahkan min-width & text-center supaya badge semua sama besar
    return '<span class="inline-flex items-center justify-center px-3 py-1 rounded-full font-semibold text-xs '.$bgColor.' '.$textColor.' min-w-[80px]">
                '.$icon.'
                <span>'.ucfirst($row->conditions).'</span>
            </span>';
})


 // Tambahkan kolom berwarna untuk inspection_post
    ->editColumn('status', function ($row) {
        $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

        return match ($row->status) {
            'Loaned' => '<span class="bg-orange-500 '.$commonClasses.'">Loaned</span>',
            'Available' => '<span class="bg-green-500 '.$commonClasses.'">Available</span>',
            'Lost' => '<span class="bg-red-500 '.$commonClasses.'">Lost</span>',
            'Disposal' => '<span class="bg-yellow-500 '.$commonClasses.'">Disposal</span>',
            default => '-'
        };
    })

->addColumn('assigned_to', function ($row) {
    if (strtolower($row->status) === 'loaned') {
        // Ambil record peminjaman aktif dari AssetLoan
        $loan = \App\Models\AssetLoan::where('asset_id', $row->id)
                    ->whereNull('date_return') // null artinya masih dipinjam
                    ->latest()
                    ->first();

        if ($loan && $loan->user) { // jika ada peminjaman aktif
            return $loan->user->name;
        }
    }

    // Jika tidak sedang dipinjam, ambil assigned_to dari ITAsset
    return $row->user ? ($row->user->name ?? '-') : '-';
})

->addColumn('supplier_id', function ($row) {
    return $row->supplier ? ($row->supplier->name ?? '-') : '-';
})

->addColumn('location', function ($row) {
    $icon = '<i data-feather="home" class="w-3 h-3 mr-2"></i>';
    return '<span class="flex items-center">'.$icon.'<span>'.($row->location ?? '-').'</span></span>';
})
->addColumn('asset_type', function ($row) {
    $icon = '<i data-feather="tag" class="w-3 h-3 mr-2"></i>';
    return '<span class="flex items-center">'.$icon.'<span>'.($row->asset_type ?? '-').'</span></span>';
})

->addColumn('created_at', function ($row) {
    return $row->created_at 
        ? \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') 
        : '-';
})
->addColumn('updated_at', function ($row) {
    return $row->updated_at 
        ? \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d H:i') 
        : '-';
})





        // Pastikan kolom ini tidak di-escape HTML (karena ada <img>)
        ->rawColumns(['action','asset_number','warranty','conditions', 'status','assigned_to','location','asset_type'])
        ->make(true);
}

    public function store(Request $request)
    {
        $request->validate([
            'asset_name'     => 'required|string|max:255',
            'asset_type'     => 'required|string|max:100',
            'acquistion_type'  => 'required|string|max:50',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'purchase_date'  => 'nullable|date',
            'warranty'       => 'nullable|integer|min:0',
            'assignment_type'=> 'required|string',
            'conditions'      => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:51200', // 50MB
            'note'          => 'nullable|string', // 50MB
        ]);

        $data = $request->all();

        // Upload photo jika ada
      if ($request->hasFile('photo')) {
    $file = $request->file('photo');
    $filename = time().'_'.$file->getClientOriginalName();
    $file->move(public_path('uploads/assets'), $filename);
    $data['photo'] = 'uploads/assets/'.$filename;
}


        $asset = ITAsset::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully',
            'data'    => $asset
        ]);
    }
}