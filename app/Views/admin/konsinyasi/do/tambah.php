<div class="content">
    <div class="container-fluid">

        <!-- Start -->
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
                            <div class="col-lg-6">
                                <!-- Customer -->
                                <div class="form-group row">
                                    <label for="idvendor" class="col-sm-4 control-label">Customer</label>
                                    <div class="col-sm-8">
                                        <select id="partner" name="partner" class="form-control select2" required>
                                            <option value="" disabled selected>--Pilih Partner--</option>
                                            <?php foreach ($partner as $dt){?>
                                                <option value="<?=$dt["id"]?>"><?=$dt["nama"]?></option>
                                            <?php } ?>
                                        </select>
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
                                    <label for="produk" class="col-sm-2 control-label">Produk</label>
                                    <div class="col-sm-10">
                                        <select id="produk" class="form-control select2">
                                            <option value="" disabled selected>-- Pilih Produk --</option>
                                            <?php foreach ($produk as $dt){?>
                                                <option value="<?=$dt["barcode"]?>" data-harga="<?=$dt["harga_konsinyasi"]?>" data-size="<?=$dt["size_available"]?>" data-sizestok="<?=$dt["stok_per_size"]?>">
                                                    <?=$dt["namaproduk"]?>
                                                </option>
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
    </div>
</div>