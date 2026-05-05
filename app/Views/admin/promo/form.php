<?= $this->include('admin/layouts/header') ?>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-tag me-2"></i><?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= $formAction ?>" method="post">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Kode Promo <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase"
                           value="<?= old('code', $promo['code'] ?? '') ?>"
                           placeholder="Contoh: DISKON10" required
                           oninput="this.value = this.value.toUpperCase()">
                    <div class="form-text">Kode yang diketik kasir saat proses pembayaran.</div>
                    <?php if (isset($errors['code'])): ?>
                        <div class="text-danger small"><?= $errors['code'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipe Diskon <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" id="tipeDiskon" required
                            onchange="updatePlaceholder()">
                        <option value="percent" <?= old('type', $promo['type'] ?? '') == 'percent' ? 'selected' : '' ?>>
                            Persen (%)
                        </option>
                        <option value="fixed" <?= old('type', $promo['type'] ?? '') == 'fixed' ? 'selected' : '' ?>>
                            Nominal (Rp)
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nilai Diskon <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" id="satuanLabel">%</span>
                        <input type="number" name="value" class="form-control" id="nilaiInput"
                               value="<?= old('value', $promo['value'] ?? '') ?>"
                               placeholder="Contoh: 10" min="0" step="0.01" required>
                    </div>
                    <div class="form-text" id="nilaiHint">Isi 10 untuk diskon 10%</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Berlaku Dari <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="valid_from" class="form-control"
                           value="<?= old('valid_from', isset($promo['valid_from']) ? date('Y-m-d\TH:i', strtotime($promo['valid_from'])) : date('Y-m-d\T00:00')) ?>"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Berlaku Sampai <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="valid_until" class="form-control"
                           value="<?= old('valid_until', isset($promo['valid_until']) ? date('Y-m-d\TH:i', strtotime($promo['valid_until'])) : date('Y-m-d\T23:59', strtotime('+30 days'))) ?>"
                           required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= old('status', $promo['status'] ?? 'active') == 'active'   ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= old('status', $promo['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

            </div>

            <!-- Preview -->
            <div class="alert alert-info mt-3 mb-0" id="previewPromo">
                <i class="bi bi-info-circle me-2"></i>
                <span id="previewText">Promo diskon <strong>0%</strong> dengan kode <strong>-</strong></span>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="<?= base_url('admin/promo') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function updatePlaceholder() {
    const tipe  = document.getElementById('tipeDiskon').value;
    const label = document.getElementById('satuanLabel');
    const hint  = document.getElementById('nilaiHint');
    if (tipe === 'percent') {
        label.textContent = '%';
        hint.textContent  = 'Isi 10 untuk diskon 10%';
    } else {
        label.textContent = 'Rp';
        hint.textContent  = 'Isi 5000 untuk potongan Rp 5.000';
    }
    updatePreview();
}

function updatePreview() {
    const kode  = document.querySelector('input[name="code"]').value || '-';
    const tipe  = document.getElementById('tipeDiskon').value;
    const nilai = document.querySelector('input[name="value"]').value || '0';
    const text  = document.getElementById('previewText');
    if (tipe === 'percent') {
        text.innerHTML = `Promo diskon <strong>${nilai}%</strong> dengan kode <strong>${kode}</strong>`;
    } else {
        text.innerHTML = `Promo potongan <strong>Rp ${parseInt(nilai||0).toLocaleString('id-ID')}</strong> dengan kode <strong>${kode}</strong>`;
    }
}

document.querySelector('input[name="code"]').addEventListener('input', updatePreview);
document.querySelector('input[name="value"]').addEventListener('input', updatePreview);
document.getElementById('tipeDiskon').addEventListener('change', updatePreview);

updatePlaceholder();
updatePreview();
</script>

<?= $this->include('admin/layouts/footer') ?>
