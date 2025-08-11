<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <div class="card-content">
        		    <div class="col-lg-6">
                			<div class="card-body">
                				<div class="form-group row">
									<label class="col-sm-4 col-form-label">Toko yang diminta</label>
									<label class="col-sm-7 col-form-label"><?=$asal?></label>
									<label class="col-sm-4 col-form-label">Toko yang meminta</label>
									<label class="col-sm-7 col-form-label"><?=$tujuan?></label>
                				</div>
                			</div>
        		        </div>
        		</div>
        	</div>
        </div>

        <div class="row">
            <div class="card">
                <input type="hidden" id="mutasi" value="<?=$key?>">
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>Barcode</th>
                    			<th>Nama Produk</th>
                    			<th>Size</th>
                    			<th>Brand</th>
                    			<th>Jumlah</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	    <tfoot>
                    		<tr>
                    			<th></th>
                    			<th></th>
                    			<th></th>
                    			<th></th>
                    			<th></th>
                    		</tr>
                    	    </tfoot>
                    	</table>
                </div>
            </div>
        </div>
        
<!-- End Container -->
    </div>
</div>