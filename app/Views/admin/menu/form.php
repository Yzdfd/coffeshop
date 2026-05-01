<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-egg-fried me-2"></i><?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= old('name', $menu['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?= $k['id'] ?>"
                                <?= old('category_id', $menu['category_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control"
                           value="<?= old('price', $menu['price'] ?? '') ?>" min="0" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">HPP (Rp)
                        <small class="text-muted">Harga Per Produk</small>
                    </label>
                    <input type="number" name="hpp" class="form-control"
                           value="<?= old('hpp', $menu['hpp'] ?? 0) ?>" min="0">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="available"   <?= old('status', $menu['status'] ?? 'available') == 'available'   ? 'selected' : '' ?>>Tersedia</option>
                        <option value="unavailable" <?= old('status', $menu['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Habis</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"><?= old('description', $menu['description'] ?? '') ?></textarea>
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="<?= base_url('admin/menu') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
