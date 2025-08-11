<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="form-group row">
                <label class="col-form-label col-sm-2">Tanggal</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="tanggal" name="tanggal" value="<?=date("m/d/Y")?>" />                    
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-2">Store</label>
                <div class="col-sm-3">
                    <select id="store" name="store" class="form-control">
                        <?php foreach ($store as $dt){?>
                            <option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-1">
                <input type="button" name="cari" id="cari" value="Cari" class="btn btn-primary">
            </div>

            <div class="card">
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>No Nota</th>
                    			<th>Tanggal</th>
                    			<th>Member ID</th>
                    			<th>Total</th>
                    			<th>Payment Method</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
                </div>
            </div>
        </div>
        
<!-- End Container -->
    </div>
</div>

<!-- Change Payment-->
<div class="modal fade" id="gantipayment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Ganti Pembayaran</h4>
			</div>
			<div class="modal-body" style="height:150px">
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Cara Bayar</label>
					<div class="col-sm-4">
					    <input type="hidden" id="nonota" name="nonota">
					    <input type="hidden" id="tglnota" name="tglnota">
						<select id="carabayar" name="carabayar" class="form-control">
							<option value="cash">Cash</option>
							<option value="debit">Debit Card</option>
							<option value="credit">Credit Card</option>
						</select>
					</div>
					<div class="col-sm-12">
                    	<table id="table_detail" class="table table-striped nowrap" width="100%">
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
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpanpembayaran">Simpan</button>
			</div>
		</div>
	</div>
</div>
