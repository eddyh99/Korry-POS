<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

                        <form id="form_nota">
                            <!-- Bagian Header Nota Konsinyasi -->
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Nota Jual</label>
                                    <input type="text" id="notajual" name="notajual" class="form-control input-lg" maxlength="6" required onkeypress="return isNumber(event)">
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-form-label">Diskon</label>
                                    <input type="text" id="diskon" name="diskon" class="form-control input-lg" maxlength="6" required onkeypress="return isNumber(event)">
                                </div>         
                                <div class="col-sm-2">
                                    <label class="col-form-label">PPN</label>
                                    <input type="text" id="ppn" name="ppn" class="form-control input-lg" maxlength="6" required onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <hr>

                            <!-- Bagian Input Detail -->
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">No. DO Konsinyasi</label>
                                    <select id="do_konsinyasi" name="do_konsinyasi" class="form-control select2" required>
                                        <option value="" disabled selected>--Pilih No. Nota--</option>
                                        <?php foreach ($do_konsinyasi as $dt){?>
                                            <option value="<?=$dt["nonota"]?>"><?=$dt["nonota"]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="col-form-label">Produk</label>
                                    <select id="produk" name="produk" class="form-control select2">
                                        <option value="" disabled selected>--Pilih Produk--</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-form-label">Jumlah</label>
                                    <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" value="1">
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-form-label">Harga</label>
                                    <input type="text" id="harga" class="form-control" readonly>
                                </div>
                                <div class="col-sm-2 d-flex align-items-end">
                                    <button type="button" id="btnAdd" class="btn btn-success">+ Tambah</button>
                                </div>
                            </div>

                            <hr>

                            <!-- Temporary Table -->
                            <table id="table_data" class="table table-striped nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>No. DO Konsinyasi</th>
                                        <th>Barcode</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <!-- Tombol Simpan -->
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Simpan Nota Konsinyasi</button>
                                    <a name="btnBack" href="<?=base_url()?>admin/konsinyasi/nota" class="btn btn-warning">Back</a>
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