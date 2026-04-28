<?= $this->include('admin/layouts/header') ?>

<div class="card">
    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid">

            <div class="form-group">
                <label>Nama Bahan <span style="color:red">*</span></label>
                <input type="text" name="nama_bahan"
                       value="<?= old('nama_bahan', $stok['nama_bahan'] ?? '') ?>" required>
                <?php if (isset($errors['nama_bahan'])): ?>
                    <small style="color:red"><?= $errors['nama_bahan'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Satuan <span style="color:red">*</span></label>
                <select name="satuan" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php foreach (['gram','kg','ml','liter','pcs','botol','sachet','sdm','sdm','bungkus'] as $sat): ?>
                        <option value="<?= $sat ?>" <?= old('satuan', $stok['satuan'] ?? '') == $sat ? 'selected' : '' ?>>
                            <?= $sat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Stok Awal <span style="color:red">*</span></label>
                <input type="number" name="stok"
                       value="<?= old('stok', $stok['stok'] ?? 0) ?>" min="0" required>
            </div>

            <div class="form-group">
                <label>Minimum Stok (Notifikasi)</label>
                <input type="number" name="min_stok"
                       value="<?= old('min_stok', $stok['min_stok'] ?? 5) ?>" min="0">
            </div>

            <div class="form-group">
                <label>Harga Satuan (Rp)</label>
                <input type="number" name="harga_satuan"
                       value="<?= old('harga_satuan', $stok['harga_satuan'] ?? 0) ?>" min="0">
            </div>

            <div class="form-group full">
                <label>Keterangan</label>
                <textarea name="keterangan"><?= old('keterangan', $stok['keterangan'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>
