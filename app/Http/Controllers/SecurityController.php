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

    // Ambil data mentah jam masuk (status 0)
    $raw = DB::table('finger_logs as f_in')
        ->join('employees', 'employees.nik', '=', 'f_in.nik')
        ->leftJoin('finger_logs as f_out', function($join) use ($date) {
            $join->on('f_out.nik', '=', 'f_in.nik')
                 ->where('f_out.status', 1)
                 ->whereDate('f_out.timestamp', $date);
        })
        ->select(
            'employees.name',
            'employees.nik',
            'f_in.timestamp as in_timestamp',
            DB::raw('TIME(f_in.timestamp) as in_time'),
            'f_out.timestamp as out_timestamp',
            DB::raw('TIME(f_out.timestamp) as out_time')
        )
        ->where('f_in.status', 0)
        ->where(function($query) {
            $query->where('employees.position_id', '!=', 13)
                  ->orWhereNull('employees.position_id');
        })
        ->whereDate('f_in.timestamp', $date)
        ->whereTime('f_in.timestamp', '>=', $start)
        ->whereTime('f_in.timestamp', '<=', $end)
        ->orderBy('f_in.timestamp', 'asc')
        ->get();

    // -----------------------------
    //  HAPUS DUPLIKAT < 15 MENIT
    // -----------------------------
    $filtered = [];
    $lastPunch = []; // menyimpan timestamp terakhir per NIK

    foreach ($raw as $row) {

        $nik = $row->nik;
        $current = strtotime($row->in_timestamp);

        if (!isset($lastPunch[$nik])) {
            $filtered[] = $row;
            $lastPunch[$nik] = $current;
            continue;
        }

        $diff = $current - $lastPunch[$nik];

        if ($diff >= 900) { // 900 detik = 15 menit
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
