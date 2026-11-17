<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otorisasi Staf — AntriSehat</title>
    <style>
        :root { --ink: #0f172a; --paper: #f8fafc; --line: #cbd5e1; --clinical: #047857; --gold: #b45309; }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .icon { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .top-strip {
            background: var(--ink);
            color: #ffffff;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--gold);
        }
        .ecg-bar { width: 100%; height: 22px; background: #ffffff; border-bottom: 1px solid var(--line); overflow: hidden; }
        .ecg-svg { width: 200%; height: 22px; animation: slideRight 14s linear infinite; }
        @keyframes slideRight { 0% { transform: translateX(-50%); } 100% { transform: translateX(0%); } }

        .center-wrap { flex: 1; display: flex; align-items: center; justify-content: center; padding: 28px 20px; }
        .auth-box {
            background: #ffffff;
            width: 100%;
            max-width: 460px;
            border: 1.5px solid var(--ink);
        }
        .auth-head {
            padding: 20px 26px;
            background: #f1f5f9;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .auth-body { padding: 26px; }
        h1 { margin: 0; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }
        label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #334155; margin-top: 14px; }
        input {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid var(--line);
            font-size: 14px;
        }
        input:focus { outline: none; border-color: var(--ink); box-shadow: inset 0 0 0 1px var(--ink); }
        .btn-login {
            width: 100%;
            background: var(--ink);
            color: #ffffff;
            border: none;
            padding: 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-login:hover { background: #1e293b; }
        .acc-section { margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--line); }
        .acc-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; display: block; margin-bottom: 10px; }
        .acc-list { display: flex; flex-direction: column; gap: 8px; }
        .acc-item {
            display: flex; justify-content: space-between; align-items: center;
            width: 100%;
            background: var(--paper);
            border: 1px solid var(--line);
            padding: 10px 12px;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.15s;
        }
        .acc-item:hover { border-color: var(--ink); background: #f1f5f9; }
        .tag { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 3px 8px; border: 1px solid var(--line); background: #ffffff; }
        .tag.op { border-color: var(--gold); color: var(--gold); }
        .alert { background: #fef2f2; color: #991b1b; border-left: 4px solid #b91c1c; padding: 10px 12px; font-size: 13px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <header class="top-strip">
        <strong style="font-size:15px; letter-spacing:0.05em; text-transform:uppercase;">AntriSehat &mdash; Portal Staf</strong>
        <a href="/" style="color:#cbd5e1; text-decoration:none; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">&larr; Layanan Pasien</a>
    </header>
    <div class="ecg-bar" aria-hidden="true">
        <svg class="ecg-svg" viewBox="0 0 1200 22" preserveAspectRatio="none">
            <path d="M0,11 L260,11 L272,11 L278,3 L285,19 L292,5 L298,14 L304,11 L580,11 L592,11 L598,3 L605,19 L612,5 L618,14 L624,11 L880,11 L892,11 L898,3 L905,19 L912,5 L918,14 L924,11 L1200,11"
                  fill="none" stroke="#047857" stroke-width="1.3" stroke-opacity="0.45"/>
        </svg>
    </div>

    <div class="center-wrap">
        <div class="auth-box">
            <div class="auth-head">
                <h1>Otorisasi Masuk</h1>
                <span style="font-family:ui-monospace, monospace; font-size:11px; color:var(--gold); font-weight:800;">ROLE ACCESS</span>
            </div>
            <div class="auth-body">
                @if(session('error'))
                    <div class="alert">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="/login">
                    @csrf
                    <label>Email Staf</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@antrisehat.id" required>

                    <label>Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>

                    <button type="submit" class="btn-login">Masuk ke Sistem</button>
                </form>

                <div class="acc-section">
                    <span class="acc-title">Akun Staf Terdaftar (Klik untuk Mengisi)</span>
                    <div class="acc-list">
                        @foreach($doctorAccounts as $acc)
                            <button type="button" class="acc-item" onclick="fillDemo('{{ $acc->email }}', 'dokter123')">
                                <div>
                                    <strong style="font-size:13px; display:block;">{{ $acc->name }}</strong>
                                    <span style="font-size:11px; color:#64748b; font-family:ui-monospace, monospace;">{{ $acc->email }}</span>
                                </div>
                                <span class="tag">{{ $acc->doctor ? $acc->doctor->poli : 'Dokter' }}</span>
                            </button>
                        @endforeach

                        @foreach($operatorAccounts as $op)
                            <button type="button" class="acc-item" onclick="fillDemo('{{ $op->email }}', 'operator123')">
                                <div>
                                    <strong style="font-size:13px; display:block;">{{ $op->name }}</strong>
                                    <span style="font-size:11px; color:#64748b; font-family:ui-monospace, monospace;">{{ $op->email }}</span>
                                </div>
                                <span class="tag op">Operator Admin</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
