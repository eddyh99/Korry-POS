<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
        	        <form id="form_input" method="post" action="<?=base_url()?>admin/stokbahanbaku/add-data">
        		        <div class="col-lg-6">
							<div class="card-body">

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
									<label class="col-sm-3 col-form-label">Bahan Baku</label>
									<div class="col-sm-7">
										<select class="form-control" id="idbahan" name="idbahan" required>
											<option value="">-- Pilih Bahan Baku --</option>
											<?php foreach($bahanbaku as $dt): ?>
												<option value="<?= $dt['id']; ?>"><?= $dt['namabahan']; ?></option>
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

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Satuan</label>
									<div class="col-sm-7">
										<select class="form-control" id="satuan" name="satuan" required>
											<option value="">-- Pilih Satuan --</option>
											<option value="yard">Yard</option>
											<option value="meter">Meter</option>
											<option value="pcs">Pcs</option>
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
                				<a name="btnBack" href="<?=base_url()?>admin/stokbahanbaku" class="btn btn-warning">
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