<?= $this->include('kasir/layouts/header') ?>

<!-- Ringkasan Hari Ini -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3">💰</div>
                <div>
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fw-bold fs-4"><?= $totalTrx ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">💵</div>
                <div>
                    <div class="text-muted small">Pendapatan</div>
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($totalPendapatan ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-3">🏦</div>
                <div>
                    <div class="text-muted small">Cash</div>
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($totalCash ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info fs-3">📱</div>
                <div>
                    <div class="text-muted small">Non-Cash</div>
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($totalNonCash ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="fw-semibold"><i class="bi bi-receipt me-2"></i>Riwayat Transaksi</span>
            <form method="get" class="d-flex gap-2 flex-wrap">
                <input type="date" name="tanggal" class="form-control form-control-sm"
                       value="<?= $tanggal ?? date('Y-m-d') ?>">
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#Trx</th>
                        <th>#Order</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Subtotal</th>
                        <th>Diskon</th>
                        <th>Total</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksis)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada transaksi.</td></tr>
                    <?php else: ?>
                    <?php foreach ($transaksis as $t): ?>
                    <tr>
                        <td><strong>#<?= $t['id'] ?></strong></td>
                        <td><a href="<?= base_url('kasir/pesanan/detail/' . $t['order_id']) ?>">#<?= $t['order_id'] ?></a></td>
                        <td><?= esc($t['kasir_name'] ?? '-') ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($t['payment_method']) ?></span></td>
                        <td>Rp <?= number_format($t['subtotal'], 0, ',', '.') ?></td>
                        <td class="text-danger">
                            <?= $t['discount_amount'] > 0 ? '- Rp ' . number_format($t['discount_amount'], 0, ',', '.') : '—' ?>
                        </td>
                        <td class="fw-bold text-success">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
                        <td><?= date('H:i', strtotime($t['paid_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('kasir/transaksi/struk/' . $t['id']) ?>"
                               class="btn btn-sm btn-outline-primary" title="Cetak Struk">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="<?= base_url('kasir/transaksi/void/' . $t['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Void / Refund"
                               onclick="return confirm('Void transaksi ini?')">
                                <i class="bi bi-arrow-counterclockwise"></i>
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

<?= $this->include('kasir/layouts/footer') ?>
