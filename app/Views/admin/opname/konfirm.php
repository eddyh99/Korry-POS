<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/opname/updatedata">
            			<div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Store</label>
                                <div class="col-sm-3">
                    				<select id="store" name="store" class="form-control" required>
                    				    <option value="" disabled selected>--Pilih Store--</option>
                				        <?php foreach ($store as $dt){?>
                				            <option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
                				        <?php } ?>
                    				</select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                            <hr>
                            <table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                        		<tr>
                        			<th>Barcode</th>
                        			<th>Produk</th>
                        			<th>Size</th>
                        			<th>Stok Lama</th>
                        			<th>Stok Baru</th>
                        		</tr>
                        	    </thead>
                        	    <tbody>
                        	    </tbody>
                        	</table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
<!-- End Container -->
    </div>
</div>