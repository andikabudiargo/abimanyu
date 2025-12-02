<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Rats\Zkteco\Lib\ZKTeco;
use Carbon\Carbon;
use App\Helpers\WebSocketHelper;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SecurityController extends Controller
{
    public function index()
    {
        return view('security');
    }

 public function getDataCatering(Request $request)
{
    $request->validate([
        'date'  => 'required|date',
        'start' => 'required',
        'end'   => 'required'
    ]);

    $date  = $request->date;
    $start = $request->start;
    $end   = $request->end;

  $raw = DB::table('finger_logs as f_in')
    ->leftJoin('employees', 'employees.nik', '=', 'f_in.nik')
    ->leftJoin('finger_logs as f_out', function($join) use ($date) {
        $join->on('f_out.nik', '=', 'f_in.nik')
             ->where('f_out.status', 1)
             ->whereDate('f_out.timestamp', $date);
    })
    ->select(
        DB::raw("
            CASE 
                WHEN employees.id IS NULL 
                    THEN 'Nama Belum Terdaftar di Abimanyulive'
                ELSE employees.name
            END AS name
        "),
        DB::raw("COALESCE(employees.nik, f_in.nik) as nik"),
        'f_in.timestamp as in_timestamp',
        DB::raw('TIME(f_in.timestamp) as in_time'),
        'f_out.timestamp as out_timestamp',
        DB::raw('TIME(f_out.timestamp) as out_time')
    )
    ->where('f_in.status', 0)
    ->whereDate('f_in.timestamp', $date)
    ->whereTime('f_in.timestamp', '>=', $start)
    ->whereTime('f_in.timestamp', '<=', $end)

    // ======== FILTER PENTING ========
    // Tampilkan jika:
    // 1. Employee tidak ditemukan (NULL), atau
    // 2. eat = 1
    ->where(function($q) {
        $q->whereNull('employees.id')      // nik tidak ada → tampil
          ->orWhere('employees.eat', 1);   // eat=1 → tampil
    })
    // =================================

    ->orderBy('f_in.timestamp', 'asc')
    ->get();




    // -----------------------------------------
    //  FILTER DUPLIKAT — KURANG DARI 1 JAM
    // -----------------------------------------
    $filtered = [];
    $lastPunch = [];

    foreach ($raw as $row) {

        $nik = $row->nik;
        $current = strtotime($row->in_timestamp);

        if (!isset($lastPunch[$nik])) {
            // NIK pertama kali muncul
            $filtered[] = $row;
            $lastPunch[$nik] = $current;
            continue;
        }

        $diff = $current - $lastPunch[$nik];

        if ($diff >= 3600) { // 3600 detik = 1 jam
            $filtered[] = $row;
            $lastPunch[$nik] = $current;
        }
    }

    return response()->json(array_values($filtered));
}




public function broadcastNewCatering(Request $request)
{
    WebSocketHelper::push('new_catering', $request->all());
    return response()->json(['status' => 'ok']);
}


}
