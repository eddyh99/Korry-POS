<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">
                        <form id="form_do">
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Nama Partner Konsinyasi</label>
                                    <select id="partner" name="partner" class="form-control select2" required>
                                        <option value="" disabled selected>--Pilih Partner--</option>
                                        <?php foreach ($partner as $dt){?>
                                            <option value="<?=$dt["id"]?>"><?=$dt["nama"]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-form-label">No Nota</label>
                                    <input type="text" id="nonota" name="nonota" class="form-control input-lg" maxlength="6" required onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <hr>

                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Produk</label>
                                    <select id="produk" class="form-control select2">
                                        <option value="" disabled selected>--Pilih Produk--</option>
                                        <?php foreach ($produk as $dt){?>
                                            <option value="<?=$dt["barcode"]?>" data-harga="<?=$dt["harga_konsinyasi"]?>">
                                                <?=$dt["namaproduk"]?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <label class="col-form-label">Jumlah</label>
                                    <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                </div>
                                <div class="col-sm-1">
                                    <label class="col-form-label">Harga</label>
                                    <input type="text" id="harga" class="form-control" readonly>
                                </div>
                                <div class="col-sm-2 d-flex align-items-end">
                                    <button type="button" id="btnAdd" class="btn btn-success">+ Tambah</button>
                                </div>
                            </div>

                            <hr>

                            <table id="table_data" class="table table-striped nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a name="btnBack" href="<?=base_url()?>admin/konsinyasi/do" class="btn btn-warning">Back</a>
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