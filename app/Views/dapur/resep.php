<?= $this->include('kasir/layouts/header') ?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= base_url('dapur') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div>
        <div class="text-muted small">Bahan & Resep</div>
        <div class="fw-semibold"><?= esc($menu['name']) ?></div>
    </div>
</div>

<div class="row g-3">

    <!-- Info Menu -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-cup-hot me-2"></i>Info Menu
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Nama</dt>
                    <dd class="col-7"><?= esc($menu['name']) ?></dd>
                    <dt class="col-5 text-muted small">Harga</dt>
                    <dd class="col-7">Rp <?= number_format($menu['price'] ?? 0, 0, ',', '.') ?></dd>
                    <dt class="col-5 text-muted small">Status</dt>
                    <dd class="col-7">
                        <span class="badge <?= ($menu['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                            <?= esc($menu['status']) ?>
                        </span>
                    </dd>
                    <?php if (!empty($menu['description'])): ?>
                    <dt class="col-5 text-muted small">Deskripsi</dt>
                    <dd class="col-7 small"><?= esc($menu['description']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <?php if (!empty($varian)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-sliders me-2"></i>Varian Menu
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Varian</th>
                            <th style="width:130px">Tambahan Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($varian as $v): ?>
                        <tr>
                            <td><?= esc($v['name']) ?></td>
                            <td class="text-muted small">
                                <?= ($v['price_diff'] != 0) ? '+ Rp ' . number_format($v['price_diff'], 0, ',', '.') : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Resep / Bahan -->
    <div class="col-12 col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-journal-text me-2"></i>Bahan-bahan (Resep)
            </div>
            <div class="card-body p-0">
                <?php if (empty($bahan)): ?>
                    <div class="text-center text-muted py-5">
                        <div class="fs-3 mb-2">📋</div>
                        Belum ada resep/bahan yang terdaftar untuk menu ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Bahan</th>
                                    <th style="width:140px">Qty Dibutuhkan</th>
                                    <th style="width:120px">Stok Saat Ini</th>
                                    <th style="width:100px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bahan as $b): ?>
                                <?php
                                    $stokCukup = ($b['stock_qty'] >= $b['qty_needed']);
                                    $stokKritis = ($b['stock_qty'] <= ($b['min_stock'] ?? 0));
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($b['ingredient_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= (float)$b['qty_needed'] ?> <?= esc($b['unit'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $stokCukup ? ($stokKritis ? 'bg-warning text-dark' : 'bg-success') : 'bg-danger' ?>">
                                            <?= (float)$b['stock_qty'] ?> <?= esc($b['unit'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!$stokCukup): ?>
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Kurang</span>
                                        <?php elseif ($stokKritis): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation me-1"></i>Kritis</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Cukup</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('kasir/layouts/footer') ?>
