<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="row mb-3">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <input type="hidden" id="key" value="<?=$key?>">
                <input type="hidden" id="memberid" value="<?=$memberid?>">
                <div class="card-content">
                    <table id="table_retur" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    		    <th></th>
                    			<th>Barcode</th>
                    			<th>Produk</th>
                    			<th>Brand</th>
                    			<th>Size</th>
                    			<th>Jumlah</th>
                    			<th>Harga</th>
                    			<th>Total</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
                </div>
                <label class="col-form-label col-sm-1">Total</label>
                <input type="hidden" id="ttlretur" value="0">
                <div class="col-sm-3"><input type="text" id="total" class="form-control mb-3" value="0" readonly></div>
            </div>
        </div>

     <!--   <div class="row">-->
     <!--       <div class="card">-->
     <!--           <div class="card-content">-->
					<!--<div class="col-sm-4">-->
					<!--	<label class="col-form-label">Barcode</label>-->
					<!--	<input type="text" id="barcode" name="barcode" class="form-control input-lg" maxlength="13" required onkeypress="return isNumber(event)">-->
					<!--</div>-->
					<!--<div class="col-sm-3">-->
					<!--	<div class="col-sm-12">-->
					<!--		<label class="col-form-label">Nama Produk</label>-->
					<!--	</div>-->
					<!--	<div class="col-sm-12">-->
     <!--       				<select id="namaproduk" name="namaproduk" class="form-control">-->
     <!--                           <option value="" disabled selected>Pilih Produk</option>-->
     <!--       				    <?php foreach($produk as $dt){?>-->
     <!--                               <option value="<?=$dt["barcode"]?>"><?=$dt["namaproduk"]?></option>-->
     <!--       				    <?php }?>-->
     <!--       				</select>-->
     <!--       			</div>-->
					<!--</div>-->
					<!--<div class="col-sm-1">-->
					<!--	<button class="hanaka-button" id="btnpayment">Simpan</button>-->
					<!--</div>-->
     <!--           </div>-->
     <!--       </div>-->
     <!--   </div>-->

     <!--   <div class="row">-->
     <!--       <div class="card">-->
     <!--           <div class="card-content">-->
     <!--           	<table id="table_data" class="table table-striped nowrap" width="100%">-->
     <!--           	    <thead>-->
     <!--           		<tr>-->
     <!--           			<th width="150px">Barcode</th>-->
     <!--           			<th width="200px">Produk</th>-->
     <!--           			<th width="100px">Size</th>-->
     <!--           			<th width="100px">Qty</th>-->
     <!--           			<th width="150px">Harga</th>-->
     <!--           			<th>Diskon</th>-->
     <!--           			<th>Diskon (%)</th>-->
     <!--           			<th>Total</th>-->
     <!--           			<th>Aksi</th>-->
     <!--           		</tr>-->
     <!--           	    </thead>-->
     <!--           	    <tbody>-->
     <!--           	    </tbody>-->
					<!--	<tfoot>-->
					<!--	<tr>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--		<th style="text-align:right">Total</th>-->
					<!--		<th></th>-->
					<!--		<th></th>-->
					<!--	</tr>-->
					<!--	</tfoot>-->
     <!--           	</table>-->
     <!--           </div>-->
     <!--       </div>-->
     <!--   </div>-->


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
							<select id="size" class="form-control" name="size"></select>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan" value="persen"> Persen
						</div>
						<div class="col-sm-6">
							<input type="radio" name="potongan" id="potongan" value="fixed"> Fixed
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Potongan</label>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="diskon" id="diskon" onkeypress="return isNumber(event)">
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="col-form-label">Keterangan</label>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="keterangan" id="keterangan" maxlength="50">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="simpan">Simpan</button>
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
					<label class="col-form-label col-sm-3">Retur</label>
					<div class="col-sm-4">
						<span id="totalretur"></span>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-form-label col-sm-3">Cara Bayar</label>
					<div class="col-sm-4">
						<select id="carabayar" name="carabayar" class="form-control">
							<option value="cash">Cash</option>
							<option value="debit">Debit Card</option>
							<option value="credit">Credit Card</option>
						</select>
					</div>
				</div>
				<div class="row form-group" id="chargecard">
					<label class="col-form-label col-sm-3">Charge Fee</label>
					<div class="col-sm-4">
						<input type="text" name="chargefee" id="chargefee" class="form-control">
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
