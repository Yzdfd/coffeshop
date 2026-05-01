<?= $this->include('kasir/layouts/header') ?>

<div class="row justify-content-center no-print">
    <div class="col-md-6">
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>Pembayaran berhasil! Transaksi #<?= $transaksi['id'] ?></span>
        </div>
        <div class="d-flex gap-2 mb-4">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-2"></i> Cetak Struk
            </button>
            <a href="<?= base_url('kasir/pesanan/buat') ?>" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Pesanan Baru
            </a>
            <a href="<?= base_url('kasir/dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-house me-2"></i> Dashboard
            </a>
        </div>
    </div>
</div>

<!-- STRUK -->
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card border shadow-sm struk-area" id="struk" style="font-family: monospace; font-size: 13px;">
            <div class="card-body p-4">
                <!-- Header Struk -->
                <div class="text-center mb-3">
                    <h5 class="fw-bold mb-0"><?= esc($setting['nama_cafe'] ?? 'Café') ?></h5>
                    <?php if (!empty($setting['alamat'])): ?>
                    <p class="small mb-0"><?= esc($setting['alamat']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($setting['telepon'])): ?>
                    <p class="small mb-0">Telp: <?= esc($setting['telepon']) ?></p>
                    <?php endif; ?>
                </div>

                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

                <div class="d-flex justify-content-between small">
                    <span>No. Transaksi</span><span>#<?= $transaksi['id'] ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Kasir</span><span><?= esc($transaksi['kasir_name'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Meja</span>
                    <span><?= $order['table_id'] ? 'Meja ' . ($order['table_number'] ?? $order['table_id']) : 'Takeaway' ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Waktu</span><span><?= date('d/m/Y H:i', strtotime($transaksi['paid_at'])) ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Metode</span><span><?= strtoupper($transaksi['payment_method']) ?></span>
                </div>

                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

                <!-- Item -->
                <?php foreach ($items as $item): ?>
                <div class="mb-1">
                    <div class="fw-semibold small"><?= esc($item['menu_name']) ?></div>
                    <div class="d-flex justify-content-between small">
                        <span><?= $item['qty'] ?> x Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></span>
                        <span>Rp <?= number_format($item['unit_price'] * $item['qty'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

                <div class="d-flex justify-content-between small">
                    <span>Subtotal</span><span>Rp <?= number_format($transaksi['subtotal'], 0, ',', '.') ?></span>
                </div>
                <?php if ($transaksi['tax_amount'] > 0): ?>
                <div class="d-flex justify-content-between small">
                    <span>Pajak</span><span>Rp <?= number_format($transaksi['tax_amount'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <?php if ($transaksi['service_amount'] > 0): ?>
                <div class="d-flex justify-content-between small">
                    <span>Service</span><span>Rp <?= number_format($transaksi['service_amount'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <?php if ($transaksi['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between small text-danger">
                    <span>Diskon</span><span>- Rp <?= number_format($transaksi['discount_amount'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>

                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span><span>Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></span>
                </div>

                <?php if ($transaksi['payment_method'] == 'cash' && isset($transaksi['uang_diterima'])): ?>
                <div class="d-flex justify-content-between small mt-1">
                    <span>Uang Diterima</span><span>Rp <?= number_format($transaksi['uang_diterima'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Kembalian</span>
                    <span>Rp <?= number_format($transaksi['uang_diterima'] - $transaksi['total'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>

                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

                <div class="text-center small text-muted">
                    <?= esc($setting['footer_struk'] ?? 'Terima kasih atas kunjungan Anda!') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('kasir/layouts/footer') ?>
