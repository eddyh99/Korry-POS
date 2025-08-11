<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-content">
                    <div class="row form-group">
        				<label class="col-form-label col-sm-1">Tanggal</label>
		                <div class="col-sm-3">
                            <input type="text" id="tgl" name="tgl" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
        				<div class="col-2 col-lg-4">
        				    <?php if (($_SESSION["logged_status"]["role"]=="Staff") || ($_SESSION["logged_status"]["role"]=="Store Manager")){?> 
        				            <input type="hidden" name="store" id="store" value="<?=$_SESSION["logged_status"]["storeid"]?>">
        				            <input type="text" class="form-control" readonly value="<?=$_SESSION["logged_status"]["store"]?>">
        				    <?php }else{ ?>
            					<select id="store" class="form-control" name="store">
                					<?php foreach ($store as $dt){?>
                							<option value="<?=$dt["storeid"]?>"><?=$dt["store"]?></option>
                					<?php }?>
            					</select>
            				<?php } ?>
        				</div>
        				<div class="col-1 col-lg-4"><button id="lihat" class="btn btn-primary">Lihat</button></div>
                    </div>
                    <hr>
                	<table id="table_data" class="mt-3 table table-striped nowrap" width="100%">
                	    <thead>
                		<tr>
                			<th>No. Nota</th>
                			<th>Tanggal</th>
                			<th>Jenis</th>
                			<th>Total</th>
                			<th>Charge (%)</th>
                			<th>Fee</th>
                			<th>Grand Total</th>
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