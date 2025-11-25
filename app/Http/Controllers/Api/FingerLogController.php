<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;   // <-- WAJIB ADA
use App\Models\FingerLog;
use Illuminate\Support\Facades\Validator;

class FingerLogController extends Controller
{
    public function receive(Request $request)
    {
        // Validasi keamanan token
        $expectedToken = hash('sha256', 'rahasia_anda');
        if ($request->token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Validasi format data
        $validator = Validator::make($request->all(), [
            'attendances' => 'required|array',
            'attendances.*.machine'   => 'required|string',
            'attendances.*.uid'       => 'required|string',
            'attendances.*.timestamp' => 'required|date',
            'attendances.*.status'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $saved = 0;

        foreach ($request->attendances as $log) {

            // Generate hash (harus sama dengan yang dibuat model)
            $logHash = md5(
                $log['machine'] . '|' .
                $log['uid'] . '|' .
                $log['timestamp']
            );

            // Cek kalau data sudah pernah masuk
            $exists = FingerLog::where('log_hash', $logHash)->first();

            if ($exists) {
                continue; // skip duplikasi
            }

            FingerLog::create([
                'machine_id' => $log['machine'],
                'nik'        => $log['uid'],
                'status'     => $log['status'],
                'timestamp'  => $log['timestamp'],
                'raw_data'   => $log,
                'log_hash'   => $logHash, // model bisa generate otomatis, tapi kita pakai supaya konsisten
            ]);

            $saved++;
        }

        return response()->json([
            'success'  => true,
            'inserted' => $saved,
            'message'  => 'Logs saved successfully'
        ]);
    }
}
