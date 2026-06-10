<?= $this->include('kasir/layouts/header') ?>

<div class="row g-4">
    <!-- KIRI: Pilih Menu -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <span class="text-muted small me-1">Kategori:</span>
                    <button onclick="filterKategori(0)" id="cat-0"
                     class="btn btn-sm btn-success">Semua</button>
                        <?php foreach ($kategoris as $k): ?>
                    <button onclick="filterKategori(<?= $k['id'] ?>)" id="cat-<?= $k['id'] ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <?= esc($k['name']) ?>
                        </button>
                        <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-3" id="menuGrid">
            <?php if (empty($menus)): ?>
            <div class="col-12">
                <div class="alert alert-info">Tidak ada menu tersedia.</div>
            </div>
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
                    <label class="form-label small fw-semibold">Tipe Pesanan</label>
                    <div class="d-flex gap-2 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipePesanan" id="tipeDineIn"
                                value="dinein" checked onchange="toggleTipe()">
                            <label class="form-check-label small" for="tipeDineIn">🪑 Dine In</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipePesanan" id="tipeTakeaway"
                                value="takeaway" onchange="toggleTipe()">
                            <label class="form-check-label small" for="tipeTakeaway">🥡 Takeaway</label>
                        </div>
                    </div>


                    <!-- Nomor Meja (Dine In) -->
                    <div id="boxMeja">
                        <input type="text" class="form-control form-control-sm" id="pilihMeja"
                            placeholder="Contoh: Meja 1, Meja 2...">
                    </div>

                    <!-- Nama / No. HP (Takeaway) -->
                    <div id="boxTakeaway" style="display:none">
                        <input type="text" class="form-control form-control-sm" id="catatanOrder"
                            placeholder="Nama pelanggan / No. HP...">
                    </div>
                </div>
                <div id="orderItems" class="mb-3 order-items-scroll">
                    <p class="text-muted small text-center py-3">
                        <i class="bi bi-cart-x fs-4 d-block mb-1"></i>Belum ada item dipilih
                    </p>
                </div>
                <!-- REKOMENDASI -->
                <div class="mt-3">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-semibold">
                                Rekomendasi Menu
                            </span>
                        </div>

                        <div id="recommendationBox">
                            <small class="text-muted">
                                Pilih menu terlebih dahulu
                            </small>
                        </div>
                    </div>
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
const allMenus = <?= json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
let recommendationRules = [];
let orderItems = [];
let aktivKategori = 0;

function filterKategori(katId) {
    aktivKategori = katId;
    document.querySelectorAll('[id^="cat-"]').forEach(btn => {
        btn.className = 'btn btn-sm btn-outline-secondary';
    });
    document.getElementById('cat-' + katId).className = 'btn btn-sm btn-success';

    fetch('<?= base_url('kasir/pesanan/menus') ?>?kategori=' + katId)
        .then(r => r.json())
        .then(menus => renderMenus(menus));
}

function renderMenus(menus) {
    const container = document.getElementById('menuGrid');
    if (menus.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info">Tidak ada menu tersedia.</div></div>';
        return;
    }
    let html = '';
    menus.forEach(m => {
        const unavail = m.status != 'available';
        html += `<div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm menu-card h-100 ${unavail ? 'unavailable' : ''}"
                 ${!unavail ? `onclick="tambahItem(${m.id}, '${m.name.replace(/'/g,"\\'")}', ${m.price})"` : ''}>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="card-title mb-0 fw-semibold" style="font-size:14px">${m.name}</h6>
                        ${unavail ? '<span class="badge bg-danger ms-1" style="font-size:10px">Habis</span>' : ''}
                    </div>
                    <p class="text-muted mb-1" style="font-size:11px">${m.nama_kategori ?? ''}</p>
                    <div class="fw-bold text-success">Rp ${Number(m.price).toLocaleString('id-ID')}</div>
                </div>
            </div>
        </div>`;

        
    });
    container.innerHTML = html;
}
console.log(allMenus);
console.log(recommendationRules);

fetch("/ml/recomendation.json")
    .then(res => res.json())
    .then(data => {
        recommendationRules = data;

        console.log("RULES:", recommendationRules);
    })
    .catch(err => {
        console.log("ERROR FETCH:", err);
    });

function tambahItem(id, nama, harga) {
    const existing = orderItems.find(i => i.id === id);
    if (existing) {
        existing.qty++;
    } else {
        orderItems.push({
            id,
            nama,
            harga,
            qty: 1,
            catatan: ''
        });
    }
    renderOrder();
    getRecommendations();
}

function kurangiItem(id) {
    const idx = orderItems.findIndex(i => i.id === id);
    if (idx !== -1) {
        orderItems[idx].qty--;
        if (orderItems[idx].qty <= 0) orderItems.splice(idx, 1);
    }
    renderOrder();
    getRecommendations();
}

function hapusItem(id) {
    orderItems = orderItems.filter(i => i.id !== id);
    renderOrder();
    getRecommendations();
}

function setCatatan(id, val) {
    const item = orderItems.find(i => i.id === id);
    if (item) item.catatan = val;
}

function getRecommendations() {
    let recommendations = [];

    // ambil semua nama menu di cart
    const cartNames = orderItems.map(i => i.nama);

    recommendationRules.forEach(rule => {

        // cek apakah antecedents cocok
        const match = rule.antecedents.every(a =>
            cartNames.some(c =>
                c.trim().toLowerCase() ===
                a.trim().toLowerCase()
            )
        );

        if (match) {

            rule.consequents.forEach(item => {

                // jangan tampil kalau udah ada di cart
                if (!cartNames.some(c =>
                        c.trim().toLowerCase() ===
                        item.trim().toLowerCase()
                    )) {

                    recommendations.push({
                        item: item,
                        confidence: rule.confidence
                    });

                }

            });

        }

    });

    // urut confidence tertinggi
    recommendations.sort((a, b) =>
        b.confidence - a.confidence
    );

    // ambil top 3 unik
    let result = [];
    let seen = new Set();

    for (let r of recommendations) {

        if (!seen.has(r.item)) {

            seen.add(r.item);
            result.push(r);

        }

        if (result.length === 3) break;
    }

    renderRecommendations(result);
}

function renderRecommendations(data) {
    const box = document.getElementById("recommendationBox");

    if (data.length === 0) {

        box.innerHTML = `
            <small class="text-muted">
                Tidak ada rekomendasi
            </small>
        `;

        return;
    }

    let html = `<div class="d-flex flex-wrap gap-2">`;

    data.forEach(r => {

        // cari menu asli
        const menu = allMenus.find(m =>
            m.name.trim().toLowerCase() === r.item.trim().toLowerCase()
        );

        if (!menu) return;

        html += `
    <button
        type="button"
        class="btn btn-sm btn-warning"
        onclick="tambahItem(
            ${menu.id},
            '${menu.name.replace(/'/g, "\\'")}',
            ${menu.price}
        )"
    >
        + ${menu.name}
    </button>
`;

    });

    html += `</div>`;

    box.innerHTML = html;
}

function renderOrder() {
    const container = document.getElementById('orderItems');
    if (orderItems.length === 0) {

        container.innerHTML =
            '<p class="text-muted small text-center py-3"><i class="bi bi-cart-x fs-4 d-block mb-1"></i>Belum ada item dipilih</p>';

        document.getElementById('subtotalText').textContent = 'Rp 0';

        document.getElementById('itemCountText').textContent = '0 item';

        document.getElementById('recommendationBox').innerHTML = `
        <small class="text-muted">
            Pilih menu terlebih dahulu
        </small>
    `;

        return;
    }
    let html = '',
        subtotal = 0,
        totalItem = 0;
    orderItems.forEach(item => {
        const sub = item.harga * item.qty;
        subtotal += sub;
        totalItem += item.qty;
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
                <button class="btn btn-sm btn-outline-success px-2 py-0" onclick="tambahItem(${item.id}, '${item.nama.replace(/'/g, "\\'")}', ${item.harga})">+</button>
                <button class="btn btn-sm btn-outline-danger px-2 py-0 ms-1" onclick="hapusItem(${item.id})"><i class="bi bi-x"></i></button>
            </div>
        </div>
        <div class="text-end text-success fw-semibold" style="font-size:12px">Rp ${sub.toLocaleString('id-ID')}</div>`;
    });
    container.innerHTML = html;
    document.getElementById('subtotalText').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('itemCountText').textContent = totalItem + ' item';
}

function clearOrder() {
    if (confirm('Kosongkan semua item?')) {
        orderItems = [];
        renderOrder();
        getRecommendations();
    }
}

function toggleTipe() {
    const isDineIn = document.getElementById('tipeDineIn').checked;
    document.getElementById('boxMeja').style.display = isDineIn ? 'block' : 'none';
    document.getElementById('boxTakeaway').style.display = isDineIn ? 'none' : 'block';
    document.getElementById('labelCatatan').textContent = isDineIn ? 'Catatan Order' : 'Nama / Nomor HP Pelanggan';
    document.getElementById('catatanOrder').placeholder = isDineIn ? 'Contoh: tidak pedas, extra ice...' :
        'Contoh: Budi / 0812xxxx';
    if (!isDineIn) document.getElementById('pilihMeja').value = '';
}

function kirimPesanan() {
    if (orderItems.length === 0) {
        alert('Tambahkan menu terlebih dahulu!');
        return;
    }

    const isDineIn = document.getElementById('tipeDineIn').checked;
    const meja = document.getElementById('pilihMeja').value;

    if (isDineIn && !meja) {
        alert('Isi nomor meja terlebih dahulu!');
        return;
    }

    let catatan = document.getElementById('catatanOrder').value;
    if (!isDineIn) {
        const nama = document.getElementById('catatanOrder').value.trim();
        if (!nama) {
            alert('Isi nama / nomor HP pelanggan untuk takeaway!');
            return;
        }
        catatan = '[Takeaway: ' + nama + '] ' + catatan;
    }

    document.getElementById('inputMeja').value = meja;
    document.getElementById('inputCatatan').value = catatan;
    document.getElementById('inputItems').value = JSON.stringify(orderItems);
    document.getElementById('formPesanan').submit();
}
</script>

<?= $this->include('kasir/layouts/footer') ?>