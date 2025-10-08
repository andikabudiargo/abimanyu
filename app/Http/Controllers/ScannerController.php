<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function store(Request $request)
    {
        $barcode = $request->barcode;

        // 🔍 Cari artikel berdasarkan kode
        $article = DB::table('articles')->where('code', $barcode)->first();

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        // 📝 Kirim respon balik (atau simpan ke session / Redis / logika lainnya)
        return response()->json([
            'message' => 'Artikel ditemukan',
            'data' => $article
        ]);
    }
}
