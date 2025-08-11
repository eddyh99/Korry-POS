<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/kategori/tambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>Nama Kategori</th>
                    			<th>Aksi</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
                </div>
            </div>
        </div>
        
<!-- End Container -->
    </div>
</div>