<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/produksize/add-data">
        		        <div class="col-lg-6">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Barcode</label>
                				<div class="col-sm-4">
                				  <input type="text" class="form-control" id="barcode" name="barcode">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Nama Produk</label>
                				<div class="col-sm-7">
                				    <input type="text" class="form-control" id="namaproduk" name="namaproduk" list="dataproduk">
                				    <datalist id="dataproduk">
                				        <?php foreach ($produk as $dt){?>
                				            <option data-barcode="<?=$dt["barcode"]?>" value="<?=$dt["namaproduk"]?>">
                				        <?php } ?>
                				    </datalist>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Size</label>
                				<div class="col-sm-7">
                				    <select name="size" class="form-control" id="size">
    									<?php
    										foreach($size as $dt) {
    											echo "<option value='".$dt["nama"]."' >".$dt["nama"]."</option>";
    										}
    									?>
                				    </select>
                				</div>
                			  </div>
                			</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/produksize" class="btn btn-warning">
                				    <i class="material-icons">reply</i>
                				    Back</a>
                			</div>
        		        </div>
        	        </form>
                </div>
            </div>
            <div class="card">
                <div class="card-content pb-2">
                    <form action="<?=base_url()?>admin/produksize/import" method="post" enctype="multipart/form-data">
          				<label class="col-md-3 col-form-label">Input file Excel</label>
                  		<div class="col-md-7">
                    		<input type="file" name="produksize" id="produksize" class="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                    	</div>
                    	<div class="col-md-12"><button class="btn btn-primary">Upload</button></div>
                	</form>
                </div>
            </div>
        </div>


<!-- End Container -->
    </div>
</div>