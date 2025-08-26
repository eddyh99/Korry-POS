<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

                        <form id="form_cicilan" method="post" action="<?=base_url()?>admin/wholesale/add-data-cicilan">

                            <div class="col-lg-6">
                                <div class="card-body">

                                    <!-- Nomor Cicilan -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">No. Cicilan</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="nonota" name="nonota" maxlength="6" required>
                                        </div>
                                    </div>

                                    <!-- Nota Order -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nota Order</label>
                                        <div class="col-sm-7">
                                            <select id="notaorder" name="notaorder" class="form-control select2" required>
                                                <option value="" disabled selected>--Pilih Wholesale Order--</option>
                                                <?php foreach ($wholesale_order as $dt){?>
                                                    <option value="<?=$dt["notaorder"]?>"><?=$dt["notaorder"]?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Nominal Bayar -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nominal Bayar</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="bayar" name="bayar" maxlength="12" required>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="col-lg-6">
                                    <button id="btnSimpan" name="btnSimpan"  class="btn btn-primary">Simpan</button>
                                </div>
                                <div class="col-lg-6 text-right">
                                    <a name="btnBack" href="<?=base_url()?>admin/wholesale/cicilan" class="btn btn-warning">
                                        <i class="material-icons">reply</i>
                                        Back
                                    </a>
                                </div>
                            </div>

                        </form>

                    </div>

				</div>
			</div>
		</div>
<!-- End Container -->
    </div>
</div>