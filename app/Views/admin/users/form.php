<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-person me-2"></i><?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= old('name', $user['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                           value="<?= old('username', $user['username'] ?? '') ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?= $errors['username'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach (['admin','waiter','kasir','dapur','owner'] as $r): ?>
                            <option value="<?= $r ?>" <?= old('role', $user['role'] ?? '') == $r ? 'selected' : '' ?>>
                                <?= ucfirst($r) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Shift</label>
                    <select name="shift" class="form-select">
                        <option value="">-- Pilih Shift --</option>
                        <?php foreach (['Pagi','Siang','Malam'] as $sh): ?>
                            <option value="<?= $sh ?>" <?= old('shift', $user['shift'] ?? '') == $sh ? 'selected' : '' ?>>
                                <?= $sh ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="aktif"    <?= old('status', $user['status'] ?? 'aktif') == 'aktif'    ? 'selected' : '' ?>>Aktif</option>
<option value="nonaktif" <?= old('status', $user['status'] ?? '') == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <?php if (! isset($user)): ?>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Untuk ganti password, gunakan tombol <strong>Reset Password</strong> di halaman daftar user.
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
