<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="row mb-3">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <input type="hidden" id="key" value="<?=$key?>">
                <div class="card-content">
                    <table id="table_pinjam" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    		    <th></th>
                    			<th>Barcode</th>
                    			<th>Produk</th>
                    			<th>Brand</th>
                    			<th>Size</th>
                    			<th>Jumlah</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
                </div>
                <div class="col-sm-3"><button id="simpan" class="btn btn-primary">Simpan</button></div>
            </div>
        </div>

<!-- End Container -->
    </div>
</div>
