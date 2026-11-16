<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AntriSehat — Sistem Antrean & Janji Temu Klinik</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f9; color: #1f2937; margin: 0; padding: 24px; }
        .container { max-width: 1050px; margin: 0 auto; }
        .header { background: #0f172a; color: #fff; padding: 20px 24px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 18px; margin-bottom: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        h2 { margin-top: 0; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        button { background: #1d4ed8; color: #fff; border: none; padding: 10px 14px; border-radius: 6px; cursor: pointer; margin-top: 14px; font-weight: 600; width: 100%; }
        button:hover { background: #1e40af; }
        .btn-green { background: #059669; }
        .btn-green:hover { background: #047857; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .called { background: #dcfce7; color: #166534; }
        .waiting { background: #fef3c7; color: #92400e; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1 style="margin:0; font-size:22px;">🏥 AntriSehat — Klinik Pratama</h1>
            <small>Sistem Nomor Antrean & Janji Temu Klinik (Studi Kasus K05) | Tanggal: {{ $today }}</small>
        </div>
        <a href="/health" style="color:#38bdf8; text-decoration:none; font-weight:600;">Status /health &rarr;</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="grid">
        <!-- Form Ambil Nomor Antrean -->
        <div class="card">
            <h2>1. Ambil Nomor Antrean</h2>
            <form method="POST" action="/queues">
                @csrf
                <label>Nama Pasien</label>
                <input type="text" name="patient_name" placeholder="Contoh: Budi Santoso" required>
                <label>Pilih Poli & Dokter</label>
                <select name="doctor_id" required>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->poli }} — {{ $doc->name }} ({{ $doc->practice_day }})</option>
                    @endforeach
                </select>
                <button type="submit">Ambil Nomor Antrean</button>
            </form>
        </div>

        <!-- Form Buat Janji Temu -->
        <div class="card">
            <h2>2. Buat Janji Temu Dokter</h2>
            <form method="POST" action="/appointments">
                @csrf
                <label>Nama Pasien</label>
                <input type="text" name="patient_name" placeholder="Contoh: Siti Aminah" required>
                <label>Dokter</label>
                <select name="doctor_id" required>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->poli }} | {{ $doc->practice_day }} {{ substr($doc->start_time,0,5) }}-{{ substr($doc->end_time,0,5) }})</option>
                    @endforeach
                </select>
                <label>Tanggal Kunjungan</label>
                <input type="date" name="appointment_date" value="{{ $today }}" required>
                <label>Jam Janji Temu</label>
                <input type="time" name="appointment_time" value="09:00" required>
                <button type="submit">Jadwalkan Janji Temu</button>
            </form>
        </div>

        <!-- Form Tambah Dokter & Panggil Antrean -->
        <div class="card">
            <h2>3. Loket Petugas (Panggil Antrean)</h2>
            <form method="POST" action="/queues/call-next">
                @csrf
                <label>Pilih Poli</label>
                <select name="poli" required>
                    @foreach($doctors->unique('poli') as $doc)
                        <option value="{{ $doc->poli }}">{{ $doc->poli }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-green">Panggil Antrean Berikutnya</button>
            </form>

            <h2 style="margin-top:22px;">Tambah Jadwal Dokter</h2>
            <form method="POST" action="/doctors">
                @csrf
                <input type="text" name="name" placeholder="Nama Dokter (dr. ...)" required>
                <input type="text" name="poli" placeholder="Poli (Poli Umum / Poli Gigi)" required>
                <input type="text" name="poli_prefix" placeholder="Kode Huruf Poli (A / G)" maxlength="3" required>
                <select name="practice_day" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
                <div style="display:flex; gap:8px;">
                    <input type="time" name="start_time" value="08:00" required>
                    <input type="time" name="end_time" value="14:00" required>
                </div>
                <button type="submit">Simpan Dokter</button>
            </form>
        </div>
    </div>

    <!-- Display Antrean Berjalan -->
    <div class="card" style="margin-bottom:20px;">
        <h2>📺 Tampilan Antrean Berjalan Hari Ini</h2>
        <table>
            <thead>
                <tr>
                    <th>Nomor Antrean</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Nama Pasien</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeQueues as $q)
                    <tr>
                        <td><strong>{{ $q->queue_number }}</strong></td>
                        <td>{{ $q->poli }}</td>
                        <td>{{ $q->doctor->name ?? '-' }}</td>
                        <td>{{ $q->patient_name }}</td>
                        <td>
                            <span class="badge {{ $q->status === 'CALLED' ? 'called' : 'waiting' }}">
                                {{ $q->status === 'CALLED' ? 'SEDANG DIPANGGIL' : 'MENUNGGU' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color:#64748b;">Belum ada antrean aktif hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Daftar Janji Temu -->
    <div class="card">
        <h2>📅 Daftar Janji Temu Terjadwal</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Dokter</th>
                    <th>Poli</th>
                    <th>Nama Pasien</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $app)
                    <tr>
                        <td>{{ $app->appointment_date }}</td>
                        <td>{{ substr($app->appointment_time, 0, 5) }}</td>
                        <td>{{ $app->doctor->name ?? '-' }}</td>
                        <td>{{ $app->doctor->poli ?? '-' }}</td>
                        <td>{{ $app->patient_name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color:#64748b;">Belum ada janji temu terjadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
