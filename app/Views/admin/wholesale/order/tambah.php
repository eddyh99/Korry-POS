<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

                        <form id="form_order">

                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Nama Wholesaler</label>
                                    <select id="wholesaler" name="wholesaler" class="form-control select2" required>
                                        <option value="" disabled selected>--Pilih Wholesaler--</option>
                                        <?php foreach ($wholesaler as $dt){?>
                                            <option value="<?=$dt["id"]?>"><?=$dt["nama"]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="col-form-label">Lama</label>
                                    <input type="text" id="lama" name="lama" class="form-control input-lg" maxlength="6" onkeypress="return isNumber(event)">
                                    <small class="form-text text-muted">Hari (contoh: 15)</small>
                                </div>      
                                <!-- <div class="col-sm-2">
                                    <label class="col-form-label">Diskon</label>
                                    <input type="text" id="diskon" name="diskon" class="form-control input-lg" maxlength="6" onkeypress="return isNumber(event)">
                                </div>          -->
                                <div class="col-sm-2">
                                    <label class="col-form-label">Diskon</label>
                                    <input type="text" id="diskon" name="diskon"
                                        class="form-control input-lg" maxlength="10">
                                    <small class="form-text text-muted">Contoh: 1000 atau 10%</small>
                                </div>
                                <!-- <div class="col-sm-2">
                                    <label class="col-form-label">PPN</label>
                                    <input type="text" id="ppn" name="ppn" class="form-control input-lg" maxlength="6" onkeypress="return isNumber(event)">
                                </div> -->
                                <div class="col-sm-2">
                                    <label class="col-form-label">PPN</label>
                                    <input type="text" id="ppn" name="ppn"
                                        class="form-control input-lg" maxlength="5">
                                    <small class="form-text text-muted">Persen (contoh: 11)</small>
                                </div>
                                <!-- <div class="col-sm-2">
                                    <label class="col-form-label">DP</label>
                                    <input type="text" id="dp" name="dp" class="form-control input-lg" maxlength="11" onkeypress="return isNumber(event)" required>
                                </div> -->
                                <div class="col-sm-2">
                                    <label class="col-form-label">DP</label>
                                    <input type="text" id="dp" name="dp"
                                        class="form-control input-lg" maxlength="11" required>
                                </div>
                            </div>

                            <hr>

                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Produk</label>
                                    <select id="produk" class="form-control select2">
                                        <option value="" disabled selected>--Pilih Produk--</option>
                                        <?php foreach ($produk as $dt){?>
                                            <option value="<?=$dt["barcode"]?>" data-harga="<?=$dt["harga_wholesale"]?>">
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
                                <!-- <div class="col-sm-1">
                                    <label class="col-form-label">Potongan</label>
                                    <input type="number" id="potongan" class="form-control" min="0" value="0">
                                </div> -->
                                <div class="col-sm-2">
                                    <label class="col-form-label">Potongan</label>
                                    <input type="text" id="potongan" class="form-control" value="0">
                                    <small class="form-text text-muted">Contoh: 500 atau 5%</small>
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
                                        <th>Potongan</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th class="text-right">Sub Total</th>
                                        <th id="subtotal">0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Simpan Order Wholesale</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a name="btnBack" href="<?=base_url()?>admin/wholesale/order" class="btn btn-warning"><i class="material-icons">reply</i>Kembali</a>
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