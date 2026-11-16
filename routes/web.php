<?php

use App\Http\Controllers\KlinikController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Endpoint wajib /health
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

// Web UI Routes
Route::get('/', [KlinikController::class, 'index']);
Route::post('/doctors', [KlinikController::class, 'storeDoctor']);
Route::delete('/doctors/{id}', [KlinikController::class, 'deleteDoctor']);
Route::post('/queues', [KlinikController::class, 'takeQueue']);
Route::post('/queues/call-next', [KlinikController::class, 'callNextQueue']);
Route::post('/appointments', [KlinikController::class, 'bookAppointment']);

// REST API Routes (Untuk integrasi & automated testing)
Route::prefix('api')->group(function () {
    Route::get('/doctors', [KlinikController::class, 'listDoctors']);
    Route::post('/doctors', [KlinikController::class, 'storeDoctor']);
    Route::delete('/doctors/{id}', [KlinikController::class, 'deleteDoctor']);
    Route::get('/queues/running', [KlinikController::class, 'runningQueues']);
    Route::post('/queues', [KlinikController::class, 'takeQueue']);
    Route::post('/queues/call-next', [KlinikController::class, 'callNextQueue']);
    Route::post('/appointments', [KlinikController::class, 'bookAppointment']);
});
