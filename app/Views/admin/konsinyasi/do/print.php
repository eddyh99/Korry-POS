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
    .summary{border-collapse:collapse;margin-top:10px;}
    .summary td{padding:4px 8px;border:none;font-size:12px;}
    .summary .label{text-align:right;padding-right:10px;}
    .summary .value{text-align:right;width:150px;}
    .summary .underline .value{border-bottom:1px solid #000;}

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
      <div class="invoice-title bold-wide">PROFORMA INVOICE</div>
      <div class="regular">#<?=$data["header"]->nonota?></div>
      <div class="regular-bold"><?=date("d-M-Y", strtotime($data["header"]->tanggal))?></div>
    </td>
  </tr>
</table>

<div class="bold-wide" style="margin:10px 0 5px;">BILL TO</div>
<div class="regular"><?=$data["header"]->nama_partner?></div>

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
    <th class="num">Total</th>
  </tr>
  <?php 
    $i=1; $subtotal=0;
    foreach ($data["detail"] as $dt) {
      $lineTotal = $dt["jumlah"]*$dt["harga"];
      $subtotal += $lineTotal;
  ?>
  <tr class="regular">
    <td><?=$i++?></td>
    <td><?=$dt["namaproduk"]?></td>
    <td><?=$dt["size"] ?? '-'?></td>
    <td><?=$dt["warna"] ?? '-'?></td>
    <td><?=$dt["sku"]?></td>
    <td class="num"><?=$dt["jumlah"]?></td>
    <td class="num"><?=number_format($dt["harga"])?></td>
    <td class="num"><?=number_format($lineTotal)?></td>
  </tr>
  <?php } ?>
</table>

<!-- summary -->
<table class="summary" align="right">
  <tr class="regular">
    <td class="label">Subtotal</td>
    <td class="value"><?=number_format($subtotal)?></td>
  </tr>
  <tr class="regular">
    <td class="label">Discount (%)</td>
    <td class="value">0</td>
  </tr>
  <tr class="regular underline">
    <td class="label">VAT (%)</td>
    <td class="value">0</td>
  </tr>
  <tr class="regular">
    <td class="label">Total</td>
    <td class="value">IDR <?=number_format($subtotal)?></td>
  </tr>
</table>

<br><br><br>

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

<div class="bold-wide" style="margin:10px 0 5px;">CHECKED & RECEIVED BY</div>
<div class="regular"><?=$data["header"]->nama_user?></div>

<script>
  window.onafterprint = window.close;
  window.print();
</script>

</body>
</html>
