<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-box-seam me-2"></i><?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= old('name', $stok['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select name="unit" class="form-select" required>
                        <option value="">-- Pilih Satuan --</option>
                        <?php foreach (['gram','kg','ml','liter','pcs','botol','sachet','sdm','bungkus'] as $sat): ?>
                            <option value="<?= $sat ?>" <?= old('unit', $stok['unit'] ?? '') == $sat ? 'selected' : '' ?>>
                                <?= $sat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stock_qty" class="form-control" step="0.01"
                           value="<?= old('stock_qty', $stok['stock_qty'] ?? 0) ?>" min="0" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Minimum Stok</label>
                    <input type="number" name="min_stock" class="form-control" step="0.01"
                           value="<?= old('min_stock', $stok['min_stock'] ?? 5) ?>" min="0">
                    <div class="form-text">Batas notifikasi stok rendah</div>
                </div>

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
