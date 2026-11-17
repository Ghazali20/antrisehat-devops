<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator &amp; Monitoring Klinik — AntriSehat</title>
    <style>
        :root { --ink: #0f172a; --paper: #f8fafc; --line: #cbd5e1; --line-soft: #e2e8f0; --clinical: #047857; --gold: #b45309; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif; background: var(--paper); color: var(--ink); margin: 0; }
        .icon { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .masthead { background: var(--ink); color: #ffffff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 3px solid var(--gold); }
        .role-tag { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 3px 9px; border: 1px solid #fbbf24; color: #fbbf24; margin-left: 10px; }
        .nav-right { display: flex; align-items: center; gap: 14px; }
        .health-tag { display: inline-flex; align-items: center; gap: 7px; border: 1px solid #34d399; color: #6ee7b7; padding: 5px 11px; font-size: 11px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.04em; }
        .dot { width: 7px; height: 7px; background: #34d399; border-radius: 50%; }
        .nav-link { color: #cbd5e1; text-decoration: none; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .btn-logout { background: transparent; color: #ffffff; border: 1px solid rgba(255,255,255,0.3); padding: 7px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; cursor: pointer; }
        .btn-logout:hover { background: #b91c1c; border-color: #b91c1c; }

        .container { max-width: 1180px; margin: 28px auto; padding: 0 20px; }

        /* Display TV Ruang Tunggu + Running Ticker Kiri -> Kanan */
        .tv-frame { background: var(--ink); color: #ffffff; border: 1.5px solid var(--ink); margin-bottom: 26px; }
        .tv-top {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.14);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .tv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .tv-cell { padding: 24px; border-right: 1px solid rgba(255,255,255,0.12); border-bottom: 1px solid rgba(255,255,255,0.12); text-align: center; }
        .tv-poli { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; }
        .tv-num { font-size: 48px; font-weight: 900; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color: #fbbf24; margin: 8px 0; letter-spacing: 0.04em; }
        .tv-ticker {
            background: #1e293b;
            border-top: 1px solid rgba(255,255,255,0.14);
            overflow: hidden;
            height: 36px;
            display: flex;
            align-items: center;
        }
        .tv-ticker-tag { background: var(--gold); color: #ffffff; font-size: 10px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0 14px; height: 100%; display: flex; align-items: center; flex-shrink: 0; }
        .tv-ticker-track { display: inline-flex; white-space: nowrap; animation: tvMoveRight 26s linear infinite; }
        @keyframes tvMoveRight { 0% { transform: translateX(-50%); } 100% { transform: translateX(0%); } }
        .tv-ticker-item { padding: 0 28px; font-size: 12px; font-weight: 600; color: #e2e8f0; border-right: 1px solid rgba(255,255,255,0.14); }
        .btn-refresh { display: inline-flex; align-items: center; gap: 6px; color: #ffffff; text-decoration: none; padding: 7px 13px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid rgba(255,255,255,0.28); }
        .btn-refresh:hover { background: #ffffff; color: var(--ink); }

        /* KPI Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); border: 1px solid var(--line); background: #ffffff; margin-bottom: 26px; }
        .kpi-cell { padding: 18px 22px; border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .kpi-lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .kpi-val { font-size: 30px; font-weight: 900; font-family: ui-monospace, monospace; margin: 4px 0; }
        .kpi-sub { font-size: 12px; color: #64748b; }

        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; margin-bottom: 26px; }
        .panel { background: #ffffff; border: 1px solid var(--line); margin-bottom: 24px; }
        .panel-head { padding: 14px 22px; background: #f1f5f9; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
        .panel-head h2 { margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }
        .panel-body { padding: 22px; }
        label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #334155; margin-top: 12px; }
        input, select { width: 100%; padding: 9px 11px; margin-top: 5px; border: 1px solid var(--line); font-size: 13px; background: #ffffff; }
        .btn-submit { background: var(--clinical); color: #ffffff; border: none; padding: 11px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; margin-top: 18px; width: 100%; }
        .btn-sm { padding: 6px 11px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; cursor: pointer; border: 1px solid var(--line); background: #ffffff; color: var(--ink); }
        .btn-sm:hover { background: #f1f5f9; }
        .btn-del { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
        .bar-bg { background: #e2e8f0; height: 8px; width: 100%; margin-top: 6px; }
        .bar-fill { background: var(--clinical); height: 100%; }
        .sys-note { margin-top: 20px; padding: 14px; background: var(--paper); border: 1px solid var(--line); font-size: 12px; color: #334155; line-height: 1.6; }
        .notice { display: flex; align-items: center; gap: 8px; padding: 12px 16px; margin-bottom: 18px; font-size: 13px; font-weight: 600; background: #ffffff; border: 1px solid var(--line); border-left: 4px solid var(--clinical); }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f8fafc; text-align: left; padding: 11px 14px; border-bottom: 1px solid var(--line); color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 12px 14px; border-bottom: 1px solid var(--line-soft); vertical-align: middle; }
        .edit-row { display: none; background: #f8fafc; }
        .edit-row.open { display: table-row; }
    </style>
</head>
<body>
    <header class="masthead">
        <div>
            <strong style="font-size:16px; letter-spacing:0.04em; text-transform:uppercase;">Operator &amp; Monitoring Klinik</strong>
            <span class="role-tag">Operator Admin</span>
            <div style="font-size:11px; color:#94a3b8; margin-top:4px; letter-spacing:0.04em;">{{ auth()->user()->name }} &bull; {{ $today }}</div>
        </div>
        <div class="nav-right">
            <a href="/health" class="health-tag">
                <span class="dot"></span>
                <span>{{ $dbHealthy ? '/health 200 OK' : 'DB Error' }}</span>
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
            <div class="notice">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- LAYAR MONITOR TV RUANG TUNGGU -->
        <section class="tv-frame">
            <div class="tv-top">
                <div>
                    <strong style="font-size:15px; letter-spacing:0.05em; text-transform:uppercase; color:#fbbf24;">Monitor Layar TV Ruang Tunggu</strong>
                    <div style="font-size:12px; color:#94a3b8;">Tampilan panggilan antrean aktif tiap poliklinik</div>
                </div>
                <a href="/operator" class="btn-refresh">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
                    <span>Muat Ulang</span>
                </a>
            </div>

            <div class="tv-grid">
                @foreach($doctors->unique('poli') as $doc)
                    @php
                        $currentPoliCall = $calledQueues->where('poli', $doc->poli)->first();
                        $nextPoliCount = $waitingQueues->where('poli', $doc->poli)->count();
                    @endphp
                    <div class="tv-cell">
                        <div class="tv-poli">{{ $doc->poli }}</div>
                        <div class="tv-num">{{ $currentPoliCall ? $currentPoliCall->queue_number : '---' }}</div>
                        <div style="font-size:14px; font-weight:700;">{{ $currentPoliCall ? $currentPoliCall->patient_name : 'Belum Ada Panggilan' }}</div>
                        <div style="font-size:12px; color:#94a3b8; margin-top:8px;">{{ $doc->name }} &bull; Menunggu: {{ $nextPoliCount }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Running Text Informasi Ruang Tunggu (Kiri ke Kanan) -->
            <div class="tv-ticker">
                <div class="tv-ticker-tag">Informasi Klinik</div>
                <div style="overflow:hidden; flex:1;">
                    <div class="tv-ticker-track">
                        @for($c = 0; $c < 2; $c++)
                            <div class="tv-ticker-item">HARAP MENUNGGU DI AREA RUANG TUNGGU SESUAI POLIKLINIK TUJUAN</div>
                            <div class="tv-ticker-item">NOMOR ANTREAN DI-RESET OTOMATIS SETIAP HARI PUKUL 00:00 WIB</div>
                            <div class="tv-ticker-item">KAPASITAS MAKSIMAL 30 PASIEN PER SESI DOKTER</div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>

        <!-- KPI MONITORING -->
        <section class="kpi-grid">
            <div class="kpi-cell">
                <div class="kpi-lbl">Total Antrean Hari Ini</div>
                <div class="kpi-val">{{ $stats['total_today'] }}</div>
                <div class="kpi-sub">Reset harian otomatis via scheduler</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-lbl">Diperiksa / Selesai</div>
                <div class="kpi-val">{{ $stats['called'] + $stats['done'] }}</div>
                <div class="kpi-sub">{{ $stats['called'] }} aktif &bull; {{ $stats['done'] }} selesai</div>
            </div>
            <div class="kpi-cell">
                <div class="kpi-lbl">Antrean Menunggu</div>
                <div class="kpi-val">{{ $stats['waiting'] }}</div>
                <div class="kpi-sub">{{ $stats['skipped'] }} pasien dilewati</div>
            </div>
            <div class="kpi-cell" style="border-right:none;">
                <div class="kpi-lbl">Janji Temu Terjadwal</div>
                <div class="kpi-val">{{ $stats['appointments_today'] }}</div>
                <div class="kpi-sub">Validasi anti-bentrok aktif</div>
            </div>
        </section>

        <section class="grid-2" style="margin-bottom:0;">
            <div class="panel">
                <div class="panel-head"><h2>Kapasitas Sesi Dokter (Maks. 30/Sesi)</h2></div>
                <div class="panel-body">
                    @foreach($doctors as $doc)
                        @php $pct = min(100, round(($doc->today_queue_count / max(1, $doc->max_quota)) * 100)); @endphp
                        <div style="margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600;">
                                <span>{{ $doc->name }} ({{ $doc->poli }})</span>
                                <span style="font-family:ui-monospace, monospace;">{{ $doc->today_queue_count }}/{{ $doc->max_quota }} ({{ $pct }}%)</span>
                            </div>
                            <div class="bar-bg">
                                <div class="bar-fill" style="width: {{ $pct }}%; background: {{ $pct >= 90 ? '#b91c1c' : '#047857' }};"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="sys-note">
                        <strong>Status Otomatisasi Infrastruktur (K05)</strong><br>
                        Healthcheck Endpoint: <code>GET /health</code> (HTTP 200)<br>
                        Scheduler Reset Harian: <code>php artisan queue:reset-daily</code>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Tambah Dokter &amp; Akun Login Pribadi</h2></div>
                <div class="panel-body">
                    <form method="POST" action="/doctors">
                        @csrf
                        <label>Nama Dokter</label>
                        <input type="text" name="name" placeholder="Contoh: dr. Rina Kusuma, Sp.A" required>

                        <label>Email Akun Dokter (Opsional — Otomatis jika kosong)</label>
                        <input type="email" name="email" placeholder="rina.kusuma@antrisehat.id">

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

                        <div style="display:grid; grid-template-columns:2fr 1fr; gap:10px;">
                            <div>
                                <label>Hari Praktik</label>
                                <select name="practice_day" required>
                                    <option value="Monday">Monday (Senin)</option>
                                    <option value="Tuesday">Tuesday (Selasa)</option>
                                    <option value="Wednesday">Wednesday (Rabu)</option>
                                    <option value="Thursday">Thursday (Kamis)</option>
                                    <option value="Friday">Friday (Jumat)</option>
                                    <option value="Saturday">Saturday (Sabtu)</option>
                                </select>
                            </div>
                            <div>
                                <label>Kuota Maks.</label>
                                <input type="number" name="max_quota" value="30" min="1" max="30" required>
                            </div>
                        </div>

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

                        <button type="submit" class="btn-submit">Simpan Jadwal &amp; Akun Dokter</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- TABEL CRUD DOKTER LENGKAP -->
        <section class="panel">
            <div class="panel-head"><h2>Manajemen Data Dokter, Jadwal &amp; Akun Login</h2></div>
            <table>
                <thead>
                    <tr>
                        <th>Dokter</th>
                        <th>Akun Login</th>
                        <th>Poliklinik</th>
                        <th>Kode</th>
                        <th>Jadwal Praktik</th>
                        <th>Kuota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $doc)
                        <tr>
                            <td><strong>{{ $doc->name }}</strong></td>
                            <td style="font-family:ui-monospace, monospace; font-size:12px;">{{ $doc->user->email ?? '-' }}</td>
                            <td>{{ $doc->poli }}</td>
                            <td><code>{{ $doc->poli_prefix }}</code></td>
                            <td>{{ $doc->practice_day }} ({{ substr($doc->start_time, 0, 5) }}&ndash;{{ substr($doc->end_time, 0, 5) }})</td>
                            <td>{{ $doc->max_quota }}</td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="btn-sm" onclick="toggleEdit({{ $doc->id }})">Edit</button>
                                    <form method="POST" action="/doctors/{{ $doc->id }}" style="margin:0;" onsubmit="return confirm('Hapus jadwal dan akun login {{ $doc->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-del">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr id="edit-row-{{ $doc->id }}" class="edit-row">
                            <td colspan="7">
                                <form method="POST" action="/doctors/{{ $doc->id }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(125px, 1fr)); gap:8px; align-items:end; padding:6px 0;">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label style="margin-top:0;">Nama Dokter</label>
                                        <input type="text" name="name" value="{{ $doc->name }}" required>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Poli</label>
                                        <input type="text" name="poli" value="{{ $doc->poli }}" required>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Kode</label>
                                        <input type="text" name="poli_prefix" value="{{ $doc->poli_prefix }}" maxlength="3" required>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Hari</label>
                                        <select name="practice_day" required>
                                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <option value="{{ $day }}" {{ $doc->practice_day === $day ? 'selected' : '' }}>{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Mulai</label>
                                        <input type="time" name="start_time" value="{{ substr($doc->start_time, 0, 5) }}" required>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Selesai</label>
                                        <input type="time" name="end_time" value="{{ substr($doc->end_time, 0, 5) }}" required>
                                    </div>
                                    <div>
                                        <label style="margin-top:0;">Kuota</label>
                                        <input type="number" name="max_quota" value="{{ $doc->max_quota }}" min="1" max="30" required>
                                    </div>
                                    <div style="display:flex; gap:6px;">
                                        <button type="submit" class="btn-sm" style="background:#047857; color:#fff; border-color:#047857; padding:9px 12px;">Simpan</button>
                                        <button type="button" class="btn-sm" style="padding:9px 10px;" onclick="toggleEdit({{ $doc->id }})">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>

    <script>
        function toggleEdit(id) {
            document.getElementById('edit-row-' + id).classList.toggle('open');
        }
    </script>
</body>
</html>
