<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <input type="hidden" id="tujuan" value="<?=$_SESSION["logged_status"]["storeid"]?>">
		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

                        <form id="form_retur">
                            <!-- Bagian Header Retur Konsinyasi -->
                            <div class="row form-group">
                                <div class="col-sm-3">
                                    <label class="col-form-label">No. DO Konsinyasi</label>
                                    <select id="do_konsinyasi" name="do_konsinyasi" class="form-control select2" required>
                                        <option value="" disabled selected>--Pilih No. DO--</option>
                                        <?php foreach ($do_konsinyasi as $dt) { ?>
                                            <option value="<?= $dt["nonota"] ?>">
                                                <?= $dt["nonota"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <!-- Bagian Input Detail Produk Retur -->
                            <div class="row form-group">
                                <!-- Pilih Produk -->
                                <div class="col-sm-3">
                                    <label class="col-form-label">Produk</label>
                                    <select id="produk" name="produk" class="form-control select2">
                                        <option value="" disabled selected>--Pilih Produk--</option>
                                    </select>
                                </div>

                                <!-- Jumlah Retur -->
                                <div class="col-sm-2">
                                    <label class="col-form-label">Jumlah</label>
                                    <input type="number" id="jumlah" name="jumlah" 
                                        class="form-control" min="1" value="1">
                                </div>

                                <!-- Alasan Retur -->
                                <div class="col-sm-3">
                                    <label class="col-form-label">Alasan Retur</label>
                                    <input type="text" id="alasan" name="alasan" 
                                        class="form-control" placeholder="Masukkan alasan retur">
                                </div>

                                <!-- Tombol Tambah ke Temporary Table -->
                                <div class="col-sm-2 d-flex align-items-end">
                                    <button type="button" id="btnAdd" class="btn btn-success">+ Tambah</button>
                                </div>
                            </div>

                            <hr>

                            <!-- Temporary Table untuk menampung detail retur -->
                            <table id="table_data" class="table table-striped nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Size</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Alasan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <!-- Tombol Simpan & Kembali -->
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">Simpan Retur Konsinyasi</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a name="btnBack" href="<?= base_url() ?>admin/konsinyasi/retur" 
                                    class="btn btn-warning">
                                        <i class="material-icons">reply</i> Kembali
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