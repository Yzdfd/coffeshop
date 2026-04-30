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
                    <select name="category_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ($filterKategori ?? '') == $k['id'] ? 'selected' : '' ?>>
                                <?= esc($k['name']) ?>
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
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>HPP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menus)): ?>
                <tr><td colspan="7" style="text-align:center;color:#aaa;padding:30px">Tidak ada data menu.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($menus as $m): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($m['name']) ?></td>
                    <td><?= esc($m['nama_kategori'] ?? '-') ?></td>
                    <td>Rp <?= number_format($m['price'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($m['hpp'], 0, ',', '.') ?></td>
                    <td>
                        <?php if ($m['status'] == 'available'): ?>
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
