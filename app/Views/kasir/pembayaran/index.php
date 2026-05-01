<?= $this->include('kasir/layouts/header') ?>

<div class="row g-4">

    <!-- Form Pembayaran -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-cash-coin me-2"></i> Pembayaran Pesanan #<?= $order['id'] ?>
            </div>
            <div class="card-body">
                <form action="<?= base_url('kasir/pembayaran/proses/' . $order['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Ringkasan Item -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Menu</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= esc($item['menu_name']) ?></td>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-end">Rp <?= number_format($item['unit_price'] * $item['qty'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Diskon & Promo -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Promo</label>
                        <div class="input-group">
                            <input type="text" name="kode_promo" id="kodePromo" class="form-control"
                                   placeholder="Masukkan kode promo..." value="<?= old('kode_promo') ?>">
                            <button type="button" class="btn btn-outline-success" onclick="cekPromo()">
                                <i class="bi bi-tag me-1"></i> Cek
                            </button>
                        </div>
                        <div id="promoInfo" class="mt-1"></div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <?php foreach (['cash'=>'💵 Cash','qris'=>'📱 QRIS','debit'=>'💳 Debit','transfer'=>'🏦 Transfer'] as $val => $label): ?>
                            <div class="col-6">
                                <div class="form-check border rounded p-2 ps-4">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="pay_<?= $val ?>" value="<?= $val ?>"
                                           <?= old('payment_method') == $val || $val == 'cash' ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="pay_<?= $val ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Uang Diterima (khusus cash) -->
                    <div class="mb-3" id="cashInput">
                        <label class="form-label fw-semibold">Uang Diterima (Rp)</label>
                        <input type="number" name="uang_diterima" id="uangDiterima" class="form-control"
                               placeholder="Masukkan jumlah uang..." oninput="hitungKembalian()">
                        <div class="mt-2 p-2 bg-light rounded" id="kembalianBox" style="display:none">
                            <span class="text-muted">Kembalian:</span>
                            <span class="fw-bold text-success ms-2" id="kembalianText">Rp 0</span>
                        </div>
                    </div>

                    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                    <input type="hidden" name="tax_amount" value="<?= $taxAmount ?>">
                    <input type="hidden" name="service_amount" value="<?= $serviceAmount ?>">
                    <input type="hidden" name="total" id="inputTotal" value="<?= $total ?>">
                    <input type="hidden" name="diskon" id="inputDiskon" value="0">
                    <input type="hidden" name="promo_id" id="inputPromoId" value="">

                    <button type="submit" class="btn btn-success w-100 fw-semibold btn-lg">
                        <i class="bi bi-check-circle me-2"></i> Konfirmasi Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Ringkasan Total -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white fw-semibold">
                <i class="bi bi-calculator me-2"></i> Rincian Total
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pajak (<?= $setting['pajak'] ?? 0 ?>%)</span>
                    <span>Rp <?= number_format($taxAmount, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Service (<?= $setting['service_charge'] ?? 0 ?>%)</span>
                    <span>Rp <?= number_format($serviceAmount, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-danger" id="diskonRow" style="display:none!important">
                    <span>Diskon Promo</span>
                    <span id="diskonText">- Rp 0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>TOTAL</span>
                    <span class="text-success" id="totalFinal">Rp <?= number_format($total, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const baseTotal = <?= $total ?>;
let diskonAmount = 0;

// Sembunyikan input cash jika bukan cash
document.querySelectorAll('input[name="payment_method"]').forEach(el => {
    el.addEventListener('change', function() {
        document.getElementById('cashInput').style.display = this.value === 'cash' ? 'block' : 'none';
    });
});

function hitungKembalian() {
    const uang  = parseInt(document.getElementById('uangDiterima').value) || 0;
    const total = baseTotal - diskonAmount;
    const box   = document.getElementById('kembalianBox');
    if (uang > 0) {
        box.style.display = 'block';
        const kembalian = uang - total;
        document.getElementById('kembalianText').textContent = 'Rp ' + Math.max(0, kembalian).toLocaleString('id-ID');
        document.getElementById('kembalianText').className = kembalian >= 0 ? 'fw-bold text-success ms-2' : 'fw-bold text-danger ms-2';
    } else {
        box.style.display = 'none';
    }
}

function cekPromo() {
    const kode = document.getElementById('kodePromo').value.trim();
    if (!kode) return;
    const info = document.getElementById('promoInfo');
    info.innerHTML = '<span class="text-muted small"><i class="bi bi-hourglass me-1"></i>Mengecek promo...</span>';

    fetch('<?= base_url('kasir/pembayaran/cek-promo') ?>?kode=' + encodeURIComponent(kode) + '&subtotal=<?= $subtotal ?>')
        .then(r => r.json())
        .then(data => {
            if (data.valid) {
                diskonAmount = data.diskon;
                document.getElementById('inputDiskon').value  = diskonAmount;
                document.getElementById('inputPromoId').value = data.promo_id;
                const totalBaru = baseTotal - diskonAmount;
                document.getElementById('inputTotal').value   = totalBaru;
                document.getElementById('totalFinal').textContent = 'Rp ' + totalBaru.toLocaleString('id-ID');
                document.getElementById('diskonRow').style.display = 'flex';
                document.getElementById('diskonText').textContent  = '- Rp ' + diskonAmount.toLocaleString('id-ID');
                info.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>';
            } else {
                info.innerHTML = '<span class="text-danger small"><i class="bi bi-x-circle me-1"></i>' + data.message + '</span>';
            }
        })
        .catch(() => { info.innerHTML = '<span class="text-danger small">Gagal cek promo.</span>'; });
}
</script>

<?= $this->include('kasir/layouts/footer') ?>
