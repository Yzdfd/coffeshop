<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $formAction ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid">

            <div class="form-group">
                <label>Nama Menu <span style="color:red">*</span></label>
                <input type="text" name="name" value="<?= old('name', $menu['name'] ?? '') ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <small style="color:red"><?= $errors['name'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Kategori <span style="color:red">*</span></label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>"
                            <?= old('category_id', $menu['category_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga Jual (Rp) <span style="color:red">*</span></label>
                <input type="number" name="price" value="<?= old('price', $menu['price'] ?? '') ?>" min="0" required>
            </div>

            <div class="form-group">
                <label>HPP - Harga Per Produk (Rp)</label>
                <input type="number" name="hpp" value="<?= old('hpp', $menu['hpp'] ?? 0) ?>" min="0">
                <small style="color:#888">Harga modal / biaya produksi per item</small>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="available" <?= old('status', $menu['status'] ?? 'available') == 'available' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="unavailable" <?= old('status', $menu['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Habis</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Deskripsi</label>
                <textarea name="description"><?= old('description', $menu['description'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/menu') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>
