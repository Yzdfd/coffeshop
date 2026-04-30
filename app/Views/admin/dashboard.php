<?= $this->include('admin/layouts/header') ?>

<div class="stat-grid">
    <div class="stat-card orange">
        <div class="stat-label">Total Menu</div>
        <div class="stat-value"><?= $totalMenu ?? 0 ?></div>
        <div class="stat-icon">🍽️</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Kategori</div>
        <div class="stat-value"><?= $totalKategori ?? 0 ?></div>
        <div class="stat-icon">🗂️</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Total User</div>
        <div class="stat-value"><?= $totalUser ?? 0 ?></div>
        <div class="stat-icon">👥</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Stok Hampir Habis</div>
        <div class="stat-value"><?= $stokRendah ?? 0 ?></div>
        <div class="stat-icon">⚠️</div>
    </div>
</div>

<?php if (!empty($stokPeringatan)): ?>
<div class="card">
    <div class="card-title">⚠️ Peringatan Stok Bahan</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Bahan</th>
                    <th>Stok Saat Ini</th>
                    <th>Satuan</th>
                    <th>Min. Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stokPeringatan as $s): ?>
                <tr>
                    <td><?= esc($s['name']) ?></td>
                    <td class="<?= $s['stock_qty'] == 0 ? 'stok-out' : 'stok-low' ?>"><?= $s['stock_qty'] ?></td>
                    <td><?= esc($s['unit']) ?></td>
                    <td><?= $s['min_stock'] ?></td>
                    <td>
                        <?php if ($s['stock_qty'] == 0): ?>
                            <span class="badge badge-danger">Habis</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Hampir Habis</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->include('admin/layouts/footer') ?>
