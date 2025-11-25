<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FingerLog;
use Illuminate\Support\Facades\Validator;

class FingerLogController extends Controller
{
    public function receive(Request $request)
    {
        // Validasi token
        $expectedToken = hash('sha256', 'rahasia_anda');
        if ($request->token !== $expectedToken) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Validasi data
        $validator = Validator::make($request->all(), [
            'attendances'            => 'required|array',
            'attendances.*.machine'   => 'required|string',
            'attendances.*.uid'       => 'required|string',
            'attendances.*.timestamp' => 'required|date',
            'attendances.*.verify'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $saved = 0;

        foreach ($request->attendances as $log) {
            $logHash = md5($log['machine'] . '|' . $log['uid'] . '|' . $log['timestamp']);

            // Skip duplikasi
            if (FingerLog::where('log_hash', $logHash)->exists()) {
                continue;
            }

            FingerLog::create([
                'machine_id' => $log['machine'],
                'nik'        => $log['uid'],
                'status'     => $log['verify'] ?? 0,
                'timestamp'  => $log['timestamp'],
                'raw_data'   => $log,
                'log_hash'   => $logHash,
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
