<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/stok/AddData">
        	            <input type="hidden" name="restock" value="<?=$restock?>">
        		        <div class="col-lg-7">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Store</label>
                				<div class="col-sm-6">
                					<select id="store" class="form-control" name="store">
									<?php foreach($store as $dt){ ?>
											<option value="<?=$dt["storeid"]?>" <?php echo (@$_SESSION["idstore"]==$dt["storeid"])?"selected":"" ?>><?=$dt["store"]?></option>
									<?php }?>
									</select>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Barcode</label>
                				<div class="col-sm-6">
                					<input type="text" class="form-control" id="barcode" name="barcode" maxlength="13" onkeypress="return isNumber(event)" required>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Nama Produk</label>
                				<div class="col-sm-6">
                				    <input type="text" class="form-control" id="produk" name="produk" list="dataproduk">
                				    <datalist id="dataproduk">
                				        <?php foreach ($produk as $dt){?>
                				            <option data-barcode="<?=$dt["barcode"]?>" value="<?=$dt["namaproduk"]?>">
                				        <?php } ?>
                				    </datalist>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Brand</label>
                				<div class="col-sm-6">
                					<input type="text" class="form-control" id="brand" readonly>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Size</label>
                				<div class="col-sm-3">
                				    <select name="size" class="form-control" id="size">
    									<?php
    										foreach($size as $dt) {
    											echo "<option value='".$dt["nama"]."' >".$dt["nama"]."</option>";
    										}
    									?>
                				    </select>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-4 col-form-label">Stok</label>
                				<div class="col-sm-6">
                					<input type="text" class="form-control" id="stok" name="stok" required onkeypress="return isNumber(event)">
                				</div>
                			  </div>
                			</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/stok" class="btn btn-warning">
                				    <i class="material-icons">reply</i>
                				    Back</a>
                			</div>
        		        </div>
        	        </form>
                </div>
            </div>
            <div class="card">
                <div class="card-content pb-2">
                    <form action="<?=base_url()?>admin/stok/import" method="post" enctype="multipart/form-data">
          				<label class="col-md-3 col-form-label">Input file Excel</label>
                  		<div class="col-md-7">
                    		<input type="file" name="stok" id="stok" class="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                    	</div>
                    	<div class="col-md-12"><button class="btn btn-primary">Upload</button></div>
                	</form>
                </div>
            </div>        
        </div>


<!-- End Container -->
    </div>
</div>