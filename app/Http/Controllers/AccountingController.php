<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountingController extends Controller
{
   public function ppn()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('accounting.ppn');
    }

    public function bbm()
    {
        // Mengembalikan view resources/views/accounting/bbm.blade.php
        return view('accounting.bbm');
    }
}
