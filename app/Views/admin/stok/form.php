<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-box-seam me-2"></i><?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">

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
                    <input type="number" name="stock" step="0.01" value="<?= old('stock', $stok['stock'] ?? 0) ?>"
                        min="0" required>
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

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                    <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
                </div>
        </form>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>