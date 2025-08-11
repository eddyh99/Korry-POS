<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-content">
                    <div class="row form-group">
        				<label class="col-form-label col-sm-1">Bulan</label>
        				<div class="col-sm-2">
        				    <select name="bulan" id="bulan" class="form-control">
        				        <option value="1" <?php echo (date("m")==1) ? "selected":"" ?> >Januari</option>
        				        <option value="2" <?php echo (date("m")==2) ? "selected":"" ?> >Februari</option>
        				        <option value="3" <?php echo (date("m")==3) ? "selected":"" ?> >Maret</option>
        				        <option value="4" <?php echo (date("m")==4) ? "selected":"" ?> >April</option>
        				        <option value="5" <?php echo (date("m")==5) ? "selected":"" ?> >Mei</option>
        				        <option value="6" <?php echo (date("m")==6) ? "selected":"" ?> >Juni</option>
        				        <option value="7" <?php echo (date("m")==7) ? "selected":"" ?> >Juli</option>
        				        <option value="8" <?php echo (date("m")==8) ? "selected":"" ?> >Agustus</option>
        				        <option value="9" <?php echo (date("m")==9) ? "selected":"" ?> >September</option>
        				        <option value="10" <?php echo (date("m")==10) ? "selected":"" ?> >Oktober</option>
        				        <option value="11" <?php echo (date("m")==11) ? "selected":"" ?> >November</option>
        				        <option value="12" <?php echo (date("m")==12) ? "selected":"" ?> >Desember</option>
        				    </select>
        				</div>
        				<div class="col-sm-1">
        				    <input type="text" name="tahun" id="tahun" class="form-control" value="<?=date("Y")?>">
        				</div>
                    </div>
                    <div class="form-group row">
        				<label class="col-form-label col-sm-1">Toko</label>
        				<div class="col-sm-2">
        					<select id="store" class="form-control" name="store">
        					    <option value="all">Semua Toko</option>
            					<?php foreach ($store as $dt){?>
            							<option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
            					<?php }?>
        					</select>
        				</div>
        				<div class="col-sm-3">
        					<select id="brand" class="form-control" name="brand" width="100%">
        					    <option value="all">--Brand--</option>
            					<?php foreach ($brand as $dt){?>
            							<option value="<?=$dt["namabrand"]?>"><?=$dt["namabrand"]?></option>
            					<?php }?>
        					</select>
        				</div>
        				<div class="col-sm-3">
        					<select id="kategori" class="form-control" name="kategori" width="100%">
        					    <option value="all">--Kategori--</option>
            					<?php foreach ($kategori as $dt){?>
            							<option value="<?=$dt["namakategori"]?>"><?=$dt["namakategori"]?></option>
            					<?php }?>
        					</select>
        				</div>
        				<div class="col-sm-1"><button id="lihat" class="btn btn-primary">Lihat</button></div>
                    </div>
                    <hr>
                	<table id="table_data" class="mt-3 table table-striped nowrap" width="100%">
                	    <thead>
                		<tr>
                			<th>Nama Produk</th>
                			<th>Nama Brand</th>
                			<th>Size</th>
                			<th>Awal</th>
                			<th>Masuk</th>
                			<th>Keluar</th>
                			<th>Penjualan</th>
                			<th>Penyesuaian</th>
                			<th>Sisa</th>
                		</tr>
                	    </thead>
                	    <tbody>
                	    </tbody>
                	    <tfoot>
                		<tr>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                			<th></th>
                		</tr>
                	    </tfoot>
                	</table>
                    
                </div>
            </div>
        </div>


<!-- End Container -->
    </div>
</div>