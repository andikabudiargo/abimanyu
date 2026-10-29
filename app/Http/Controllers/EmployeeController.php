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
     * Tampilkan halaman list employees. Data tabel dimuat lewat endpoint data()
     * (DataTables server-side); departments & employeeTypes hanya untuk opsi filter.
     */
    public function index()
    {
        return view('hr.employee', [
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'employeeTypes' => Employee::query()
                ->select('employee_type')
                ->whereNotNull('employee_type')
                ->distinct()
                ->orderBy('employee_type')
                ->pluck('employee_type'),
        ]);
    }

    /**
     * Sumber data list employee untuk DataTables server-side (search + filter).
     *
     * @param  \Illuminate\Http\Request  $request  Parameter DataTables + filter form (search_text, employee_type, department_id).
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $query = Employee::query()
            ->select(['id', 'nik', 'name', 'employee_type', 'status'])
            ->when($request->filled('search_text'), function ($q) use ($request) {
                $text = $request->search_text;
                $q->where(fn($q2) => $q2->where('name', 'like', "%{$text}%")->orWhere('nik', 'like', "%{$text}%"));
            })
            ->when($request->filled('employee_type'), fn($q) => $q->where('employee_type', $request->employee_type))
            // Filter department wajib whereHas (bukan join): pivot department_employee bisa berisi
            // lebih dari satu department per employee sehingga join menduplikasi baris.
            ->when($request->filled('department_id'), fn($q) => $q->whereHas('departments',
                fn($q2) => $q2->where('departments.id', $request->department_id)));

        // Tidak ada orderBy statis di sini: urutan default (name asc) diatur view lewat
        // DataTables `order`, agar klik header kolom bisa mengganti asc/desc.

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $detailUrl = route('hr.employee.show', $row->id);
                $editUrl = route('hr.employee.edit', $row->id);
                $deleteUrl = route('hr.employee.destroy', $row->id);

                // Markup aksi disamakan dengan view hr.employee (ikon feather + teks, spoofing DELETE + confirm native).
                return '<a href="' . $detailUrl . '" class="text-blue-600 hover:underline" title="Detail"><i data-feather="eye" class="w-4 h-4 inline-block align-middle"></i> Detail</a>'
                    . ' <a href="' . $editUrl . '" class="text-blue-600 hover:underline ml-3" title="Edit"><i data-feather="edit-2" class="w-4 h-4 inline-block align-middle"></i> Edit</a>'
                    . ' <form action="' . $deleteUrl . '" method="POST" class="inline ml-3" onsubmit="return confirm(\'Yakin ingin hapus employee ini?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="text-red-600 hover:underline" title="Hapus"><i data-feather="trash-2" class="w-4 h-4 inline-block align-middle"></i> Hapus</button>'
                    . '</form>';
            })
            ->rawColumns(['action'])
            ->make(true);
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
