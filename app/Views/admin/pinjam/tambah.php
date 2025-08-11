<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <input type="hidden" id="tujuan" value="<?=$_SESSION["logged_status"]["storeid"]?>">
		<div class="row">
			<div class="card">
				<div class="card-body">
					<div class="col-sm-12">
						<div class="row form-group">
							<label class="col-form-label col-sm-2">Penanggung Jawab</label>
							<div class="col-sm-4">
							    <input type="text" name="nama" id="nama" class="form-control" required>
							</div>
							<label class="col-form-label col-sm-2">Keterangan</label>
							<div class="col-sm-4">
							    <input type="text" name="keterangan" id="keterangan" class="form-control" required>
							</div>
						</div>
						<div class="col-sm-7">
							<label class="col-form-label">Barcode</label>
							<input type="text" id="barcode" name="barcode" class="form-control input-lg" maxlength="13">
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
							<input type="text" class="form-control" name="jumlah" id="jumlah">
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