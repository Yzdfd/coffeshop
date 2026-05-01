<?= $this->include('admin/layouts/header') ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-3">🍽️</div>
                <div>
                    <div class="text-muted small">Total Menu</div>
                    <div class="fw-bold fs-4"><?= $totalMenu ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">🗂️</div>
                <div>
                    <div class="text-muted small">Kategori</div>
                    <div class="fw-bold fs-4"><?= $totalKategori ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3">👥</div>
                <div>
                    <div class="text-muted small">Total User</div>
                    <div class="fw-bold fs-4"><?= $totalUser ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger fs-3">⚠️</div>
                <div>
                    <div class="text-muted small">Stok Hampir Habis</div>
                    <div class="fw-bold fs-4"><?= $stokRendah ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Peringatan Stok -->
<?php if (!empty($stokPeringatan)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Peringatan Stok Bahan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Stok Saat Ini</th>
                        <th>Satuan</th>
                        <th>Min. Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stokPeringatan as $s): ?>
                    <tr>
                        <td><?= esc($s['name']) ?></td>
                        <td class="<?= $s['stock'] == 0 ? 'stok-out' : 'stok-low' ?>"><?= $s['stock'] ?></td>
                        <td><?= esc($s['unit']) ?></td>
                        <td><?= $s['min_stock'] ?></td>
                        <td>
                            <?php if ($s['stock'] == 0): ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Hampir Habis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->include('admin/layouts/footer') ?>
