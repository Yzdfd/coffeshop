<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus me-1"></i> Tambah User
            </a>
            <form method="get" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm" value="<?= esc($search ?? '') ?>"
                    placeholder="Cari nama / username...">
                <select name="role" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Role</option>
                    <?php foreach (['admin','waiter','kasir','dapur','owner'] as $r): ?>
                    <option value="<?= $r ?>" <?= ($filterRole ?? '') == $r ? 'selected' : '' ?>>
                        <?= ucfirst($r) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Shift</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada user.</td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($users as $u): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($u['name']) ?></td>
                        <td><?= esc($u['username']) ?></td>
                        <td>
                            <?php
                            $roleColors = [
                                'admin'  => 'bg-danger',
                                'owner'  => 'bg-warning text-dark',
                                'kasir'  => 'bg-info text-dark',
                                'waiter' => 'bg-success',
                                'dapur'  => 'bg-secondary',
                            ];
                            $cls = $roleColors[$u['role']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $cls ?>"><?= ucfirst($u['role']) ?></span>
                        </td>
                        <td><?= esc($u['shift'] ?? '—') ?></td>
                        <td>
                            <?php if ($u['status'] == 'aktif'): ?>
                            <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('admin/users/reset-password/' . $u['id']) ?>"
                                class="btn btn-warning btn-sm"
                                onclick="return confirm('Reset password user ini ke: password123?')">
                                <i class="bi bi-key"></i>
                            </a>
                            <?php if ($u['id'] != session('user_id')): ?>
                            <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus user ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>