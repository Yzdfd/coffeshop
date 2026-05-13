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

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= base_url('admin/resep') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div>
        <div class="text-muted small">Kelola Resep</div>
        <div class="fw-semibold"><?= esc((string) ($menu['name'] ?? '')) ?></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-2"></i>Info Menu
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Nama</dt>
                    <dd class="col-7"><?= esc((string) ($menu['name'] ?? '')) ?></dd>
                    <dt class="col-5 text-muted small">Harga</dt>
                    <dd class="col-7">Rp <?= number_format((float) ($menu['price'] ?? 0), 0, ',', '.') ?></dd>
                    <dt class="col-5 text-muted small">Status</dt>
                    <dd class="col-7">
                        <span class="badge <?= (($menu['status'] ?? '') === 'available') ? 'bg-success' : 'bg-secondary' ?>">
                            <?= esc((string) ($menu['status'] ?? '')) ?>
                        </span>
                    </dd>
                </dl>
                <?php if (!empty($menu['description'])): ?>
                <hr class="my-3">
                <div class="small text-muted"><?= esc((string) ($menu['description'] ?? '')) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Resep
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/resep/store/' . (int) ($menu['id'] ?? 0)) ?>" method="post" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-6">
                        <label class="form-label">Bahan <span class="text-danger">*</span></label>
                        <select name="ingredient_id" class="form-select <?= isset($errors['ingredient_id']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Bahan --</option>
                            <?php foreach (($ingredients ?? []) as $ing): ?>
                                <option value="<?= (int) ($ing['id'] ?? 0) ?>" <?= old('ingredient_id') == ($ing['id'] ?? 0) ? 'selected' : '' ?>>
                                    <?= esc((string) ($ing['name'] ?? '')) ?> (Stok: <?= (float) ($ing['stock_qty'] ?? 0) ?> <?= esc((string) ($ing['unit'] ?? '')) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['ingredient_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['ingredient_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Qty Dibutuhkan <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01"
                               name="qty_needed"
                               value="<?= old('qty_needed', '1') ?>"
                               class="form-control <?= isset($errors['qty_needed']) ? 'is-invalid' : '' ?>" required>
                        <?php if (isset($errors['qty_needed'])): ?>
                            <div class="invalid-feedback"><?= $errors['qty_needed'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="unit"
                               value="<?= old('unit') ?>"
                               placeholder="gram / ml / pcs"
                               class="form-control <?= isset($errors['unit']) ? 'is-invalid' : '' ?>" required>
                        <?php if (isset($errors['unit'])): ?>
                            <div class="invalid-feedback"><?= $errors['unit'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-journal-text me-2"></i>Daftar Bahan Resep
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bahan</th>
                                <th style="width: 140px;">Qty Dibutuhkan</th>
                                <th style="width: 130px;">Stok Saat Ini</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recipes)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada bahan resep untuk menu ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (($recipes ?? []) as $r): ?>
                            <tr>
                                <td><?= esc((string) ($r['ingredient_name'] ?? '-')) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= (float) ($r['qty_needed'] ?? 0) ?> <?= esc((string) ($r['unit'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark">
                                        <?= (float) ($r['stock_qty'] ?? 0) ?> <?= esc((string) ($r['ingredient_unit'] ?? '')) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= base_url('admin/resep/delete/' . (int) ($r['id'] ?? 0)) ?>" method="post"
                                          onsubmit="return confirm('Hapus bahan ini dari resep?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
</div>

<?= $this->include('admin/layouts/footer') ?>

