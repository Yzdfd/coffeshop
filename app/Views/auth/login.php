<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - Café System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cafe-brown   : #6F4E37;
            --cafe-latte   : #C4956A;
            --cafe-cream   : #FFF8F0;
            --cafe-dark    : #2C1810;
            --cafe-accent  : #E8C49A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--cafe-dark) 0%, var(--cafe-brown) 50%, var(--cafe-latte) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        body::before { width: 500px; height: 500px; top: -150px; left: -150px; }
        body::after  { width: 400px; height: 400px; bottom: -100px; right: -100px; }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            animation: slideUp .4s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo & header */
        .login-logo {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .logo-circle {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--cafe-brown), var(--cafe-latte));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: .75rem;
            box-shadow: 0 8px 20px rgba(111,78,55,.3);
        }
        .login-logo h1 { font-size: 1.4rem; font-weight: 700; color: var(--cafe-dark); }
        .login-logo p  { font-size: .83rem; color: #888; margin-top: .2rem; }

        /* Role badges */
        .role-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: center;
            margin-bottom: 1.75rem;
        }
        .role-badge {
            font-size: .7rem;
            padding: .25rem .65rem;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: .3px;
        }
        .badge-admin  { background:#fff3e0; color:#e65100; }
        .badge-owner  { background:#e8f5e9; color:#2e7d32; }
        .badge-kasir  { background:#e3f2fd; color:#1565c0; }
        .badge-waiter { background:#fce4ec; color:#880e4f; }
        .badge-dapur  { background:#f3e5f5; color:#6a1b9a; }

        /* Form */
        .form-label { font-size: .82rem; font-weight: 600; color: #444; margin-bottom: .35rem; }
        .form-control {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--cafe-brown);
            box-shadow: 0 0 0 3px rgba(111,78,55,.12);
        }
        .input-icon-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #bbb;
            font-size: 1rem;
            pointer-events: none;
        }
        .has-icon { padding-left: 2.6rem !important; }

        /* Toggle password */
        .toggle-pass {
            position: absolute;
            right: .9rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #bbb;
            font-size: 1rem;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
        .toggle-pass:hover { color: var(--cafe-brown); }

        /* Alerts */
        .alert {
            border-radius: 10px;
            font-size: .85rem;
            padding: .75rem 1rem;
            border: none;
            margin-bottom: 1.2rem;
        }
        .alert-danger  { background: #fdecea; color: #c62828; }
        .alert-success { background: #e8f5e9; color: #2e7d32; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: .75rem;
            background: linear-gradient(135deg, var(--cafe-brown), var(--cafe-latte));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            margin-top: .25rem;
            letter-spacing: .3px;
        }
        .btn-login:hover   { opacity: .92; transform: translateY(-1px); }
        .btn-login:active  { transform: translateY(0); }
        .btn-login:disabled{ opacity: .6; cursor: not-allowed; }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .78rem;
            color: #999;
        }

        /* Validation error */
        .invalid-feedback { font-size: .78rem; }
        .is-invalid { border-color: #e53935 !important; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">
            <div class="logo-circle">☕</div>
            <h1>Café System</h1>
            <p>Masuk ke panel sesuai role Anda</p>
        </div>

        <!-- Role badges info -->
        <div class="role-badges">
            <span class="role-badge badge-admin">Admin</span>
            <span class="role-badge badge-owner">Owner</span>
            <span class="role-badge badge-kasir">Kasir</span>
            <span class="role-badge badge-waiter">Waiter</span>
            <span class="role-badge badge-dapur">Dapur</span>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                ⚠️ <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                ✅ <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <strong>Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="<?= base_url('login/process') ?>" method="POST" id="loginForm">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control has-icon <?= (old('username') === null && isset($errors['username'])) ? 'is-invalid' : '' ?>"
                        placeholder="Masukkan username"
                        value="<?= esc(old('username')) ?>"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control has-icon"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                        style="padding-right: 2.8rem;"
                    >
                    <button type="button" class="toggle-pass" id="togglePass" title="Tampilkan/sembunyikan password">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                Masuk ke Sistem
            </button>
        </form>

        <div class="login-footer">
            &copy; <?= date('Y') ?> Café System &mdash; Sistem Informasi Café
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePass').addEventListener('click', function () {
        const input = document.getElementById('password');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        this.textContent = isHidden ? '🙈' : '👁️';
    });

    // Disable button on submit to prevent double click
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
    });
</script>

</body>
</html>
