<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Info ringkas nomor yang sedang dipanggil per poli untuk pasien
        $currentlyCalled = Queue::whereDate('queue_date', $today)
            ->where('status', 'CALLED')
            ->get()
            ->keyBy('poli');

        return view('patient.index', compact('doctors', 'currentlyCalled', 'today'));
    }

    // 2. DASHBOARD DOKTER (WAJIB LOGIN ROLE: DOKTER)
    public function doctorDashboard(Request $request)
    {
        $today = now()->toDateString();
        $user = Auth::user();
        $doctors = Doctor::all();

        // Default filter ke dokter yang login jika terhubung, atau filter pilihan
        $selectedDoctorId = $request->query('doctor_id', $user->doctor_id);

        $doctorQueues = Queue::with('doctor')
            ->whereDate('queue_date', $today)
            ->where('status', '!=', 'RESET')
            ->when($selectedDoctorId, fn($q) => $q->where('doctor_id', $selectedDoctorId))
            ->orderByRaw("CASE status WHEN 'CALLED' THEN 1 WHEN 'WAITING' THEN 2 WHEN 'DONE' THEN 3 WHEN 'SKIPPED' THEN 4 ELSE 5 END")
            ->orderBy('sequence_num', 'asc')
            ->get();

        $appointments = Appointment::with('doctor')
            ->when($selectedDoctorId, fn($q) => $q->where('doctor_id', $selectedDoctorId))
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('doctor.dashboard', compact('doctors', 'doctorQueues', 'appointments', 'today', 'selectedDoctorId'));
    }

    // 3. DASHBOARD OPERATOR / ADMIN (WAJIB LOGIN ROLE: OPERATOR)
    public function operatorDashboard()
    {
        $today = now()->toDateString();
        $doctors = Doctor::withCount([
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

    // CRUD Dokter & Jadwal
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
        ]);

        $validated['max_quota'] = $validated['max_quota'] ?? 30;
        $doctor = Doctor::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dokter berhasil ditambahkan', 'data' => $doctor], 201);
        }
        return redirect('/operator')->with('success', "Jadwal {$doctor->name} ({$doctor->poli}) berhasil ditambahkan.");
    }

    public function deleteDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dokter berhasil dihapus'], 200);
        }
        return redirect('/operator')->with('success', 'Data dokter berhasil dihapus.');
    }

    // Ambil Nomor Antrean per Poli (BR-01 & BR-03)
    public function takeQueue(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:100',
            'queue_date' => 'nullable|date',
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $date = $validated['queue_date'] ?? now()->toDateString();

        // BR-03: Maksimal 30 pasien per sesi dokter
        $activeSessionCount = Queue::where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $date)
            ->where('status', '!=', 'RESET')
            ->count();

        if ($activeSessionCount >= 30) {
            $msg = 'Kuota sesi dokter penuh (maksimal 30 pasien per sesi).';
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        // BR-01: Nomor antrean berurutan per poli dan kembali ke 1 setiap hari / setelah reset
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

        // Kirim data tiket antrean ke session agar langsung tampil kartu tiket cetak di halaman pasien
        return redirect('/')->with([
            'success' => "Nomor antrean {$formattedNumber} berhasil diterbitkan!",
            'issued_ticket' => [
                'queue_number' => $queue->queue_number,
                'patient_name' => $queue->patient_name,
                'poli' => $doctor->poli,
                'doctor_name' => $doctor->name,
                'queue_date' => $date,
            ]
        ]);
    }

    // Panggil Antrean Berikutnya per Poli (Dokter)
    public function callNextQueue(Request $request)
    {
        $validated = $request->validate([
            'poli' => 'required|string',
            'queue_date' => 'nullable|date',
        ]);

        $date = $validated['queue_date'] ?? now()->toDateString();

        Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->where('status', 'CALLED')
            ->update(['status' => 'DONE']);

        $next = Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->where('status', 'WAITING')
            ->orderBy('sequence_num', 'asc')
            ->first();

        if (!$next) {
            $msg = "Tidak ada antrean menunggu di {$validated['poli']}.";
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

    // Update Status Antrean Spesifik oleh Dokter
    public function updateQueueStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:CALLED,DONE,SKIPPED',
        ]);

        $queue = Queue::findOrFail($id);

        if ($validated['status'] === 'CALLED') {
            Queue::where('poli', $queue->poli)
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

    // Buat Janji Temu Dokter (BR-02 & BR-03)
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

        // BR-02a: Validasi hari praktik dokter
        if (strcasecmp($dayName, $doctor->practice_day) !== 0) {
            $msg = "Dokter {$doctor->name} hanya praktik pada hari {$doctor->practice_day} (tanggal dipilih: {$dayName}).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        // BR-02b: Validasi jam praktik dokter
        $reqTime = Carbon::parse($validated['appointment_time'])->format('H:i:s');
        if ($reqTime < $doctor->start_time || $reqTime >= $doctor->end_time) {
            $msg = "Jam janji temu di luar jadwal praktik dokter ({$doctor->start_time} - {$doctor->end_time}).";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect('/')->with('error', $msg);
        }

        // BR-02c: Anti-bentrok slot waktu dokter
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

        // BR-03: Maksimal 30 pasien per sesi dokter
        $totalScheduled = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('status', 'SCHEDULED')
            ->count();

        if ($totalScheduled >= 30) {
            $msg = 'Kuota janji temu sesi dokter sudah penuh (maksimal 30 pasien).';
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
            'success' => "Janji temu dengan {$doctor->name} berhasil dijadwalkan!",
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
