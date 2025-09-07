<div class="content">
    <div class="container-fluid">
<!-- Start -->
    <form id="form_produksi" class="form-horizontal">
        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
				<div class="card-header">
					<h5 class="card-title">PRODUCTION DETAILS</h5>
				</div>
                <div class="card-content">
						<div class="col-lg-6">
							<div class="form-group">
								<label for="idvendor" class="col-sm-4">Vendor</label>
								<div class="col-sm-8">
								<select id="idvendor" name="idvendor" class="form-control select2" required>
									<option value="" disabled selected>-- Pilih Vendor --</option>
									<?php foreach ($vendor as $dt){ ?>
									<option value="<?=$dt["id"]?>"><?=$dt["nama"]?></option>
									<?php } ?>
								</select>
								</div>
							</div>

							<!-- Estimasi (Hari) -->
							<div class="form-group">
								<label for="estimasi" class="col-sm-4">Estimasi (Hari)</label>
								<div class="col-sm-8">
								<input type="number" id="estimasi" name="estimasi" class="form-control" min="0" required>
								</div>
							</div>

							<!-- DP -->
							<div class="form-group">
								<label for="dp" class="col-sm-4">DP</label>
								<div class="col-sm-8">
								<input type="number" id="dp" name="dp" class="form-control" min="0">
								</div>
							</div>
						</div>
                </div>
            </div>
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">ADD PRODUCT</h5>
				</div>
				<div class="card-content">
					<div class="row">
						<div class="col-sm-6">
							<!-- Kiri -->
							<div class="form-group">
								<label for="produk" class="col-sm-2">Produk</label>
								<div class="col-sm-8">
									<select id="produk" class="form-control select2">
										<option value="" disabled selected>-- Pilih Produk --</option>
										<?php foreach ($produk as $dt){ ?>
										<option value="<?= $dt["barcode"] ?>"
												data-nama="<?= $dt["namaproduk"] ?>"
												data-harga="<?= $dt["harga_produksi"] ?>"
												data-size="<?=$dt["size_available"]?>"
												data-bahan='<?= json_encode($dt["bahan"], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
											<?=$dt['namaproduk']; ?>
										</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2">Size</label>
								<div class="col-sm-4">
									<select id="size" class="form-control">
										
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2">Jumlah</label>
								<div class="col-sm-4">
									<input type="number" id="jumlah" class="form-control" min="1" value="1">
								</div>
							</div>
							<!-- <div class="form-group">
								<label class="col-sm-2 text-right" style="padding-left:10px;">Harga</label>
								<div class="col-sm-4">
									<input type="text" id="harga" class="form-control">
								</div>
							</div> -->

							<div class="form-group">
								<label class="col-sm-2" style="padding-left:10px;">Biaya</label>
								<div class="col-sm-4">
									<input type="text" id="harga" class="form-control" readonly>
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<!-- Kanan -->
							<div id="content"></div>
						</div>
					</div>

					<div class="form-group mt-3">
						<button type="button" id="btnAdd" class="btn btn-success btn-block">
							<i class="material-icons" style="font-size: 18px; vertical-align: middle;">add</i>
							&nbsp;TAMBAH
						</button>
					</div>
				</div>
			</div>
            <div class="card">
				<div class="card-header">
					<h5 class="card-title">PRODUCT LIST</h5>
				</div>
                <div class="card-content">
					<div class="table-responsive">
						<table id="table_data" class="table table-striped table-bordered">
						<thead>
							<tr>
							<th>Barcode</th>
							<th>Nama Produk</th>
							<th>Jumlah</th>
							<th>Size</th>
							<th>Biaya</th>
							<th>Total</th>
							<th>Aksi</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<tr>
							<th colspan="4" class="text-right">Sub Total:</th>
							<th id="subtotal">0</th>
							<th></th>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<button type="submit" class="btn btn-primary btn-lg btn-block">
				<i class="material-icons" style="font-size: 18px; vertical-align: middle;">save</i>
				&nbsp;SIMPAN PRODUKSI
				</button>
			</div>
			<div class="col-xs-6">
				<a href="<?=base_url()?>produksi" class="btn btn-default btn-lg btn-block">
				<i class="material-icons" style="font-size: 18px; vertical-align: middle;">reply</i>
				&nbsp;KEMBALI
				</a>
			</div>			
		</div>
	</form>


<!-- End Container -->
    </div>
</div>