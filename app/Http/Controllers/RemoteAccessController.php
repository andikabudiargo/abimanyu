<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemoteAccess;

class RemoteAccessController extends Controller
{
     public function index() {
         $clients = RemoteAccess::with(['employee'])
    ->get();
        return view('it.remote',compact('clients'));
    }

}
