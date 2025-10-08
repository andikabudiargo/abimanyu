<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Supplier;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('purchasing.supplier');
    }

     public function create()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('purchasing.create-supplier');
    }

    public function data(Request $request)
{
    $suppliers = Supplier::select([
        'id',
        'code',
        'name',
        'contact_person',
        'telephone',
        'mobile_phone',
        'fax',
        'address',
        'top',
        'pkp',
        'category',
        'join_date',
    ]);

    return datatables()->of($suppliers)
        ->editColumn('pkp', function ($row) {
            return $row->pkp ? 'Yes' : 'No';
        })
        ->addColumn('action', function ($row) {
            return '<button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">Edit</button>';
        })
        ->rawColumns(['action'])
        ->make(true);
}

    public function downloadExcelTemplate()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Kolom Wajib
    $sheet->setCellValue('A1', 'code');          // Harus diisi manual
    $sheet->setCellValue('B1', 'name');          // Wajib
    $sheet->setCellValue('C1', 'initial');       // Wajib
    $sheet->setCellValue('D1', 'category');      // Wajib (dropdown)

    // Kolom Opsional (bisa disesuaikan lagi jika perlu)
    $sheet->setCellValue('E1', 'join_date');
    $sheet->setCellValue('F1', 'as_customer');         // 1 / 0
    $sheet->setCellValue('G1', 'coa_hutang');
    $sheet->setCellValue('H1', 'coa_retur');
    $sheet->setCellValue('I1', 'address');
    $sheet->setCellValue('J1', 'provinsi');
    $sheet->setCellValue('K1', 'city');
    $sheet->setCellValue('L1', 'kecamatan');
    $sheet->setCellValue('M1', 'kelurahan');
    $sheet->setCellValue('N1', 'postal_code');
    $sheet->setCellValue('O1', 'contact_person');
    $sheet->setCellValue('P1', 'telephone');
    $sheet->setCellValue('Q1', 'mobile_phone');
    $sheet->setCellValue('R1', 'fax');
    $sheet->setCellValue('S1', 'email');
    $sheet->setCellValue('T1', 'top');                 // Term of Payment
    $sheet->setCellValue('U1', 'pkp');                 // 1 / 0
    $sheet->setCellValue('V1', 'npwp_number');
    $sheet->setCellValue('W1', 'npwp_name');
    $sheet->setCellValue('X1', 'npwp_address');
    $sheet->setCellValue('Y1', 'bank_type');
    $sheet->setCellValue('Z1', 'bank_name');
    $sheet->setCellValue('AA1', 'branch');
    $sheet->setCellValue('AB1', 'account_bank_name');
    $sheet->setCellValue('AC1', 'account_bank_number');

    // (Optional) Tambahkan dropdown validasi kategori
    $validation = $sheet->getCell('D2')->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $validation->setAllowBlank(false);
    $validation->setShowInputMessage(true);
    $validation->setShowErrorMessage(true);
    $validation->setShowDropDown(true);
    $validation->setFormula1('"Raw Material,Chemical,Consumable,Other"');

    $filename = 'template_supplier.xlsx';
    $writer = new Xlsx($spreadsheet);
    $temp_file = tempnam(sys_get_temp_dir(), $filename);
    $writer->save($temp_file);

    return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
}

public function store(Request $request)
{
   $request->validate([
        'supplier_name'       => 'required|string|max:255',
        'initial'             => 'required|string|max:10',
        'category'            => 'nullable|string|max:50',
        'join_date'           => 'nullable|date',
        'coa_hutang'          => 'nullable|string|max:50',
        'coa_retur'           => 'nullable|string|max:50',
        'address'             => 'nullable|string|max:500',
        'provinsi'            => 'nullable|string|max:100',
        'city'                => 'nullable|string|max:100',
        'kecamatan'           => 'nullable|string|max:100',
        'kelurahan'           => 'nullable|string|max:100',
        'postal_code'         => 'nullable|string|max:20',
        'contact_person'      => 'nullable|string|max:100',
        'telephone'           => 'nullable|string|max:30',
        'mobile_phone'        => 'nullable|string|max:30',
        'fax'                 => 'nullable|string|max:30',
        'email'               => 'nullable|email|max:100',
        'top'                 => 'nullable|integer|min:0',
        'pkp'                 => 'nullable|boolean',
        'npwp_number'         => 'nullable|string|max:30',
        'npwp_name'           => 'nullable|string|max:255',
        'npwp_address'        => 'nullable|string|max:255',
        'bank_type'           => 'nullable|string|max:50',
        'bank_name'           => 'nullable|string|max:100',
        'branch'              => 'nullable|string|max:100',
        'account_bank_name'   => 'nullable|string|max:100',
        'account_bank_number' => 'nullable|string|max:50',
        'as_customer'         => 'nullable|boolean',
    ]);

    // 🔢 Hitung kode urut berdasarkan initial
    $latest = Supplier::where('initial', $request->initial)
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->first();

    $lastNumber = 0;
    if ($latest && preg_match('/\d+/', $latest->code, $matches)) {
        $lastNumber = (int) $matches[0];
    }

    $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    $generatedCode = strtoupper($request->initial) . $nextNumber . 'SUPP';

    // ✅ Simpan ke database
    $supplier = new Supplier();
    $supplier->code = $generatedCode;
    $supplier->name = $request->supplier_name;
    $supplier->initial = strtoupper($request->initial);
    $supplier->category = $request->category;
    $supplier->as_customer = $request->has('as_customer');
    $supplier->join_date = $request->join_date;
    $supplier->coa_hutang = $request->coa_hutang;
    $supplier->coa_retur = $request->coa_retur;
    $supplier->address = $request->address;
    $supplier->provinsi = $request->provinsi;
    $supplier->city = $request->city;
    $supplier->kecamatan = $request->kecamatan;
    $supplier->kelurahan = $request->kelurahan;
    $supplier->postal_code = $request->postal_code;
    $supplier->contact_person = $request->contact_person;
    $supplier->telephone = $request->telephone;
    $supplier->mobile_phone = $request->mobile_phone;
    $supplier->fax = $request->fax;
    $supplier->email = $request->email;
    $supplier->top = $request->top;
    $supplier->pkp = $request->has('pkp');
    $supplier->npwp_number = $request->npwp_number;
    $supplier->npwp_name = $request->npwp_name;
    $supplier->npwp_address = $request->npwp_address;
    $supplier->bank_type = $request->bank_type;
    $supplier->bank_name = $request->bank_name;
    $supplier->branch = $request->branch;
    $supplier->account_bank_name = $request->account_bank_name;
    $supplier->account_bank_number = $request->account_bank_number;

    $supplier->save();

    return redirect()->back()->with('success', 'Supplier berhasil disimpan.');
}

public function import(Request $request)
{
    $request->validate(['csv_file' => 'required|file|mimes:xlsx']);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('csv_file'));
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $errors = [];

    foreach ($data as $i => $row) {
        if ($i == 1) continue; // Skip header

        $validator = Validator::make($row, [
            'A' => 'required', // code
            'B' => 'required', // name
        ]);

        if ($validator->fails()) {
            $errors[] = "Row $i: " . implode(', ', $validator->errors()->all());
            continue;
        }

        Supplier::create([
            'code'                  => $row['A'],
            'name'                  => $row['B'],
            'initial'               => $row['C'],
            'category'              => $row['D'],
            'join_date'             => $row['E'] ?? null,
            'as_customer'           => isset($row['F']) ? (bool)$row['F'] : false,
            'coa_hutang'            => $row['G'] ?? null,
            'coa_retur'             => $row['H'] ?? null,
            'address'               => $row['I'] ?? null,
            'provinsi'              => $row['J'] ?? null,
            'city'                  => $row['K'] ?? null,
            'kecamatan'             => $row['L'] ?? null,
            'kelurahan'             => $row['M'] ?? null,
            'postal_code'           => $row['N'] ?? null,
            'contact_person'        => $row['O'] ?? null,
            'telephone'             => $row['P'] ?? null,
            'mobile_phone'          => $row['Q'] ?? null,
            'fax'                   => $row['R'] ?? null,
            'email'                 => $row['S'] ?? null,
            'top'                   => $row['T'] ?? null,
            'pkp'                   => isset($row['U']) ? (bool)$row['U'] : false,
            'npwp_number'           => $row['V'] ?? null,
            'npwp_name'             => $row['W'] ?? null,
            'npwp_address'          => $row['X'] ?? null,
            'bank_type'             => $row['Y'] ?? null,
            'bank_name'             => $row['Z'] ?? null,
            'branch'                => $row['AA'] ?? null,
            'account_bank_name'     => $row['AB'] ?? null,
            'account_bank_number'   => $row['AC'] ?? null,
        ]);
    }

    if (!empty($errors)) {
        return redirect()->back()->with('error', implode("\n", $errors));
    }

    return redirect()->back()->with('success', 'Supplier imported successfully.');
}

public function select()
{
    $suppliers = Supplier::select('id', 'code', 'name')->get();
    return response()->json($suppliers);
}

}
