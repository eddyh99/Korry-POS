<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/produk/AddData">
        		        <div class="col-lg-6">
                			<div class="card-body">
                				<div class="form-group row">
									<label class="col-sm-3 col-form-label">Barcode</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="barcode" name="barcode" maxlength="13" minlength="13" required onkeypress="return isNumber(event)">
									</div>
                				</div>
                				<div class="form-group row">
									<label class="col-sm-3 col-form-label">Nama Produk</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="produk" name="produk" maxlength="50" required>
									</div>
                				</div>
                				<div class="form-group row">
									<label class="col-sm-3 col-form-label">Harga</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="harga" name="harga" maxlength="7" required onkeypress="return isNumber(event)">
									</div>
                				</div>
                				<div class="form-group row">
									<label class="col-sm-3 col-form-label">Diskon</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="diskon" name="diskon" maxlength="7" required onkeypress="return isNumber(event)">
									</div>
                				</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Brand</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="brand" name="brand" maxlength="50" list="brandlist" required>
									  <datalist name="brandlist" id="brandlist">
										<?php
											foreach($brand as $dt) {
												echo "<option value='".$dt["namabrand"]."' />";
											}
										?>
										</datalist>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Kategori</label>
									<div class="col-sm-7">
									  <input type="text" class="form-control" id="kategori" name="kategori" maxlength="50" list="kategorilist" required>
									  <datalist name="kategorilist" id="kategorilist">
										<?php
											foreach($kategori as $dt) {
												echo "<option value='".$dt["namakategori"]."' />";
											}
										?>
										</datalist>
									</div>
								</div>
                			</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/produk" class="btn btn-warning">
                				    <i class="material-icons">reply</i>
                				    Back</a>
                			</div>
        		        </div>
        	        </form>
                </div>
            </div>
            <div class="card">
                <div class="card-content pb-2">
                    <form action="<?=base_url()?>admin/produk/import" method="post" enctype="multipart/form-data">
          				<label class="col-md-3 col-form-label">Input file Excel</label>
                  		<div class="col-md-7">
                    		<input type="file" name="produk" id="produk" class="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                    	</div>
                    	<div class="col-md-12"><button class="btn btn-primary">Upload</button></div>
                	</form>
                </div>
            </div>        
        </div>


<!-- End Container -->
    </div>
</div>