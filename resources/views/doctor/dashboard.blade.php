<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pemeriksaan Dokter — AntriSehat</title>
    <style>
        :root { --navy: #0f172a; --accent: #0d9488; --border: #e2e8f0; --bg: #f8fafc; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--navy); margin: 0; }
        .icon { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .navbar { background: var(--navy); color: #ffffff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .role-pill { display: inline-block; background: rgba(45,212,191,0.16); color: #5eead4; border: 1px solid rgba(45,212,191,0.3); padding: 2px 9px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-left: 8px; letter-spacing: 0.04em; }
        .nav-right { display: flex; align-items: center; gap: 14px; }
        .nav-link { color: #cbd5e1; text-decoration: none; font-size: 13px; font-weight: 500; }
        .nav-link:hover { color: #ffffff; }
        .btn-logout { background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 500; cursor: pointer; }
        .btn-logout:hover { background: #dc2626; border-color: #dc2626; }
        .container { max-width: 1140px; margin: 28px auto; padding: 0 20px; }
        .card { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 22px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 18px; }
        .call-bar { background: #f8fafc; border: 1px solid var(--border); padding: 14px 16px; border-radius: 10px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 18px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; background: var(--accent); color: #ffffff; border: none; padding: 7px 13px; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #0f766e; }
        .btn-navy { background: var(--navy); }
        .btn-navy:hover { background: #1e293b; }
        .btn-outline { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        .btn-outline:hover { background: #f1f5f9; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #f8fafc; text-align: left; padding: 11px 12px; border-bottom: 1px solid var(--border); color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-called { background: #dcfce7; color: #166534; }
        .badge-waiting { background: #fef3c7; color: #92400e; }
        .badge-done { background: #f1f5f9; color: #334155; }
        .badge-skipped { background: #fee2e2; color: #991b1b; }
        .alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <header class="navbar">
        <div>
            <strong style="font-size:17px;">Ruang Pemeriksaan Dokter</strong>
            <span class="role-pill">Dokter</span>
            <div style="font-size:12px; color:#94a3b8; margin-top:4px;">{{ auth()->user()->name }} ({{ auth()->user()->email }}) &bull; {{ $today }}</div>
        </div>
        <div class="nav-right">
            <a href="/" class="nav-link">Portal Pasien</a>
            <form method="POST" action="/logout" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </header>

    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <section class="card">
            <div class="card-header">
                <h2 style="margin:0; font-size:17px;">Daftar Antrean Pasien Hari Ini</h2>
                <form method="GET" action="/dokter" style="margin:0;">
                    <select name="doctor_id" onchange="this.form.submit()" style="padding:7px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:13px;">
                        <option value="">Semua Dokter</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }} ({{ $doc->poli }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="call-bar">
                <span style="font-size:13px; font-weight:600; color:#334155;">Panggil Antrean Berikutnya:</span>
                @foreach($doctors->unique('poli') as $doc)
                    <form method="POST" action="/queues/call-next" style="margin:0;">
                        @csrf
                        <input type="hidden" name="poli" value="{{ $doc->poli }}">
                        <button type="submit" class="btn">
                            <svg class="icon" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            <span>{{ $doc->poli }}</span>
                        </button>
                    </form>
                @endforeach
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No. Antrean</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Nama Pasien</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctorQueues as $q)
                        <tr>
                            <td><strong style="font-size:15px; font-family:ui-monospace, monospace;">{{ $q->queue_number }}</strong></td>
                            <td>{{ $q->poli }}</td>
                            <td>{{ $q->doctor->name ?? '-' }}</td>
                            <td><strong>{{ $q->patient_name }}</strong></td>
                            <td>
                                @if($q->status === 'CALLED')
                                    <span class="badge badge-called">Sedang Diperiksa</span>
                                @elseif($q->status === 'WAITING')
                                    <span class="badge badge-waiting">Menunggu</span>
                                @elseif($q->status === 'DONE')
                                    <span class="badge badge-done">Selesai</span>
                                @else
                                    <span class="badge badge-skipped">Dilewati</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    @if($q->status !== 'CALLED')
                                        <form method="POST" action="/queues/{{ $q->id }}/status" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="status" value="CALLED">
                                            <button type="submit" class="btn">Panggil</button>
                                        </form>
                                    @endif
                                    @if($q->status !== 'DONE')
                                        <form method="POST" action="/queues/{{ $q->id }}/status" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="status" value="DONE">
                                            <button type="submit" class="btn btn-navy">Selesai</button>
                                        </form>
                                    @endif
                                    @if($q->status === 'WAITING')
                                        <form method="POST" action="/queues/{{ $q->id }}/status" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="status" value="SKIPPED">
                                            <button type="submit" class="btn btn-outline">Lewati</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center; color:#64748b; padding:24px;">Belum ada pasien dalam antrean hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="card">
            <h2 style="margin:0 0 16px 0; font-size:17px;">Jadwal Janji Temu Pasien</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Nama Pasien</th>
                        <th>Dokter</th>
                        <th>Poli</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td>{{ $app->appointment_date }}</td>
                            <td><strong>{{ substr($app->appointment_time, 0, 5) }} WIB</strong></td>
                            <td>{{ $app->patient_name }}</td>
                            <td>{{ $app->doctor->name ?? '-' }}</td>
                            <td>{{ $app->doctor->poli ?? '-' }}</td>
                            <td><span class="badge badge-done">{{ $app->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center; color:#64748b; padding:20px;">Belum ada janji temu terjadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
