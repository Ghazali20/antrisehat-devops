<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pemeriksaan Dokter — AntriSehat</title>
    <style>
        :root { --ink: #0f172a; --paper: #f8fafc; --line: #cbd5e1; --line-soft: #e2e8f0; --clinical: #047857; --gold: #b45309; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif; background: var(--paper); color: var(--ink); margin: 0; }
        .icon { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .masthead { background: var(--ink); color: #ffffff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 3px solid var(--clinical); }
        .role-tag { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 3px 9px; border: 1px solid #34d399; color: #6ee7b7; margin-left: 10px; }
        .nav-right { display: flex; align-items: center; gap: 14px; }
        .nav-link { color: #cbd5e1; text-decoration: none; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .btn-logout { background: transparent; color: #ffffff; border: 1px solid rgba(255,255,255,0.3); padding: 7px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; cursor: pointer; }
        .btn-logout:hover { background: #b91c1c; border-color: #b91c1c; }

        /* Left-to-Right Active Queue Ticker */
        .ticker-wrap { background: #ffffff; border-bottom: 1px solid var(--line); overflow: hidden; display: flex; align-items: center; height: 34px; }
        .ticker-badge { background: var(--ink); color: #fbbf24; font-size: 10px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0 14px; height: 100%; display: flex; align-items: center; }
        .ticker-track-outer { overflow: hidden; flex: 1; }
        .ticker-track { display: inline-flex; white-space: nowrap; animation: moveRight 24s linear infinite; }
        @keyframes moveRight { 0% { transform: translateX(-50%); } 100% { transform: translateX(0%); } }
        .ticker-item { padding: 0 24px; font-size: 12px; font-weight: 600; color: #334155; border-right: 1px solid var(--line-soft); }

        .container { max-width: 1140px; margin: 28px auto; padding: 0 20px; }
        .doctor-banner {
            background: #ffffff; border: 1px solid var(--line);
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
        }
        @media (max-width: 768px) { .doctor-banner { grid-template-columns: 1fr; } }
        .doc-info { padding: 20px 24px; border-right: 1px solid var(--line); }
        .doc-stats { display: grid; grid-template-columns: repeat(4, 1fr); }
        .doc-stat { padding: 16px; border-right: 1px solid var(--line-soft); display: flex; flex-direction: column; justify-content: center; }
        .doc-stat:last-child { border-right: none; }
        .doc-stat small { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .doc-stat strong { font-size: 22px; font-weight: 800; font-family: ui-monospace, monospace; margin-top: 4px; }

        .panel { background: #ffffff; border: 1px solid var(--line); margin-bottom: 24px; }
        .panel-head {
            padding: 14px 22px;
            background: #f1f5f9;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .panel-head h2 { margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }
        .btn { display: inline-flex; align-items: center; gap: 6px; background: var(--clinical); color: #ffffff; border: none; padding: 8px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; cursor: pointer; }
        .btn:hover { opacity: 0.9; }
        .btn-ink { background: var(--ink); }
        .btn-ghost { background: #ffffff; color: var(--ink); border: 1px solid var(--line); }
        .btn-ghost:hover { background: #f1f5f9; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f8fafc; text-align: left; padding: 11px 16px; border-bottom: 1px solid var(--line); color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line-soft); }
        .st { font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border: 1px solid var(--line); display: inline-block; }
        .st-called { background: #ecfdf5; border-color: var(--clinical); color: var(--clinical); }
        .st-waiting { background: #fffbeb; border-color: var(--gold); color: var(--gold); }
        .st-done { background: #f1f5f9; color: #334155; }
        .st-skipped { background: #fef2f2; border-color: #b91c1c; color: #b91c1c; }
        .notice { display: flex; align-items: center; gap: 8px; padding: 12px 16px; margin-bottom: 18px; font-size: 13px; font-weight: 600; background: #ffffff; border: 1px solid var(--line); border-left: 4px solid var(--clinical); }
        .notice.err { border-left-color: #b91c1c; color: #991b1b; }
    </style>
</head>
<body>
    <header class="masthead">
        <div>
            <strong style="font-size:16px; letter-spacing:0.04em; text-transform:uppercase;">Ruang Pemeriksaan Dokter</strong>
            <span class="role-tag">{{ $doctor ? $doctor->poli : 'Dokter' }}</span>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px; letter-spacing:0.04em;">{{ auth()->user()->name }} ({{ auth()->user()->email }}) &bull; {{ $today }}</div>
        </div>
        <div class="nav-right">
            <a href="/" class="nav-link">Portal Pasien</a>
            <form method="POST" action="/logout" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </header>

    <div class="ticker-wrap">
        <div class="ticker-badge">Sesi Dokter</div>
        <div class="ticker-track-outer">
            <div class="ticker-track">
                @for($i = 0; $i < 2; $i++)
                    <div class="ticker-item">DOKTER: {{ auth()->user()->name }}</div>
                    <div class="ticker-item">POLIKLINIK: {{ $doctor ? $doctor->poli : '-' }}</div>
                    <div class="ticker-item">MENUNGGU: {{ $stats['waiting'] }} PASIEN</div>
                    <div class="ticker-item">SELESAI: {{ $stats['done'] }} PASIEN</div>
                    <div class="ticker-item">BATAS SESI: {{ $stats['quota'] }} PASIEN</div>
                @endfor
            </div>
        </div>
    </div>

    <main class="container">
        @if(session('success'))
            <div class="notice">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="notice err">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($doctor)
            <section class="doctor-banner">
                <div class="doc-info">
                    <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--gold);">Profil Praktik Aktif</span>
                    <div style="font-size:18px; font-weight:800; margin:4px 0;">{{ $doctor->name }} &mdash; {{ $doctor->poli }}</div>
                    <div style="font-size:13px; color:#475569;">Hari Praktik: {{ $doctor->practice_day }} &bull; Pukul {{ substr($doctor->start_time,0,5) }} &ndash; {{ substr($doctor->end_time,0,5) }} WIB</div>
                </div>
                <div class="doc-stats">
                    <div class="doc-stat"><small>Menunggu</small><strong>{{ $stats['waiting'] }}</strong></div>
                    <div class="doc-stat"><small>Diperiksa</small><strong>{{ $stats['called'] }}</strong></div>
                    <div class="doc-stat"><small>Selesai</small><strong>{{ $stats['done'] }}</strong></div>
                    <div class="doc-stat"><small>Kuota</small><strong>{{ $stats['quota'] }}</strong></div>
                </div>
            </section>
        @endif

        <section class="panel">
            <div class="panel-head">
                <h2>Antrean Pasien Sesi Anda</h2>
                @if($doctor)
                    <form method="POST" action="/queues/call-next" style="margin:0;">
                        @csrf
                        <input type="hidden" name="poli" value="{{ $doctor->poli }}">
                        <button type="submit" class="btn">
                            <svg class="icon" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            <span>Panggil Antrean Berikutnya</span>
                        </button>
                    </form>
                @endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Poliklinik</th>
                        <th>Nama Pasien</th>
                        <th>Status</th>
                        <th>Tindakan Pemeriksaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctorQueues as $q)
                        <tr>
                            <td><strong style="font-size:15px; font-family:ui-monospace, monospace;">{{ $q->queue_number }}</strong></td>
                            <td>{{ $q->poli }}</td>
                            <td><strong>{{ $q->patient_name }}</strong></td>
                            <td>
                                @if($q->status === 'CALLED')
                                    <span class="st st-called">Sedang Diperiksa</span>
                                @elseif($q->status === 'WAITING')
                                    <span class="st st-waiting">Menunggu</span>
                                @elseif($q->status === 'DONE')
                                    <span class="st st-done">Selesai</span>
                                @else
                                    <span class="st st-skipped">Dilewati</span>
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
                                            <button type="submit" class="btn btn-ink">Selesai</button>
                                        </form>
                                    @endif
                                    @if($q->status === 'WAITING')
                                        <form method="POST" action="/queues/{{ $q->id }}/status" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="status" value="SKIPPED">
                                            <button type="submit" class="btn btn-ghost">Lewati</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center; color:#64748b; padding:24px;">Belum ada antrean pasien untuk sesi Anda hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Jadwal Janji Temu Pasien Anda</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Nama Pasien</th>
                        <th>Poliklinik</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td>{{ $app->appointment_date }}</td>
                            <td><strong>{{ substr($app->appointment_time, 0, 5) }} WIB</strong></td>
                            <td>{{ $app->patient_name }}</td>
                            <td>{{ $app->doctor->poli ?? '-' }}</td>
                            <td><span class="st st-done">{{ $app->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center; color:#64748b; padding:20px;">Belum ada janji temu untuk sesi Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
