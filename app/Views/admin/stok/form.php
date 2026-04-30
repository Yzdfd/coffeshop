<?= $this->include('admin/layouts/header') ?>

<div class="card">
    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-grid">

            <!-- NAMA -->
            <div class="form-group">
                <label>Nama Bahan <span style="color:red">*</span></label>

                <input type="text" name="name"
                       value="<?= old('name', $stok['name'] ?? '') ?>" required>

                <?php if (!empty($errors['name'])): ?>
                    <small style="color:red"><?= $errors['name'] ?></small>
                <?php endif; ?>
            </div>

            <!-- SATUAN -->
            <div class="form-group">
                <label>Satuan <span style="color:red">*</span></label>

                <select name="unit" required>
                    <option value="">-- Pilih Satuan --</option>

                    <?php foreach (['gram','kg','ml','liter','pcs','botol','sachet','sdm','bungkus'] as $sat): ?>
                        <option value="<?= $sat ?>"
                            <?= old('unit', $stok['unit'] ?? '') === $sat ? 'selected' : '' ?>>
                            <?= ucfirst($sat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($errors['unit'])): ?>
                    <small style="color:red"><?= $errors['unit'] ?></small>
                <?php endif; ?>
            </div>

            <!-- STOCK -->
            <div class="form-group">
                <label>Stok <span style="color:red">*</span></label>

                <input type="number" name="stock_qty" step="0.01" min="0"
                       value="<?= old('stock_qty', $stok['stock_qty'] ?? 0) ?>" required>

                <?php if (!empty($errors['stock_qty'])): ?>
                    <small style="color:red"><?= $errors['stock_qty'] ?></small>
                <?php endif; ?>
            </div>

            <!-- MIN STOCK -->
            <div class="form-group">
                <label>Minimum Stok (Notifikasi)</label>

                <input type="number" name="min_stock" step="0.01" min="0"
                       value="<?= old('min_stock', $stok['min_stock'] ?? 5) ?>">

                <?php if (!empty($errors['min_stock'])): ?>
                    <small style="color:red"><?= $errors['min_stock'] ?></small>
                <?php endif; ?>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>