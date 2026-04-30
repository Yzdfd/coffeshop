<?= $this->include('admin/layouts/header') ?>

<div class="card" style="max-width:480px">

    <?php
        // 🔒 safety fallback
        $stock = (float) ($stok['stock_qty'] ?? 0);
        $unit  = $stok['unit'] ?? '';
        $name  = $stok['name'] ?? '';
    ?>

    <div style="margin-bottom:16px;padding:14px;background:#f9f9f9;border-radius:6px;border:1px solid #eee">
        <p style="margin-bottom:4px;color:#888;font-size:12px">Bahan</p>
        <p style="font-size:16px;font-weight:600"><?= esc($name) ?></p>

        <p style="margin-top:6px;color:#888;font-size:12px">Stok saat ini</p>
        <p style="font-size:18px;font-weight:700;color:#2e7d32">
            <?= $stock ?> <?= esc($unit) ?>
        </p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('admin/stok/tambah/' . $stok['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom:14px">
            <label>Jumlah Tambah <span style="color:red">*</span></label>

            <input type="number" name="jumlah" step="0.01" min="0.01" required
                   value="<?= old('jumlah') ?>"
                   placeholder="Masukkan jumlah yang ditambahkan">

            <?php if (session()->getFlashdata('error_jumlah')): ?>
                <small style="color:red">
                    <?= session()->getFlashdata('error_jumlah') ?>
                </small>
            <?php endif; ?>
        </div>

        <!-- OPSIONAL: keterangan -->
        <div class="form-group" style="margin-bottom:16px">
            <label>Keterangan Pengadaan</label>
            <input type="text" name="keterangan"
                   value="<?= old('keterangan') ?>"
                   placeholder="Contoh: Beli dari supplier A">
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-success">✅ Tambah Stok</button>
            <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>