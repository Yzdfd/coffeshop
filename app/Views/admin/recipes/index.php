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
            <div>
                <h6 class="mb-0 fw-bold">Manajemen Resep Menu</h6>
                <small class="text-muted">Pilih menu kemudian atur bahan-bahan yang digunakan.</small>
            </div>
            <form method="get" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($search ?? '') ?>" placeholder="Cari nama menu...">
                <select name="category_id" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Kategori</option>
                    <?php foreach (($kategoris ?? []) as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filterKategori ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc((string) ($k['name'] ?? '')) ?>
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
                        <th>Harga</th>
                        <th>Status</th>
                        <th style="width: 140px;">Resep</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($menus)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada menu tersedia.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($menus as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc((string) ($m['name'] ?? '')) ?></td>
                        <td><?= esc((string) ($m['nama_kategori'] ?? '-')) ?></td>
                        <td>Rp <?= number_format($m['price'], 0, ',', '.') ?></td>
                        <td>
                            <?php if (($m['status'] ?? '') === 'available'): ?>
                                <span class="badge bg-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/resep/menu/' . $m['id']) ?>"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-journal-text me-1"></i> Atur Resep
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

