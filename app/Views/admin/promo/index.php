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
            <a href="<?= base_url('admin/promo/create') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Promo
            </a>
            <form method="get" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Status</option>
                    <option value="active"   <?= ($filterStatus??'')=='active'   ?'selected':'' ?>>Aktif</option>
                    <option value="inactive" <?= ($filterStatus??'')=='inactive' ?'selected':'' ?>>Nonaktif</option>
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
                        <th>Kode Promo</th>
                        <th>Tipe</th>
                        <th>Nilai Diskon</th>
                        <th>Berlaku Dari</th>
                        <th>Berlaku Sampai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promos)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada promo.</td></tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($promos as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="badge bg-dark fs-6 px-3"><?= esc($p['code']) ?></span>
                        </td>
                        <td>
                            <?php if ($p['type'] == 'percent'): ?>
                                <span class="badge bg-info text-dark">Persen (%)</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nominal (Rp)</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold text-success">
                            <?php if ($p['type'] == 'percent'): ?>
                                <?= $p['value'] ?>%
                            <?php else: ?>
                                Rp <?= number_format($p['value'], 0, ',', '.') ?>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($p['valid_from'])) ?></td>
                        <td>
                            <?php
                            $expired = strtotime($p['valid_until']) < time();
                            ?>
                            <span class="<?= $expired ? 'text-danger' : '' ?>">
                                <?= date('d M Y H:i', strtotime($p['valid_until'])) ?>
                                <?= $expired ? '<small>(Kadaluarsa)</small>' : '' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['status'] == 'active'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/promo/edit/' . $p['id']) ?>"
                               class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('admin/promo/toggle/' . $p['id']) ?>"
                               class="btn btn-sm <?= $p['status'] == 'active' ? 'btn-warning' : 'btn-success' ?>">
                                <?php if ($p['status'] == 'active'): ?>
                                    <i class="bi bi-pause-circle"></i>
                                <?php else: ?>
                                    <i class="bi bi-play-circle"></i>
                                <?php endif; ?>
                            </a>
                            <a href="<?= base_url('admin/promo/delete/' . $p['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus promo ini?')">
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
