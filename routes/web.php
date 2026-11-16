<?php

use App\Http\Controllers\AuthController;
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

// 1. PORTAL PASIEN (Publik Tanpa Login)
Route::get('/', [KlinikController::class, 'patientPortal']);
Route::post('/queues', [KlinikController::class, 'takeQueue']);
Route::post('/appointments', [KlinikController::class, 'bookAppointment']);

// 2. AUTENTIKASI STAF (Dokter & Operator)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// 3. DASHBOARD DOKTER (Wajib Login Role: dokter)
Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter', [KlinikController::class, 'doctorDashboard']);
    Route::post('/queues/call-next', [KlinikController::class, 'callNextQueue']);
    Route::post('/queues/{id}/status', [KlinikController::class, 'updateQueueStatus']);
});

// 4. DASHBOARD OPERATOR / ADMIN (Wajib Login Role: operator)
Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/operator', [KlinikController::class, 'operatorDashboard']);
    Route::post('/doctors', [KlinikController::class, 'storeDoctor']);
    Route::delete('/doctors/{id}', [KlinikController::class, 'deleteDoctor']);
});

// 5. REST API ROUTES (Untuk Automated Testing & Integrasi)
Route::prefix('api')->group(function () {
    Route::get('/doctors', [KlinikController::class, 'listDoctors']);
    Route::post('/doctors', [KlinikController::class, 'storeDoctor']);
    Route::delete('/doctors/{id}', [KlinikController::class, 'deleteDoctor']);
    Route::get('/queues/running', [KlinikController::class, 'runningQueues']);
    Route::post('/queues', [KlinikController::class, 'takeQueue']);
    Route::post('/queues/call-next', [KlinikController::class, 'callNextQueue']);
    Route::post('/appointments', [KlinikController::class, 'bookAppointment']);
});
