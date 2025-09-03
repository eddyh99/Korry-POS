<html> 
<head>
<style>
    /* === Fonts (via base_url) === */
    @font-face{
        font-family:'Agrandir';
        src:url('<?=base_url()?>assets/fonts/Agrandir Family/Agrandir-RegularC3.otf') format('opentype');
        font-weight:400;
    }
    @font-face{
        font-family:'Agrandir';
        src:url('<?=base_url()?>assets/fonts/Agrandir Family/Agrandir-BoldC5.otf') format('opentype');
        font-weight:700;
    }
    @font-face{
        font-family:'Agrandir Wide';
        src:url('<?=base_url()?>assets/fonts/Agrandir Family/Agrandir-WideBoldD5.otf') format('opentype');
        font-weight:700;
    }

    body{font-family:'Agrandir',sans-serif;font-size:12px;color:#000;margin:20px;}
    .regular{font-weight:400;}
    .regular-bold{font-weight:700;}
    .bold-wide{font-family:'Agrandir Wide',sans-serif;font-weight:700;}

    .invoice-header{width:100%;margin-bottom:15px;}
    .invoice-header td{vertical-align:top;}
    .logo img{height:48px;}
    .invoice-title{font-size:16px;}

    table{border-collapse:collapse;width:100%;}
    th,td{border:1px solid #000;padding:5px;font-size:12px;}
    th{background:#f2f2f2;text-align:left;}
    td.num,th.num{text-align:right;}

    table.no-border td{border:none !important;padding:2px 5px;}

    /* summary */
    .summary {
        border-collapse: collapse;
        margin-top: 10px;
        margin-left: auto;  /* supaya nempel ke kanan */
        width: auto;        /* jangan full, cukup selebar konten */
    }
    .summary td {
        padding: 4px 8px;
        border: none;
        font-size: 12px;
    }
    .summary .label {
        text-align: right;
        padding-right: 10px;
    }
    .summary .value {
        text-align: right;
        width: 150px;
        white-space: nowrap;
    }

    /* underline untuk value saja (lama, masih bisa dipakai kalau perlu) */
    .summary .underline .value {
        border-bottom: 1px solid #000;
    }

    /* underline untuk seluruh baris (label + value) */
    .summary .row-underline td {
        border-bottom: 1px solid #000;
    }

    /* sejajarkan IDR dan angka */
    .summary .value .currency {
        display: inline-block;
        width: 28px; /* lebar tetap untuk IDR */
        text-align: left;
    }
    .summary .value .amount {
        display: inline-block;
        text-align: right;
        min-width: 80px; /* supaya angka rata kanan */
    }

    /* bank info */
    .bank-info {border-collapse: collapse; margin-top: 5px; width: auto;}
    .bank-info td {border: none; padding: 2px 6px; font-size: 12px;}
    .bank-info .label {white-space: nowrap; padding-right: 2px;}
    .bank-info .sep {text-align:center; padding: 0 4px;}
    .bank-info .value {white-space: nowrap; padding-left: 2px;}
</style>

</head>
<body>

<table class="invoice-header no-border">
  <tr>
    <td>
      <div class="logo"><img src="<?=base_url()?>assets/img/korry-crop.png" alt="Korry Logo"></div>
      <br>
      <div class="regular"><?=$store->alamat?></div>
      <div class="regular"><?=$store->kontak?></div>
    </td>
    <td align="right">
      <div class="invoice-title bold-wide">INVOICE</div>
      <div class="regular">#<?=$data["header"]->nonota?></div>
      <div class="regular-bold"><?=date("d-M-Y", strtotime($data["header"]->tgl_cicilan))?></div>
    </td>
  </tr>
</table>

<div class="bold-wide" style="margin:10px 0 5px;">BILL TO</div>
<div class="regular"><?=$data["header"]->nama_wholesaler?></div>

<br>

<table>
  <tr class="regular">
    <th>No</th>
    <th>Item</th>
    <th>Size</th>
    <th>Colour</th>
    <th>SKU</th>
    <th class="num">Qty</th>
    <th class="num">Price</th>
    <th class="num">Potongan</th>
    <th class="num">Total</th>
  </tr>
  <?php 
    $i=1; 
    $subtotal=0;
    foreach ($data["detail"] as $dt) {
      // hitung total per baris dengan potongan
      $lineTotal = ($dt["jumlah"] * $dt["harga"]) - $dt["potongan"];
      $subtotal += $lineTotal;
  ?>
  <tr class="regular">
    <td><?=$i++?></td>
    <td><?=$dt["namaproduk"]?></td>
    <td><?=$dt["size"]?></td>
    <td><?=$dt["warna"]?></td>
    <td><?=$dt["sku"]?></td>
    <td class="num"><?=$dt["jumlah"]?></td>
    <td class="num"><?=number_format($dt["harga"])?></td>
    <td class="num"><?=number_format($dt["potongan"])?></td>
    <td class="num"><?=number_format($lineTotal)?></td>
  </tr>
  <?php } ?>
</table>

<?php 
  // Ambil diskon & ppn dari header
  $diskonNominal = floatval($data["header"]->diskon); // ini nominal, bukan persen
  $ppnPersen     = floatval($data["header"]->ppn);
  $down_payment  = floatval($data["header"]->dp);

  // Hitung subtotal setelah diskon
  $afterDiskon = $subtotal - $diskonNominal;

  // Hitung PPN dari afterDiskon
  $ppnNominal = ($ppnPersen/100) * $afterDiskon;

  // Grand total
  $grandTotal = $afterDiskon + $ppnNominal;

  // Untuk tampilan, hitung persentase diskon agar mirip invoice
  $diskonPersenView = $subtotal > 0 ? round(($diskonNominal/$subtotal)*100,2) : 0;

  // Hitung total cicilan (dari semua payment)
  $totalPaid = 0;
  if (!empty($data["cicilan"])) {
      foreach ($data["cicilan"] as $c) {
          $totalPaid += floatval($c["bayar"]);
      }
  }

  // Hitung amount due
  $amountDue = $grandTotal - $down_payment - $totalPaid;
?>

<!-- summary -->
<table class="summary">
  <tr class="regular">
    <td class="label">Subtotal</td>
    <td class="value">
      <span class="amount"><?=number_format($subtotal)?></span>
    </td>
  </tr>
  <tr class="regular">
    <td class="label">Discount (<?=$diskonPersenView?>%)</td>
    <td class="value">
      <span class="amount">-<?=number_format($diskonNominal)?></span>
    </td>
  </tr>
  <tr class="regular row-underline">
    <td class="label">VAT (<?=$ppnPersen?>%)</td>
    <td class="value">
      <span class="amount">+<?=number_format($ppnNominal)?></span>
    </td>
  </tr>
  <tr class="regular">
    <td class="label">Total</td>
    <td class="value">
      <span class="currency">IDR</span>
      <span class="amount"><?=number_format($grandTotal)?></span>
    </td>
  </tr>
  <tr class="regular row-underline">
    <td class="label">Down Payment</td>
    <td class="value">
      <span class="currency">IDR</span>
      <span class="amount">-<?=number_format($down_payment)?></span>
    </td>
  </tr>

  <?php if (!empty($data["cicilan"])): ?>
    <?php foreach ($data["cicilan"] as $i => $c): ?>
      <tr class="regular <?=($i === array_key_last($data["cicilan"])) ? 'row-underline' : ''?>">
        <td class="label">Payment <?=$i+1?></td>
        <td class="value">
          <span class="currency">IDR</span>
          <span class="amount">-<?=number_format($c["bayar"])?></span>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>

  <tr>
    <td class="label bold-wide">Amount Due</td>
    <td class="value">
      <span class="currency">IDR</span>
      <span class="amount"><?=number_format($amountDue)?></span>
    </td>
  </tr>
</table>

<br><br><br>

<div class="bold-wide">TERMS</div>
<div class="regular">
  Please settle the remaining balance to proceed with shipment.<br>
  Payment is due within <?=$data["header"]->lama?> days.
</div>

<br>

<div class="bold-wide">PAYMENT METHOD VIA BANK TRANSFER</div>
<table class="bank-info regular">
  <tr><td class="label">ACCOUNT NAME</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">ACCOUNT NUMBER</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">BANK NAME</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">BRANCH</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">SWIFT CODE (for international transfer)</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">CURRENCY</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
  <tr><td class="label">COUNTRY</td><td class="sep">:</td><td class="value">XXXXXXXX</td></tr>
</table>

<br>

<!-- <script>
  window.onafterprint = window.close;
  window.print();
</script> -->
<script>
window.onload = function() {
    // langsung munculin dialog print
    window.print();

    // setelah print selesai atau dibatalkan, balik ke daftar cicilan
    window.onafterprint = function() {
        window.location.href = "<?= base_url('admin/wholesale/cicilan') ?>";
    };
};
</script>


</body>
</html>
