<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/assignstaff/AddData">
        		        <div class="col-lg-6">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Nama</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="nama" name="nama" list="stafflist">
								  <datalist name="stafflist" id="stafflist">
									<?php
										foreach($staff as $dt) {
											echo "<option data-uname='".$dt["username"]."' value='".$dt["nama"]."' />";
										}
									?>
									</datalist>
									<input type="hidden" id="username" name="username">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Store Name</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="store" name="store" list="storelist">
								  <datalist name="storelist" id="storelist">
									<?php
										foreach($store as $dt) {
											echo "<option data-storeid='".$dt["storeid"]."' value='".$dt["store"]."' />";
										}
									?>
									</datalist>
									<input type="hidden" id="storeid" name="storeid">
                				</div>
                			  </div>
                			</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/assignstaff" class="btn btn-warning">
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