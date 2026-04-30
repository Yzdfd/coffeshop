<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px">
    <form action="<?= base_url('admin/setting/save') ?>" method="post">
        <?= csrf_field() ?>

        <div class="card-title">🏪 Informasi Café</div>
        <div class="form-grid" style="margin-bottom:20px">

            <div class="form-group">
                <label>Nama Café</label>
                <input type="text" name="nama_cafe"
                       value="<?= esc($setting['nama_cafe'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telepon"
                       value="<?= esc($setting['telepon'] ?? '') ?>">
            </div>

            <div class="form-group full">
                <label>Alamat</label>
                <textarea name="alamat"><?= esc($setting['alamat'] ?? '') ?></textarea>
            </div>

            <div class="form-group full">
                <label>Catatan Struk / Footer Struk</label>
                <textarea name="footer_struk"><?= esc($setting['footer_struk'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="card-title">💰 Pengaturan Keuangan</div>
        <div class="form-grid" style="margin-bottom:20px">

            <div class="form-group">
                <label>Pajak (%)</label>
                <input type="number" name="pajak" step="0.01" min="0" max="100"
                       value="<?= esc($setting['pajak'] ?? 0) ?>">
                <small style="color:#888">Masukkan 0 jika tidak ada pajak</small>
            </div>

            <div class="form-group">
                <label>Service Charge (%)</label>
                <input type="number" name="service_charge" step="0.01" min="0" max="100"
                       value="<?= esc($setting['service_charge'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label>Mata Uang</label>
                <select name="mata_uang">
                    <option value="IDR" <?= ($setting['mata_uang'] ?? 'IDR') == 'IDR' ? 'selected' : '' ?>>IDR (Rp)</option>
                    <option value="USD" <?= ($setting['mata_uang'] ?? '') == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                </select>
            </div>

        </div>

        <div class="card-title">🍽️ Pengaturan Meja</div>
        <div class="form-grid" style="margin-bottom:24px">

            <div class="form-group">
                <label>Aktifkan Manajemen Meja?</label>
                <select name="manajemen_meja">
                    <option value="1" <?= ($setting['manajemen_meja'] ?? 0) == '1' ? 'selected' : '' ?>>Ya</option>
                    <option value="0" <?= ($setting['manajemen_meja'] ?? 1) == '0' ? 'selected' : '' ?>>Tidak</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Meja</label>
                <input type="number" name="jumlah_meja" min="1"
                       value="<?= esc($setting['jumlah_meja'] ?? 10) ?>">
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan Setting</button>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>