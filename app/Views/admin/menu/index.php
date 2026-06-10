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

            <!-- Tombol Tambah + Toggle Status -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="<?= base_url('admin/menu/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Menu
                </a>

                <!-- Toggle Aktif / Non-Aktif -->
                <?php
                    $currentStatus   = $filterStatus ?? 'available';
                    $toggleStatus    = $currentStatus === 'available' ? 'unavailable' : 'available';
                    $toggleLabel     = $currentStatus === 'available' ? 'Lihat Non-Aktif' : 'Lihat Aktif';
                    $toggleIcon      = $currentStatus === 'available' ? 'bi-eye-slash' : 'bi-eye';
                    $toggleClass     = $currentStatus === 'available' ? 'btn-outline-danger' : 'btn-outline-success';
                    $badgeClass      = $currentStatus === 'available' ? 'bg-success' : 'bg-danger';
                    $badgeLabel      = $currentStatus === 'available' ? 'Aktif' : 'Non-Aktif';
                    $toggleUrl       = base_url('admin/menu') . '?status=' . $toggleStatus
                                       . ($search        ? '&search='      . urlencode($search)        : '')
                                       . ($filterKategori ? '&category_id=' . $filterKategori : '');
                ?>
                <span class="badge <?= $badgeClass ?> fs-6 px-3 py-2">
                    <?= $badgeLabel ?>
                </span>
                <a href="<?= $toggleUrl ?>" class="btn <?= $toggleClass ?> btn-sm">
                    <i class="bi <?= $toggleIcon ?> me-1"></i> <?= $toggleLabel ?>
                </a>
            </div>

            <!-- Form Filter Pencarian & Kategori -->
            <form method="get" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($search ?? '') ?>" placeholder="Cari nama menu...">
                <select name="category_id" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filterKategori ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['name']) ?>
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
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>HPP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($menus)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada menu dengan status <strong><?= $currentStatus === 'available' ? 'Aktif' : 'Non-Aktif' ?></strong>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($menus as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($m['name']) ?></td>
                        <td><?= esc($m['nama_kategori'] ?? '-') ?></td>
                        <td>Rp <?= number_format($m['price'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($m['hpp'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($m['status'] == 'available'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/menu/edit/' . $m['id']) ?>"
                               class="btn btn-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('admin/menu/delete/' . $m['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus menu ini?')">
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
