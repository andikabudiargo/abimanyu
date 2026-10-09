<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeController extends Controller
{
    /**
     * Tampilkan dashboard data employees lengkap dengan relasi department dan position.
     */
    public function index()
    {
        $employees = Employee::with(['departments', 'positions'])
            ->orderBy('name')
            ->get();

        return view('hr.employee', compact('employees'));
    }

    /**
     * Tampilkan form tambah employee beserta data department dan position untuk dropdown.
     */
    public function create()
    {
        return view('hr.create-employee', [
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'positions' => Position::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Tampilkan form kontrak employee.
     * Tidak ada view kontrak terpisah, form kontrak berada di tab Job Information pada hr.create-employee.
     */
    public function createContract()
    {
        return $this->create();
    }

    /**
     * Simpan data employee baru dari form.
     * Department dan position disimpan lewat pivot, sedangkan field tanpa kolom tujuan diabaikan.
     *
     * @param  \Illuminate\Http\Request  $request  Request berisi data form employee.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nik' => 'required|integer|unique:employees,nik',
            'employee_type' => 'required|string|max:50',
            'gender' => 'nullable|in:Male,Female',
            'religion' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'last_education' => 'nullable|string|max:50',
            'dependents' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'ktp_number' => 'nullable|string|max:30',
            'kk_number' => 'nullable|string|max:30',
            'bpjs_tk' => 'nullable|string|max:30',
            'bpjs_ks' => 'nullable|string|max:30',
            'npwp_number' => 'nullable|string|max:30',
            'bank_number' => 'nullable|string|max:20',
            'bank_account_name' => 'nullable|string|max:100',
            'join_date' => 'nullable|date',
            'department' => 'nullable|integer|exists:departments,id',
            'position_id' => 'nullable|integer|exists:job_positions,id',
        ]);

        $departmentId = $validated['department'] ?? null;
        $positionId = $validated['position_id'] ?? null;

        // Form memakai nilai Male/Female, kolom gender di database bertipe enum('Laki-Laki','Perempuan').
        if ($validated['gender'] !== null) {
            $validated['gender'] = ['Male' => 'Laki-Laki', 'Female' => 'Perempuan'][$validated['gender']];
        }
        unset($validated['department'], $validated['position_id']);

        $employee = Employee::create($validated);

        if ($departmentId) {
            $employee->departments()->attach($departmentId);
        }

        if ($positionId) {
            $employee->positions()->attach($positionId);
        }

        return redirect()->route('hr.employee.index')->with('success', 'Data employee berhasil disimpan.');
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
