<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/store/AddData">
        		        <div class="col-lg-6">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Nama Store</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="store" name="store" maxlength="50">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Alamat</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="alamat" name="alamat" maxlength="100">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Kontak</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="kontak" name="kontak" maxlength="50">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Keterangan</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="keterangan" name="keterangan" maxlength="100">
                				</div>
                			  </div>
                			</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/store" class="btn btn-warning">
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