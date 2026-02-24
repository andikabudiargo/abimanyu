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
use Intervention\Image\Facades\Image;
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
        'start_datetime' => 'required|date',
        'end_datetime'   => 'required|date|after_or_equal:start_datetime',
    ]);

    $start = $request->start_datetime;
    $end   = $request->end_datetime;

    $raw = DB::table('finger_logs as f_in')
        ->leftJoin('employees', 'employees.nik', '=', 'f_in.nik')
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

            // ===================================
            // OUT = LOG DARI MESIN KELUAR
            // ===================================
            DB::raw("(
                SELECT fo.timestamp
                FROM finger_logs fo
                WHERE fo.nik = f_in.nik
                  AND fo.machine_id = '192.168.0.202'
                  AND fo.timestamp > f_in.timestamp
                ORDER BY fo.timestamp ASC
                LIMIT 1
            ) as out_timestamp"),

            DB::raw("TIME((
                SELECT fo.timestamp
                FROM finger_logs fo
                WHERE fo.nik = f_in.nik
                  AND fo.machine_id = '192.168.0.202'
                  AND fo.timestamp > f_in.timestamp
                ORDER BY fo.timestamp ASC
                LIMIT 1
            )) as out_time")
        )

        // ===================================
        // IN = MESIN MASUK
        // ===================================
        ->where('f_in.machine_id', '192.168.0.201')

        // ===================================
        // RANGE DATETIME
        // ===================================
        ->whereBetween('f_in.timestamp', [$start, $end])

        // ===================================
        // FILTER KARYAWAN MAKAN
        // ===================================
        ->where(function ($q) {
            $q->whereNull('employees.id')
              ->orWhere('employees.eat', 1);
        })

        ->orderBy('f_in.timestamp', 'asc')
        ->get();

    // -----------------------------------------
    // FILTER DUPLIKAT & NOISE ABSEN
    // -----------------------------------------
    $filtered = [];
    $lastValid = [];

    foreach ($raw as $row) {

        $nik = $row->nik;
        $current = strtotime($row->in_timestamp);

        if (!isset($lastValid[$nik])) {
            $filtered[] = $row;
            $lastValid[$nik] = $row;
            continue;
        }

        $lastTime = strtotime($lastValid[$nik]->in_timestamp);
        $diff = $current - $lastTime;

        // ❌ Abaikan IN terlalu dekat (< 1 jam)
        if ($diff < 3600) {
            continue;
        }

        $filtered[] = $row;
        $lastValid[$nik] = $row;
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

    
    // Kompres tanpa resize
    $r->save($destinationPath.'/'.$fileName, 70);

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

