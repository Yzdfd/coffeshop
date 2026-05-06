<?= $this->include('kasir/layouts/header') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="text-muted small">Dapur</div>
        <div class="fw-semibold">Stok Bahan Baku</div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari bahan..." value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-auto">
                <select name="filter" class="form-select form-select-sm">
                    <option value="">Semua Stok</option>
                    <option value="ok"    <?= ($filter === 'ok')    ? 'selected' : '' ?>>Stok Aman</option>
                    <option value="low"   <?= ($filter === 'low')   ? 'selected' : '' ?>>Stok Menipis</option>
                    <option value="empty" <?= ($filter === 'empty') ? 'selected' : '' ?>>Habis</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="<?= base_url('dapur/stok') ?>" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Stok Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($stoks)): ?>
            <div class="text-center text-muted py-5">
                <div class="fs-2 mb-2">📦</div>
                Tidak ada data bahan ditemukan.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Bahan</th>
                            <th style="width:140px">Stok Saat Ini</th>
                            <th style="width:140px">Stok Minimum</th>
                            <th style="width:120px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stoks as $s): ?>
                        <?php
                            $qty    = (float) $s['stock_qty'];
                            $min    = (float) ($s['min_stock'] ?? 0);
                            $unit   = esc($s['unit'] ?? '');
                            if ($qty <= 0) {
                                $statusClass = 'bg-danger';
                                $statusLabel = '<i class="bi bi-x-circle me-1"></i>Habis';
                            } elseif ($qty <= $min) {
                                $statusClass = 'bg-warning text-dark';
                                $statusLabel = '<i class="bi bi-exclamation-triangle me-1"></i>Menipis';
                            } else {
                                $statusClass = 'bg-success';
                                $statusLabel = '<i class="bi bi-check-circle me-1"></i>Aman';
                            }
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($s['name']) ?></td>
                            <td>
                                <span class="badge <?= ($qty <= $min) ? ($qty <= 0 ? 'bg-danger' : 'bg-warning text-dark') : 'bg-success' ?>">
                                    <?= $qty ?> <?= $unit ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= $min ?> <?= $unit ?></td>
                            <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('kasir/layouts/footer') ?>
