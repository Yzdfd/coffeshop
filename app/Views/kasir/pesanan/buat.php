<?= $this->include('kasir/layouts/header') ?>

<div class="row g-4">
    <!-- KIRI: Pilih Menu -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <span class="text-muted small me-1">Kategori:</span>
                    <a href="<?= base_url('kasir/pesanan/buat') ?>"
                       class="btn btn-sm <?= !$filterKategori ? 'btn-success' : 'btn-outline-secondary' ?>">Semua</a>
                    <?php foreach ($kategoris as $k): ?>
                    <a href="<?= base_url('kasir/pesanan/buat?kategori=' . $k['id']) ?>"
                       class="btn btn-sm <?= $filterKategori == $k['id'] ? 'btn-success' : 'btn-outline-secondary' ?>">
                        <?= esc($k['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <?php if (empty($menus)): ?>
            <div class="col-12"><div class="alert alert-info">Tidak ada menu tersedia.</div></div>
            <?php else: ?>
            <?php foreach ($menus as $m): ?>
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm menu-card h-100 <?= $m['status'] != 'available' ? 'unavailable' : '' ?>"
                     <?= $m['status'] == 'available' ? "onclick=\"tambahItem({$m['id']}, '" . addslashes($m['name']) . "', {$m['price']})\"" : '' ?>>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="card-title mb-0 fw-semibold" style="font-size:14px"><?= esc($m['name']) ?></h6>
                            <?php if ($m['status'] != 'available'): ?>
                                <span class="badge bg-danger ms-1" style="font-size:10px">Habis</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted mb-1" style="font-size:11px"><?= esc($m['nama_kategori'] ?? '') ?></p>
                        <div class="fw-bold text-success">Rp <?= number_format($m['price'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- KANAN: Ringkasan Pesanan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm order-summary">
            <div class="card-header bg-success text-white fw-semibold">
                <i class="bi bi-cart3 me-2"></i> Ringkasan Pesanan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Meja</label>
                    <select class="form-select form-select-sm" id="pilihMeja">
                        <option value="">Takeaway / Tanpa Meja</option>
                        <?php foreach ($mejas as $meja): ?>
                        <option value="<?= $meja['id'] ?>" <?= $meja['status'] != 'available' ? 'disabled' : '' ?>>
                            Meja <?= $meja['number'] ?> <?= $meja['status'] != 'available' ? '(Terisi)' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan Order</label>
                    <textarea class="form-control form-control-sm" id="catatanOrder" rows="2"
                              placeholder="Contoh: tidak pedas, extra ice..."></textarea>
                </div>
                <div id="orderItems" class="mb-3">
                    <p class="text-muted small text-center py-3">
                        <i class="bi bi-cart-x fs-4 d-block mb-1"></i>Belum ada item dipilih
                    </p>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-semibold mb-1">
                    <span>Subtotal</span><span id="subtotalText">Rp 0</span>
                </div>
                <div class="text-muted small mb-3" id="itemCountText">0 item</div>
                <button class="btn btn-success w-100 fw-semibold" onclick="kirimPesanan()">
                    <i class="bi bi-send me-2"></i> Kirim ke Dapur
                </button>
                <button class="btn btn-outline-secondary w-100 mt-2 btn-sm" onclick="clearOrder()">
                    <i class="bi bi-trash me-1"></i> Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>

<form id="formPesanan" action="<?= base_url('kasir/pesanan/store') ?>" method="post" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="table_id" id="inputMeja">
    <input type="hidden" name="notes" id="inputCatatan">
    <input type="hidden" name="items" id="inputItems">
</form>

<script>
let orderItems = [];

function tambahItem(id, nama, harga) {
    const existing = orderItems.find(i => i.id === id);
    if (existing) { existing.qty++; }
    else { orderItems.push({ id, nama, harga, qty: 1, catatan: '' }); }
    renderOrder();
}

function kurangiItem(id) {
    const idx = orderItems.findIndex(i => i.id === id);
    if (idx !== -1) { orderItems[idx].qty--; if (orderItems[idx].qty <= 0) orderItems.splice(idx, 1); }
    renderOrder();
}

function hapusItem(id) { orderItems = orderItems.filter(i => i.id !== id); renderOrder(); }

function setCatatan(id, val) { const item = orderItems.find(i => i.id === id); if (item) item.catatan = val; }

function renderOrder() {
    const container = document.getElementById('orderItems');
    if (orderItems.length === 0) {
        container.innerHTML = '<p class="text-muted small text-center py-3"><i class="bi bi-cart-x fs-4 d-block mb-1"></i>Belum ada item dipilih</p>';
        document.getElementById('subtotalText').textContent = 'Rp 0';
        document.getElementById('itemCountText').textContent = '0 item';
        return;
    }
    let html = '', subtotal = 0, totalItem = 0;
    orderItems.forEach(item => {
        const sub = item.harga * item.qty;
        subtotal += sub; totalItem += item.qty;
        html += `<div class="order-item-row">
            <div style="flex:1">
                <div class="fw-semibold" style="font-size:13px">${item.nama}</div>
                <div class="text-muted" style="font-size:11px">Rp ${Number(item.harga).toLocaleString('id-ID')} x ${item.qty}</div>
                <input type="text" class="form-control form-control-sm mt-1" style="font-size:11px"
                       placeholder="Catatan item..." value="${item.catatan}"
                       onchange="setCatatan(${item.id}, this.value)">
            </div>
            <div class="d-flex align-items-center gap-1 ms-2">
                <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="kurangiItem(${item.id})">-</button>
                <span class="fw-bold">${item.qty}</span>
                <button class="btn btn-sm btn-outline-success px-2 py-0" onclick="tambahItem(${item.id}, '${item.nama.replace(/'/g,"\\'")}', ${item.harga})">+</button>
                <button class="btn btn-sm btn-outline-danger px-2 py-0 ms-1" onclick="hapusItem(${item.id})"><i class="bi bi-x"></i></button>
            </div>
        </div>
        <div class="text-end text-success fw-semibold" style="font-size:12px">Rp ${sub.toLocaleString('id-ID')}</div>`;
    });
    container.innerHTML = html;
    document.getElementById('subtotalText').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('itemCountText').textContent = totalItem + ' item';
}

function clearOrder() { if (confirm('Kosongkan semua item?')) { orderItems = []; renderOrder(); } }

function kirimPesanan() {
    if (orderItems.length === 0) { alert('Tambahkan menu terlebih dahulu!'); return; }
    document.getElementById('inputMeja').value    = document.getElementById('pilihMeja').value;
    document.getElementById('inputCatatan').value = document.getElementById('catatanOrder').value;
    document.getElementById('inputItems').value   = JSON.stringify(orderItems);
    document.getElementById('formPesanan').submit();
}
</script>

<?= $this->include('kasir/layouts/footer') ?>
