<?= $this->include('kasir/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="bi bi-exclamation-circle-fill me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h6 class="mb-0 fw-bold">Daftar Pesanan</h6>
        <small class="text-muted">Kelola semua pesanan aktif &amp; riwayat</small>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <!-- Auto-refresh toggle -->
        <span class="text-muted small me-1">Auto Refresh: <strong id="autoRefreshStatus">OFF</strong></span>
        <button id="autoRefreshOn"  class="btn btn-outline-success btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>ON</button>
        <button id="autoRefreshOff" class="btn btn-outline-danger  btn-sm"><i class="bi bi-stop-circle  me-1"></i>OFF</button>
        <a href="<?= base_url('kasir/pesanan/buat') ?>" class="btn btn-success btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Buat Pesanan
        </a>
    </div>
</div>

<!-- Auto Refresh Toggle -->
<div class="d-flex justify-content-end align-items-center gap-2 mb-3 flex-wrap">
    <span class="text-muted small">Auto Refresh:</span>
    <button id="autoRefreshOn" type="button" class="btn btn-outline-success btn-sm">Auto Refresh ON</button>
    <button id="autoRefreshOff" type="button" class="btn btn-outline-danger btn-sm">Auto Refresh OFF</button>
    <span class="badge bg-light text-dark border">
        Status: <span id="autoRefreshStatus">—</span>
    </span>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
            <?php
            $statuses = [
                ''          => ['label' => 'Semua',   'icon' => 'bi-list-ul'],
                'open'      => ['label' => 'Open',    'icon' => 'bi-circle'],
                'paid'      => ['label' => 'Dibayar', 'icon' => 'bi-check-circle-fill'],
                'cancelled' => ['label' => 'Batal',   'icon' => 'bi-x-circle-fill'],
            ];
            ?>
            <?php foreach ($statuses as $val => $sf): ?>
            <label class="filter-pill <?= ($filterStatus ?? '') === $val ? 'active' : '' ?>">
                <input type="radio" name="status" value="<?= $val ?>"
                       <?= ($filterStatus ?? '') === $val ? 'checked' : '' ?>
                       onchange="this.form.submit()" style="display:none">
                <i class="<?= $sf['icon'] ?> me-1"></i><?= $sf['label'] ?>
            </label>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
            Tidak ada pesanan ditemukan.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 pesanan-table">
                <thead>
                    <tr>
                        <th class="ps-3">#Order</th>
                        <th>Meja</th>
                        <th>Kasir</th>
                        <th class="text-center">Item</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th class="pe-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sMap = [
                    'open'      => ['label' => 'Open',     'cls' => 'status-open',      'dot' => '#3b82f6'],
                    'process'   => ['label' => 'Diproses', 'cls' => 'status-process',   'dot' => '#f59e0b'],
                    'ready'     => ['label' => 'Siap',     'cls' => 'status-ready',     'dot' => '#10b981'],
                    'paid'      => ['label' => 'Dibayar',  'cls' => 'status-paid',      'dot' => '#6b7280'],
                    'cancelled' => ['label' => 'Batal',    'cls' => 'status-cancelled', 'dot' => '#ef4444'],
                ];
                foreach ($orders as $o):
                    $s = $sMap[$o['status']] ?? ['label' => $o['status'], 'cls' => 'status-paid', 'dot' => '#6b7280'];
                ?>
                <tr class="order-row">
                    <td class="ps-3">
                        <span class="fw-bold text-dark">#<?= (int)$o['id'] ?></span>
                    </td>
                    <td>
                        <?php if ($o['table_id']): ?>
                            <span class="table-badge">
                                <i class="bi bi-grid-3x3-gap me-1"></i>Meja <?= esc((string)($o['table_number'] ?? $o['table_id'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="takeaway-badge">
                                <i class="bi bi-bag me-1"></i>Takeaway
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= esc($o['kasir_name'] ?? '-') ?></td>
                    <td class="text-center">
                        <span class="fw-semibold"><?= (int)($o['jumlah_item'] ?? 0) ?></span>
                        <span class="text-muted small"> item</span>
                    </td>
                    <td>
                        <span class="order-status-badge <?= $s['cls'] ?>">
                            <span class="status-dot" style="background:<?= $s['dot'] ?>"></span>
                            <?= $s['label'] ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= date('d/m H:i', strtotime($o['ordered_at'])) ?></td>
                    <td class="pe-3 text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?= base_url('kasir/pesanan/detail/' . $o['id']) ?>"
                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($o['status'] === 'open'): ?>
                            <a href="<?= base_url('kasir/pembayaran/' . $o['id']) ?>"
                               class="btn btn-sm btn-success px-2" title="Proses Pembayaran">
                                <i class="bi bi-cash-coin me-1"></i>Bayar
                            </a>
                            <?php endif; ?>
                            <?php if (in_array($o['status'], ['open', 'process'])): ?>
                            <a href="<?= base_url('kasir/pesanan/cancel/' . $o['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Batalkan"
                               onclick="return confirm('Batalkan pesanan #<?= (int)$o['id'] ?>?')">
                                <i class="bi bi-x-circle"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Filter Pills */
.filter-pill {
    display: inline-flex;
    align-items: center;
    padding: .3rem .85rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 500;
    cursor: pointer;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
    background: #fff;
    transition: all .15s;
    user-select: none;
}
.filter-pill:hover  { border-color: #94a3b8; color: #1e293b; }
.filter-pill.active { border-color: #4e73df; background: #4e73df; color: #fff; }

/* Table */
.pesanan-table thead th {
    background: #f8fafc;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    padding-top: .75rem;
    padding-bottom: .75rem;
}
.order-row td          { padding: .75rem .6rem; border-color: #f1f5f9; vertical-align: middle; }
.order-row:hover td    { background: #f8fafc; }

/* Meja & Takeaway tags */
.table-badge, .takeaway-badge {
    display: inline-flex;
    align-items: center;
    padding: .25rem .65rem;
    border-radius: 6px;
    font-size: .78rem;
    font-weight: 600;
}
.table-badge    { background: #1e293b14; color: #1e293b; }
.takeaway-badge { background: #64748b14; color: #475569; }

/* Status badges */
.order-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .8rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 600;
}
.status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.status-open      { background: #eff6ff; color: #1d4ed8; }
.status-process   { background: #fffbeb; color: #b45309; }
.status-ready     { background: #f0fdf4; color: #15803d; }
.status-paid      { background: #f1f5f9; color: #475569; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }
</style>

<?= $this->include('kasir/layouts/footer') ?>
