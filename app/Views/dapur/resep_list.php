<?= $this->include('kasir/layouts/header') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="text-muted small">Dapur</div>
        <div class="fw-semibold">Resep Menu</div>
    </div>
</div>

<?php if (empty($menus)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <div class="fs-2 mb-2">📋</div>
            Belum ada menu aktif terdaftar.
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($menus as $m): ?>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-light rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;">
                        <i class="bi bi-cup-hot fs-4 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate"><?= esc($m['name']) ?></div>
                        <div class="small text-muted">Rp <?= number_format($m['price'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <a href="<?= base_url('dapur/resep/' . (int)$m['id']) ?>"
                       class="btn btn-outline-primary btn-sm flex-shrink-0">
                        <i class="bi bi-journal-text me-1"></i> Lihat Resep
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->include('kasir/layouts/footer') ?>
