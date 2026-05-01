<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CaféSystem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --navy: #1a1a2e;
        --navy-deep: #12122a;
        --navy-mid: #2e2e4e;
        --amber: #e8a87c;
        --amber-dim: #c9895c;
        --text: #e0e0f0;
        --text-muted: #8888aa;
        --input-bg: #242440;
        --border: #3a3a5a;
        --error: #e57373;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--navy-deep);
        min-height: 100vh;
        display: flex;
        align-items: stretch;
        overflow: hidden;
    }

    /* ── LEFT PANEL ─────────────────────────────── */
    .panel-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 60px 48px;
        background: var(--navy);
        position: relative;
        overflow: hidden;
    }

    /* decorative rings */
    .ring {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(232, 168, 124, .12);
        pointer-events: none;
    }

    .ring-1 {
        width: 420px;
        height: 420px;
        top: -120px;
        left: -120px;
    }

    .ring-2 {
        width: 260px;
        height: 260px;
        top: 60px;
        left: -30px;
        border-color: rgba(232, 168, 124, .07);
    }

    .ring-3 {
        width: 320px;
        height: 320px;
        bottom: -100px;
        right: -80px;
    }

    .ring-4 {
        width: 160px;
        height: 160px;
        bottom: 40px;
        right: 20px;
        border-color: rgba(232, 168, 124, .07);
    }

    .brand {
        text-align: center;
        z-index: 1;
    }

    .brand-icon {
        font-size: 64px;
        line-height: 1;
        margin-bottom: 20px;
        filter: drop-shadow(0 0 24px rgba(232, 168, 124, .4));
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    .brand-name {
        font-family: 'Playfair Display', serif;
        font-size: 40px;
        color: var(--amber);
        letter-spacing: 1px;
        line-height: 1.1;
    }

    .brand-sub {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
    }

    .divider {
        width: 48px;
        height: 2px;
        background: var(--amber);
        margin: 28px auto;
        opacity: .5;
    }

    .tagline {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.7;
        max-width: 280px;
        text-align: center;
    }

    .role-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-top: 32px;
    }

    .pill {
        padding: 5px 14px;
        border-radius: 999px;
        background: rgba(232, 168, 124, .1);
        border: 1px solid rgba(232, 168, 124, .25);
        color: var(--amber);
        font-size: 12px;
        letter-spacing: .5px;
    }

    /* ── RIGHT PANEL ─────────────────────────────── */
    .panel-right {
        width: 460px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--navy-deep);
        padding: 48px 40px;
    }

    .login-box {
        width: 100%;
        max-width: 380px;
    }

    .login-heading {
        font-family: 'Playfair Display', serif;
        font-size: 30px;
        color: var(--text);
        margin-bottom: 6px;
    }

    .login-sub {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 36px;
    }

    /* ── ALERT ───────────────────────────────────── */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-error {
        background: rgba(229, 115, 115, .12);
        border: 1px solid rgba(229, 115, 115, .3);
        color: var(--error);
    }

    .alert-success {
        background: rgba(102, 187, 106, .12);
        border: 1px solid rgba(102, 187, 106, .3);
        color: #81c784;
    }

    /* ── FORM ────────────────────────────────────── */
    .field {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
        opacity: .6;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px 14px 12px 42px;
        background: var(--input-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        border-color: var(--amber);
        box-shadow: 0 0 0 3px rgba(232, 168, 124, .15);
    }

    input::placeholder {
        color: var(--text-muted);
        opacity: .7;
    }

    .toggle-pass {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        opacity: .5;
        transition: opacity .2s;
    }

    .toggle-pass:hover {
        opacity: 1;
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        margin-top: 8px;
        background: var(--amber);
        color: var(--navy-deep);
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        font-weight: 500;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        letter-spacing: .5px;
        transition: background .2s, transform .1s, box-shadow .2s;
        box-shadow: 0 4px 20px rgba(232, 168, 124, .25);
    }

    .btn-login:hover {
        background: var(--amber-dim);
        box-shadow: 0 6px 28px rgba(232, 168, 124, .35);
    }

    .btn-login:active {
        transform: scale(.98);
    }

    .btn-login:disabled {
        opacity: .7;
        cursor: not-allowed;
    }

    .footer-note {
        margin-top: 32px;
        text-align: center;
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ── RESPONSIVE ──────────────────────────────── */
    @media (max-width: 768px) {
        body {
            flex-direction: column;
        }

        .panel-left {
            padding: 48px 32px;
            flex: 0 0 auto;
        }

        .panel-right {
            width: 100%;
            padding: 40px 24px;
        }

        .brand-name {
            font-size: 32px;
        }
    }

    /* ── LOADING SPINNER ─────────────────────────── */
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(26, 26, 46, .4);
        border-top-color: var(--navy-deep);
        border-radius: 50%;
        animation: spin .6s linear infinite;
        vertical-align: middle;
        margin-right: 6px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body>

    <!-- ── LEFT BRAND PANEL ── -->
    <div class="panel-left">
        <div class="ring ring-1"></div>
        <div class="ring ring-2"></div>
        <div class="ring ring-3"></div>
        <div class="ring ring-4"></div>

        <div class="brand">
            <div class="brand-icon">☕</div>
            <div class="brand-name">CaféSystem</div>
            <div class="brand-sub">Point of Sale</div>
            <div class="divider"></div>
            <p class="tagline">
                Sistem manajemen café terpadu — dari meja ke dapur hingga laporan owner.
            </p>
            <div class="role-pills">
                <span class="pill">👨‍💼 Admin</span>
                <span class="pill">🧾 Kasir</span>
                <span class="pill">🍽️ Waiter</span>
                <span class="pill">🍳 Dapur</span>
                <span class="pill">📊 Owner</span>
            </div>
        </div>
    </div>

    <!-- ── RIGHT LOGIN PANEL ── -->
    <div class="panel-right">
        <div class="login-box">

            <h1 class="login-heading">Selamat Datang</h1>
            <p class="login-sub">Masuk untuk melanjutkan ke sistem</p>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
                <?= csrf_field() ?>

                <div class="field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" placeholder="Masukkan username"
                            value="<?= esc(old('username')) ?>" autocomplete="username" required>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password"
                            autocomplete="current-password" required>
                        <button type="button" class="toggle-pass" id="togglePass"
                            title="Tampilkan / sembunyikan password">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    Masuk
                </button>
            </form>

            <div class="footer-note">
                © <?= date('Y') ?> CaféSystem · Hak akses dikelola oleh Admin
            </div>

        </div>
    </div>

    <script>
    // Toggle password visibility
    document.getElementById('togglePass').addEventListener('click', function() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.textContent = input.type === 'password' ? '👁️' : '🙈';
    });

    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnLogin');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Memverifikasi…';
    });
    </script>

</body>

</html>