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
     * Department dan position disimpan lewat pivot, marital status dipetakan dari form
     * (Lajang/Menikah) ke kolom marriage_status (0/1), sedangkan field tanpa kolom tujuan diabaikan.
     *
     * @param  \Illuminate\Http\Request  $request  Request berisi data form employee.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rulesEmployee());

        [$data, $departmentId, $positionId] = $this->mapEmployeeData($validated);

        $employee = Employee::create($data);

        if ($departmentId) {
            $employee->departments()->attach($departmentId);
        }

        if ($positionId) {
            $employee->positions()->attach($positionId);
        }

        return redirect()->route('hr.employee.index')->with('success', 'Data employee berhasil disimpan.');
    }

    /**
     * Tampilkan halaman detail employee lengkap dengan relasi department, position, dan distribusi APD.
     *
     * @param  \App\Models\Employee  $employee  Employee yang ditampilkan (route model binding).
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Employee $employee)
    {
        $employee = Employee::with(['departments', 'positions', 'distributions'])->findOrFail($employee->id);

        return view('hr.detail-employee', compact('employee'));
    }

    /**
     * Tampilkan form edit employee; view yang sama dengan create, input sudah terisi data employee.
     *
     * @param  \App\Models\Employee  $employee  Employee yang akan diedit (route model binding).
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Employee $employee)
    {
        return view('hr.create-employee', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'positions' => Position::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Perbarui data employee dari form edit.
     * Pemetaan form ke kolom database sama dengan store(), department & position di-sync
     * (bukan attach) agar bisa diganti.
     *
     * @param  \Illuminate\Http\Request  $request  Request berisi data form employee.
     * @param  \App\Models\Employee  $employee  Employee yang diperbarui.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->rulesEmployee($employee));

        [$data, $departmentId, $positionId] = $this->mapEmployeeData($validated);

        $employee->update($data);

        $employee->departments()->sync($departmentId ? [$departmentId] : []);
        $employee->positions()->sync($positionId ? [$positionId] : []);

        return redirect()->route('hr.employee.index')->with('success', 'Data employee berhasil diperbarui.');
    }

    /**
     * Hapus employee. Tabel pivot department & position di-detach dulu karena tanpa FK
     * (relasi lain seperti apd_distribution_items/remote_access/users dibiarkan orphan).
     *
     * @param  \App\Models\Employee  $employee  Employee yang dihapus.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Employee $employee)
    {
        $employee->departments()->detach();
        $employee->positions()->detach();
        $employee->delete();

        return redirect()->route('hr.employee.index')->with('success', 'Data employee berhasil dihapus.');
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

    /**
     * Aturan validasi 24 field form employee, satu sumber kebenaran untuk store & update.
     * Saat update ($employee diisi), unique pada nik mengabaikan id employee yang sedang diedit
     * supaya nik yang tidak diubah tidak dianggap duplikat.
     *
     * @param  \App\Models\Employee|null  $employee  Employee yang sedang diedit (opsional).
     * @return array
     */
    private function rulesEmployee(?Employee $employee = null): array
    {
        return [
            'name' => 'required|string|max:100',
            'nik' => 'required|integer|unique:employees,nik' . ($employee ? ',' . $employee->id : ''),
            'employee_type' => 'required|string|max:50',
            'gender' => 'nullable|in:Male,Female',
            'marital_status' => 'required|in:Lajang,Menikah',
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
        ];
    }

    /**
     * Petakan data form valid ke kolom database untuk create/update.
     * Form memakai Male/Female dan Lajang/Menikah, kolom tujuan enum gender ('Laki-Laki','Perempuan')
     * dan marriage_status (0/1); field pivot di-unset dan dikembalikan [$data, $departmentId, $positionId].
     *
     * @param  array  $validated  Data form hasil validasi.
     * @return array
     */
    private function mapEmployeeData(array $validated): array
    {
        $departmentId = $validated['department'] ?? null;
        $positionId = $validated['position_id'] ?? null;

        // Form memakai nilai Male/Female, kolom gender di database bertipe enum('Laki-Laki','Perempuan').
        if ($validated['gender'] !== null) {
            $validated['gender'] = ['Male' => 'Laki-Laki', 'Female' => 'Perempuan'][$validated['gender']];
        }

        // Form mengirim Lajang/Menikah, kolom marriage_status bertipe boolean (0/1).
        $validated['marriage_status'] = $validated['marital_status'] === 'Menikah' ? 1 : 0;
        unset($validated['department'], $validated['position_id'], $validated['marital_status']);

        return [$validated, $departmentId, $positionId];
    }
}
