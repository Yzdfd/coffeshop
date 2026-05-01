<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-gear me-2"></i> Setting Sistem
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/setting/save') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Informasi Café -->
            <p class="fw-semibold text-muted small text-uppercase mb-3">🏪 Informasi Café</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Café</label>
                    <input type="text" name="nama_cafe" class="form-control"
                           value="<?= old('nama_cafe', $setting['nama_cafe'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="telepon" class="form-control"
                           value="<?= old('telepon', $setting['telepon'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"><?= old('alamat', $setting['alamat'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Footer / Catatan Struk</label>
                    <textarea name="footer_struk" class="form-control" rows="2"
                              placeholder="Contoh: Terima kasih atas kunjungan Anda!"><?= old('footer_struk', $setting['footer_struk'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>

            <!-- Keuangan -->
            <p class="fw-semibold text-muted small text-uppercase mb-3 mt-3">💰 Pengaturan Keuangan</p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Pajak (%)</label>
                    <input type="number" name="pajak" class="form-control" step="0.01" min="0" max="100"
                           value="<?= old('pajak', $setting['pajak'] ?? 0) ?>">
                    <div class="form-text">Isi 0 jika tidak ada pajak</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service Charge (%)</label>
                    <input type="number" name="service_charge" class="form-control" step="0.01" min="0" max="100"
                           value="<?= old('service_charge', $setting['service_charge'] ?? 0) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mata Uang</label>
                    <select name="mata_uang" class="form-select">
                        <option value="IDR" <?= old('mata_uang', $setting['mata_uang'] ?? 'IDR') == 'IDR' ? 'selected' : '' ?>>IDR (Rp)</option>
                        <option value="USD" <?= old('mata_uang', $setting['mata_uang'] ?? '') == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                    </select>
                </div>
            </div>

            <hr>

            <!-- Manajemen Meja -->
            <p class="fw-semibold text-muted small text-uppercase mb-3 mt-3">🍽️ Pengaturan Meja</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Aktifkan Manajemen Meja?</label>
                    <select name="manajemen_meja" class="form-select">
                        <option value="1" <?= old('manajemen_meja', $setting['manajemen_meja'] ?? 1) == '1' ? 'selected' : '' ?>>Ya</option>
                        <option value="0" <?= old('manajemen_meja', $setting['manajemen_meja'] ?? '') == '0' ? 'selected' : '' ?>>Tidak</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah Meja</label>
                    <input type="number" name="jumlah_meja" class="form-control" min="1"
                           value="<?= old('jumlah_meja', $setting['jumlah_meja'] ?? 10) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Setting
            </button>
        </form>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
