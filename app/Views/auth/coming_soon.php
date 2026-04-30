<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Coming Soon') ?> - Café System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2C1810, #6F4E37);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            text-align: center;
            padding: 1.5rem;
        }
        .container { max-width: 480px; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: .5rem; }
        p  { font-size: .95rem; opacity: .8; line-height: 1.6; margin-bottom: .5rem; }

        .role-pill {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border-radius: 20px;
            padding: .3rem .9rem;
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,.2);
        }

        .btn-logout {
            display: inline-block;
            margin-top: 2rem;
            padding: .65rem 1.8rem;
            background: rgba(255,255,255,.15);
            border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            transition: background .2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,.25); color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <div class="icon"><?= esc($icon ?? '🚧') ?></div>
    <h1><?= esc($title ?? 'Panel Segera Hadir') ?></h1>
    <div class="role-pill">Role: <?= esc(ucfirst(session('role'))) ?> &mdash; <?= esc(session('name')) ?></div>
    <p><?= esc($message ?? 'Panel untuk role ini sedang dalam pengembangan.') ?></p>
    <p>Tim developer sedang mengerjakan fitur ini. Pantau terus ya!</p>
    <a href="<?= base_url('logout') ?>" class="btn-logout">🚪 Logout</a>
</div>
</body>
</html>
