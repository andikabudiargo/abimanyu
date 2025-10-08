<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ITProjectController extends Controller
{
     public function index() {
        return view('it.project');
    }

    public function create() {
        return view('it.create-project');
    }
}
