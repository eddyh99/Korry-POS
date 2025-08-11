<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/pengguna/updateData">
        		        <div class="col-lg-6">
                			<div class="card-body">
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Username</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="username" name="username" maxlength="10" value="<?=$detail[0]->username?>" readonly>
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Password</label>
                				<div class="col-sm-7">
                				  <input type="password" class="form-control" id="password" name="password" maxlength="10">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Nama</label>
                				<div class="col-sm-7">
                				  <input type="text" class="form-control" id="nama" name="nama" value="<?=$detail[0]->nama?>"  maxlength="50">
                				</div>
                			  </div>
                			  <div class="form-group row">
                				<label class="col-sm-3 col-form-label">Role</label>
                				<div class="col-sm-7">
                					<select name="role" id="role" class="form-control">
                						<option value="Owner" <?php echo ($detail[0]->role=="Owner") ? "selected": "" ?>>Owner</option>
                						<option value="Store Manager" <?php echo ($detail[0]->role=="Store Manager") ? "selected": "" ?>>Store Manager</option>
                						<option value="Office Manager" <?php echo ($detail[0]->role=="Office Manager") ? "selected": "" ?>>Office Manager</option>
                						<option value="Staff" <?php echo ($detail[0]->role=="Staff") ? "selected": "" ?>>Staff</option>
                						<option value="Admin" <?php echo ($detail[0]->role=="Admin") ? "selected": "" ?>>Admin</option>
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
                				<a name="btnBack" href="<?=base_url()?>admin/pengguna" class="btn btn-warning">
                				    <i class="material-icons">reply</i>
                				    Back</a>
                			</div>
        		        </div>
            	        <div class="col-lg-12" style="margin-bottom: 15px;">
            	            <font color="red">Note: Kosongkan password jika tidak ingin mengubah</font>
            	        </div>
        	        </form>
                </div>
            </div>
        </div>


<!-- End Container -->
    </div>
</div>