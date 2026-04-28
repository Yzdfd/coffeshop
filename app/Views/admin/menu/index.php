<?= $this->include('admin/layouts/header') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="action-bar">
        <a href="<?= base_url('admin/menu/create') ?>" class="btn btn-primary">+ Tambah Menu</a>
        <div class="search-box">
            <form method="get">
                <div style="display:flex;gap:8px">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Cari nama menu...">
                    <select name="kategori_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ($filterKategori ?? '') == $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Varian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menus)): ?>
                <tr><td colspan="8" style="text-align:center;color:#aaa;padding:30px">Tidak ada data menu.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($menus as $m): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?php if (!empty($m['gambar'])): ?>
                            <img src="<?= base_url('uploads/menu/' . $m['gambar']) ?>" class="img-preview">
                        <?php else: ?>
                            <span style="color:#ccc">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($m['nama_menu']) ?></td>
                    <td><?= esc($m['nama_kategori'] ?? '-') ?></td>
                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                    <td><?= !empty($m['varian']) ? esc($m['varian']) : '<span style="color:#ccc">—</span>' ?></td>
                    <td>
                        <?php if ($m['status'] == 'tersedia'): ?>
                            <span class="badge badge-success">Tersedia</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= base_url('admin/menu/edit/' . $m['id']) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="<?= base_url('admin/menu/delete/' . $m['id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus menu ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
