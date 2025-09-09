<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

                            <form id="form_order" class="form-horizontal">
            <div class="row">
                <div class="card">
                    <?php if (isset($_SESSION["message"])){?>
                        <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                    <?php } ?>
                    <div class="card-header">
                        <h5 class="card-title">Customer</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="produk" class="col-sm-2 control-label">No. DO Konsinyasi</label>
                                    <div class="col-sm-10">
                                        <select id="do_konsinyasi" name="do_konsinyasi" class="form-control select2">
                                            <option value="" selected>--Tanpa DO--</option>
                                            <?php foreach ($do_konsinyasi as $dt){?>
                                                <option value="<?=$dt["nonota"]?>"><?=$dt["nonota"]?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Size</label>
                                    <div class="col-sm-10">
                                        <select id="size" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Jumlah</label>
                                    <div class="col-sm-10">
                                        <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Harga</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="harga" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </div>
                </div>

                <!-- ADD PRODUCT -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add Product</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Kiri -->
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="produk" class="col-sm-2 control-label">No. DO Konsinyasi</label>
                                    <div class="col-sm-10">
                                        <select id="do_konsinyasi" name="do_konsinyasi" class="form-control select2">
                                            <option value="" selected>--Tanpa DO--</option>
                                            <?php foreach ($do_konsinyasi as $dt){?>
                                                <option value="<?=$dt["nonota"]?>"><?=$dt["nonota"]?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Size</label>
                                    <div class="col-sm-10">
                                        <select id="size" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Jumlah</label>
                                    <div class="col-sm-10">
                                        <input type="number" id="jumlah" class="form-control" min="1" value="1">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label">Harga</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="harga" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Kanan -->
                            <div class="col-sm-6">
                                <div id="content"></div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="button" id="btnAdd" class="btn btn-success">
                                <i class="material-icons" style="font-size: 18px; vertical-align: middle;">add</i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PRODUCT LIST -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Product List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table_data" class="table table-striped nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Nama Barang</th>
                                        <th>Size</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- BUTTONS -->
                <div class="row">
                    <div class="col-xs-6">
                        <a href="<?=base_url()?>admin/konsinyasi/do" class="btn btn-default btn-block">
                            <i class="material-icons" style="font-size: 18px; vertical-align: middle;">reply</i>
                            Kembali
                        </a>
                    </div>
                    <div class="col-xs-6">
                        <button type="submit" class="btn btn-primary btn-block">Simpan Delivery Order</button>
                    </div>
                </div>
            </div>
            <!-- End Container -->
        </form>
                        <form id="form_nota" class="form-horizontal">
                            <!-- Bagian Header Nota Konsinyasi -->
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">Diskon</label>
                                    <input type="text" id="diskon" name="diskon" class="form-control input-lg" maxlength="6" onkeypress="return isNumber(event)">
                                </div>         
                                <div class="col-sm-2">
                                    <label class="col-form-label">PPN</label>
                                    <input type="text" id="ppn" name="ppn" class="form-control input-lg" maxlength="6" onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <hr>

                            <!-- Bagian Input Detail -->
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="col-form-label">No. DO Konsinyasi</label>
                                    <select id="do_konsinyasi" name="do_konsinyasi" class="form-control select2">
                                        <option value="" selected>--Tanpa DO--</option>
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
                                    <input type="text" id="harga" class="form-control"><!-- readonly dihapus -->
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
                                        <th>Harga</th>
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

                            <!-- Tombol Simpan -->
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Simpan Nota Konsinyasi</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a name="btnBack" href="<?=base_url()?>admin/konsinyasi/nota" class="btn btn-warning"><i class="material-icons">reply</i>Kembali</a>
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