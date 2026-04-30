<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="action-bar">
        <a href="<?= base_url('admin/stok/create') ?>" class="btn btn-primary">+ Tambah Bahan</a>
        <div class="search-box">
            <form method="get">
                <div style="display:flex;gap:8px">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Cari nama bahan...">
                    <select name="filter">
                        <option value="">Semua Status</option>
                        <option value="ok"    <?= ($filter ?? '') == 'ok'    ? 'selected' : '' ?>>Stok Aman</option>
                        <option value="low"   <?= ($filter ?? '') == 'low'   ? 'selected' : '' ?>>Hampir Habis</option>
                        <option value="empty" <?= ($filter ?? '') == 'empty' ? 'selected' : '' ?>>Habis</option>
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
                    <th>Nama Bahan</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Min. Stok</th>
                    <th>Harga Satuan</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stoks)): ?>
                <tr><td colspan="9" style="text-align:center;color:#aaa;padding:30px">Tidak ada data inventory.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($stoks as $s):
                    if ($s['stock'] <= 0) {
                        $statusClass = 'stok-out';
                        $badge = '<span class="badge badge-danger">Habis</span>';
                    } elseif ($s['stock'] <= $s['min_stock']) {
                        $statusClass = 'stok-low';
                        $badge = '<span class="badge badge-warning">Hampir Habis</span>';
                    } else {
                        $statusClass = 'stok-ok';
                        $badge = '<span class="badge badge-success">Aman</span>';
                    }
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($s['name']) ?></td>
                    <td class="<?= $statusClass ?>"><strong><?= $s['stock'] ?></strong></td>
                    <td><?= esc($s['unit']) ?></td>
                    <td><?= $s['min_stock'] ?></td>
                    <td>Rp <?= number_format($s['price'] ?? 0, 0, ',', '.') ?></td>
                    <td><?= esc($s['notes'] ?? '—') ?></td>
                    <td><?= $badge ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= base_url('admin/stok/tambah/' . $s['id']) ?>" class="btn btn-success btn-sm">+ Stok</a>
                            <a href="<?= base_url('admin/stok/edit/' . $s['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="<?= base_url('admin/stok/delete/' . $s['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus bahan ini?')">Hapus</a>
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
