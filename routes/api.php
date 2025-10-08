<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

Route::post('/scanner/store', function (Request $request) {
    $code = $request->input('code');
    Cache::put('last_scanned_code', $code, now()->addSeconds(5));
    return response()->json(['status' => 'ok']);
});

Route::get('/scanner/latest', function () {
    return response()->json(['code' => Cache::pull('last_scanned_code')]);
});

Route::get('/scanner/ping', function () {
    return response()->json(['pong' => true]);
});

Route::post('/scanner/reset', function () {
    Cache::forget('last_scanned_code');
    return response()->json(['status' => 'reset']);
});

