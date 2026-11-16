<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return response()->json([
        'app' => 'AntriSehat - Sistem Antrean & Janji Temu Klinik (K05)',
        'status' => 'running'
    ]);
});

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'healthy',
            'service' => 'AntriSehat API',
            'database' => 'connected',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'service' => 'AntriSehat API',
            'database' => 'disconnected',
            'error' => $e->getMessage(),
        ], 503);
    }
});
