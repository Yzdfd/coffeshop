<?= $this->include('/kasir/layouts/header') ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">📋</div>
                <div>
                    <div class="text-muted small">Pesanan Aktif</div>
                    <div class="fw-bold fs-4"><?= $totalPesananAktif ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-3">🍳</div>
                <div>
                    <div class="text-muted small">Sedang Diproses</div>
                    <div class="fw-bold fs-4"><?= $totalDiproses ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3">💰</div>
                <div>
                    <div class="text-muted small">Transaksi Hari Ini</div>
                    <div class="fw-bold fs-4"><?= $totalTransaksiHari ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info fs-3">💵</div>
                <div>
                    <div class="text-muted small">Pendapatan Hari Ini</div>
                    <div class="fw-bold" style="font-size:16px">Rp
                        <?= number_format($pendapatanHari ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Aktif -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-2"></i>Pesanan Aktif Hari Ini</span>
        <a href="<?= base_url('kasir/pesanan/buat') ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#Order</th>
                        <th>Meja</th>
                        <th>Kasir</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesananAktif)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada pesanan aktif.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pesananAktif as $p): ?>
                    <tr>
                        <td><strong>#<?= $p['id'] ?></strong></td>
                        <td>
                            <?php if ($p['table_id']): ?>
                            <span class="badge bg-secondary">Meja <?= $p['table_number'] ?? $p['table_id'] ?></span>
                            <?php else: ?>
                            <span class="text-muted">Takeaway</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($p['kasir_name'] ?? '-') ?></td>
                        <td>
                            <?php
                            $statusClass = [
                                'open'      => 'status-open',
                                'process'   => 'status-process',
                                'ready'     => 'status-ready',
                                'paid'      => 'status-paid',
                                'cancelled' => 'status-cancelled',
                            ][$p['status']] ?? 'bg-secondary text-white';
                            $statusLabel = [
                                'open'      => 'Buka',
                                'process'   => 'Diproses',
                                'ready'     => 'Siap',
                                'paid'      => 'Dibayar',
                                'cancelled' => 'Dibatalkan',
                            ][$p['status']] ?? $p['status'];
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                        </td>
                        <td><?= date('H:i', strtotime($p['ordered_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('kasir/pesanan/detail/' . $p['id']) ?>"
                                class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <?php if ($p['status'] == 'ready'): ?>
                            <a href="<?= base_url('kasir/pembayaran/' . $p['id']) ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-cash-coin"></i> Bayar
                            </a>
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

<?= $this->include('/kasir/layouts/footer') ?>