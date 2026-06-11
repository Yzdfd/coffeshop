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

<?php
// Parse icon & color dari description
function parseKatMeta(?string $desc): array {
    $icon  = '';
    $color = '';
    if (!$desc) return compact('icon','color');
    if (preg_match('/\[icon:([^\]]+)\]/', $desc, $m)) $icon  = trim($m[1]);
    if (preg_match('/\[color:([^\]]+)\]/', $desc, $m)) $color = trim($m[1]);
    return compact('icon','color');
}
function stripKatMeta(?string $desc): string {
    if (!$desc) return '';
    $desc = preg_replace('/\[icon:[^\]]+\]/', '', $desc);
    $desc = preg_replace('/\[color:[^\]]+\]/', '', $desc);
    return trim($desc);
}

// Pilihan icon yang bisa dipilih admin
$iconOptions = [
    '' => '— Tidak ada icon —',
    '☕' => '☕ Kopi',
    '🍵' => '🍵 Teh',
    '🥤' => '🥤 Minuman',
    '🍹' => '🍹 Jus',
    '🥛' => '🥛 Susu / Smoothie',
    '🍽️' => '🍽️ Makanan',
    '🍚' => '🍚 Nasi',
    '🍜' => '🍜 Mie',
    '🍲' => '🍲 Soto / Bakso',
    '🍟' => '🍟 Snack',
    '🍰' => '🍰 Dessert / Kue',
    '🥗' => '🥗 Salad',
    '🧁' => '🧁 Kue Kecil',
    '🍦' => '🍦 Es Krim',
    '🥐' => '🥐 Roti',
    '🍴' => '🍴 Lainnya',
];

$editMeta = isset($editKategori) ? parseKatMeta($editKategori['description'] ?? '') : ['icon'=>'','color'=>''];
$editDescBersih = isset($editKategori) ? stripKatMeta($editKategori['description'] ?? '') : '';
?>

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
                                <th>Icon</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Urutan</th>
                                <th>Jumlah Menu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kategoris)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada kategori.</td>
                            </tr>
                            <?php else: ?>
                            <?php $no = 1; foreach ($kategoris as $k):
                                $km = parseKatMeta($k['description'] ?? '');
                                $deskBersih = stripKatMeta($k['description'] ?? '');
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td style="font-size:20px"><?= $km['icon'] ?: '—' ?></td>
                                <td>
                                    <strong><?= esc($k['name']) ?></strong>
                                    <?php if ($km['color']): ?>
                                    <span class="ms-1"
                                        style="display:inline-block;width:14px;height:14px;border-radius:50%;background:<?= esc($km['color']) ?>;vertical-align:middle;border:1px solid #ccc"></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($deskBersih ?: '—') ?></td>
                                <td><?= $k['sort_order'] ?? 0 ?></td>
                                <td><span class="badge bg-info text-dark"><?= $k['jumlah_menu'] ?? 0 ?> menu</span></td>
                                <td>
                                    <a href="<?= base_url('admin/kategori/edit/' . $k['id']) ?>"
                                        class="btn btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (($k['jumlah_menu'] ?? 0) == 0): ?>
                                    <a href="<?= base_url('admin/kategori/delete/' . $k['id']) ?>"
                                        class="btn btn-danger btn-sm" onclick="return confirm('Hapus kategori ini?')">
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
                        <input type="text" name="name"
                            class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            value="<?= old('name', $editKategori['name'] ?? '') ?>" placeholder="Contoh: Minuman Panas"
                            required>
                        <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Pilih Icon -->
                    <div class="mb-3">
                        <label class="form-label">Icon Kategori</label>
                        <select name="icon_kategori" class="form-select form-select-sm" id="selectIcon"
                            onchange="updatePreview()">
                            <?php foreach ($iconOptions as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($editMeta['icon'] === $val) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-1" id="iconPreview" style="font-size:24px"><?= $editMeta['icon'] ?: '' ?></div>
                    </div>

                    <!-- Pilih Warna -->
                    <div class="mb-3">
                        <label class="form-label">Warna Aksen</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color_kategori" id="colorPicker"
                                class="form-control form-control-color" value="<?= $editMeta['color'] ?: '#6c757d' ?>"
                                style="width:50px;height:36px;padding:2px" onchange="updatePreview()">
                            <span class="text-muted small">Warna untuk card menu di kasir</span>
                        </div>
                        <div class="mt-2 rounded-2 px-3 py-2 d-flex align-items-center gap-2" id="colorPreview"
                            style="background:linear-gradient(135deg,<?= $editMeta['color'] ?: '#6c757d' ?> 0%,#f5f5f5 100%);font-size:13px">
                            <span id="previewIcon"><?= $editMeta['icon'] ?: '🍴' ?></span>
                            <span class="text-white fw-semibold" style="text-shadow:0 1px 3px rgba(0,0,0,.5)">Preview
                                Card</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"
                            placeholder="Deskripsi singkat (opsional)"><?= old('description', $editDescBersih) ?></textarea>
                        <div class="form-text">Kosongkan jika tidak perlu deskripsi. Icon & warna diatur di atas.</div>
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

<script>
function updatePreview() {
    const icon = document.getElementById('selectIcon').value;
    const color = document.getElementById('colorPicker').value;
    document.getElementById('iconPreview').textContent = icon;
    document.getElementById('previewIcon').textContent = icon || '🍴';
    document.getElementById('colorPreview').style.background =
        `linear-gradient(135deg,${color} 0%,#f5f5f5 100%)`;
}
</script>

<?= $this->include('admin/layouts/footer') ?>