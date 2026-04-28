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
                    <th>Stok Saat Ini</th>
                    <th>Satuan</th>
                    <th>Min. Stok</th>
                    <th>Harga Satuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stoks)): ?>
                <tr><td colspan="8" style="text-align:center;color:#aaa;padding:30px">Tidak ada data stok.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($stoks as $s):
                    $status = '';
                    $badge  = '';
                    if ($s['stok'] <= 0) {
                        $status = 'stok-out'; $badge = '<span class="badge badge-danger">Habis</span>';
                    } elseif ($s['stok'] <= $s['min_stok']) {
                        $status = 'stok-low'; $badge = '<span class="badge badge-warning">Hampir Habis</span>';
                    } else {
                        $status = 'stok-ok'; $badge = '<span class="badge badge-success">Aman</span>';
                    }
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($s['nama_bahan']) ?></td>
                    <td class="<?= $status ?>"><strong><?= $s['stok'] ?></strong></td>
                    <td><?= esc($s['satuan']) ?></td>
                    <td><?= $s['min_stok'] ?></td>
                    <td>Rp <?= number_format($s['harga_satuan'] ?? 0, 0, ',', '.') ?></td>
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
