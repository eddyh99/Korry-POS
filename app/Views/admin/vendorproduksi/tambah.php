<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/vendorproduksi/add-data">
        		        <div class="col-lg-6">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Nama Vendor</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="vendorproduksi" name="vendorproduksi" maxlength="50">
                				</div>
                			  </div>

                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Kontak</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="kontak" name="kontak" maxlength="50" placeholder="Masukkan No. HP atau Telepon (ex: 0812..., 021...)">
                				</div>
                			  </div>

							  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Tipe</label>
                				<div class="col-sm-7">
                					<select name="tipe" id="tipe" class="form-control">
                						<option value="penjahit">Penjahit</option>
                						<option value="bordir">Penjahit Bordir</option>
                						<option value="kain">Pemotong kain</option>
                						<option value="footwear">Pengrajin Footwear</option>
                						<option value="tas">Pengrajin Tas</option>
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