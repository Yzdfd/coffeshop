<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="action-bar">
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">+ Tambah User</a>
        <div class="search-box">
            <form method="get">
                <div style="display:flex;gap:8px">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Cari nama / username...">
                    <select name="role">
                        <option value="">Semua Role</option>
                        <?php foreach (['admin','waiter','kasir','dapur','owner'] as $r): ?>
                            <option value="<?= $r ?>" <?= ($filterRole ?? '') == $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="7" style="text-align:center;color:#aaa;padding:30px">Tidak ada user.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($users as $u): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($u['nama_lengkap']) ?></td>
                    <td><?= esc($u['username']) ?></td>
                    <td>
                        <?php
                        $roleColors = [
                            'admin'  => 'badge-danger',
                            'owner'  => 'badge-warning',
                            'kasir'  => 'badge-info',
                            'waiter' => 'badge-success',
                            'dapur'  => 'badge-secondary',
                        ];
                        $cls = $roleColors[$u['role']] ?? 'badge-secondary';
                        ?>
                        <span class="badge <?= $cls ?>"><?= ucfirst($u['role']) ?></span>
                    </td>
                    <td>
                        <?php if ($u['status'] == 'aktif'): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="<?= base_url('admin/users/reset-password/' . $u['id']) ?>"
                               class="btn btn-primary btn-sm"
                               onclick="return confirm('Reset password user ini?')">Reset PW</a>
                            <?php if ($u['id'] != session('user_id')): ?>
                            <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus user ini?')">Hapus</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
