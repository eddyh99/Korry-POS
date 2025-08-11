<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <?php if (isset($_SESSION["message"])){?>
        <div class="alert alert-success"><?=$_SESSION["message"]?></div>
        <?php } ?>
        <?php if (isset($_SESSION["gagal"])){?>
        <div class="alert alert-danger"><?=$_SESSION["gagal"]?></div>
        <?php } ?>

        <div class="card">
            <div class="card-content">
                <form action="<?=base_url()?>staff/kas/tutupharian" method="get">
                    <?php if (($_SESSION["logged_status"]["role"]=="Office Manager")||($_SESSION["logged_status"]["role"]=="Office Staff")||($_SESSION["logged_status"]["role"]=="Owner")){?>
                    <div class="row form-group">
        				<label class="col-form-label col-sm-1">Store</label>
    	                <div class="col-sm-3">
    	                    <select class="form-control" name="store" id="store">
    	                        <?php foreach($store as $dt){?>
    	                                <option value="<?=$dt["storeid"]?>" <?php echo ($dt["storeid"]==$storeid) ? "selected":"" ?>><?=$dt["store"]?></option>
    	                        <?php }?>
    	                    </select>
                        </div>
                    </div>
                    <?php }?>
                    <div class="row form-group">
        				<label class="col-form-label col-sm-1">Tanggal</label>
    	                <div class="col-sm-3">
                            <input type="text" id="tgl" name="tgl" class="form-control" value="<?=$tgl?>">
                        </div>
    	                <div class="col-sm-1">
                            <button type="submit" class="btn btn-primary">Lihat</button>
                        </div>
                    </div>
                </form>

                <div class="row" id="printarea">
                    <div class="col-sm-12 col-md-6 text-center"><h3>Laporan Penjualan Harian</h3></div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-form-label col-sm-3">Tanggal</label>
                            <label class="col-form-label col-sm-2 text-right"><?=$tgl?></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label col-sm-3"><b>TOTAL PENJUALAN</b></label>
                            <label class="col-form-label col-sm-2 text-right"><b><?=number_format($penjualan["jual"])?></b></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label col-sm-3"><b>TOTAL RETUR</b></label>
                            <label class="col-form-label col-sm-2 text-right"><b><?=number_format($penjualan["retur"]["tunai"]+$penjualan["retur"]["non"])?></b></label>
                        </div>
                        <br>
                        <label class="col-form-label col-sm-12"><b>TUNAI</b></label>
                        <div class="col-sm-12">
                            <label class="col-form-label col-sm-3">Sisa Kas Sebelumnya</label>
                            <label class="col-form-label col-sm-2 text-right"><?=number_format($penjualan["saldo"])?></label>
                            <label class="col-sm-7">&nbsp;</label>
                            <label class="col-form-label col-sm-3">Total Tunai</label>
                            <label class="col-form-label col-sm-2 text-right"><?=number_format($penjualan["tunai"]-$penjualan["retur"]["tunai"]);?></label>
                            <label class="col-sm-7">&nbsp;</label>
                        </div>
                        <br>        
                        <div class="form-group">
                            <label class="col-form-label col-sm-3"><b>NON TUNAI</b></label>
                            <label class="col-form-label col-sm-2 text-right"><b><?=number_format($penjualan["jual"]-$penjualan["tunai"]-$penjualan["retur"]["non"])?></b></label>
                            <label class="col-sm-7">&nbsp;</label>
                        </div>
                        <br>
                        <label class="col-form-label col-sm-12"><b>KAS KELUAR</b></label>
                        <div class="col-sm-12">
                            <table border="0">
                            <?php 
                                $kaskeluar=0;
                                foreach($penjualan["kas"] as $dt){
                                    if ($dt["jenis"]=="Keluar"){
                                        $kaskeluar=$kaskeluar+$dt["nominal"];
                            ?>
                                        <tr>
                                            <td class="col-sm-10"><label class="col-form-label"><?=$dt["keterangan"]?></label></td>
                                            <td align="right"><?=number_format($dt["nominal"])?></td>
                                        </tr>
                            <?php   }
                                  }
                            ?>
                            </table>
                        </div>
                        <br>
                        <label class="col-form-label col-sm-12"><b>KAS MASUK</b></label>
                        <div class="col-sm-12">
                            <table border="0">
                            <?php 
                                $kasmasuk=0;
                                foreach($penjualan["kas"] as $dt){
                                    if ($dt["jenis"]=="Masuk"){
                                        $kasmasuk=$kasmasuk+$dt["nominal"];
                            ?>
                                        <tr>
                                            <td class="col-sm-10"><label class="col-form-label"><?=$dt["keterangan"]?></label></td>
                                            <td align="right"><?=number_format($dt["nominal"])?></td>
                                        </tr>
                            <?php   }
                                  }
                            ?>
                            </table>
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="col-form-label col-sm-3"><b>TOTAL UANG TUNAI</b></label>
                            <label class="col-form-label col-sm-2 text-right"><b>
                                <?php 
                                    $totaltunai=$penjualan["tunai"]+$penjualan["saldo"]-$kaskeluar+$kasmasuk-$penjualan["retur"]["tunai"];
                                    echo number_format($totaltunai);
                                ?></b>
                            </label>
                            <label class="col-sm-7">&nbsp;</label>
                        </div>
                        <div class="form-group mt-2">
                            <label class="col-form-label col-sm-3"><b>UANG DISETOR</b></label>
                            <label class="col-form-label col-sm-2 text-right"><b>
                                <?php
                                    $setor=floor($totaltunai / 50000)*50000;
                                    echo number_format(floor($totaltunai / 50000)*50000);
                                ?></b>
                            </label>
                            <label class="col-sm-7">&nbsp;</label>
                        </div>
                        <form action="<?=base_url()?>staff/kas/sisakas" method="POST">
                            <input type="hidden" name="tglback" value="<?=$tgl?>">
                            <input type="hidden" name="storeid" value="<?=$storeid?>">
                            <div class="form-group">
                                <label class="col-form-label col-sm-3"><b>SISA</b></label>
                                <label class="col-form-label col-sm-2 text-right">
                                    <input type="hidden" name="sisa" value="<?=($totaltunai-$setor)?>">
                                    <b><?php echo number_format($totaltunai-$setor)?></b>
                                </label>
                                <label class="col-sm-7">&nbsp;</label>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <input type="button" class="btn btn-primary" onclick="printDiv('printarea')" value="Print">
                                    <input type="button" class="btn btn-primary" onclick="printPDF('printarea')" value="PDF">
                                </div>
                                <?php if (($tgl==date("d M Y")) || ($_SESSION["logged_status"]["role"]=="Store Manager") || ($_SESSION["logged_status"]["role"]=="Office Manager")){?>
                                <div class="col-sm-2 text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                                <?php }?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!-- End Container -->
    </div>
</div>