<?= $this->include('kasir/layouts/header') ?>

<!-- KDS Top Bar -->
<div class="kds-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="kds-label">Kitchen Display</span>
        <h6 class="mb-0 fw-bold">Incoming Orders</h6>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox" id="autoRefreshSwitch" role="switch">
            <label class="form-check-label small text-muted" for="autoRefreshSwitch">Auto refresh</label>
        </div>
        <button class="btn btn-sm btn-outline-secondary" id="btnRefresh">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
    </div>
</div>

<?php if (empty($orders)): ?>
<div class="kds-empty">
    <div class="kds-empty-icon">🍽️</div>
    <div class="kds-empty-text">Tidak ada pesanan masuk</div>
    <div class="kds-empty-sub">Semua pesanan sudah selesai diproses</div>
</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($orders as $o):
        // Tentukan urgensi berdasarkan waktu
        $menit = (time() - strtotime($o['ordered_at'])) / 60;
        $urgency = $menit >= 20 ? 'urgent' : ($menit >= 10 ? 'warning' : 'normal');

        // Hitung progress item
        $total   = count($o['items']);
        $cooked  = count(array_filter($o['items'], fn($i) => in_array($i['status'], ['cooking','ready'])));
        $pct     = $total > 0 ? round(($cooked / $total) * 100) : 0;
    ?>
    <div class="col-12 col-lg-6 col-xxl-4" id="order-card-<?= (int)$o['order_id'] ?>">
        <div class="kds-card kds-card--<?= $urgency ?>">

            <!-- Card Header -->
            <div class="kds-card-header">
                <div class="kds-card-id">
                    <span class="kds-hash">#</span><?= (int)$o['order_id'] ?>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if (!empty($o['table_number'])): ?>
                        <span class="kds-meja-badge">
                            <i class="bi bi-grid-3x3-gap"></i> Meja <?= esc((string)$o['table_number']) ?>
                        </span>
                    <?php else: ?>
                        <span class="kds-meja-badge kds-meja-badge--takeaway">
                            <i class="bi bi-bag"></i> Takeaway
                        </span>
                    <?php endif; ?>
                    <span class="kds-time <?= $urgency === 'urgent' ? 'kds-time--urgent' : ($urgency === 'warning' ? 'kds-time--warning' : '') ?>">
                        <i class="bi bi-clock"></i> <?= date('H:i', strtotime($o['ordered_at'])) ?>
                    </span>
                    <button class="kds-cancel-btn" onclick="cancelOrder(<?= (int)$o['order_id'] ?>)" title="Batalkan Order">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="kds-progress-wrap">
                <div class="kds-progress-bar" style="width:<?= $pct ?>%"></div>
            </div>

            <!-- Catatan order -->
            <?php if (!empty($o['order_notes'])): ?>
            <div class="kds-note">
                <i class="bi bi-sticky me-1"></i><?= esc((string)$o['order_notes']) ?>
            </div>
            <?php endif; ?>

            <!-- Items -->
            <div class="kds-items">
                <?php foreach ($o['items'] as $it): ?>
                <div class="kds-item kds-item--<?= $it['status'] ?>" id="item-row-<?= (int)$it['order_item_id'] ?>">
                    <div class="kds-item-left">
                        <span class="kds-item-qty"><?= (int)$it['qty'] ?>×</span>
                        <div>
                            <div class="kds-item-name">
                                <?= esc((string)($it['menu_name'] ?? '-')) ?>
                                <?php if (!empty($it['menu_id'])): ?>
                                <a href="<?= base_url('dapur/resep/' . (int)$it['menu_id']) ?>"
                                   target="_blank" class="kds-resep-link" title="Lihat Resep">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($it['notes'])): ?>
                            <div class="kds-item-note"><?= esc((string)$it['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="kds-item-right">
                        <!-- Status pill -->
                        <span class="kds-status-pill kds-status-pill--<?= $it['status'] ?>"
                              data-status-badge="<?= (int)$it['order_item_id'] ?>">
                            <?php if ($it['status'] === 'pending'): ?>
                                <i class="bi bi-hourglass-split"></i> Antri
                            <?php elseif ($it['status'] === 'cooking'): ?>
                                <i class="bi bi-fire"></i> Dimasak
                            <?php elseif ($it['status'] === 'ready'): ?>
                                <i class="bi bi-check-circle-fill"></i> Siap
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill"></i> Batal
                            <?php endif; ?>
                        </span>

                        <!-- Action buttons -->
                        <div class="kds-actions">
                            <?php if ($it['status'] === 'pending'): ?>
                                <button class="kds-btn kds-btn--cook"
                                        onclick="updateItemStatus(<?= (int)$it['order_item_id'] ?>, 'cooking')">
                                    <i class="bi bi-fire me-1"></i>Mulai Masak
                                </button>
                                <button class="kds-btn kds-btn--cancel-item"
                                        onclick="updateItemStatus(<?= (int)$it['order_item_id'] ?>, 'cancelled')"
                                        title="Batalkan item ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php elseif ($it['status'] === 'cooking'): ?>
                                <button class="kds-btn kds-btn--ready"
                                        onclick="updateItemStatus(<?= (int)$it['order_item_id'] ?>, 'ready')">
                                    <i class="bi bi-check2-circle me-1"></i>Selesai
                                </button>
                                <button class="kds-btn kds-btn--cancel-item"
                                        onclick="updateItemStatus(<?= (int)$it['order_item_id'] ?>, 'cancelled')"
                                        title="Batalkan item ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
/* ── KDS Top Bar ──────────────────────────────────────── */
.kds-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; display: block; }

/* ── Empty State ─────────────────────────────────────── */
.kds-empty { text-align: center; padding: 4rem 2rem; }
.kds-empty-icon { font-size: 3rem; margin-bottom: .75rem; }
.kds-empty-text { font-weight: 600; font-size: 1.1rem; color: #334155; }
.kds-empty-sub  { color: #94a3b8; font-size: .875rem; margin-top: .25rem; }

/* ── Order Card ──────────────────────────────────────── */
.kds-card {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    background: #fff;
    border: 1.5px solid #e2e8f0;
    transition: box-shadow .2s;
}
.kds-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.kds-card--urgent { border-color: #fca5a5; }
.kds-card--warning { border-color: #fcd34d; }

/* Card header */
.kds-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .9rem 1rem .75rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    gap: .5rem;
    flex-wrap: wrap;
}
.kds-card--urgent  .kds-card-header { background: #fff5f5; border-color: #fca5a5; }
.kds-card--warning .kds-card-header { background: #fffbeb; border-color: #fde68a; }

.kds-card-id {
    font-size: 1.2rem;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: -.02em;
}
.kds-hash { color: #94a3b8; font-weight: 400; font-size: 1rem; }

.kds-meja-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: #1e293b;
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    padding: .25rem .65rem;
    border-radius: 6px;
    letter-spacing: .01em;
}
.kds-meja-badge--takeaway { background: #64748b; }

.kds-time {
    font-size: .75rem;
    font-weight: 600;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #f1f5f9;
    padding: .25rem .6rem;
    border-radius: 6px;
}
.kds-time--warning { background: #fef9c3; color: #854d0e; }
.kds-time--urgent  { background: #fee2e2; color: #991b1b; animation: pulse-time 1.5s ease-in-out infinite; }
@keyframes pulse-time { 0%,100% { opacity:1; } 50% { opacity:.6; } }

.kds-cancel-btn {
    background: none;
    border: 1.5px solid #e2e8f0;
    color: #94a3b8;
    border-radius: 6px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: .8rem;
    transition: all .15s;
    padding: 0;
    flex-shrink: 0;
}
.kds-cancel-btn:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* Progress bar */
.kds-progress-wrap { height: 3px; background: #e2e8f0; }
.kds-progress-bar  { height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); transition: width .4s ease; }

/* Order note */
.kds-note {
    margin: .6rem .85rem;
    padding: .45rem .75rem;
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    border-radius: 0 6px 6px 0;
    font-size: .8rem;
    color: #78350f;
}

/* Items list */
.kds-items { padding: .5rem .75rem .75rem; display: flex; flex-direction: column; gap: .4rem; }

.kds-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .6rem .75rem;
    border-radius: 10px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    transition: background .15s, border-color .15s;
}
.kds-item--cooking { background: #fffbeb; border-color: #fde68a; }
.kds-item--ready   { background: #f0fdf4; border-color: #bbf7d0; opacity: .7; }
.kds-item--cancelled { background: #f8fafc; opacity: .45; }

.kds-item-left {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    flex: 1;
    min-width: 0;
}
.kds-item-qty {
    font-size: 1rem;
    font-weight: 800;
    color: #4e73df;
    min-width: 28px;
    flex-shrink: 0;
    line-height: 1.4;
}
.kds-item-name {
    font-weight: 600;
    font-size: .875rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: .35rem;
    flex-wrap: wrap;
}
.kds-item-note {
    font-size: .75rem;
    color: #64748b;
    margin-top: .1rem;
}
.kds-resep-link {
    color: #94a3b8;
    text-decoration: none;
    font-size: .8rem;
    transition: color .15s;
}
.kds-resep-link:hover { color: #4e73df; }

.kds-item-right {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
}

/* Status pills */
.kds-status-pill {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 600;
    padding: .25rem .6rem;
    border-radius: 999px;
    white-space: nowrap;
}
.kds-status-pill--pending   { background: #f1f5f9; color: #64748b; }
.kds-status-pill--cooking   { background: #fef3c7; color: #92400e; }
.kds-status-pill--ready     { background: #dcfce7; color: #166534; }
.kds-status-pill--cancelled { background: #fee2e2; color: #991b1b; }

/* Action buttons */
.kds-actions { display: flex; gap: .35rem; }
.kds-btn {
    display: inline-flex;
    align-items: center;
    border: none;
    border-radius: 8px;
    font-size: .78rem;
    font-weight: 600;
    padding: .35rem .75rem;
    cursor: pointer;
    transition: filter .15s, transform .1s;
    white-space: nowrap;
}
.kds-btn:active { transform: scale(.96); }
.kds-btn:hover  { filter: brightness(.92); }

.kds-btn--cook        { background: #4e73df; color: #fff; }
.kds-btn--ready       { background: #16a34a; color: #fff; }
.kds-btn--cancel-item {
    background: #f1f5f9;
    color: #94a3b8;
    padding: .35rem .5rem;
    border: 1.5px solid #e2e8f0;
}
.kds-btn--cancel-item:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

/* loading state */
.kds-item.loading { opacity: .5; pointer-events: none; }
</style>

<script>
const updateStatusUrl = '<?= base_url('dapur/updateStatus') ?>';

function getCsrf() {
    const m = document.cookie.match(/csrf_cookie_name=([^;]+)/);
    return m ? m[1] : '';
}

async function postStatus(payload) {
    const res = await fetch(updateStatusUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-CSRF-TOKEN': getCsrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams(payload).toString(),
    });
    return res.json();
}

async function updateItemStatus(itemId, status) {
    const row = document.getElementById(`item-row-${itemId}`);
    if (row) row.classList.add('loading');

    try {
        const data = await postStatus({ order_item_id: itemId, status });
        if (!data?.success) { alert(data?.message || 'Gagal update status.'); return; }
        setTimeout(() => location.reload(), 180);
    } catch {
        alert('Terjadi kesalahan jaringan.');
    } finally {
        if (row) row.classList.remove('loading');
    }
}

async function cancelOrder(orderId) {
    if (!confirm('Batalkan order #' + orderId + '?')) return;
    const card = document.getElementById(`order-card-${orderId}`);
    if (card) card.style.opacity = '.5';

    try {
        const data = await postStatus({ action: 'cancel_order', order_id: orderId });
        if (!data?.success) { alert(data?.message || 'Gagal membatalkan order.'); if (card) card.style.opacity = ''; return; }
        if (card) card.remove();
    } catch {
        alert('Terjadi kesalahan jaringan.');
        if (card) card.style.opacity = '';
    }
}

document.getElementById('btnRefresh')?.addEventListener('click', () => location.reload());

// Auto refresh
const autoSwitch = document.getElementById('autoRefreshSwitch');
if (autoSwitch) {
    autoSwitch.checked = localStorage.getItem('dapur_auto_refresh') === '1';
    autoSwitch.addEventListener('change', () => {
        localStorage.setItem('dapur_auto_refresh', autoSwitch.checked ? '1' : '0');
    });
    setInterval(() => {
        if (localStorage.getItem('dapur_auto_refresh') === '1') location.reload();
    }, 15000);
}
</script>

<?= $this->include('kasir/layouts/footer') ?>
