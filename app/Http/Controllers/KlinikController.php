<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KlinikController extends Controller
{
    // 1. PORTAL PASIEN (PUBLIK TANPA LOGIN)
    public function patientPortal()
    {
        $today = now()->toDateString();
        $doctors = Doctor::withCount([
            'queues as today_queue_count' => function ($query) use ($today) {
                $query->whereDate('queue_date', $today)->where('status', '!=', 'RESET');
            }
        ])->get();

        $currentlyCalled = Queue::whereDate('queue_date', $today)
            ->where('status', 'CALLED')
            ->get()
            ->keyBy('poli');

        return view('patient.index', compact('doctors', 'currentlyCalled', 'today'));
    }

    // 2. DASHBOARD DOKTER (PRIBADI PER DOKTER YANG LOGIN)
    public function doctorDashboard()
    {
        $today = now()->toDateString();
        $user = Auth::user();
        $doctor = $user->doctor;

        $doctorQueues = Queue::with('doctor')
            ->whereDate('queue_date', $today)
            ->where('status', '!=', 'RESET')
            ->when($doctor, fn($q) => $q->where('doctor_id', $doctor->id))
            ->orderByRaw("CASE status WHEN 'CALLED' THEN 1 WHEN 'WAITING' THEN 2 WHEN 'DONE' THEN 3 WHEN 'SKIPPED' THEN 4 ELSE 5 END")
            ->orderBy('sequence_num', 'asc')
            ->get();

        $appointments = Appointment::with('doctor')
            ->when($doctor, fn($q) => $q->where('doctor_id', $doctor->id))
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $stats = [
            'waiting' => $doctorQueues->where('status', 'WAITING')->count(),
            'called' => $doctorQueues->where('status', 'CALLED')->count(),
            'done' => $doctorQueues->where('status', 'DONE')->count(),
            'quota' => $doctor ? $doctor->max_quota : 30,
        ];

        return view('doctor.dashboard', compact('doctor', 'doctorQueues', 'appointments', 'stats', 'today'));
    }

    // 3. DASHBOARD OPERATOR / ADMIN (WAJIB LOGIN ROLE: OPERATOR)
    public function operatorDashboard()
    {
        $today = now()->toDateString();
        $doctors = Doctor::with('user')->withCount([
            'queues as today_queue_count' => function ($query) use ($today) {
                $query->whereDate('queue_date', $today)->where('status', '!=', 'RESET');
            }
        ])->get();

        $activeQueues = Queue::with('doctor')
            ->whereDate('queue_date', $today)
            ->whereIn('status', ['WAITING', 'CALLED'])
            ->orderBy('sequence_num', 'asc')
            ->get();

        $calledQueues = $activeQueues->where('status', 'CALLED')->values();
        $waitingQueues = $activeQueues->where('status', 'WAITING')->values();

        $stats = [
            'total_today' => Queue::whereDate('queue_date', $today)->where('status', '!=', 'RESET')->count(),
            'waiting' => $waitingQueues->count(),
            'called' => $calledQueues->count(),
            'done' => Queue::whereDate('queue_date', $today)->where('status', 'DONE')->count(),
            'skipped' => Queue::whereDate('queue_date', $today)->where('status', 'SKIPPED')->count(),
            'appointments_today' => Appointment::whereDate('appointment_date', $today)->where('status', 'SCHEDULED')->count(),
        ];

        $dbHealthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbHealthy = false;
        }

        return view('operator.dashboard', compact(
            'doctors',
            'calledQueues',
            'waitingQueues',
            'stats',
            'today',
            'dbHealthy'
        ));
    }

    // =========================================================
    // CRUD DOKTER, JADWAL & AKUN LOGIN DOKTER OTOMATIS
    // =========================================================
    public function listDoctors()
    {
        return response()->json(['data' => Doctor::all()], 200);
    }

    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'poli' => 'required|string|max:50',
            'poli_prefix' => 'required|string|max:5',
            'practice_day' => 'required|string|max:20',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_quota' => 'nullable|integer|min:1|max:30',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $validated['max_quota'] = $validated['max_quota'] ?? 30;
        $doctor = Doctor::create($validated);

        // Otomatis buatkan akun login pribadi untuk dokter baru
        $slug = Str::slug(preg_replace('/^(dr\.|drg\.)\s*/i', '', $doctor->name), '.');
        $email = $validated['email'] ?? ($slug . '@antrisehat.id');
        if (User::where('email', $email)->exists()) {
            $email = $slug . '.' . $doctor->id . '@antrisehat.id';
        }

        User::create([
            'name' => $doctor->name,
            'email' => $email,
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
            'doctor_id' => $doctor->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dokter berhasil ditambahkan', 'data' => $doctor], 201);
        }
        return redirect('/operator')->with('success', "Dokter {$doctor->name} ditambahkan. Akun login: {$email} (sandi: dokter123).");
    }

    public function updateDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'poli' => 'required|string|max:50',
            'poli_prefix' => 'required|string|max:5',
            'practice_day' => 'required|string|max:20',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_quota' => 'nullable|integer|min:1|max:30',
        ]);

        $validated['max_quota'] = $validated['max_quota'] ?? $doctor->max_quota;
        $doctor->update($validated);

        if ($doctor->user) {
            $doctor->user->update(['name' => $doctor->name]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Data dokter berhasil diperbarui', 'data' => $doctor], 200);
        }
        return redirect('/operator')->with('success', "Data {$doctor->name} ({$doctor->poli}) berhasil diperbarui.");
    }

    public function deleteDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctorName = $doctor->name;
        if ($doctor->user) {
            $doctor->user->delete();
        }
        $doctor->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dokter berhasil dihapus'], 200);
        }
        return redirect('/operator')->with('success', "Data dan akun login {$doctorName} berhasil dihapus.");
    }

    // =========================================================
    // ANTREAN HARIAN (BR-01 & BR-03)
    // =========================================================
    public function takeQueue(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:100',
            'queue_date' => 'nullable|date',
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $date = $validated['queue_date'] ?? now()->toDateString();
        $quotaLimit = min(30, (int) $doctor->max_quota);

        $activeSessionCount = Queue::where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $date)
            ->where('status', '!=', 'RESET')
            ->count();

        if ($activeSessionCount >= $quotaLimit) {
            $msg = "Kuota sesi dokter penuh (maksimal {$quotaLimit} pasien per sesi).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        $lastSequence = Queue::where('poli', $doctor->poli)
            ->whereDate('queue_date', $date)
            ->where('status', '!=', 'RESET')
            ->max('sequence_num') ?? 0;

        $nextSequence = $lastSequence + 1;
        $formattedNumber = strtoupper($doctor->poli_prefix) . '-' . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'doctor_id' => $doctor->id,
            'poli' => $doctor->poli,
            'queue_number' => $formattedNumber,
            'sequence_num' => $nextSequence,
            'patient_name' => $validated['patient_name'],
            'queue_date' => $date,
            'status' => 'WAITING',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Nomor antrean berhasil diterbitkan', 'data' => $queue], 201);
        }

        return redirect('/')->with([
            'success' => "Nomor antrean {$formattedNumber} berhasil diterbitkan.",
            'issued_ticket' => [
                'queue_number' => $queue->queue_number,
                'patient_name' => $queue->patient_name,
                'poli' => $doctor->poli,
                'doctor_name' => $doctor->name,
                'queue_date' => $date,
            ]
        ]);
    }

    // Panggil Antrean Berikutnya (Terisolasi per Dokter saat Login Web)
    public function callNextQueue(Request $request)
    {
        $validated = $request->validate([
            'poli' => 'required|string',
            'queue_date' => 'nullable|date',
        ]);

        $date = $validated['queue_date'] ?? now()->toDateString();
        $authDoctorId = Auth::check() && Auth::user()->role === 'dokter' ? Auth::user()->doctor_id : null;

        Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->when($authDoctorId, fn($q) => $q->where('doctor_id', $authDoctorId))
            ->where('status', 'CALLED')
            ->update(['status' => 'DONE']);

        $next = Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->when($authDoctorId, fn($q) => $q->where('doctor_id', $authDoctorId))
            ->where('status', 'WAITING')
            ->orderBy('sequence_num', 'asc')
            ->first();

        if (!$next) {
            $msg = "Tidak ada antrean menunggu untuk sesi Anda saat ini.";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 404)
                : redirect('/dokter')->with('error', $msg);
        }

        $next->update(['status' => 'CALLED']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Memanggil antrean berikutnya', 'data' => $next], 200);
        }
        return redirect('/dokter')->with('success', "Memanggil nomor antrean {$next->queue_number} — {$next->patient_name}");
    }

    // Update Status Antrean (Hanya untuk pasien milik dokter yang login)
    public function updateQueueStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:CALLED,DONE,SKIPPED',
        ]);

        $queue = Queue::findOrFail($id);

        if (Auth::check() && Auth::user()->role === 'dokter' && Auth::user()->doctor_id !== $queue->doctor_id) {
            abort(403, 'Anda tidak memiliki akses ke antrean dokter lain.');
        }

        if ($validated['status'] === 'CALLED') {
            Queue::where('doctor_id', $queue->doctor_id)
                ->whereDate('queue_date', $queue->queue_date)
                ->where('status', 'CALLED')
                ->update(['status' => 'DONE']);
        }

        $queue->update(['status' => $validated['status']]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Status antrean diperbarui', 'data' => $queue], 200);
        }
        return redirect('/dokter')->with('success', "Status antrean {$queue->queue_number} diperbarui menjadi {$validated['status']}.");
    }

    // Tampilan Antrean Berjalan (JSON API)
    public function runningQueues(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $queues = Queue::with('doctor')
            ->whereDate('queue_date', $date)
            ->whereIn('status', ['WAITING', 'CALLED'])
            ->orderBy('poli', 'asc')
            ->orderBy('sequence_num', 'asc')
            ->get();

        return response()->json(['date' => $date, 'data' => $queues], 200);
    }

    // =========================================================
    // JANJI TEMU DOKTER (BR-02 & BR-03)
    // =========================================================
    public function bookAppointment(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:100',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $dayName = Carbon::parse($validated['appointment_date'])->format('l');
        $quotaLimit = min(30, (int) $doctor->max_quota);

        if (strcasecmp($dayName, $doctor->practice_day) !== 0) {
            $msg = "Dokter {$doctor->name} hanya praktik pada hari {$doctor->practice_day} (tanggal dipilih: {$dayName}).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        $reqTime = Carbon::parse($validated['appointment_time'])->format('H:i:s');
        if ($reqTime < $doctor->start_time || $reqTime >= $doctor->end_time) {
            $msg = "Jam janji temu di luar jadwal praktik dokter ({$doctor->start_time} - {$doctor->end_time}).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        $isConflict = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $reqTime)
            ->where('status', 'SCHEDULED')
            ->exists();

        if ($isConflict) {
            $msg = 'Slot jadwal janji temu sudah terisi pasien lain (bentrok).';
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        $totalScheduled = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('status', 'SCHEDULED')
            ->count();

        if ($totalScheduled >= $quotaLimit) {
            $msg = "Kuota janji temu sesi dokter sudah penuh (maksimal {$quotaLimit} pasien).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_name' => $validated['patient_name'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $reqTime,
            'status' => 'SCHEDULED',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Janji temu berhasil dibuat', 'data' => $appointment], 201);
        }

        return redirect('/')->with([
            'success' => "Janji temu dengan {$doctor->name} berhasil dijadwalkan.",
            'issued_appointment' => [
                'patient_name' => $appointment->patient_name,
                'doctor_name' => $doctor->name,
                'poli' => $doctor->poli,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => substr($reqTime, 0, 5),
            ]
        ]);
    }
}
