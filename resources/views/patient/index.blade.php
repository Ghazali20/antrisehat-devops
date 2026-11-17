<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AntriSehat — Sistem Layanan &amp; Antrean Klinik Pratama</title>
    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #334155;
            --muted: #64748b;
            --paper: #f8fafc;
            --white: #ffffff;
            --line: #cbd5e1;
            --line-soft: #e2e8f0;
            --clinical: #047857;
            --clinical-bg: #ecfdf5;
            --gold: #b45309;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            background: var(--paper);
            color: var(--ink);
            margin: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        .icon { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        /* Top Masthead */
        .masthead {
            background: var(--ink);
            color: var(--white);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 3px solid var(--gold);
        }
        .brand { display: flex; align-items: center; gap: 14px; }
        .cross-box {
            width: 34px; height: 34px;
            border: 1.5px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            color: #fbbf24;
        }
        .brand-name { font-size: 18px; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; display: block; }
        .brand-meta { font-size: 11px; color: #94a3b8; letter-spacing: 0.06em; text-transform: uppercase; }
        .staff-btn {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--white);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 9px 14px;
            border: 1px solid rgba(255,255,255,0.28);
            background: transparent;
            transition: all 0.15s ease;
        }
        .staff-btn:hover { background: var(--white); color: var(--ink); }

        /* Left-to-Right Clinical Running Ticker */
        .ticker-wrap {
            background: var(--white);
            border-bottom: 1px solid var(--line);
            overflow: hidden;
            display: flex;
            align-items: center;
            height: 38px;
        }
        .ticker-label {
            background: var(--clinical);
            color: var(--white);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0 16px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 2;
            flex-shrink: 0;
        }
        .pulse-dot {
            width: 7px; height: 7px;
            background: #a7f3d0;
            border-radius: 50%;
            animation: blink 1.6s infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.25; } }
        .ticker-track-outer { overflow: hidden; flex: 1; position: relative; }
        .ticker-track {
            display: inline-flex;
            white-space: nowrap;
            animation: moveLeftToRight 28s linear infinite;
        }
        .ticker-wrap:hover .ticker-track { animation-play-state: paused; }
        @keyframes moveLeftToRight {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0%); }
        }
        .ticker-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0 28px;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-soft);
            border-right: 1px solid var(--line-soft);
        }
        .ticker-code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color: var(--clinical); font-weight: 800; }

        /* ECG Line Divider */
        .ecg-bar {
            width: 100%;
            height: 24px;
            background: var(--paper);
            border-bottom: 1px solid var(--line-soft);
            overflow: hidden;
            position: relative;
        }
        .ecg-svg {
            width: 200%;
            height: 24px;
            animation: moveLeftToRight 14s linear infinite;
        }

        .container { max-width: 1140px; margin: 28px auto; padding: 0 20px; }

        /* Alerts */
        .notice {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 16px;
            margin-bottom: 22px;
            font-size: 13px;
            font-weight: 600;
            border-left: 4px solid var(--clinical);
            background: var(--white);
            border-top: 1px solid var(--line);
            border-right: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }
        .notice.err { border-left-color: #b91c1c; color: #991b1b; }

        /* Printed Ticket Slip */
        .slip {
            background: var(--white);
            border: 1.5px solid var(--ink);
            margin-bottom: 26px;
            display: grid;
            grid-template-columns: 1fr auto;
            position: relative;
        }
        .slip-main { padding: 22px 26px; border-right: 1.5px dashed var(--line); }
        .slip-side { padding: 22px 26px; background: var(--ink); color: var(--white); display: flex; flex-direction: column; justify-content: center; text-align: right; min-width: 210px; }
        .slip-tag { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--gold); }
        .slip-number { font-size: 46px; font-weight: 900; letter-spacing: 0.03em; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; margin: 4px 0; color: var(--ink); }

        /* Current Call Board */
        .call-strip {
            background: var(--white);
            border: 1px solid var(--line);
            margin-bottom: 26px;
            display: grid;
            grid-template-columns: 1.2fr 1.8fr;
        }
        @media (max-width: 768px) { .call-strip { grid-template-columns: 1fr; } .slip { grid-template-columns: 1fr; } }
        .call-intro { padding: 22px 24px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: center; }
        .call-intro h1 { margin: 0 0 6px 0; font-size: 20px; font-weight: 800; letter-spacing: -0.02em; }
        .call-intro p { margin: 0; font-size: 13px; color: var(--muted); }
        .call-cells { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        .call-cell { padding: 18px 20px; border-right: 1px solid var(--line-soft); display: flex; flex-direction: column; justify-content: center; }
        .call-cell:last-child { border-right: none; }
        .call-cell-poli { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); }
        .call-cell-num { font-size: 28px; font-weight: 900; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color: var(--ink); margin-top: 4px; }

        /* Two-Column Swiss Grid */
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; margin-bottom: 26px; }
        .panel { background: var(--white); border: 1px solid var(--line); }
        .panel-top {
            padding: 14px 22px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
        }
        .step-num { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; font-weight: 800; color: var(--gold); }
        .panel-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
        .panel-body { padding: 22px; }
        .panel-note { font-size: 13px; color: var(--muted); margin: 0 0 16px 0; }

        label { display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ink-soft); margin-top: 14px; }
        input, select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid var(--line);
            border-radius: 0;
            font-size: 14px;
            color: var(--ink);
            background: var(--white);
        }
        input:focus, select:focus { outline: none; border-color: var(--ink); box-shadow: inset 0 0 0 1px var(--ink); }
        .btn-action {
            width: 100%;
            margin-top: 20px;
            padding: 12px 16px;
            border: none;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .btn-action:hover { opacity: 0.92; }
        .btn-clinical { background: var(--clinical); color: var(--white); }
        .btn-ink { background: var(--ink); color: var(--white); }

        /* Schedule Table */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 12px 16px; background: #f1f5f9; border-bottom: 1px solid var(--line); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--ink-soft); }
        td { padding: 12px 16px; border-bottom: 1px solid var(--line-soft); }
        .mono-tag { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; background: #f1f5f9; padding: 3px 8px; border: 1px solid var(--line); }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="brand">
            <div class="cross-box">
                <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            </div>
            <div>
                <span class="brand-name">AntriSehat</span>
                <span class="brand-meta">Unit Layanan Rawat Jalan &bull; {{ $today }}</span>
            </div>
        </div>
        <div>
            @auth
                <a href="{{ auth()->user()->role === 'dokter' ? '/dokter' : '/operator' }}" class="staff-btn">
                    <span>Masuk Ruang {{ ucfirst(auth()->user()->role) }}</span>
                    <svg class="icon" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @else
                <a href="/login" class="staff-btn">
                    <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="1"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Akses Staf &amp; Dokter</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- Animasi Bergerak dari Kiri ke Kanan: Status Antrean & Jadwal Poliklinik -->
    <div class="ticker-wrap">
        <div class="ticker-label">
            <span class="pulse-dot"></span>
            <span>Status Poli</span>
        </div>
        <div class="ticker-track-outer">
            <div class="ticker-track">
                @for($loopCopy = 0; $loopCopy < 2; $loopCopy++)
                    @foreach($doctors as $doc)
                        @php $curr = $currentlyCalled->get($doc->poli); @endphp
                        <div class="ticker-item">
                            <span>{{ strtoupper($doc->poli) }} ({{ $doc->name }})</span>
                            <span class="ticker-code">AKTIF: {{ $curr ? $curr->queue_number : 'BELUM ADA' }}</span>
                            <span>KUOTA: {{ $doc->today_queue_count }}/{{ $doc->max_quota }}</span>
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>

    <!-- Garis Gelombang Klinis Bergerak Kiri ke Kanan -->
    <div class="ecg-bar" aria-hidden="true">
        <svg class="ecg-svg" viewBox="0 0 1200 24" preserveAspectRatio="none">
            <path d="M0,12 L260,12 L272,12 L278,3 L285,21 L292,6 L298,15 L304,12 L580,12 L592,12 L598,3 L605,21 L612,6 L618,15 L624,12 L880,12 L892,12 L898,3 L905,21 L912,6 L918,15 L924,12 L1200,12"
                  fill="none" stroke="#0d9488" stroke-width="1.3" stroke-opacity="0.45"/>
        </svg>
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

        @if(session('issued_ticket'))
            @php $t = session('issued_ticket'); @endphp
            <section class="slip">
                <div class="slip-main">
                    <span class="slip-tag">Lembar Nomor Antrean Pasien</span>
                    <div class="slip-number">{{ $t['queue_number'] }}</div>
                    <div style="font-size:14px;">Pasien: <strong>{{ $t['patient_name'] }}</strong> &mdash; {{ $t['poli'] }} ({{ $t['doctor_name'] }})</div>
                </div>
                <div class="slip-side">
                    <span style="font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em;">Tanggal Layanan</span>
                    <strong style="font-size:16px; margin:4px 0 10px 0;">{{ $t['queue_date'] }}</strong>
                    <span style="font-size:11px; color:#fbbf24; font-weight:700; text-transform:uppercase;">Status: Menunggu Panggilan</span>
                </div>
            </section>
        @endif

        @if(session('issued_appointment'))
            @php $a = session('issued_appointment'); @endphp
            <section class="slip">
                <div class="slip-main">
                    <span class="slip-tag">Konfirmasi Reservasi Janji Temu</span>
                    <div style="font-size:24px; font-weight:800; margin:8px 0;">{{ $a['appointment_date'] }} &bull; Pukul {{ $a['appointment_time'] }} WIB</div>
                    <div style="font-size:14px;">Pasien: <strong>{{ $a['patient_name'] }}</strong> &mdash; {{ $a['doctor_name'] }} ({{ $a['poli'] }})</div>
                </div>
                <div class="slip-side">
                    <span style="font-size:11px; color:#94a3b8; text-transform:uppercase;">Status Reservasi</span>
                    <strong style="font-size:15px; color:#34d399; margin-top:6px;">TERJADWAL</strong>
                </div>
            </section>
        @endif

        <section class="call-strip">
            <div class="call-intro">
                <h1>Registrasi Mandiri Pasien</h1>
                <p>Penerbitan nomor urut poliklinik harian dan penjadwalan konsultasi dokter tanpa perlu membuat akun.</p>
            </div>
            <div class="call-cells">
                @foreach($doctors->unique('poli') as $doc)
                    @php $curr = $currentlyCalled->get($doc->poli); @endphp
                    <div class="call-cell">
                        <span class="call-cell-poli">Panggilan &bull; {{ $doc->poli }}</span>
                        <span class="call-cell-num">{{ $curr ? $curr->queue_number : '---' }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid-2">
            <div class="panel">
                <div class="panel-top">
                    <h2 class="panel-title">Ambil Nomor Antrean Harian</h2>
                    <span class="step-num">01 / ANTREAN</span>
                </div>
                <div class="panel-body">
                    <p class="panel-note">Nomor antrean diurutkan per poli dan di-reset otomatis setiap pergantian hari (maksimal 30 pasien per sesi).</p>
                    <form method="POST" action="/queues">
                        @csrf
                        <label>Nama Lengkap Pasien</label>
                        <input type="text" name="patient_name" placeholder="Tuliskan nama lengkap pasien" required>

                        <label>Poliklinik &amp; Dokter Pemeriksa</label>
                        <select name="doctor_id" required>
                            @foreach($doctors as $doc)
                                @php $sisa = max(0, $doc->max_quota - $doc->today_queue_count); @endphp
                                <option value="{{ $doc->id }}" {{ $sisa === 0 ? 'disabled' : '' }}>
                                    {{ $doc->poli }} &mdash; {{ $doc->name }} (Sisa Kuota: {{ $sisa }}/{{ $doc->max_quota }})
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn-action btn-clinical">Terbitkan Nomor Antrean</button>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-top">
                    <h2 class="panel-title">Jadwalkan Janji Temu Dokter</h2>
                    <span class="step-num">02 / RESERVASI</span>
                </div>
                <div class="panel-body">
                    <p class="panel-note">Sistem memvalidasi kesesuaian hari, jam praktik dokter, serta mencegah bentrok jadwal antar pasien.</p>
                    <form method="POST" action="/appointments">
                        @csrf
                        <label>Nama Lengkap Pasien</label>
                        <input type="text" name="patient_name" placeholder="Tuliskan nama lengkap pasien" required>

                        <label>Dokter Tujuan</label>
                        <select name="doctor_id" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">
                                    {{ $doc->name }} &mdash; {{ $doc->poli }} ({{ $doc->practice_day }}, {{ substr($doc->start_time,0,5) }}&ndash;{{ substr($doc->end_time,0,5) }})
                                </option>
                            @endforeach
                        </select>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label>Tanggal Kunjungan</label>
                                <input type="date" name="appointment_date" value="{{ $today }}" required>
                            </div>
                            <div>
                                <label>Jam Kunjungan</label>
                                <input type="time" name="appointment_time" value="09:00" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-action btn-ink">Konfirmasi Janji Temu</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-top">
                <h2 class="panel-title">Jadwal Praktik &amp; Kapasitas Sesi Dokter</h2>
                <span class="step-num">03 / JADWAL</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Dokter</th>
                        <th>Poliklinik</th>
                        <th>Prefix</th>
                        <th>Hari Praktik</th>
                        <th>Jam Operasional</th>
                        <th>Kapasitas Terisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $doc)
                        <tr>
                            <td><strong>{{ $doc->name }}</strong></td>
                            <td>{{ $doc->poli }}</td>
                            <td><span class="mono-tag">{{ $doc->poli_prefix }}-001</span></td>
                            <td>{{ $doc->practice_day }}</td>
                            <td>{{ substr($doc->start_time, 0, 5) }} &ndash; {{ substr($doc->end_time, 0, 5) }} WIB</td>
                            <td><strong>{{ $doc->today_queue_count }}</strong> / {{ $doc->max_quota }} pasien</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
