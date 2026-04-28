<?= $this->include('admin/layouts/header') ?>

<!-- Stat Cards -->
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

<!-- Stok Peringatan -->
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
                    <th>Minimum Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stokPeringatan as $s): ?>
                <tr>
                    <td><?= esc($s['nama_bahan']) ?></td>
                    <td class="<?= $s['stok'] == 0 ? 'stok-out' : 'stok-low' ?>"><?= $s['stok'] ?></td>
                    <td><?= esc($s['satuan']) ?></td>
                    <td><?= $s['min_stok'] ?></td>
                    <td>
                        <?php if ($s['stok'] == 0): ?>
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

<!-- Info Singkat -->
<div class="card">
    <div class="card-title">ℹ️ Info Sistem</div>
    <table>
        <tr>
            <td style="width:180px;color:#888">Nama Café</td>
            <td><?= esc($setting['nama_cafe'] ?? '-') ?></td>
        </tr>
        <tr>
            <td style="color:#888">Pajak</td>
            <td><?= $setting['pajak'] ?? 0 ?>%</td>
        </tr>
        <tr>
            <td style="color:#888">Service Charge</td>
            <td><?= $setting['service_charge'] ?? 0 ?>%</td>
        </tr>
    </table>
</div>

<?= $this->include('admin/layouts/footer') ?>
