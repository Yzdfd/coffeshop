<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- Tabel -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-tags me-2"></i> Daftar Kategori
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
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
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                            <?php else: ?>
                            <?php $no = 1; foreach ($kategoris as $k): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($k['name']) ?></strong></td>
                                <td><?= esc($k['description'] ?? '—') ?></td>
                                <td><?= $k['sort_order'] ?? 0 ?></td>
                                <td><span class="badge bg-info text-dark"><?= $k['jumlah_menu'] ?? 0 ?> menu</span></td>
                                <td>
                                    <a href="<?= base_url('admin/kategori/edit/' . $k['id']) ?>"
                                       class="btn btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (($k['jumlah_menu'] ?? 0) == 0): ?>
                                    <a href="<?= base_url('admin/kategori/delete/' . $k['id']) ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Hapus kategori ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled title="Ada menu di kategori ini">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <?= isset($editKategori) ? '<i class="bi bi-pencil me-2"></i>Edit Kategori' : '<i class="bi bi-plus-lg me-2"></i>Tambah Kategori' ?>
            </div>
            <div class="card-body">
                <form action="<?= $formAction ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= old('name', $editKategori['name'] ?? '') ?>"
                               placeholder="Contoh: Minuman Panas" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Deskripsi singkat (opsional)"><?= old('description', $editKategori['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" min="0"
                               value="<?= old('sort_order', $editKategori['sort_order'] ?? 0) ?>">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <?php if (isset($editKategori)): ?>
                            <a href="<?= base_url('admin/kategori') ?>" class="btn btn-secondary btn-sm">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?= $this->include('admin/layouts/footer') ?>
