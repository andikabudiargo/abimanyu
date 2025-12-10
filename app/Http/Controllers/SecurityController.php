<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Rats\Zkteco\Lib\ZKTeco;
use Carbon\Carbon;
use App\Helpers\WebSocketHelper;
use App\Models\Employee;
use App\Models\SecurityGood;
use App\Models\SecurityGoodItem;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SecurityController extends Controller
{
    public function index()
    {
        return view('security');
    }

  public function getDataBarang()
{
    $data = SecurityGood::with('items')
        ->orderBy('tanggal', 'DESC') // TANGGAL TERBARU DI ATAS
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
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

public function storeBarang(Request $r)
    {
        DB::beginTransaction();
        try {
// Simpan foto surat jalan
$fotoSuratJalan = null;

if ($r->hasFile('surat_jalan')) {

    // Tentukan lokasi folder tujuan (path absolut server)
    $destinationPath = '/home/abimany3/public_html/surat_jalan';

    // Ambil nama file asli
    $fileName = time() . '_' . $r->file('surat_jalan')->getClientOriginalName();

    // Pindahkan file ke folder tujuan
    $r->file('surat_jalan')->move($destinationPath, $fileName);

    // Simpan hanya nama file ke database
    $fotoSuratJalan = $fileName;
}
            // Simpan data utama
            $barang = SecurityGood::create([
                'jenis_barang'      => $r->jenis_barang,
                'tanggal'    => $r->tanggal,
                'jam_masuk'    => $r->jam_masuk,
                'jam_keluar'        => $r->jam_keluar,
                'perusahaan'   => $r->perusahaan,
                'identitas'   => $r->identitas,
                'nama_pengirim'     => $r->nama_pengirim,
                'nomor_kendaraan'      => $r->nomor_kendaraan,
                'nama_penerima'     => $r->nama_penerima,
                'surat_jalan'  => $fotoSuratJalan,
            ]);

            // Simpan item daftar barang
            if ($r->nama_barang) {
                foreach ($r->nama_barang as $i => $nama) {

                  $fotoItem = null;

if ($r->hasFile("foto.$i")) {

    $file = $r->file("foto.$i");

    // Nama file unik
    $filename = time() . '_' . $file->getClientOriginalName();

    // Path tujuan
    $destinationPath = '/home/abimany3/public_html/barang_item';

    // Pindahkan file
    $file->move($destinationPath, $filename);

    // Simpan hanya nama file
    $fotoItem = $filename;
}


                    SecurityGoodItem::create([
                        'security_good_id' => $barang->id, // <-- wajib
                        'nama_barang' => $nama,
                        'jumlah' => $r->jumlah[$i],
                        'foto' => $fotoItem,
                        'kondisi' => $r->kondisi[$i],
                        'catatan' => $r->catatan[$i] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil disimpan!',
                 'id' => $barang->id // → penting!
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

