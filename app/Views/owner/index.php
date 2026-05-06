<?= $this->include('kasir/layouts/header') ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3">💰</div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($totalRevenue ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3">🧾</div>
                <div>
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fw-bold fs-4"><?= (int) ($totalTransactions ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info fs-3">📅</div>
                <div>
                    <div class="text-muted small">Penjualan Hari Ini</div>
                    <div class="fw-bold" style="font-size:15px">Rp <?= number_format($todaySales ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-3">🍽️</div>
                <div>
                    <div class="text-muted small">Pesanan Aktif</div>
                    <div class="fw-bold fs-4"><?= (int)($activeOrders ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sub stat row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-secondary bg-opacity-10 text-secondary fs-4">📊</div>
                <div>
                    <div class="text-muted small">Rata-rata Nilai Order</div>
                    <div class="fw-bold" style="font-size:14px">Rp <?= number_format($avgOrder ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <a href="<?= base_url('owner/stok-alert') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-4">⚠️</div>
                    <div>
                        <div class="text-muted small">Alert Stok Bahan</div>
                        <div class="fw-bold text-warning" style="font-size:13px">Lihat stok kritis &rsaquo;</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-4">
        <form method="get" action="<?= base_url('owner/export-penjualan') ?>" class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-2"><i class="bi bi-download me-1"></i>Export Laporan CSV</div>
                <div class="d-flex gap-1 flex-wrap">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" style="width:130px">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" style="width:130px">
                    <button type="submit" class="btn btn-success btn-sm">Export</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Sales Chart</span>
                <div class="btn-group btn-group-sm" role="group" aria-label="chart mode">
                    <button type="button" class="btn btn-outline-secondary active" id="btnDaily">Daily</button>
                    <button type="button" class="btn btn-outline-secondary" id="btnMonthly">Monthly</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-people me-2"></i>Employee Performance (Transactions)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th style="width:140px">Transactions</th>
                                <th style="width:200px">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($employeePerf)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($employeePerf as $e): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc((string) ($e['user_name'] ?? '-')) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= (int) ($e['total_trx'] ?? 0) ?></span></td>
                                        <td>Rp <?= number_format($e['total_sales'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-award me-2"></i>Best Selling Menu (Top 5)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Menu</th>
                                <th style="width:110px">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topMenus)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-4">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topMenus as $m): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc((string) ($m['menu_name'] ?? '-')) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= (int) ($m['total_qty'] ?? 0) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const daily = {
        labels: <?= json_encode($dailyLabels ?? []) ?>,
        data: <?= json_encode($dailyTotals ?? []) ?>
    };
    const monthly = {
        labels: <?= json_encode($monthlyLabels ?? []) ?>,
        data: <?= json_encode($monthlyTotals ?? []) ?>
    };

    const ctx = document.getElementById('salesChart');
    const config = (payload, label) => ({
        type: 'line',
        data: {
            labels: payload.labels,
            datasets: [{
                label,
                data: payload.data,
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.15)',
                fill: true,
                tension: 0.25,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Rp ' + Number(ctx.parsed.y || 0).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    let chart = new Chart(ctx, config(daily, 'Daily Sales'));

    function setMode(mode) {
        const isDaily = mode === 'daily';
        document.getElementById('btnDaily').classList.toggle('active', isDaily);
        document.getElementById('btnMonthly').classList.toggle('active', !isDaily);
        chart.destroy();
        chart = new Chart(ctx, config(isDaily ? daily : monthly, isDaily ? 'Daily Sales' : 'Monthly Sales'));
    }

    document.getElementById('btnDaily')?.addEventListener('click', () => setMode('daily'));
    document.getElementById('btnMonthly')?.addEventListener('click', () => setMode('monthly'));
</script>

<?= $this->include('kasir/layouts/footer') ?>

