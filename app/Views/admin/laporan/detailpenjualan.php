<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-content">
                    <input type="hidden" id="key" value="<?=$key?>">
                	<table id="table_data" class="mt-3 table table-striped nowrap" width="100%">
                	    <thead>
                		<tr>
                			<th>Nota</th>
                			<th>Nama Produk</th>
                			<th>Nama Brand</th>
                			<th>Size</th>
                			<th>Jumlah</th>
                			<th>Harga</th>
                			<th>Disc</th>
                			<th>Disc (%)</th>
                			<th>Total</th>
                			<?php if (($_SESSION["logged_status"]["role"]=="Office Manager") || ($_SESSION["logged_status"]["role"]=="Owner")) {?>
                			<th>Alasan</th>
                			<?php }?>
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