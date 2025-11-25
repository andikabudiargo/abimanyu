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

    // Ambil data mentah
    $raw = DB::table('finger_logs')
        ->join('employees', 'employees.nik', '=', 'finger_logs.nik')
        ->select(
            'employees.name',
            'employees.nik',
            'finger_logs.timestamp',
            DB::raw('TIME(finger_logs.timestamp) as time'),
            'finger_logs.status'
        )
        ->where('finger_logs.status', 0)
        ->where(function($query) {
    $query->where('employees.position_id', '!=', 13)
          ->orWhereNull('employees.position_id');
})
        ->whereDate('finger_logs.timestamp', $date)
        ->whereTime('finger_logs.timestamp', '>=', $start)
        ->whereTime('finger_logs.timestamp', '<=', $end)
        ->orderBy('finger_logs.timestamp', 'asc')
        ->get();

    // -----------------------------
    //  HAPUS DUPLIKAT < 30 DETIK
    // -----------------------------
    $filtered = [];
    $lastPunch = [];   // menyimpan timestamp terakhir per NIK

    foreach ($raw as $row) {

        $nik = $row->nik;
        $current = strtotime($row->timestamp);

        // Jika belum ada punch sebelumnya untuk NIK tersebut → ambil
        if (!isset($lastPunch[$nik])) {
            $filtered[] = $row;
            $lastPunch[$nik] = $current;
            continue;
        }

        // Hitung selisih detik
        $diff = $current - $lastPunch[$nik];

        // Hilangkan duplikat jika selisih < 30 detik
        if ($diff >= 900) {
            $filtered[] = $row;
            $lastPunch[$nik] = $current; // update punch terakhir
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
