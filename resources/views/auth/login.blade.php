<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staf — AntriSehat</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .icon { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .card {
            background: #ffffff;
            width: 100%;
            max-width: 410px;
            border-radius: 14px;
            padding: 32px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }
        .header-mark {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: #0f172a;
            color: #2dd4bf;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        h1 { margin: 0 0 6px 0; font-size: 20px; font-weight: 700; letter-spacing: -0.02em; }
        p.sub { margin: 0 0 22px 0; font-size: 13px; color: #64748b; }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-top: 14px; }
        input {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: #0f172a; box-shadow: 0 0 0 3px rgba(15,23,42,0.08); }
        .btn-submit {
            width: 100%;
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 11px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-submit:hover { background: #1e293b; }
        .demo-section { margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; }
        .demo-title { font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 10px; }
        .demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-demo {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 9px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-demo:hover { background: #f1f5f9; border-color: #94a3b8; }
        .alert { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #475569; text-decoration: none; font-weight: 500; margin-top: 18px; }
        .back-link:hover { color: #0f172a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header-mark">
            <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        </div>
        <h1>Login Staf Klinik</h1>
        <p class="sub">Masuk menggunakan kredensial Dokter Poli atau Operator Admin.</p>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <label>Alamat Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@antrisehat.id" required>

            <label>Kata Sandi</label>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>

        <div class="demo-section">
            <span class="demo-title">Isi Otomatis Akun Pengujian</span>
            <div class="demo-grid">
                <button type="button" class="btn-demo" onclick="fillDemo('dokter@antrisehat.id', 'dokter123')">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Akun Dokter</span>
                </button>
                <button type="button" class="btn-demo" onclick="fillDemo('operator@antrisehat.id', 'operator123')">
                    <svg class="icon" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    <span>Akun Operator</span>
                </button>
            </div>
            <div style="text-align:center;">
                <a href="/" class="back-link">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Kembali ke Portal Pasien</span>
                </a>
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
