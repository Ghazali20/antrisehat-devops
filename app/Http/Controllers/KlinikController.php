<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KlinikController extends Controller
{
    // Halaman Dashboard Web AntriSehat
    public function index()
    {
        $today = now()->toDateString();
        $doctors = Doctor::all();
        $activeQueues = Queue::with('doctor')
            ->whereDate('queue_date', $today)
            ->whereIn('status', ['WAITING', 'CALLED'])
            ->orderBy('id', 'asc')
            ->get();
        $appointments = Appointment::with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('dashboard', compact('doctors', 'activeQueues', 'appointments', 'today'));
    }

    // CRUD Dokter & Jadwal: List & Create
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
        return redirect('/')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function deleteDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dokter berhasil dihapus'], 200);
        }
        return redirect('/')->with('success', 'Dokter berhasil dihapus.');
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

        // BR-03: Maksimal 30 pasien per sesi dokter pada tanggal tersebut
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
        return redirect('/')->with('success', "Nomor antrean Anda: {$formattedNumber} ({$doctor->poli})");
    }

    // Panggil Antrean Berikutnya per Poli
    public function callNextQueue(Request $request)
    {
        $validated = $request->validate([
            'poli' => 'required|string',
            'queue_date' => 'nullable|date',
        ]);

        $date = $validated['queue_date'] ?? now()->toDateString();

        // Tandai antrean yang sedang CALLED menjadi DONE
        Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->where('status', 'CALLED')
            ->update(['status' => 'DONE']);

        // Ambil antrean WAITING berikutnya
        $next = Queue::where('poli', $validated['poli'])
            ->whereDate('queue_date', $date)
            ->where('status', 'WAITING')
            ->orderBy('sequence_num', 'asc')
            ->first();

        if (!$next) {
            $msg = "Tidak ada antrean menunggu di {$validated['poli']}.";
            return $request->wantsJson()
                ? response()->json(['message' => $msg], 404)
                : redirect('/')->with('error', $msg);
        }

        $next->update(['status' => 'CALLED']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Memanggil antrean berikutnya', 'data' => $next], 200);
        }
        return redirect('/')->with('success', "Memanggil nomor antrean {$next->queue_number} - {$next->patient_name}");
    }

    // Tampilan Antrean Berjalan
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
        $dayName = Carbon::parse($validated['appointment_date'])->format('l'); // Monday, Tuesday, ...

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

        // BR-02c: Anti-bentrok slot waktu dokter pada tanggal yang sama
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
        return redirect('/')->with('success', "Janji temu dengan {$doctor->name} berhasil dijadwalkan.");
    }
}
