<?php
/**
 * ============================================================
 *  DEFAULT USERS SEEDER — Café System
 * ============================================================
 *  Cara pakai:
 *    1. Sesuaikan konfigurasi database di bawah
 *    2. Jalankan lewat terminal:  php seeder_users.php
 *    3. Hapus file ini setelah selesai (jangan dibiarkan di server)
 * ============================================================
 */

// ─── Konfigurasi Database ────────────────────────────────────
$host   = '127.0.0.1';
$port   = '3306';
$dbname = 'caffeshop';
$dbuser = 'root';
$dbpass = '';
// ────────────────────────────────────────────────────────────

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $dbuser,
        $dbpass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("❌ Gagal koneksi database: " . $e->getMessage() . "\n");
}

// ─── Data Default Users ──────────────────────────────────────
$users = [
    [
        'name'     => 'Administrator',
        'username' => 'admin',
        'password' => 'admin123',
        'role'     => 'admin',
        'shift'    => 'pagi',
        'status'   => 'active',
    ],
    [
        'name'     => 'Pemilik Café',
        'username' => 'owner',
        'password' => 'owner123',
        'role'     => 'owner',
        'shift'    => 'pagi',
        'status'   => 'active',
    ],
    [
        'name'     => 'Kasir Utama',
        'username' => 'kasir',
        'password' => 'kasir123',
        'role'     => 'kasir',
        'shift'    => 'pagi',
        'status'   => 'active',
    ],
    [
        'name'     => 'Waiter Satu',
        'username' => 'waiter',
        'password' => 'waiter123',
        'role'     => 'waiter',
        'shift'    => 'pagi',
        'status'   => 'active',
    ],
    [
        'name'     => 'Staff Dapur',
        'username' => 'dapur',
        'password' => 'dapur123',
        'role'     => 'dapur',
        'shift'    => 'pagi',
        'status'   => 'active',
    ],
];

$stmt = $pdo->prepare("
    INSERT INTO users (name, username, password_hash, role, shift, status)
    VALUES (:name, :username, :password_hash, :role, :shift, :status)
    ON DUPLICATE KEY UPDATE
        password_hash = VALUES(password_hash),
        status        = VALUES(status)
");

echo "\n🌱 Menambahkan default users...\n\n";

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt->execute([
        ':name'          => $user['name'],
        ':username'      => $user['username'],
        ':password_hash' => $hash,
        ':role'          => $user['role'],
        ':shift'         => $user['shift'],
        ':status'        => $user['status'],
    ]);
    echo "  ✅ [{$user['role']}] username: {$user['username']}  |  password: {$user['password']}\n";
}

echo "\n✅ Seeder selesai! Semua default user berhasil dibuat.\n";
echo "⚠️  PENTING: Ganti password default setelah login pertama!\n";
echo "⚠️  Hapus file seeder_users.php dari server setelah dipakai!\n\n";
