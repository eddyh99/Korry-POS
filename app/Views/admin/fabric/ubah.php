<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/fabric/update-data">
    		        <div class="col-lg-6">
            			<div class="card-body">
            			  <div class="form-group row">
            				<label class="col-sm-3 col-form-label">Nama Fabric</label>
            				<div class="col-sm-7">
            				  <input type="hidden" name="fabricid" value="<?=$detail[0]->id?>">
            				  <input type="text" class="form-control" id="fabric" name="fabric" maxlength="50" value="<?=$detail[0]->nama?>">
            				</div>
            			  </div>
            			</div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>admin/fabric" class="btn btn-warning">
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