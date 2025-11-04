<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Armada;
use App\Models\Bbm;

class CalculatorFuelController extends Controller
{
    public function index()
{
    // Join armada dengan tabel bbms
    $armadas = \DB::table('armada')
        ->join('bbms', 'armada.bbm_id', '=', 'bbms.id')
        ->select(
            'armada.id',
            'armada.nama_armada',
            'armada.icon',
            'armada.rasio',
            'armada.spare',
            'bbms.nama_bbm as nama_bbm',
            'bbms.harga_bbm as harga_bbm'
        )
        ->get();

    // Kalau kamu masih butuh semua data BBM juga
    $bbms = \DB::table('bbms')->get();

    return view('accounting.bbm', compact('armadas', 'bbms'));
}

     public function storeBBM(Request $request)
    {
        $validated = $request->validate([
            'nama_bbm' => 'required|string|max:100|unique:bbms,nama_bbm',
            'harga_bbm' => 'required|numeric|min:0',
        ]);

        Bbm::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data BBM berhasil disimpan.'
        ]);
    }

    /**
     * Store data Armada
     */
    public function storeArmada(Request $request)
    {
        $validated = $request->validate([
            'nama_armada' => 'required|string|max:100|unique:armada,nama_armada',
            'bbm_id' => 'required|exists:bbms,id',
            'spare' => 'required|numeric|min:0',
            'rasio' => 'required|string',
            'icon' => 'required|string|max:50',
        ]);

        Armada::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Armada berhasil disimpan.'
        ]);
    }
}
