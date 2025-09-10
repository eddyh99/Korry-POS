<div class="content">
    <div class="container-fluid">
        <!-- Start -->
        <div class="row mb-3">
            <div class="card">
                <?php if (isset($_SESSION["message"])) { ?>
                    <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <input type="hidden" id="key" value="<?=$key?>">
                <input type="hidden" id="memberid" value="<?=$memberid?>">

                <div class="card-content">
                    <table id="table_retur" class="table table-striped nowrap" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Barcode</th>
                                <th>Produk</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Tombol simpan retur -->
                <div class="col-sm-3 mb-3">
                    <button id="btnpayment" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
        <!-- End Container -->
    </div>
</div>