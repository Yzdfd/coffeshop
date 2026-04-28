<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">

            <!-- Nama Menu -->
            <div class="form-group">
                <label>Nama Menu <span style="color:red">*</span></label>
                <input type="text" name="nama_menu" value="<?= old('nama_menu', $menu['nama_menu'] ?? '') ?>" required>
                <?php if (isset($errors['nama_menu'])): ?>
                    <small style="color:red"><?= $errors['nama_menu'] ?></small>
                <?php endif; ?>
            </div>

            <!-- Kategori -->
            <div class="form-group">
                <label>Kategori <span style="color:red">*</span></label>
                <select name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>"
                            <?= old('kategori_id', $menu['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Harga -->
            <div class="form-group">
                <label>Harga (Rp) <span style="color:red">*</span></label>
                <input type="number" name="harga" value="<?= old('harga', $menu['harga'] ?? '') ?>" min="0" required>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="tersedia" <?= old('status', $menu['status'] ?? 'tersedia') == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="habis"    <?= old('status', $menu['status'] ?? '') == 'habis' ? 'selected' : '' ?>>Habis</option>
                </select>
            </div>

            <!-- Varian -->
            <div class="form-group">
                <label>Varian / Takaran <small style="color:#aaa">(pisahkan dengan koma)</small></label>
                <input type="text" name="varian" value="<?= old('varian', $menu['varian'] ?? '') ?>" placeholder="Kecil, Sedang, Besar">
            </div>

            <!-- Gambar -->
            <div class="form-group">
                <label>Gambar Menu</label>
                <input type="file" name="gambar" accept="image/*">
                <?php if (!empty($menu['gambar'])): ?>
                    <small style="color:#888">Gambar saat ini: <?= esc($menu['gambar']) ?></small>
                <?php endif; ?>
            </div>

            <!-- Deskripsi -->
            <div class="form-group full">
                <label>Deskripsi</label>
                <textarea name="deskripsi"><?= old('deskripsi', $menu['deskripsi'] ?? '') ?></textarea>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= base_url('admin/menu') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->include('admin/layouts/footer') ?>
