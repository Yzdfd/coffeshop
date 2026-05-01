<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:480px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-plus-circle me-2"></i> Tambah Stok
    </div>
    <div class="card-body">

        <div class="p-3 rounded-3 bg-light mb-4">
            <div class="text-muted small mb-1">Nama Bahan</div>
            <div class="fw-bold fs-6"><?= esc($stok['name']) ?></div>
            <div class="text-muted small mt-2 mb-1">Stok Saat Ini</div>
            <div class="fw-bold fs-5 text-success"><?= $stok['stock_qty'] ?> <?= esc($stok['unit']) ?></div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/stok/tambah/' . $stok['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Jumlah Tambah <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" class="form-control" step="0.01" min="0.01"
                       required placeholder="Masukkan jumlah yang ditambahkan">
            </div>
            <div class="mb-4">
                <label class="form-label">Keterangan Pengadaan</label>
                <input type="text" name="keterangan" class="form-control"
                       placeholder="Contoh: Beli dari supplier A">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Tambah Stok
                </button>
                <a href="<?= base_url('admin/stok') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->include('admin/layouts/footer') ?>
