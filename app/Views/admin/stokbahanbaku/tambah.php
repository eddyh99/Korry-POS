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
						<div class="col-lg-8">  
							<div class="card-body" id="bahan-container">
								<!-- Row pertama default -->
								<div class="form-group row align-items-center" id="row-0">
									<label class="col-sm-2 col-form-label">Bahan 1</label>
									<div class="col-sm-3">
										<select class="form-control bahan-select" name="idbahan[0]" required>
											<option value="">-- Pilih Bahan Baku --</option>
											<?php foreach($bahanbaku as $dt): ?>
												<option value="<?= $dt['id']; ?>"><?= $dt['namabahan']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-sm-2">
										<input type="number" class="form-control jumlah-input" name="jumlah[0]" 
											min="0" step="0.01" placeholder="Jumlah" required>
									</div>
									<div class="col-sm-2">
										<select class="form-control satuan-select" name="satuan[0]" required>
											<option value="">-- Satuan --</option>
											<option value="yard">Yard</option>
											<option value="meter">Meter</option>
											<option value="pcs">Pcs</option>
										</select>
									</div>
									<div class="col-sm-2">
										<input type="text" class="form-control harga-input" name="harga[0]" 
											placeholder="Harga" maxlength="11" required onkeypress="return isNumber(event)">
									</div>
									<div class="col-sm-1">
										<!-- tombol hapus hidden utk row pertama -->
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-12 mt-3">
							<div class="col-lg-6">
								<button id="btnAddBahan" type="button" class="btn btn-light">Tambah Bahan Baku</button>
								<button id="btnSimpan" name="btnSimpan" type="submit" class="btn btn-primary">Simpan</button>
							</div>
							<div class="col-lg-6 text-right">
								<a name="btnBack" href="<?=base_url()?>admin/stokbahanbaku" class="btn btn-warning">
									<i class="material-icons">reply</i> Back
								</a>
							</div>
						</div>
					</form>

                </div>
            </div>
        </div>


<!-- End Container -->
    </div>
</div>