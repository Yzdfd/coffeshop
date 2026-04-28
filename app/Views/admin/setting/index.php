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
                       value="<?= old('nama_cafe', $setting['nama_cafe'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="telepon"
                       value="<?= old('telepon', $setting['telepon'] ?? '') ?>">
            </div>

            <div class="form-group full">
                <label>Alamat</label>
                <textarea name="alamat"><?= old('alamat', $setting['alamat'] ?? '') ?></textarea>
            </div>

            <div class="form-group full">
                <label>Catatan Struk / Footer Struk</label>
                <textarea name="footer_struk" placeholder="Contoh: Terima kasih atas kunjungan Anda!"><?= old('footer_struk', $setting['footer_struk'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="card-title">💰 Pengaturan Keuangan</div>
        <div class="form-grid" style="margin-bottom:20px">

            <div class="form-group">
                <label>Pajak (%)</label>
                <input type="number" name="pajak" step="0.01" min="0" max="100"
                       value="<?= old('pajak', $setting['pajak'] ?? 0) ?>">
                <small style="color:#888">Masukkan 0 jika tidak ada pajak</small>
            </div>

            <div class="form-group">
                <label>Service Charge (%)</label>
                <input type="number" name="service_charge" step="0.01" min="0" max="100"
                       value="<?= old('service_charge', $setting['service_charge'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label>Mata Uang</label>
                <select name="mata_uang">
                    <option value="IDR" <?= old('mata_uang', $setting['mata_uang'] ?? 'IDR') == 'IDR' ? 'selected' : '' ?>>IDR (Rp)</option>
                    <option value="USD" <?= old('mata_uang', $setting['mata_uang'] ?? '') == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                </select>
            </div>

        </div>

        <div class="card-title">🍽️ Pengaturan Meja</div>
        <div class="form-grid" style="margin-bottom:24px">

            <div class="form-group">
                <label>Aktifkan Manajemen Meja?</label>
                <select name="manajemen_meja">
                    <option value="1" <?= old('manajemen_meja', $setting['manajemen_meja'] ?? 0) == '1' ? 'selected' : '' ?>>Ya</option>
                    <option value="0" <?= old('manajemen_meja', $setting['manajemen_meja'] ?? 1) == '0' ? 'selected' : '' ?>>Tidak</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Meja</label>
                <input type="number" name="jumlah_meja" min="1"
                       value="<?= old('jumlah_meja', $setting['jumlah_meja'] ?? 10) ?>">
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan Setting</button>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>
