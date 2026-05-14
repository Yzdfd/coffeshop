<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Struk</title>

    <style>
    @page {
        /* Menghilangkan margin browser saat print */
        margin: 0;
    }

    body {
        font-family: 'Courier New', Courier, monospace;
        /* Monospace wajib biar sejajar */
        width: 58mm;
        /* Standar kertas thermal kecil */
        margin: 0 auto;
        padding: 5px;
        font-size: 11px;
        /* Dikecilkan dikit biar gak wrap ke bawah */
        color: #000;
        line-height: 1.2;
    }

    .center {
        text-align: center;
    }

    .line {
        border-top: 1px dashed #000;
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        padding: 1px 0;
        vertical-align: top;
    }

    .right {
        text-align: right;
    }

    /* Utility buat teks yang kepanjangan */
    .item-name {
        word-break: break-all;
    }

    @media print {
        body {
            width: 58mm;
            padding: 2mm;
        }

        /* Sembunyikan tombol print jika ada */
    }
    </style>
</head>

<body onload="window.print()">

    <div class="center">
        <strong style="font-size: 13px; display: block;">
            <?= esc($setting['store_name'] ?? 'Coffee Shop') ?>
        </strong>
        <span style="font-size: 10px;">
            <?= esc($setting['store_address'] ?? '-') ?>
        </span>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No</td>
            <td>: TRX<?= str_pad($transaksi['id'], 3, '0', STR_PAD_LEFT) ?></td>
        </tr>
        <tr>
            <td>Tgl</td>
            <td>: <?= date('d/m/y H:i', strtotime($transaksi['paid_at'])) ?></td>
        </tr>
        <tr>
            <td>Ksr</td>
            <td>: <?= esc(substr($transaksi['kasir_name'], 0, 10)) ?></td>
        </tr>
        <tr>
            <td>Meja</td>
            <td>: <?= $order['table_number'] ?? 'Takeaway' ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <?php foreach ($items as $item): ?>
    <div class="item-name"><?= esc($item['menu_name']) ?></div>
    <table>
        <tr>
            <td style="width: 50%;">
                <?= $item['qty'] ?> x <?= number_format($item['unit_price'], 0, ',', '.') ?>
            </td>
            <td class="right">
                <?= number_format($item['qty'] * $item['unit_price'], 0, ',', '.') ?>
            </td>
        </tr>
    </table>
    <?php endforeach; ?>

    <div class="line"></div>

    <table>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="right">
                <strong>Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></strong>
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="center" style="margin-top: 10px;">
        Terima Kasih ☕<br>
        Silahkan Datang Kembali
    </div>

</body>

</html>