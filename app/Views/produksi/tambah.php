<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

						<form id="form_produksi">

							<div class="row form-group">
								<div class="col-sm-3">
									<label class="col-form-label">Vendor</label>
									<select id="idvendor" name="idvendor" class="form-control select2" required>
										<option value="" disabled selected>-- Pilih Vendor --</option>
										<?php foreach ($vendor as $dt){?>
											<option value="<?=$dt["id"]?>"><?=$dt["nama"]?></option>
										<?php } ?>
									</select>
								</div>

								<div class="col-sm-2">
									<label class="col-form-label">Estimasi (Hari)</label>
									<div class="input-group">
										<input type="number" id="estimasi" name="estimasi" class="form-control" min="0" required>
									</div>
								</div>

								<div class="col-sm-2">
									<label class="col-form-label">DP</label>
									<input type="number" id="dp" name="dp" class="form-control" min="0">
								</div>

								<div class="col-sm-2">
									<label class="col-form-label">Total</label>
									<input type="number" id="totalproduksi" name="totalproduksi" class="form-control" min="0">
								</div>
							</div>

							<hr>

							<!-- Detail Produksi -->
							<div class="row form-group">
								<div class="col-sm-3">
									<label class="col-form-label">Produk</label>
									<select id="produk" class="form-control select2">
										<option value="" disabled selected>-- Pilih Produk --</option>
										<?php foreach ($produk as $dt){?>
										<option value="<?=$dt["barcode"]?>" 
												data-nama="<?=$dt["namaproduk"]?>" 
												data-harga="<?=$dt["harga"]?>">
											<?= $dt['barcode']; ?> - <?= $dt['namaproduk']; ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-2">
									<label class="col-form-label">Jumlah</label>
									<input type="number" id="jumlah" class="form-control" min="1" value="1">
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

							<!-- Tabel Detail -->
							<table id="table_data" class="table table-striped nowrap" width="100%">
								<thead>
									<tr>
										<th>Barcode</th>
										<th>Nama Produk</th>
										<th>Jumlah</th>
										<th>Harga</th>
										<th>Total</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<th colspan="3"></th>
										<th class="text-right">Sub Total</th>
										<th id="subtotal">0</th>
										<th></th>
									</tr>
								</tfoot>
							</table>

							<div class="row mt-3">
								<div class="col-sm-6">
									<button type="submit" class="btn btn-primary">Simpan Produksi</button>
								</div>
								<div class="col-sm-6 text-right">
									<a name="btnBack" href="<?=base_url()?>produksi" class="btn btn-warning">
										<i class="material-icons">reply</i>Kembali</a>
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