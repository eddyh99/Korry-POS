<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">

					<form id="form_input" method="post" action="<?=base_url()?>admin/metodebayar/update-data">
						
						<div class="col-lg-7">
							<div class="card-body">

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Account Name</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="namaakun" name="namaakun" maxlength="50" value="<?=$detail[0]->namaakun?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Account Number</label>
									<div class="col-sm-7">
										<input type="hidden" name="oldnoakun" value="<?=$detail[0]->noakun?>">
										<input type="text" class="form-control" id="noakun" name="noakun" maxlength="30" value="<?=$detail[0]->noakun?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Bank Name</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="namabank" name="namabank" maxlength="50" value="<?=$detail[0]->namabank?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Branch</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="cabangbank" name="cabangbank" maxlength="50" value="<?=$detail[0]->cabangbank?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">SWIFT Code</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="kodeswift" name="kodeswift" maxlength="20" value="<?=$detail[0]->kodeswift?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Currency</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="matauang" name="matauang" maxlength="10" value="<?=$detail[0]->matauang?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Country</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="negara" name="negara" maxlength="50" value="<?=$detail[0]->negara?>" required>
									</div>
								</div>

							</div>
						</div>

						<div class="col-lg-12">
							<div class="col-lg-6">
								<button id="btnSimpan" name="btnSimpan" class="btn btn-primary">Simpan</button>
							</div>
							<div class="col-lg-6 text-right">
								<a name="btnBack" href="<?=base_url()?>admin/metodebayar" class="btn btn-warning">
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