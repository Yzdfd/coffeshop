<?= $this->include('kasir/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- Detail Pesanan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-receipt me-2"></i>Detail Pesanan #<?= $order['id'] ?></span>
                <?php
                $statusClass = [
                    'open'=>'status-open','process'=>'status-process',
                    'ready'=>'status-ready','paid'=>'status-paid','cancelled'=>'status-cancelled'
                ][$order['status']] ?? 'bg-secondary text-white';
                $statusLabel = [
                    'open'=>'Buka','process'=>'Diproses Dapur','ready'=>'Siap Disajikan',
                    'paid'=>'Sudah Dibayar','cancelled'=>'Dibatalkan'
                ][$order['status']] ?? $order['status'];
                ?>
                <span class="badge <?= $statusClass ?> fs-6"><?= $statusLabel ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <div class="text-muted small">Meja</div>
                        <div class="fw-semibold">
                            <?= $order['table_id'] ? 'Meja ' . ($order['table_number'] ?? $order['table_id']) : 'Takeaway' ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Kasir</div>
                        <div class="fw-semibold"><?= esc($order['kasir_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Waktu Order</div>
                        <div class="fw-semibold"><?= date('d M Y H:i', strtotime($order['ordered_at'])) ?></div>
                    </div>
                    <?php if ($order['notes']): ?>
                    <div class="col-12">
                        <div class="text-muted small">Catatan</div>
                        <div class="fw-semibold"><?= esc($order['notes']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Item Pesanan -->
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Menu</th>
                                <th>Catatan</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotal = 0; foreach ($items as $item): ?>
                            <?php $sub = $item['unit_price'] * $item['qty']; $grandTotal += $sub; ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($item['menu_name'] ?? '-') ?></td>
                                <td class="text-muted small"><?= esc($item['notes'] ?? '—') ?></td>
                                <td class="text-center"><?= $item['qty'] ?></td>
                                <td class="text-end">Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                                <td class="text-end fw-semibold">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $iClass = ['pending'=>'bg-warning text-dark','cooking'=>'bg-info text-dark','ready'=>'bg-success','cancelled'=>'bg-danger'][$item['status']] ?? 'bg-secondary';
                                    $iLabel = ['pending'=>'Pending','cooking'=>'Dimasak','ready'=>'Siap','cancelled'=>'Batal'][$item['status']] ?? $item['status'];
                                    ?>
                                    <span class="badge <?= $iClass ?>"><?= $iLabel ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aksi -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-lightning me-2"></i> Aksi
            </div>
            <div class="card-body d-grid gap-2">

                <?php if ($order['status'] == 'ready'): ?>
                <a href="<?= base_url('kasir/pembayaran/' . $order['id']) ?>"
                   class="btn btn-success">
                    <i class="bi bi-cash-coin me-2"></i> Proses Pembayaran
                </a>
                <?php endif; ?>

                <?php if (in_array($order['status'], ['open', 'process'])): ?>
                <a href="<?= base_url('kasir/pesanan/tambah-item/' . $order['id']) ?>"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Item
                </a>
                <a href="<?= base_url('kasir/pesanan/cancel/' . $order['id']) ?>"
                   class="btn btn-outline-danger"
                   onclick="return confirm('Batalkan pesanan ini?')">
                    <i class="bi bi-x-circle me-2"></i> Batalkan Pesanan
                </a>
                <?php endif; ?>

                <a href="<?= base_url('kasir/pesanan') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Info Singkat -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pajak (<?= $setting['pajak'] ?? 0 ?>%)</span>
                    <span>Rp <?= number_format($grandTotal * (($setting['pajak'] ?? 0) / 100), 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Service (<?= $setting['service_charge'] ?? 0 ?>%)</span>
                    <span>Rp <?= number_format($grandTotal * (($setting['service_charge'] ?? 0) / 100), 0, ',', '.') ?></span>
                </div>
                <hr>
                <?php
                $pajak   = $grandTotal * (($setting['pajak'] ?? 0) / 100);
                $service = $grandTotal * (($setting['service_charge'] ?? 0) / 100);
                $total   = $grandTotal + $pajak + $service;
                ?>
                <div class="d-flex justify-content-between fw-bold fs-6">
                    <span>Total</span>
                    <span class="text-success">Rp <?= number_format($total, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('kasir/layouts/footer') ?>
