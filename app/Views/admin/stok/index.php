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
            <a href="<?= base_url('admin/stok/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Bahan
            </a>
            <form method="get" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($search ?? '') ?>" placeholder="Cari nama bahan...">
                <select name="filter" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Status</option>
                    <option value="ok"    <?= ($filter ?? '') == 'ok'    ? 'selected' : '' ?>>Stok Aman</option>
                    <option value="low"   <?= ($filter ?? '') == 'low'   ? 'selected' : '' ?>>Hampir Habis</option>
                    <option value="empty" <?= ($filter ?? '') == 'empty' ? 'selected' : '' ?>>Habis</option>
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
                        <th>Nama Bahan</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Min. Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stoks)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data bahan.</td></tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($stoks as $s):
                        if ($s['stock_qty'] <= 0) {
                            $statusClass = 'stok-out';
                            $badge = '<span class="badge bg-danger">Habis</span>';
                        } elseif ($s['stock_qty'] <= $s['min_stock']) {
                            $statusClass = 'stok-low';
                            $badge = '<span class="badge bg-warning text-dark">Hampir Habis</span>';
                        } else {
                            $statusClass = 'stok-ok';
                            $badge = '<span class="badge bg-success">Aman</span>';
                        }
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($s['name']) ?></td>
                        <td class="<?= $statusClass ?>"><strong><?= $s['stock_qty'] ?></strong></td>
                        <td><?= esc($s['unit']) ?></td>
                        <td><?= $s['min_stock'] ?></td>
                        <td><?= $badge ?></td>
                        <td>
                            <a href="<?= base_url('admin/stok/tambah/' . $s['id']) ?>"
                               class="btn btn-success btn-sm" title="Tambah Stok">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                            <a href="<?= base_url('admin/stok/edit/' . $s['id']) ?>"
                               class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('admin/stok/delete/' . $s['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus bahan ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
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
