<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <div class="card">
        <div class="card-title">Daftar Kategori</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Urutan</th>
                        <th>Jumlah Menu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategoris)): ?>
                    <tr><td colspan="6" style="text-align:center;color:#aaa;padding:30px">Belum ada kategori.</td></tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($kategoris as $k): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= esc($k['name']) ?></strong></td>
                        <td><?= esc($k['description'] ?? '—') ?></td>
                        <td><?= $k['sort_order'] ?? 0 ?></td>
                        <td><span class="badge badge-info"><?= $k['jumlah_menu'] ?? 0 ?> menu</span></td>
                        <td>
                            <div class="btn-group">
                                <a href="<?= base_url('admin/kategori/edit/' . $k['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <?php if (($k['jumlah_menu'] ?? 0) == 0): ?>
                                <a href="<?= base_url('admin/kategori/delete/' . $k['id']) ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                                <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled title="Ada menu di kategori ini">Hapus</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-title"><?= isset($editKategori) ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?></div>
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:12px">
                <label>Nama Kategori <span style="color:red">*</span></label>
                <input type="text" name="name"
                       value="<?= old('name', $editKategori['name'] ?? '') ?>"
                       placeholder="Contoh: Minuman Panas" required>
                <?php if (isset($errors['name'])): ?>
                    <small style="color:red"><?= $errors['name'] ?></small>
                <?php endif; ?>
            </div>
            <div class="form-group" style="margin-bottom:12px">
                <label>Deskripsi</label>
                <textarea name="description" placeholder="Deskripsi singkat (opsional)"><?= old('description', $editKategori['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label>Urutan Tampil</label>
                <input type="number" name="sort_order" min="0"
                       value="<?= old('sort_order', $editKategori['sort_order'] ?? 0) ?>">
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <?php if (isset($editKategori)): ?>
                    <a href="<?= base_url('admin/kategori') ?>" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

</div>

<?= $this->include('admin/layouts/footer') ?>
