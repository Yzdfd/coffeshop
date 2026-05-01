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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="<?= base_url('kasir/pesanan/buat') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Baru
            </a>
            <form method="get" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-select form-select-sm" style="width:auto">
                    <option value="">Semua Status</option>
                    <option value="open"      <?= ($filterStatus??'')=='open'      ?'selected':'' ?>>Buka</option>
                    <option value="process"   <?= ($filterStatus??'')=='process'   ?'selected':'' ?>>Diproses</option>
                    <option value="ready"     <?= ($filterStatus??'')=='ready'     ?'selected':'' ?>>Siap</option>
                    <option value="paid"      <?= ($filterStatus??'')=='paid'      ?'selected':'' ?>>Dibayar</option>
                    <option value="cancelled" <?= ($filterStatus??'')=='cancelled' ?'selected':'' ?>>Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#Order</th>
                        <th>Meja</th>
                        <th>Kasir</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr>
                    <?php else: ?>
                    <?php foreach ($orders as $o):
                        $statusClass = [
                            'open'=>'status-open','process'=>'status-process',
                            'ready'=>'status-ready','paid'=>'status-paid','cancelled'=>'status-cancelled'
                        ][$o['status']] ?? 'bg-secondary text-white';
                        $statusLabel = [
                            'open'=>'Buka','process'=>'Diproses','ready'=>'Siap',
                            'paid'=>'Dibayar','cancelled'=>'Dibatalkan'
                        ][$o['status']] ?? $o['status'];
                    ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td>
                            <?php if ($o['table_id']): ?>
                                <span class="badge bg-secondary">Meja <?= $o['table_number'] ?? $o['table_id'] ?></span>
                            <?php else: ?>
                                <span class="text-muted">Takeaway</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($o['kasir_name'] ?? '-') ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $o['jumlah_item'] ?? 0 ?> item</span></td>
                        <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                        <td><?= date('d/m H:i', strtotime($o['ordered_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('kasir/pesanan/detail/' . $o['id']) ?>"
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (in_array($o['status'], ['open','process'])): ?>
                            <a href="<?= base_url('kasir/pesanan/cancel/' . $o['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Batalkan pesanan #<?= $o['id'] ?>?')">
                                <i class="bi bi-x-circle"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($o['status'] == 'ready'): ?>
                            <a href="<?= base_url('kasir/pembayaran/' . $o['id']) ?>"
                               class="btn btn-success btn-sm">
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

<?= $this->include('kasir/layouts/footer') ?>
