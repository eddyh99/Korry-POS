<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/vendorproduksi/update-data">
    		        <div class="col-lg-6">
            			<div class="card-body">
            			  <div class="form-group row">
            				<label class="col-sm-3 col-form-label">Nama Vendor</label>
            				<div class="col-sm-7">
            				  <input type="hidden" name="vendorid" value="<?=$detail[0]->id?>">
            				  <input type="text" class="form-control" id="vendorproduksi" name="vendorproduksi" maxlength="50" value="<?=$detail[0]->nama?>">
            				</div>
            			  </div>

            			  <div class="form-group row">
            				<label class="col-sm-3 col-form-label">Kontak</label>
            				<div class="col-sm-7">
            				  <input type="text" class="form-control" id="kontak" name="kontak" maxlength="50" value="<?=$detail[0]->kontak?>">
            				</div>
            			  </div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Role</label>
								<div class="col-sm-7">
									<select name="tipe" id="tipe" class="form-control">
										<option value="penjahit" 	<?php echo ($detail[0]->tipe=="penjahit") 	? "selected": "" ?>>Penjahit</option>
										<option value="bordir" 		<?php echo ($detail[0]->tipe=="bordir") 	? "selected": "" ?>>Penjahit Bordir</option>
										<option value="kain" 		<?php echo ($detail[0]->tipe=="kain") 		? "selected": "" ?>>Pemotong Kain</option>
										<option value="footwear" 	<?php echo ($detail[0]->tipe=="footwear") 	? "selected": "" ?>>Pengrajin Footwear</option>
										<option value="tas" 		<?php echo ($detail[0]->tipe=="tas") 		? "selected": "" ?>>Pengrajin Tas</option>
									</select>
								</div>
							</div>
						  
            			</div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/vendorproduksi" class="btn btn-warning">
                				    <i class="material-icons">reply</i>
                				    Back</a>
                			</div>
        		        </div>
        	        </form>
                </div>
            </div>
        </div>


<!-- End Container -->
    </div>
</div>