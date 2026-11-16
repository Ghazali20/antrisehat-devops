<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AntriSehat — Portal Layanan Pasien</title>
    <style>
        :root {
            --navy: #0f172a;
            --navy-soft: #1e293b;
            --accent: #0d9488;
            --accent-hover: #0f766e;
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        .icon { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
        .navbar {
            background: var(--navy);
            color: #ffffff;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid #1e293b;
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-mark {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.16);
            display: flex; align-items: center; justify-content: center;
            color: #2dd4bf;
        }
        .brand-title { font-size: 17px; font-weight: 700; letter-spacing: -0.02em; display: block; }
        .brand-sub { font-size: 12px; color: #94a3b8; display: block; }
        .nav-link {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.07);
            color: #f8fafc;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.14);
            transition: background 0.15s;
        }
        .nav-link:hover { background: rgba(255,255,255,0.14); }
        .container { max-width: 1120px; margin: 28px auto; padding: 0 20px; }

        /* Alert */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Ticket Output */
        .ticket-card {
            background: var(--navy);
            color: #ffffff;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border-left: 4px solid #2dd4bf;
        }
        .ticket-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; font-weight: 600; }
        .ticket-code { font-size: 42px; font-weight: 800; letter-spacing: 0.04em; color: #ffffff; margin: 4px 0; font-variant-numeric: tabular-nums; }
        .ticket-meta { font-size: 14px; color: #cbd5e1; }
        .status-tag {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(45, 212, 191, 0.14);
            color: #5eead4;
            border: 1px solid rgba(45, 212, 191, 0.3);
        }

        /* Live Queue Strip */
        .strip {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .strip-title { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.01em; }
        .strip-desc { margin: 4px 0 0 0; font-size: 13px; color: var(--muted); }
        .queue-counters { display: flex; gap: 12px; flex-wrap: wrap; }
        .counter-box {
            border: 1px solid var(--border);
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 18px;
            min-width: 150px;
        }
        .counter-poli { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); }
        .counter-val { font-size: 22px; font-weight: 800; color: var(--navy); margin-top: 2px; font-variant-numeric: tabular-nums; }

        /* Grid & Form Cards */
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 22px; margin-bottom: 24px; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .card-head {
            display: flex; align-items: center; gap: 10px;
            margin: 0 0 6px 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--navy);
        }
        .card-sub { margin: 0 0 18px 0; font-size: 13px; color: var(--muted); }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-top: 14px; }
        input, select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: var(--text);
            background: #ffffff;
        }
        input:focus, select:focus { outline: none; border-color: var(--navy); box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%;
            margin-top: 20px;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-primary { background: var(--accent); color: #ffffff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-navy { background: var(--navy); color: #ffffff; }
        .btn-navy:hover { background: var(--navy-soft); }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 11px 12px; background: #f8fafc; border-bottom: 1px solid var(--border); color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .code-pill {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: #f1f5f9;
            color: var(--navy);
            border: 1px solid var(--border);
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="brand">
            <div class="brand-mark">
                <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            </div>
            <div>
                <span class="brand-title">AntriSehat Klinik Pratama</span>
                <span class="brand-sub">Layanan Mandiri Pasien &bull; {{ $today }}</span>
            </div>
        </div>
        <div>
            @auth
                <a href="{{ auth()->user()->role === 'dokter' ? '/dokter' : '/operator' }}" class="nav-link">
                    <span>Dashboard {{ ucfirst(auth()->user()->role) }}</span>
                    <svg class="icon" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @else
                <a href="/login" class="nav-link">
                    <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Login Staf Klinik</span>
                </a>
            @endauth
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

        @if(session('issued_ticket'))
            @php $t = session('issued_ticket'); @endphp
            <section class="ticket-card">
                <div>
                    <div class="ticket-label">Nomor Antrean Diterbitkan</div>
                    <div class="ticket-code">{{ $t['queue_number'] }}</div>
                    <div class="ticket-meta">Pasien: <strong>{{ $t['patient_name'] }}</strong> &mdash; {{ $t['poli'] }} ({{ $t['doctor_name'] }})</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:13px; color:#94a3b8; margin-bottom:8px;">Tanggal: {{ $t['queue_date'] }}</div>
                    <span class="status-tag">Menunggu Panggilan</span>
                </div>
            </section>
        @endif

        @if(session('issued_appointment'))
            @php $a = session('issued_appointment'); @endphp
            <section class="ticket-card" style="border-left-color:#38bdf8;">
                <div>
                    <div class="ticket-label">Konfirmasi Janji Temu</div>
                    <div style="font-size:24px; font-weight:700; margin:6px 0;">{{ $a['appointment_date'] }} &bull; Pukul {{ $a['appointment_time'] }} WIB</div>
                    <div class="ticket-meta">Pasien: <strong>{{ $a['patient_name'] }}</strong> &mdash; {{ $a['doctor_name'] }} ({{ $a['poli'] }})</div>
                </div>
                <div>
                    <span class="status-tag" style="color:#7dd3fc; border-color:rgba(125,211,252,0.3); background:rgba(56,189,248,0.12);">Terjadwal</span>
                </div>
            </section>
        @endif

        <section class="strip">
            <div>
                <h1 class="strip-title">Pendaftaran &amp; Antrean Online</h1>
                <p class="strip-desc">Ambil nomor antrean harian atau jadwalkan konsultasi dokter tanpa registrasi akun.</p>
            </div>
            <div class="queue-counters">
                @foreach($doctors->unique('poli') as $doc)
                    @php $curr = $currentlyCalled->get($doc->poli); @endphp
                    <div class="counter-box">
                        <div class="counter-poli">Dipanggil &bull; {{ $doc->poli }}</div>
                        <div class="counter-val">{{ $curr ? $curr->queue_number : '—' }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid-2">
            <div class="card">
                <h2 class="card-head">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                    <span>Ambil Nomor Antrean</span>
                </h2>
                <p class="card-sub">Nomor diterbitkan berurutan per poli dengan batas 30 pasien per sesi dokter.</p>
                <form method="POST" action="/queues">
                    @csrf
                    <label>Nama Lengkap Pasien</label>
                    <input type="text" name="patient_name" placeholder="Masukkan nama lengkap" required>

                    <label>Poliklinik &amp; Dokter</label>
                    <select name="doctor_id" required>
                        @foreach($doctors as $doc)
                            @php $sisa = max(0, 30 - $doc->today_queue_count); @endphp
                            <option value="{{ $doc->id }}" {{ $sisa === 0 ? 'disabled' : '' }}>
                                {{ $doc->poli }} &mdash; {{ $doc->name }} (Tersedia {{ $sisa }}/30)
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">Terbitkan Nomor Antrean</button>
                </form>
            </div>

            <div class="card">
                <h2 class="card-head">
                    <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span>Buat Janji Temu Dokter</span>
                </h2>
                <p class="card-sub">Pilih tanggal dan jam kunjungan sesuai jadwal praktik dokter yang tersedia.</p>
                <form method="POST" action="/appointments">
                    @csrf
                    <label>Nama Lengkap Pasien</label>
                    <input type="text" name="patient_name" placeholder="Masukkan nama lengkap" required>

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

                    <button type="submit" class="btn btn-navy">Jadwalkan Janji Temu</button>
                </form>
            </div>
        </section>

        <section class="card">
            <h2 class="card-head" style="margin-bottom:16px;">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <span>Jadwal Praktik Dokter</span>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Dokter</th>
                        <th>Poliklinik</th>
                        <th>Kode Poli</th>
                        <th>Hari Praktik</th>
                        <th>Jam Operasional</th>
                        <th>Terisi Hari Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $doc)
                        <tr>
                            <td><strong>{{ $doc->name }}</strong></td>
                            <td>{{ $doc->poli }}</td>
                            <td><span class="code-pill">{{ $doc->poli_prefix }}-001</span></td>
                            <td>{{ $doc->practice_day }}</td>
                            <td>{{ substr($doc->start_time, 0, 5) }} &ndash; {{ substr($doc->end_time, 0, 5) }} WIB</td>
                            <td>{{ $doc->today_queue_count }} / 30</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
