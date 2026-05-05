<?= $this->include('kasir/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">📋</div>
                <div>
                    <div class="text-muted small">Pesanan Belum Dibayar</div>
                    <div class="fw-bold fs-4"><?= $totalPesananAktif ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger fs-3">❌</div>
                <div>
                    <div class="text-muted small">Dibatalkan Hari Ini</div>
                    <div class="fw-bold fs-4"><?= $totalDibatalkan ?? 0 ?></div>
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
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($pendapatanHari ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Belum Dibayar -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-2"></i>Pesanan Belum Dibayar</span>
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
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pesananAktif)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada pesanan menunggu.</td></tr>
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
                        <td><?= date('H:i', strtotime($p['ordered_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('kasir/pesanan/detail/' . $p['id']) ?>"
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="<?= base_url('kasir/pembayaran/' . $p['id']) ?>"
                               class="btn btn-success btn-sm">
                                <i class="bi bi-cash-coin"></i> Bayar
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
