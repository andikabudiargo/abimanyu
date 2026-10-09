<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    /**
     * Tampilkan dashboard data employees.
     * View hr.employee masih statis sehingga belum membutuhkan variabel apa pun.
     */
    public function index()
    {
        return view('hr.employee');
    }

    /**
     * Tampilkan form tambah employee beserta tab kontrak, training, dan APD.
     * View hr.create-employee memuat seluruh input secara statis tanpa data dinamis.
     */
    public function create()
    {
        return view('hr.create-employee');
    }

    /**
     * Tampilkan form kontrak employee.
     * Tidak ada view kontrak terpisah, form kontrak berada di tab Job Information pada hr.create-employee.
     */
    public function createContract()
    {
        return view('hr.create-employee');
    }

    /**
     * Ambil daftar section untuk department tertentu dalam bentuk JSON.
     * Tabel sections belum tersedia pada database, maka endpoint mengembalikan array kosong.
     *
     * @param  string  $department  Kode atau id department yang menjadi filter section.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSectionByDepartment($department)
    {
        if (! Schema::hasTable('sections')) {
            return response()->json([]);
        }

        $sections = DB::table('sections')
            ->where('department_id', $department)
            ->get();

        return response()->json($sections);
    }
}
