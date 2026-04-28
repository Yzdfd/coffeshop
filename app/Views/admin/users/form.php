<?= $this->include('admin/layouts/header') ?>

<div class="card" style="max-width:560px">
    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid">

            <div class="form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="nama_lengkap"
                       value="<?= old('nama_lengkap', $user['nama_lengkap'] ?? '') ?>" required>
                <?php if (isset($errors['nama_lengkap'])): ?>
                    <small style="color:red"><?= $errors['nama_lengkap'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Username <span style="color:red">*</span></label>
                <input type="text" name="username"
                       value="<?= old('username', $user['username'] ?? '') ?>" required>
                <?php if (isset($errors['username'])): ?>
                    <small style="color:red"><?= $errors['username'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Role <span style="color:red">*</span></label>
                <select name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach (['admin','waiter','kasir','dapur','owner'] as $r): ?>
                        <option value="<?= $r ?>" <?= old('role', $user['role'] ?? '') == $r ? 'selected' : '' ?>>
                            <?= ucfirst($r) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="aktif"    <?= old('status', $user['status'] ?? 'aktif') == 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= old('status', $user['status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>

            <?php if (!isset($user)): // hanya saat create ?>
            <div class="form-group">
                <label>Password <span style="color:red">*</span></label>
                <input type="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <small style="color:red"><?= $errors['password'] ?></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password <span style="color:red">*</span></label>
                <input type="password" name="password_confirm" required>
            </div>
            <?php else: ?>
            <div class="form-group full">
                <div class="alert alert-warning" style="margin:0">
                    ℹ️ Untuk ganti password, gunakan fitur <strong>Reset Password</strong> di tabel user.
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>
