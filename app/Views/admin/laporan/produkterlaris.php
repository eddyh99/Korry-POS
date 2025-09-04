<div class="content">
    <div class="container-fluid">
<!-- Start -->
        <div class="col-sm-12 mb-3">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Laporan 10 Produk Terlaris & Margin</h5>
				</div>
				<div class="card-content">
					<div class="row form-group">
						<label class="col-form-label col-sm-1">Bulan</label>
						<div class="col-sm-2">
							<select name="bulan" id="bulan" class="form-control">
								<option value="all-time">Sepanjang Waktu</option>
								<option value="1">Januari</option>
								<option value="2">Februari</option>
								<option value="3">Maret</option>
								<option value="4">April</option>
								<option value="5">Mei</option>
								<option value="6">Juni</option>
								<option value="7">Juli</option>
								<option value="8">Agustus</option>
								<option value="9">September</option>
								<option value="10">Oktober</option>
								<option value="11">November</option>
								<option value="12">Desember</option>
							</select>
						</div>
						<div class="col-sm-1">
							<input type="text" name="tahun" id="tahun" class="form-control" value="<?= date("Y") ?>">
						</div>
						<div class="col-sm-1">
							<button id="lihat" class="btn btn-primary">Lihat</button>
						</div>
					</div>
					<hr>
					<table id="table_data" class="mt-3 table table-striped nowrap" width="100%">
						<thead>
							<tr>
								<th>Nama Produk</th>
								<th>Total Terjual</th>
								<th>Rata-Rata Harga Jual</th>
								<th>Rata-Rata Biaya Produksi</th>
								<th>Rata-Rata Profit</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
        </div>
<!-- End Container -->
    </div>
</div>