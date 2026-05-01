<?= $this->include('kasir/layouts/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2"></i> Pembayaran</h5>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Belum ada pesanan yang siap dibayar.
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($orders as $order): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Meja <?= esc($order['table_number'] ?? '-') ?></span>
                    <span class="badge bg-success">Siap Bayar</span>
                </div>
                <div class="card-body">
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-hash me-1"></i> Order #<?= $order['id'] ?>
                    </p>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-person me-1"></i> <?= esc($order['kasir_name'] ?? '-') ?>
                    </p>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($order['ordered_at'])) ?>
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="<?= base_url('kasir/pembayaran/form/' . $order['id']) ?>"
                       class="btn btn-primary w-100 btn-sm">
                        <i class="bi bi-cash me-1"></i> Proses Pembayaran
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->include('kasir/layouts/footer') ?>
