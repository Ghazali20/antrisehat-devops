<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator &amp; Monitoring Klinik — AntriSehat</title>
    <style>
        :root { --navy: #0f172a; --accent: #0d9488; --border: #e2e8f0; --bg: #f8fafc; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--navy); margin: 0; }
        .icon { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .navbar { background: var(--navy); color: #ffffff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .role-pill { display: inline-block; background: rgba(56,189,248,0.16); color: #7dd3fc; border: 1px solid rgba(56,189,248,0.3); padding: 2px 9px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-left: 8px; letter-spacing: 0.04em; }
        .nav-right { display: flex; align-items: center; gap: 14px; }
        .health-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(16,185,129,0.15); border: 1px solid rgba(52,211,153,0.35); color: #6ee7b7; padding: 5px 12px; border-radius: 99px; font-size: 12px; font-weight: 600; text-decoration: none; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: #34d399; }
        .nav-link { color: #cbd5e1; text-decoration: none; font-size: 13px; font-weight: 500; }
        .btn-logout { background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 500; cursor: pointer; }
        .btn-logout:hover { background: #dc2626; border-color: #dc2626; }
        .container { max-width: 1180px; margin: 28px auto; padding: 0 20px; }

        /* Display Monitor Ruang Tunggu */
        .monitor-board { background: var(--navy); color: #ffffff; border-radius: 14px; padding: 24px 28px; margin-bottom: 24px; }
        .monitor-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .monitor-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-top: 18px; }
        .monitor-tile { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; padding: 20px; text-align: center; }
        .monitor-poli { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; }
        .monitor-num { font-size: 44px; font-weight: 800; color: #2dd4bf; letter-spacing: 0.04em; margin: 6px 0; font-variant-numeric: tabular-nums; }
        .btn-refresh { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.1); color: #ffffff; text-decoration: none; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; border: 1px solid rgba(255,255,255,0.15); }
        .btn-refresh:hover { background: rgba(255,255,255,0.18); }

        /* KPI Stats */
        .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-box { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; }
        .stat-lbl { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
        .stat-num { font-size: 28px; font-weight: 800; color: var(--navy); margin: 4px 0; font-variant-numeric: tabular-nums; }
        .stat-note { font-size: 12px; color: #64748b; }

        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 22px; margin-bottom: 24px; }
        .card { background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; }
        .card h2 { margin: 0 0 16px 0; font-size: 16px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-top: 12px; }
        input, select { width: 100%; padding: 9px 12px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        .btn-submit { background: var(--accent); color: #ffffff; border: none; padding: 11px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 18px; width: 100%; }
        .btn-submit:hover { background: #0f766e; }
        .progress-bg { background: #e2e8f0; border-radius: 99px; height: 8px; width: 100%; overflow: hidden; margin-top: 6px; }
        .progress-fill { background: var(--accent); height: 100%; }
        .sys-box { margin-top: 20px; padding: 14px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border); font-size: 12px; color: #475569; line-height: 1.6; }
        .alert-success { display: flex; align-items: center; gap: 8px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
    </style>
</head>
<body>
    <header class="navbar">
        <div>
            <strong style="font-size:17px;">Operator &amp; Monitoring Klinik</strong>
            <span class="role-pill">Operator Admin</span>
            <div style="font-size:12px; color:#94a3b8; margin-top:4px;">{{ auth()->user()->name }} &bull; Tanggal Operasional: {{ $today }}</div>
        </div>
        <div class="nav-right">
            <a href="/health" class="health-pill">
                <span class="dot"></span>
                <span>{{ $dbHealthy ? '/health 200 OK' : 'DB Disconnected' }}</span>
            </a>
            <a href="/" class="nav-link">Portal Pasien</a>
            <form method="POST" action="/logout" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </header>

    <main class="container">
        @if(session('success'))
            <div class="alert-success">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <section class="monitor-board">
            <div class="monitor-head">
                <div>
                    <h2 style="margin:0; font-size:18px; letter-spacing:-0.01em;">Layar Informasi Antrean Ruang Tunggu</h2>
                    <span style="font-size:13px; color:#94a3b8;">Monitor tampilan panggilan antrean aktif per poliklinik</span>
                </div>
                <a href="/operator" class="btn-refresh">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
                    <span>Segarkan Data</span>
                </a>
            </div>

            <div class="monitor-grid">
                @foreach($doctors->unique('poli') as $doc)
                    @php
                        $currentPoliCall = $calledQueues->where('poli', $doc->poli)->first();
                        $nextPoliCount = $waitingQueues->where('poli', $doc->poli)->count();
                    @endphp
                    <div class="monitor-tile">
                        <div class="monitor-poli">{{ $doc->poli }}</div>
                        <div class="monitor-num">{{ $currentPoliCall ? $currentPoliCall->queue_number : '—' }}</div>
                        <div style="font-size:14px; font-weight:600; color:#f8fafc;">{{ $currentPoliCall ? $currentPoliCall->patient_name : 'Belum Ada Panggilan' }}</div>
                        <div style="font-size:12px; color:#94a3b8; margin-top:8px;">{{ $doc->name }} &bull; Menunggu: {{ $nextPoliCount }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid-4">
            <div class="stat-box">
                <div class="stat-lbl">Total Antrean Hari Ini</div>
                <div class="stat-num">{{ $stats['total_today'] }}</div>
                <div class="stat-note">Reset harian otomatis via scheduler</div>
            </div>
            <div class="stat-box">
                <div class="stat-lbl">Sedang Diperiksa / Selesai</div>
                <div class="stat-num">{{ $stats['called'] + $stats['done'] }}</div>
                <div class="stat-note">{{ $stats['called'] }} aktif &bull; {{ $stats['done'] }} selesai</div>
            </div>
            <div class="stat-box">
                <div class="stat-lbl">Antrean Menunggu</div>
                <div class="stat-num">{{ $stats['waiting'] }}</div>
                <div class="stat-note">{{ $stats['skipped'] }} pasien dilewati</div>
            </div>
            <div class="stat-box">
                <div class="stat-lbl">Janji Temu Terjadwal</div>
                <div class="stat-num">{{ $stats['appointments_today'] }}</div>
                <div class="stat-note">Validasi anti-bentrok aktif</div>
            </div>
        </section>

        <section class="grid-2">
            <div class="card">
                <h2>Monitoring Kapasitas Sesi Dokter (Maks. 30/Sesi)</h2>
                @foreach($doctors as $doc)
                    @php $pct = min(100, round(($doc->today_queue_count / 30) * 100)); @endphp
                    <div style="margin-bottom:16px;">
                        <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600;">
                            <span>{{ $doc->name }} ({{ $doc->poli }})</span>
                            <span>{{ $doc->today_queue_count }} / 30 ({{ $pct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width: {{ $pct }}%; background: {{ $pct >= 90 ? '#dc2626' : '#0d9488' }};"></div>
                        </div>
                    </div>
                @endforeach

                <div class="sys-box">
                    <strong>Konfigurasi Otomatisasi Sistem</strong><br>
                    Endpoint Healthcheck: <code>GET /health</code> (HTTP 200)<br>
                    Command Reset Antrean Harian: <code>php artisan queue:reset-daily</code>
                </div>
            </div>

            <div class="card">
                <h2>Tambah Dokter &amp; Jadwal Praktik</h2>
                <form method="POST" action="/doctors">
                    @csrf
                    <label>Nama Dokter</label>
                    <input type="text" name="name" placeholder="Contoh: dr. Rina Kusuma, Sp.A" required>

                    <div style="display:grid; grid-template-columns:2fr 1fr; gap:10px;">
                        <div>
                            <label>Poliklinik</label>
                            <input type="text" name="poli" placeholder="Poli Anak" required>
                        </div>
                        <div>
                            <label>Kode Huruf</label>
                            <input type="text" name="poli_prefix" placeholder="K" maxlength="3" required>
                        </div>
                    </div>

                    <label>Hari Praktik</label>
                    <select name="practice_day" required>
                        <option value="Monday">Monday (Senin)</option>
                        <option value="Tuesday">Tuesday (Selasa)</option>
                        <option value="Wednesday">Wednesday (Rabu)</option>
                        <option value="Thursday">Thursday (Kamis)</option>
                        <option value="Friday">Friday (Jumat)</option>
                        <option value="Saturday">Saturday (Sabtu)</option>
                    </select>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <label>Jam Mulai</label>
                            <input type="time" name="start_time" value="08:00" required>
                        </div>
                        <div>
                            <label>Jam Selesai</label>
                            <input type="time" name="end_time" value="14:00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Jadwal Dokter</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
