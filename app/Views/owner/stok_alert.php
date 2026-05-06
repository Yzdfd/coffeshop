<?= $this->include('kasir/layouts/header') ?>

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= base_url('owner') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
    <div>
        <div class="text-muted small">Monitoring Stok</div>
        <div class="fw-semibold">Alert Stok Bahan Kritis</div>
    </div>
</div>

<!-- Bahan Kritis / Hampir Habis -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold text-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>Bahan di Bawah Stok Minimum
        <span class="badge bg-danger ms-2"><?= count($bahanKritis) ?></span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($bahanKritis)): ?>
            <div class="text-center text-muted py-5">
                <div class="fs-3 mb-2">✅</div>
                Semua stok bahan dalam kondisi aman.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bahan</th>
                            <th style="width:140px">Stok Saat Ini</th>
                            <th style="width:140px">Stok Minimum</th>
                            <th style="width:120px">Selisih</th>
                            <th>Supplier</th>
                            <th style="width:100px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bahanKritis as $b): ?>
                        <?php $selisih = (float)$b['stock_qty'] - (float)$b['min_stock']; ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($b['name']) ?></td>
                            <td>
                                <span class="badge bg-danger">
                                    <?= (float)$b['stock_qty'] ?> <?= esc($b['unit']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= (float)$b['min_stock'] ?> <?= esc($b['unit']) ?></td>
                            <td>
                                <span class="text-danger fw-semibold"><?= number_format($selisih, 2) ?></span>
                            </td>
                            <td class="text-muted small"><?= esc($b['supplier_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($b['stock_qty'] <= 0): ?>
                                    <span class="badge bg-dark">Habis</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Kritis</span>
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

<!-- Semua Bahan -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-boxes me-2"></i>Semua Bahan Baku
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bahan</th>
                        <th style="width:140px">Stok</th>
                        <th style="width:140px">Stok Min</th>
                        <th>Supplier</th>
                        <th style="width:100px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allBahan)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data bahan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($allBahan as $b): ?>
                        <?php
                            $habis   = ($b['stock_qty'] <= 0);
                            $kritis  = (!$habis && $b['stock_qty'] <= $b['min_stock']);
                            $aman    = (!$habis && !$kritis);
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($b['name']) ?></td>
                            <td>
                                <span class="badge <?= $habis ? 'bg-dark' : ($kritis ? 'bg-danger' : 'bg-success') ?>">
                                    <?= (float)$b['stock_qty'] ?> <?= esc($b['unit']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= (float)$b['min_stock'] ?> <?= esc($b['unit']) ?></td>
                            <td class="text-muted small"><?= esc($b['supplier_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($habis): ?>
                                    <span class="badge bg-dark">Habis</span>
                                <?php elseif ($kritis): ?>
                                    <span class="badge bg-danger">Kritis</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Aman</span>
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

<?= $this->include('kasir/layouts/footer') ?>
