<?= $this->include('admin/layouts/header') ?>

<div class="card">
    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid">

            <div class="form-group">
                <label>Nama Bahan <span style="color:red">*</span></label>
                <input type="text" name="name" value="<?= old('name', $stok['name'] ?? '') ?>" required>
                <?php if (isset($errors['name'])): ?>
                <small style="color:red"><?= $errors['name'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Satuan <span style="color:red">*</span></label>
                <select name="unit" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php foreach (['gram','kg','ml','liter','pcs','botol','sachet','sdm','bungkus'] as $sat): ?>
                    <option value="<?= $sat ?>" <?= old('unit', $stok['unit'] ?? '') == $sat ? 'selected' : '' ?>>
                        <?= $sat ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Stok <span style="color:red">*</span></label>
                <input type="number" name="stock_qty" step="0.01"
                    value="<?= old('stock_qty', $stok['stock_qty'] ?? 0) ?>" min="0" required>
            </div>

            <div class="form-group">
                <label>Minimum Stok (Notifikasi)</label>
                <input type="number" name="min_stock" step="0.01"
                    value="<?= old('min_stock', $stok['min_stock'] ?? 5) ?>" min="0">
            </div>

            <div class="form-group">
                <label>Harga Satuan (Rp)</label>
                <input type="number" name="price" value="<?= old('price', $stok['price'] ?? 0) ?>" min="0">
            </div>

            <div class="form-group full">
                <label>Catatan</label>
                <textarea name="notes"><?= old('notes', $stok['notes'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>