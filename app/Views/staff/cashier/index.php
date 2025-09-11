<div class="content">
    <div class="container-fluid" style="background-color:white">
<!-- Start -->
        <input type="hidden" id="tujuan" value="<?=$_SESSION["logged_status"]["storeid"];?>">
		<div class="form-group row">
			<div class="col-sm-3">
				<img src="<?=base_url()?>/assets/img/korry-crop.png" style="width:100%" />
			</div>
			<div class="col-sm-9 text-right">
			    <label class="col-form-label">Store : <?=$_SESSION["logged_status"]["store"]?></label><br>
			    <a href="<?=base_url()?>staff/dashboard">Back</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="form-group row">
						<label class="col-form-label col-sm-2">Nama Member :</label>
						<div class="col-sm-5">
							<span class="membername"></span>
						</div>
					</div>
					<div class="card-body">
						<div class="col-sm-4">
							<label class="col-form-label">Barcode</label>
							<input type="text" id="barcode" name="barcode" class="form-control input-lg" maxlength="13" required  onkeypress="return isNumber(event)">
						</div>
						<div class="col-sm-3">
							<div class="col-sm-12">
    							<label class="col-form-label">Nama Produk</label>
    						</div>
							<div class="col-sm-12">
                				<select id="namaproduk" name="namaproduk" class="form-control">
                                    <option value="" disabled selected>Pilih Produk</option>
                				    <?php foreach($produk as $dt){?>
                                        <option value="<?=$dt["barcode"]?>"><?=$dt["namaproduk"]?></option>
                				    <?php }?>
                				</select>
                			</div>
						</div>
						<div class="col-sm-1">
							<button class="hanaka-button" id="btnnew">Nota Baru</button>
						</div>
						<div class="col-sm-1">
							<button class="hanaka-button" id="btnmember">Member</button>
						</div>
						<div class="col-sm-1">
							<button class="hanaka-button" id="btncari">Cari Barang</button>
						</div>
						<div class="col-sm-1">
							<button class="hanaka-button" id="btnpayment">Simpan</button>
						</div>
					</div>
				</div>				
			</div>
		</div>        

		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th width="150px">Barcode</th>
                    			<th width="200px">Produk</th>
                    			<th width="100px">Size</th>
                    			<th width="100px">Qty</th>
                    			<th width="150px">Harga</th>
                    			<th>Diskon</th>
                    			<th>Diskon (%)</th>
                    			<th>Total</th>
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
								<th></th>
								<th style="text-align:right">Total</th>
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
				<h4 class="modal-title">Size & Discount</h4>
			</div>
			<div class="modal-body">
				<div class="col-sm-12">
					<input type="hidden" id="produk">
					<input type="hidden" id="harga">
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Size</label>
						</div>
						<div class="col-sm-4">
							<select id="size" class="form-control" name="size" id="size"></select>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan1" value="persen"> Persen
						</div>
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan2" value="fixed"> Fixed
						</div>
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan3" value="sale"> Sale
						</div>
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan4" value="nodisk" checked> No Diskon
						</div>
					</div>
					<div id="diskonsale" class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Potongan</label>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="diskon" id="diskon" onkeypress="return isNumber(event)" disabled>
						</div>
					</div>
					<div id="ketsale" class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Keterangan</label>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="keterangan" id="keterangan" maxlength="50" disabled>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" id="btlbeli" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpan">Simpan</button>
			</div>
		</div>
	</div>
</div>

<!-- Input Member -->
<div class="modal fade" id="modalmember">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Member ID</h4>
			</div>
			<div class="modal-body" style="height:150px">
				<div class="col-sm-12">
					<input type="text" class="form-control" name="memberid" id="memberid">
					Nama: <span class="membername"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" id="batalmember" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpanmember">Simpan</button>
			</div>
		</div>
	</div>
</div>

<!-- Pencarian Produk-->
<div class="modal fade" id="cariproduk">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Pencarian Produk</h4>
			</div>
			<div class="modal-body" style="height:150px">
				<div class="row form-group">

					<div class="col-sm-12">
                    	<table id="table_cari" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>Barcode</th>
                    			<th>Produk</th>
                    			<th>Brand</th>
                    			<th>Harga</th>
                    			<th>Size</th>
                    			<th>Qty</th>
                    			<th>Lokasi</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Batal</button>
			</div>
		</div>
	</div>
</div>

<!-- Payment Page -->
<div class="modal fade" id="modalpayment">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Payment Page</h4>
			</div>
			<div class="modal-body">
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Total</label>
					<div class="col-sm-4">
						<span id="totalbelanja"></span>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Cara Bayar</label>
					<div class="col-sm-4">
						<select id="carabayar" name="carabayar" class="form-control">
							<option value="cash">Cash</option>
							<option value="debit">Debit Card</option>
							<option value="credit">Credit Card</option>
							<option value="qris">QRis</option>
							<option value="transfer">Transfer</option>
						</select>
					</div>
				</div>
				<div class="row form-group" id="chargecard">
					<label class="col-form-label col-sm-3">Charge Fee</label>
					<div class="col-sm-4">
						<input type="text" name="chargefee" id="chargefee" class="form-control"  onkeypress="return isNumber(event)">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Grand Total</label>
					<div class="col-sm-4">
						<span id="grandtotal"></span>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Pembayaran</label>
					<div class="col-sm-4">
						<input type="text" name="amountpay" id="amountpay" class="form-control" required  onkeypress="return isNumber(event)">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Kembalian</label>
					<div class="col-sm-4">
						<span id="exchange"></span>						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpaninvoice">Simpan</button>
			</div>
		</div>
	</div>
</div>
