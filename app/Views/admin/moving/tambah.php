<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">
					<div class="col-sm-12">
						<div class="row form-group">
					    <?php 
					    if($_SESSION["logged_status"]["role"]=="Staff"){
					    ?>
					        <input type="hidden" name="asal" id="asal" value="<?=@$_SESSION["logged_status"]["storeid"]?>">
					    <?php } else {  ?>
							<label class="col-form-label col-sm-2">Toko yang meminta</label>
							<div class="col-sm-4">
    								<select id="asal" class="form-control asal" name="asal">
    								<?php foreach ($store as $dt){?>
    										<option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
    								<?php }?>
    								</select>
							</div>
						<?php } ?>
							<label class="col-form-label col-sm-2">Toko yang diminta</label>
							<div class="col-sm-4">
								<select id="tujuan" class="form-control" name="tujuan">
								<?php foreach ($store as $dt){?>
										<option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
								<?php }?>								
								</select>
							</div>
						</div>
						<div class="col-sm-7">
							<label class="col-form-label">Barcode</label>
							<input type="text" id="barcode" name="barcode" class="form-control input-lg" maxlength="13" required  onkeypress="return isNumber(event)">
						</div>
						<div class="col-sm-7">
            				<label class="col-form-label">Nama Produk</label>
            				<select id="produk" name="produk" class="form-control">
            				    <option value="" disabled selected>--Pilih Produk--</option>
        				        <?php foreach ($produk as $dt){?>
        				            <option value="<?=$dt["barcode"]?>"><?=$dt["namaproduk"]?></option>
        				        <?php } ?>
            				</select>
            			</div>
						<div class="col-sm-1">
							<button class="btn btn-primary" id="btnpayment">Simpan</button>
						</div>
						<div class="col-sm-12"><hr></div>
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>Barcode</th>
                    			<th>Produk</th>
                    			<th>Size</th>
                    			<th>Qty</th>
                    			<th>Aksi</th>
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
							</tr>
							</tfoot>
                    	</table>
					</div>
				</div>				
			</div>
		</div>
<!-- End Container -->
    </div>
</div>

<!-- Input size -->
<div class="modal fade" id="modalsize">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Size</h4>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<input type="hidden" id="produk">
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Size</label>
						</div>
						<div class="col-sm-4">
							<select id="size" class="form-control" name="size"></select>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-4">
							Jumlah
						</div>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="jumlah" id="jumlah" required  onkeypress="return isNumber(event)">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btlbeli" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpan">Simpan</button>
			</div>
		</div>
	</div>
</div>