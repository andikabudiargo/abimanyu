<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Supplier;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
     public function index()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('marketing.customer');
    }

     public function create()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('marketing.create-customer');
    }
}