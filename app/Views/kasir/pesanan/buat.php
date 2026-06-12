<?= $this->include('kasir/layouts/header') ?>

<?php
$categoryStyles = [
    'kopi'      => ['icon' => '☕', 'gradient' => 'linear-gradient(135deg,#6f4e37 0%,#c8a97a 100%)', 'badge' => '#6f4e37'],
    'coffee'    => ['icon' => '☕', 'gradient' => 'linear-gradient(135deg,#6f4e37 0%,#c8a97a 100%)', 'badge' => '#6f4e37'],
    'teh'       => ['icon' => '🍵', 'gradient' => 'linear-gradient(135deg,#2e7d32 0%,#a5d6a7 100%)', 'badge' => '#2e7d32'],
    'tea'       => ['icon' => '🍵', 'gradient' => 'linear-gradient(135deg,#2e7d32 0%,#a5d6a7 100%)', 'badge' => '#2e7d32'],
    'jus'       => ['icon' => '🍹', 'gradient' => 'linear-gradient(135deg,#e65100 0%,#ffcc80 100%)', 'badge' => '#e65100'],
    'juice'     => ['icon' => '🍹', 'gradient' => 'linear-gradient(135deg,#e65100 0%,#ffcc80 100%)', 'badge' => '#e65100'],
    'minuman'   => ['icon' => '🥤', 'gradient' => 'linear-gradient(135deg,#1565c0 0%,#90caf9 100%)', 'badge' => '#1565c0'],
    'drink'     => ['icon' => '🥤', 'gradient' => 'linear-gradient(135deg,#1565c0 0%,#90caf9 100%)', 'badge' => '#1565c0'],
    'makanan'   => ['icon' => '🍽️', 'gradient' => 'linear-gradient(135deg,#4527a0 0%,#ce93d8 100%)', 'badge' => '#4527a0'],
    'food'      => ['icon' => '🍽️', 'gradient' => 'linear-gradient(135deg,#4527a0 0%,#ce93d8 100%)', 'badge' => '#4527a0'],
    'snack'     => ['icon' => '🍟', 'gradient' => 'linear-gradient(135deg,#f57f17 0%,#fff59d 100%)', 'badge' => '#f57f17'],
    'cemilan'   => ['icon' => '🍟', 'gradient' => 'linear-gradient(135deg,#f57f17 0%,#fff59d 100%)', 'badge' => '#f57f17'],
    'dessert'   => ['icon' => '🍰', 'gradient' => 'linear-gradient(135deg,#ad1457 0%,#f48fb1 100%)', 'badge' => '#ad1457'],
    'kue'       => ['icon' => '🍰', 'gradient' => 'linear-gradient(135deg,#ad1457 0%,#f48fb1 100%)', 'badge' => '#ad1457'],
    'nasi'      => ['icon' => '🍚', 'gradient' => 'linear-gradient(135deg,#795548 0%,#d7ccc8 100%)', 'badge' => '#795548'],
    'mie'       => ['icon' => '🍜', 'gradient' => 'linear-gradient(135deg,#bf360c 0%,#ffccbc 100%)', 'badge' => '#bf360c'],
    'bakso'     => ['icon' => '🍲', 'gradient' => 'linear-gradient(135deg,#880e4f 0%,#f8bbd0 100%)', 'badge' => '#880e4f'],
    'smoothie'  => ['icon' => '🥛', 'gradient' => 'linear-gradient(135deg,#00695c 0%,#b2dfdb 100%)', 'badge' => '#00695c'],
    'default'   => ['icon' => '🍴', 'gradient' => 'linear-gradient(135deg,#37474f 0%,#b0bec5 100%)', 'badge' => '#37474f'],
];

if (!function_exists('parseKategoriMeta')) {
    function parseKategoriMeta(string $desc): array {
        $icon  = null;
        $color = null;
        if (preg_match('/\[icon:([^\]]+)\]/', $desc, $m)) $icon  = trim($m[1]);
        if (preg_match('/\[color:([^\]]+)\]/', $desc, $m)) $color = trim($m[1]);
        return compact('icon', 'color');
    }
}

if (!function_exists('getKategoriStyle')) {
    function getKategoriStyle(string $namaKategori, string $descKategori, array $styleMap): array {
        $meta = parseKategoriMeta($descKategori);
        if ($meta['icon'] || $meta['color']) {
            $base = $styleMap['default'];
            if ($meta['icon'])  $base['icon']  = $meta['icon'];
            if ($meta['color']) {
                $c = $meta['color'];
                $base['gradient'] = "linear-gradient(135deg,{$c} 0%,#f5f5f5 100%)";
                $base['badge'] = $c;
            }
            return $base;
        }
        $lower = mb_strtolower(trim($namaKategori));
        foreach ($styleMap as $keyword => $style) {
            if ($keyword === 'default') continue;
            if (str_contains($lower, $keyword)) return $style;
        }
        return $styleMap['default'];
    }
}

$katStyleMap = [];
foreach ($kategoris as $k) {
    $katStyleMap[$k['id']] = getKategoriStyle(
        $k['name'],
        $k['description'] ?? '',
        $categoryStyles
    );
}
$katStyleMap[0] = $categoryStyles['default'];
?>

<style>
.menu-card {
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    border-radius: 12px !important;
    overflow: hidden;
}
.menu-card:hover:not(.unavailable) {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .15) !important;
}
.menu-card.unavailable {
    cursor: default;
    opacity: .55;
    filter: grayscale(0.4);
}
.menu-card .card-header-cat { height: 6px; }
.menu-card .cat-icon { font-size: 22px; line-height: 1; }
.stock-badge { font-size: 10px; border-radius: 20px; padding: 2px 7px; }
.order-items-scroll { max-height: 280px; overflow-y: auto; }
.order-item-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
}
</style>

<div class="row g-4">
    <!-- KIRI: Pilih Menu -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <span class="text-muted small me-1">Kategori:</span>
                    <button onclick="filterKategori(0)" id="cat-0" class="btn btn-sm btn-success">Semua</button>
                    <?php foreach ($kategoris as $k):
                            $ks = $katStyleMap[$k['id']];
                        ?>
                    <button onclick="filterKategori(<?= $k['id'] ?>)" id="cat-<?= $k['id'] ?>"
                        class="btn btn-sm btn-outline-secondary">
                        <?= $ks['icon'] ?> <?= esc($k['name']) ?>
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
            <?php foreach ($menus as $m):
                $katId      = $m['category_id'] ?? 0;
                $ks         = $katStyleMap[$katId] ?? $categoryStyles['default'];
                $stockMenu  = $m['stock_menu'];
                $isOutOfStock = ($stockMenu !== null && $stockMenu <= 0);
                $isUnavail  = ($m['status'] != 'available') || $isOutOfStock;
                $stokParam  = ($stockMenu !== null) ? (int)$stockMenu : 'null';
                $namaEsc    = addslashes($m['name']);
            ?>
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm menu-card h-100 <?= $isUnavail ? 'unavailable' : '' ?>"
                    <?php if (!$isUnavail): ?>
                    onclick="tambahItem(<?= $m['id'] ?>, '<?= $namaEsc ?>', <?= $m['price'] ?>, <?= $stokParam ?>)"
                    <?php endif; ?>>
                    <div class="card-header-cat" style="background:<?= $ks['gradient'] ?>"></div>
                    <div class="card-body pt-2 pb-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <span class="cat-icon"><?= $ks['icon'] ?></span>
                                <div>
                                    <h6 class="card-title mb-0 fw-semibold" style="font-size:13px;line-height:1.2">
                                        <?= esc($m['name']) ?></h6>
                                    <?php if ($stockMenu !== null): ?>
                                        <?php if ($stockMenu <= 0): ?>
                                        <span class="stock-badge" style="background:#fee2e2;color:#dc2626">Stok habis</span>
                                        <?php elseif ($stockMenu <= 3): ?>
                                        <span class="stock-badge" style="background:#fef3c7;color:#d97706">Sisa <?= $stockMenu ?> pcs</span>
                                        <?php else: ?>
                                        <span class="stock-badge" style="background:#d1fae5;color:#065f46">Stok <?= $stockMenu ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                    <span class="stock-badge" style="background:#e0f2fe;color:#0369a1">Tersedia</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($isUnavail): ?>
                            <span class="badge ms-1"
                                style="font-size:10px;background:<?= $ks['badge'] ?>;color:#fff">Habis</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-success" style="font-size:13px">Rp
                                <?= number_format($m['price'], 0, ',', '.') ?></span>
                            <span class="badge"
                                style="font-size:9px;background:<?= $ks['badge'] ?>22;color:<?= $ks['badge'] ?>;border:1px solid <?= $ks['badge'] ?>44"><?= esc($m['nama_kategori'] ?? '') ?></span>
                        </div>
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
                    <div id="boxMeja">
                        <input type="text" class="form-control form-control-sm" id="pilihMeja"
                            placeholder="Contoh: Meja 1, Meja 2...">
                    </div>
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
                            <span class="fw-semibold">Rekomendasi Menu</span>
                        </div>
                        <div id="recommendationBox">
                            <small class="text-muted">Pilih menu terlebih dahulu</small>
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
const katStyles = <?= json_encode($katStyleMap, JSON_UNESCAPED_UNICODE) ?>;

let recommendationRules = [];
let orderItems = [];
let aktivKategori = 0;

function renderMenuCard(m) {
    const ks = katStyles[m.category_id] || katStyles[0];
    const stockMenu = m.stock_menu;
    const isOutOfStock = (stockMenu !== null && parseInt(stockMenu) <= 0);
    const unavail = m.status != 'available' || isOutOfStock;

    let stockHtml = '';
    if (stockMenu === null || stockMenu === undefined) {
        stockHtml = `<span class="stock-badge" style="background:#e0f2fe;color:#0369a1">Tersedia</span>`;
    } else if (parseInt(stockMenu) <= 0) {
        stockHtml = `<span class="stock-badge" style="background:#fee2e2;color:#dc2626">Stok habis</span>`;
    } else if (parseInt(stockMenu) <= 3) {
        stockHtml = `<span class="stock-badge" style="background:#fef3c7;color:#d97706">Sisa ${stockMenu} pcs</span>`;
    } else {
        stockHtml = `<span class="stock-badge" style="background:#d1fae5;color:#065f46">Stok ${stockMenu}</span>`;
    }

    const nama = m.name.replace(/'/g, "\\'");
    const stokParam = (stockMenu !== null && stockMenu !== undefined) ? stockMenu : 'null';
    return `<div class="col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm menu-card h-100 ${unavail ? 'unavailable' : ''}"
             ${!unavail ? `onclick="tambahItem(${m.id}, '${nama}', ${m.price}, ${stokParam})"` : ''}>
            <div class="card-header-cat" style="background:${ks.gradient}"></div>
            <div class="card-body pt-2 pb-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <span class="cat-icon">${ks.icon}</span>
                        <div>
                            <h6 class="card-title mb-0 fw-semibold" style="font-size:13px;line-height:1.2">${m.name}</h6>
                            ${stockHtml}
                        </div>
                    </div>
                    ${unavail ? `<span class="badge ms-1" style="font-size:10px;background:${ks.badge};color:#fff">Habis</span>` : ''}
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="fw-bold text-success" style="font-size:13px">Rp ${Number(m.price).toLocaleString('id-ID')}</span>
                    <span class="badge" style="font-size:9px;background:${ks.badge}22;color:${ks.badge};border:1px solid ${ks.badge}44">${m.nama_kategori ?? ''}</span>
                </div>
            </div>
        </div>
    </div>`;
}

function filterKategori(katId) {
    aktivKategori = katId;
    document.querySelectorAll('[id^="cat-"]').forEach(btn => {
        btn.className = 'btn btn-sm btn-outline-secondary';
    });
    document.getElementById('cat-' + katId).className = 'btn btn-sm btn-success';

    fetch('<?= base_url('kasir/pesanan/menus') ?>?kategori=' + katId)
        .then(r => r.json())
        .then(menus => {
            const container = document.getElementById('menuGrid');
            if (menus.length === 0) {
                container.innerHTML = '<div class="col-12"><div class="alert alert-info">Tidak ada menu tersedia.</div></div>';
                return;
            }
            container.innerHTML = menus.map(renderMenuCard).join('');
        });
}

fetch("/ml/recomendation.json")
    .then(res => res.json())
    .then(data => { recommendationRules = data; })
    .catch(err => { console.log("Rekomendasi tidak tersedia:", err); });

function tambahItem(id, nama, harga, stokMenu) {
    const existing = orderItems.find(i => i.id === id);
    const stok = (stokMenu !== null && stokMenu !== undefined && stokMenu !== 'null')
                 ? parseInt(stokMenu) : null;

    if (existing) {
        if (stok !== null && existing.qty >= stok) {
            alert('Stok "' + nama + '" saat ini hanya ' + stok + ' pcs, tidak bisa melebihi stok yang tersedia.');
            return;
        }
        existing.qty++;
    } else {
        if (stok !== null && stok <= 0) {
            alert('Stok "' + nama + '" sudah habis.');
            return;
        }
        orderItems.push({ id, nama, harga, qty: 1, catatan: '', stokMenu: stok });
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
    const cartNames = orderItems.map(i => i.nama);
    recommendationRules.forEach(rule => {
        const match = rule.antecedents.every(a =>
            cartNames.some(c => c.trim().toLowerCase() === a.trim().toLowerCase())
        );
        if (match) {
            rule.consequents.forEach(item => {
                if (!cartNames.some(c => c.trim().toLowerCase() === item.trim().toLowerCase())) {
                    recommendations.push({ item, confidence: rule.confidence });
                }
            });
        }
    });
    recommendations.sort((a, b) => b.confidence - a.confidence);
    let result = [], seen = new Set();
    for (let r of recommendations) {
        if (!seen.has(r.item)) { seen.add(r.item); result.push(r); }
        if (result.length === 3) break;
    }
    renderRecommendations(result);
}

function renderRecommendations(data) {
    const box = document.getElementById("recommendationBox");
    if (data.length === 0) {
        box.innerHTML = `<small class="text-muted">Tidak ada rekomendasi</small>`;
        return;
    }
    let html = `<div class="d-flex flex-wrap gap-2">`;
    data.forEach(r => {
        const menu = allMenus.find(m => m.name.trim().toLowerCase() === r.item.trim().toLowerCase());
        if (!menu) return;
        const stokParam = (menu.stock_menu !== null && menu.stock_menu !== undefined) ? menu.stock_menu : 'null';
        html += `<button type="button" class="btn btn-sm btn-warning"
            onclick="tambahItem(${menu.id},'${menu.name.replace(/'/g,"\\'")}',${menu.price},${stokParam})">
            + ${menu.name}</button>`;
    });
    html += `</div>`;
    box.innerHTML = html;
}

function renderOrder() {
    const container = document.getElementById('orderItems');
    if (orderItems.length === 0) {
        container.innerHTML = '<p class="text-muted small text-center py-3"><i class="bi bi-cart-x fs-4 d-block mb-1"></i>Belum ada item dipilih</p>';
        document.getElementById('subtotalText').textContent = 'Rp 0';
        document.getElementById('itemCountText').textContent = '0 item';
        document.getElementById('recommendationBox').innerHTML = `<small class="text-muted">Pilih menu terlebih dahulu</small>`;
        return;
    }
    let html = '', subtotal = 0, totalItem = 0;
    orderItems.forEach(item => {
        const sub = item.harga * item.qty;
        subtotal += sub;
        totalItem += item.qty;
        const stokParam = (item.stokMenu !== null && item.stokMenu !== undefined) ? item.stokMenu : 'null';
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
                <button class="btn btn-sm btn-outline-success px-2 py-0" onclick="tambahItem(${item.id},'${item.nama.replace(/'/g,"\\'")}',${item.harga},${stokParam})">+</button>
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
        const nama = catatan.trim();
        if (!nama) {
            alert('Isi nama / nomor HP pelanggan untuk takeaway!');
            return;
        }
        catatan = '[Takeaway: ' + nama + '] ';
    }
    document.getElementById('inputMeja').value = meja;
    document.getElementById('inputCatatan').value = catatan;
    document.getElementById('inputItems').value = JSON.stringify(orderItems);
    document.getElementById('formPesanan').submit();
}
</script>

<?= $this->include('kasir/layouts/footer') ?>
