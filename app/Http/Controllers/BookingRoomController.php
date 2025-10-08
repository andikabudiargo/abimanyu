<?php

namespace App\Http\Controllers;

use App\Models\Room;
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

class BookingRoomController extends Controller
{
    public function index()
    {
        return view('facility.booking-room');
    }

     public function history()
    {
        return view('facility.booking-room-history');
    }

   public function schedule(Request $request)
{
    $date = $request->input('date', now()->toDateString());
    $rooms = Room::all();

    // jam 08:00 - 17:00
    $times = [];
    for ($hour = 8; $hour < 18; $hour++) {
        $start = sprintf("%02d:00", $hour);
        $end   = sprintf("%02d:00", $hour + 1);
        $times[] = "$start - $end";
    }

    $schedule = [];

    $dayOfWeek = \Carbon\Carbon::parse($date)->format('N'); // 1 = Senin, ..., 5 = Jumat

    foreach ($times as $time) {
        [$start, $end] = explode(" - ", $time);

        foreach ($rooms as $room) {

            

            // cek booking normal
            $booking = BookingRoom::with('creator')
                ->where('room_id', $room->id)
                ->where('booking_date', $date)
                ->where('status', '!=', 'Cancelled') // ignore cancelled
                ->where(function($q) use ($start, $end) {
                    $q->where(function($qb) use ($start, $end) {
                        $qb->where('start_time', '<', $end)
                           ->where('end_time', '>', $start);
                    });
                })
                ->first();

            if ($booking) {
                $schedule[$time][$room->name] = [
                    'status'      => $booking->status,
                    'booked_by'   => $booking->creator->name,
                    'purpose'     => $booking->purpose,
                    'description' => $booking->description,
                ];
            } else {
                $schedule[$time][$room->name] = [
                    'status' => 'Available'
                ];
            }
        }
    }

    return response()->json([
        'date'     => $date,
        'rooms'    => $rooms,
        'schedule' => $schedule
    ]);
}

public function getUserBookings()
{
    $user = Auth::user();

    // Jika Admin GA atau bagian General Affair
    if (
        $user->roles->pluck('name')->contains('Admin GA')
    ) {
        // Ambil semua booking
        $bookings = BookingRoom::with('room','creator.departments','creator.roles')
        ->where('status', '!=', 'Cancelled')
            ->orderBy('booking_date','asc')
            ->orderBy('start_time','asc')
            ->get();
    } else {
        // Hanya booking sesuai aturan user
        $bookings = BookingRoom::with('room','creator.departments','creator.roles')
         ->where('status', '!=', 'Cancelled')
         ->where('created_by', $user->id)
         ->orderBy('booking_date','asc')
         ->orderBy('start_time','asc')
         ->get();
    }

    return response()->json($bookings);
}


    /**
     * Cancel booking → ubah status room menjadi available
     */
   public function cancelBooking(Request $request, $id)
{
    $user = Auth::user();

    $booking = BookingRoom::with('room', 'creator')
        ->where(function($q) use ($user) {
            if (
                $user->roles->pluck('name')->contains('Admin GA') || 
                $user->departments->pluck('name')->contains('General Affair')
            ) {
                // Admin GA → bisa ambil semua
            } else {
                // User biasa → hanya booking miliknya / departemennya
                $q->where('created_by', $user->id)
                  ->orWhereHas('creator.departments', function($q2) use ($user) {
                      $q2->whereIn('departments.id', $user->departments->pluck('id'));
                  })
                  ->orWhereHas('creator.roles', function($q3){
                      $q3->whereIn('name',['Supervisor Special Access','Manager Special Access']);
                  });
            }
        })
        ->findOrFail($id);

    // === Jika Admin GA yang cancel ===
    if ($user->roles->pluck('name')->contains('Admin GA')) {
        $cancelReason = $request->input('cancel_reason');

        $booking->update([
            'status'       => 'Cancelled',
            'cancel_reason'=> $cancelReason,
            'cancel_by' => $user->id,
            'cancel_at' => now(),
        ]);

        if (!empty($booking->creator->email)) {
    Mail::to($booking->creator->email)
        ->send(new BookingRoomCancelled($booking));
}

        return response()->json([
            'message' => 'Booking has been cancelled by Admin GA.'
        ]);
    }

    // === Jika user sendiri yang cancel ===
    if ($booking->created_by == $user->id) {
        $booking->update([
            'status'       => 'Cancelled',
            'cancel_by' => $user->id,
            'cancel_at' => now(),
        ]);

        return response()->json([
            'message' => 'Booking successfully cancelled.'
        ]);
    }

    return response()->json(['message' => 'Tidak diizinkan membatalkan booking ini'], 403);
}




    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'purpose'      => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'room_id'      => 'required|exists:rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = BookingRoom::create([
            'booking_date' => $request->booking_date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'purpose'      => $request->purpose,
            'description'  => $request->description,
            'room_id'      => $request->room_id,
            'status'       => 'Waiting Approval',
            'created_by'   => Auth::id(),
        ]);

        $bookingEmail = [
    'admin.generalaffair@asnusantara.co.id',
    'it2@asnusantara.co.id'
];

Mail::to($bookingEmail)->send(new BookingRoomRequest($booking));


       return response()->json([
    'success' => true,
    'message' => 'Room Succesfully Booked and Waiting for Approval',
    'data'    => $booking
]);

    }

     public function export(Request $request)
    {
        $dateRange = $request->input('date_range');

        // Flatpickr bisa hasilkan "2025-09-15 to 2025-09-20" atau 1 tanggal saja
        if (strpos($dateRange, " to ") !== false) {
            [$startDate, $endDate] = explode(" to ", $dateRange);
        } else {
            $startDate = $endDate = $dateRange;
        }

        // Ambil data dari database
        $bookings = BookingRoom::with(['room', 'creator'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

       // === Header Judul ===
$sheet->mergeCells("A1:G2");
$sheet->setCellValue("A1", "Data Penggunaan Ruangan Meeting ($startDate s/d $endDate)");

// Style untuk judul
$sheet->getStyle("A1")->getFont()
    ->setBold(true)
    ->setSize(14)
    ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); // teks putih

$sheet->getStyle("A1")->getAlignment()
    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

// Background warna hijau misalnya
$sheet->getStyle("A1")->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('4CAF50'); // hijau material design


        // === Header Kolom ===
        $sheet->setCellValue("A3", "No");
        $sheet->setCellValue("B3", "Tanggal");
        $sheet->setCellValue("C3", "Jam");
        $sheet->setCellValue("D3", "Ruangan");
        $sheet->setCellValue("E3", "User");
        $sheet->setCellValue("F3", "Purpose");
        $sheet->setCellValue("G3", "Description");
        
// Rata tengah untuk header kolom No, Tanggal, Jam
$sheet->getStyle("A3:C3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                                       ->setVertical(Alignment::VERTICAL_CENTER);

// Header lainnya tetap default atau bisa diatur sesuai kebutuhan
$sheet->getStyle("D3:G3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
                                       ->setVertical(Alignment::VERTICAL_CENTER);

// Buat font header tebal
$sheet->getStyle("A3:G3")->getFont()->setBold(true);

// Background hijau (material design)
$sheet->getStyle("A3:G3")->getFill()->setFillType(Fill::FILL_SOLID)
                                   ->getStartColor()->setARGB('4CAF50'); // hijau

                                   // Style untuk judul
$sheet->getStyle("A3:G3")->getFont()
    ->setBold(true)
    ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); // teks putih

        // === Isi Data ===
        $row = 4;
        foreach ($bookings as $i => $b) {
            $sheet->setCellValue("A$row", $i + 1);
            $sheet->setCellValue("B$row", $b->booking_date);
            $sheet->setCellValue("C$row", substr($b->start_time, 0, 5) . " - " . substr($b->end_time, 0, 5));
            $sheet->setCellValue("D$row", $b->room->name ?? "-");
            $sheet->setCellValue("E$row", $b->creator->name ?? "-");
            $sheet->setCellValue("F$row", $b->purpose);
            $sheet->setCellValue("G$row", $b->description);
            $row++;
        }

        // === Auto Size Kolom (setelah data selesai ditulis) ===
$highestColumn = $sheet->getHighestColumn();
foreach (range('A', $highestColumn) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// === Tambahkan Border untuk semua cell yang ada data ===
$highestRow = $sheet->getHighestRow(); // baris terakhir ada data
$sheet->getStyle("A3:{$highestColumn}{$highestRow}")->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
]);

        // === Export File ===
        $fileName = "Data_Booking_Ruangan_{$startDate}_to_{$endDate}.xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save("php://output");
        }, $fileName);
    }

   public function approve(Request $request)
{
    $booking = BookingRoom::findOrFail($request->id);

    if ($booking->status !== 'Waiting Approval') {
        return response()->json([
            'success' => false, 
            'message' => 'Booking sudah diproses.'
        ]);
    }

    $booking->update([
        'status'      => 'Booked',
        'approved_at' => now(),
        'approved_by' => auth()->id(),
    ]);

    if (!empty($booking->creator->email)) {
    Mail::to($booking->creator->email)
        ->send(new BookingRoomApproved($booking));
}
    return response()->json([
        'success' => true, 
        'message' => 'Booking successfully Approved.'
    ]);
}


  public function data(Request $request)
{
   $query = BookingRoom::with(['cancel', 'creator', 'approved', 'room'])
    ->orderByRaw("CASE WHEN status = 'Waiting Approval' THEN 0 ELSE 1 END")
    ->orderBy('booking_date', 'desc') // bisa diganti 'desc' kalau mau terbaru dulu
    ->orderBy('created_at', 'desc');

if ($request->filled('start_date') && $request->filled('end_date')) {
    $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
}


// Filter status
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// Filter room
if ($request->filled('room')) {
    $query->where('room_id', $request->room);
}
  

    return DataTables::of($query)
    ->addColumn('room_id', function ($row) {
    return $row->room ? $row->room->name : '-';
})
 ->addColumn('created_by', function ($row) {
    return $row->creator ? $row->creator->name : '-';
})
->addColumn('approved_by', function ($row) {
    return $row->approved ? $row->approved->name : '-';
})

->addColumn('cancel_by', function ($row) {
    return $row->cancel ? $row->cancel->name : '-';
})
->editColumn('status', function ($row) {

    $commonClasses = 'inline-block w-28 text-center text-gray-100 text-xs font-medium p-1 rounded-xl';

    if ($row->status === 'Waiting Approval') {
        return '<span class="bg-yellow-500 ' . $commonClasses . '">Waiting Approval</span>';
    } elseif ($row->status === 'Booked') {
        return '<span class="bg-green-500 ' . $commonClasses . '">Booked</span>';
    } elseif ($row->status === 'Cancelled') {
        return '<span class="bg-red-500 ' . $commonClasses . '">Cancelled</span>';
    }
})
 ->addColumn('time', function ($row) {
    $start = Carbon::parse($row->start_time)->format('H:i');
    $end   = Carbon::parse($row->end_time)->format('H:i');
    return $start . ' - ' . $end;
})

->addColumn('created_at', function ($row) {
    return $row->created_at 
        ? Carbon::parse($row->created_at)->format('Y-m-d H:i') 
        : '-';
})


->addColumn('approved_at', function ($row) {
    return $row->approved_at 
        ? Carbon::parse($row->approved_at)->format('Y-m-d H:i') 
        : '-';
})

->addColumn('cancel_at', function ($row) {
    return $row->cancel_at 
        ? Carbon::parse($row->cancel_at)->format('Y-m-d H:i') 
        : '-';
})


        ->rawColumns(['status'])
        ->make(true);
}


}
