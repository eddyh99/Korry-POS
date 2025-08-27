<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>produksi/add-data">
        		        <div class="col-lg-6">
							<div class="card-body">
								
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Vendor</label>
									<div class="col-sm-7">
										<select class="form-control" id="idvendor" name="idvendor" required>
											<option value="">-- Pilih Vendor --</option>
											<?php foreach($vendor as $dt): ?>
												<option value="<?= $dt['id']; ?>"><?= $dt['nama']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Estimasi</label>
									<div class="col-sm-7">
										<input type="number" class="form-control" id="estimasi" name="estimasi" min="0" required>
									</div>
									<div class="col-sm-2">Hari</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">DP</label>
									<div class="col-sm-7">
										<input type="number" class="form-control" id="dp" name="dp" min="0" required>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Total</label>
									<div class="col-sm-7">
										<input type="number" class="form-control" id="total" name="total" min="1" required>
									</div>
								</div>

								<!-- input ke tabel produksi_detail -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Barcode</label>
									<div class="col-sm-7">
										<select class="form-control" id="barcode" name="barcode" required>
											<option value="">-- Pilih Barcode Produk --</option>
											<?php foreach($produk as $dt): ?>
												<option value="<?= $dt['barcode']; ?>">
													<?= $dt['barcode']; ?> - <?= $dt['namaproduk']; ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Jumlah</label>
									<div class="col-sm-7">
										<input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
									</div>
								</div>

							</div>
        		        </div>
        		        <div class="col-lg-12">
            			    <div class="col-lg-6">
                    		    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                    		</div>
                    	    <div class="col-lg-6 text-right">
                				<a name="btnBack" href="<?=base_url()?>produksi" class="btn btn-warning">
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