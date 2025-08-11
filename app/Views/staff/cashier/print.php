<html>
<style>
@media print and (width: 9.8cm) {
     @page {
        font-size:xx-small;
     }
}

#toko td, #footer td {
  text-align: center;
}
</style>
<body>
<center>
    <table border="0" width="100%" id="toko">
        <tr><td><img src="<?=base_url()?>assets/img/logo3.png" width="50%"></td></tr>
        <tr><td><?=$store->store?></td></tr>
        <tr><td><?=$store->alamat?></td></tr>
        <tr><td><?=$store->kontak?></td></tr>
    </table>
    <hr>
    <table border="0" width="100%" id="header">
        <tr><td>No. Invoice</td><td>:</td><td><?=$data["header"]->nonota?></td><td>Date</td><td>:</td><td><?php
            $datetime=explode(" ",$data["header"]->tanggal);
            echo date("d-m-Y", strtotime($datetime[0]));
        ?></td></tr>
        <tr><td>Cashier</td><td>:</td><td>&nbsp;</td><td>Time</td><td>:</td><td><?=$datetime[1];?></td></tr>
        <tr><td colspan="4"><?=$data["header"]->nama?></td></tr>
    </table>
</center>
<hr>
<table border="0" width="100%">
    <?php 
        $total=0;
        $disc=0;
        foreach ($data["detail"] as $dt){
            $total=$total+($dt["jumlah"]*$dt["harga"])-($dt["diskonn"]+$dt["diskonp"]);
    ?>
            <tr><td width="60%"><?=$dt["barcode"]?></td><td>Rp. <?=number_format($dt["harga"]*$dt["jumlah"]-($dt["diskonn"]+$dt["diskonp"]))?></td></tr>
            <tr><td colspan="2"><?=$dt["namaproduk"]?></td></tr>
            <tr><td colspan="2" style="padding-bottom:7px">Size:<?=$dt["size"]?> Qty : <?=$dt["jumlah"]?> x Rp. <?=number_format($dt["harga"]-($dt["diskonn"]+$dt["diskonp"]))?></td></tr>
    <?php } ?>
</table>
<hr>
<table border="0" width="100%">
    <tr><td>Total</td><td width="100px" align="right"><?=number_format($total)?></td></tr>
    <tr><td>Payment Method</td><td width="100px" align="right"><?=$data["header"]->method?></td></tr>
</table>
<br><br><br>
<center>
    <table border="0" width="100%" id="footer">
        <tr><td>THANK YOU FOR SHOPPING AT HANAKA</td></tr>
        <tr><td>Exch within 3 days with original receipt at store of the purchase only, and applied with same price or higher. Not applicable for discounted items. Any kind of refunds are not given unless the purchased items is defective.</td></tr>
        <tr><td><br>ig : @hanakaclassic</td></tr>
        <tr><td>www.hanakaclassic.com</td></tr>
    </table>
</center>
<script>
    window.onafterprint = window.close;
    window.print();
</script>
</body>
</html>