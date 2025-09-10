<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-warning"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
					<form id="form_input" method="post" action="<?=base_url()?>admin/produk/add-data">
						<div class="col-lg-6">
							<div class="card-body">

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Barcode</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="barcode" name="barcode" 
											maxlength="13" minlength="13" required onkeypress="return isNumber(event)" 
											value="<?=old('barcode')?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Nama Produk</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="produk" name="produk" 
											maxlength="50" required value="<?=old('produk')?>">
									</div>
								</div>

								<!-- input baru: Fabric & Warna -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Fabric</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="fabric" name="fabric" 
											maxlength="50" list="fabriclist" required value="<?=old('fabric')?>">
										<datalist id="fabriclist">
											<?php foreach($fabric as $dt): ?>
												<option value="<?=$dt['namafabric']?>"></option>
											<?php endforeach; ?>
										</datalist>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Warna</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="warna" name="warna" 
											maxlength="50" list="warnalist" required value="<?=old('warna')?>">
										<datalist id="warnalist">
											<?php foreach($warna as $dt): ?>
												<option value="<?=$dt['namawarna']?>"></option>
											<?php endforeach; ?>
										</datalist>
									</div>
								</div>	

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Harga Retail</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="harga" name="harga" 
											maxlength="7" required onkeypress="return isNumber(event)" 
											value="<?=old('harga')?>">
									</div>
								</div>

								<!-- Tambahan : Harga Konsinyasi & Wholesale -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Harga Konsinyasi</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="hargakonsinyasi" name="hargakonsinyasi" 
											maxlength="7" required onkeypress="return isNumber(event)" 
											value="<?=old('hargakonsinyasi')?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Harga Wholesale</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="hargawholesale" name="hargawholesale" 
											maxlength="7" required onkeypress="return isNumber(event)" 
											value="<?=old('hargawholesale')?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Diskon</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="diskon" name="diskon" 
											maxlength="7" onkeypress="return isNumber(event)" 
											value="<?=old('diskon')?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Brand</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="brand" name="brand" 
											maxlength="50" list="brandlist" required value="<?=old('brand')?>">
										<datalist id="brandlist">
											<?php foreach($brand as $dt): ?>
												<option value="<?=$dt['namabrand']?>"></option>
											<?php endforeach; ?>
										</datalist>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Kategori</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="kategori" name="kategori" 
											maxlength="50" list="kategorilist" required value="<?=old('kategori')?>">
										<datalist id="kategorilist">
											<?php foreach($kategori as $dt): ?>
												<option value="<?=$dt['namakategori']?>"></option>
											<?php endforeach; ?>
										</datalist>
									</div>
								</div>

								<!-- input baru: SKU -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">SKU</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="sku" name="sku" maxlength="10" 
											required value="<?=old('sku')?>">
									</div>
								</div>
								<hr>
								<!-- input baru: Dynamic Bahan Baku -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Bahan Baku</label>
									<div class="col-sm-7">
										<button type="button" id="btnAddBahan" class="btn btn-secondary btn-sm">
											+ Tambah Bahan Baku
										</button>
									</div>
								</div>

								<!-- container baris bahan -->
								<div id="bahan-container"></div>

								<!-- input baru: Jenis Biaya Produksi -->


								<!-- input baru: Jumlah Biaya Produksi -->



								<!-- input baru: Dynamic Biaya Produksi -->
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">Biaya Produksi</label>
									<div class="col-sm-7">
										<button type="button" id="btnAddBiaya" class="btn btn-secondary btn-sm">
											+ Tambah Biaya Produksi
										</button>
									</div>
								</div>

								<!-- container baris biaya produksi -->
								<div id="biayaproduksi-container"></div>

							</div>
						</div>

						<div class="col-lg-12">
							<div class="col-lg-6">
								<button id="btnSimpan" name="btnSimpan" class="btn btn-primary">Simpan</button>
							</div>
							<div class="col-lg-6 text-right">
								<a name="btnBack" href="<?=base_url()?>admin/produk" class="btn btn-warning">
									<i class="material-icons">reply</i>
									Back</a>
							</div>
						</div>
					</form>
                </div>
            </div>
            <div class="card">
                <div class="card-content pb-2">
                    <form action="<?=base_url()?>admin/produk/import" method="post" enctype="multipart/form-data">
          				<label class="col-md-3 col-form-label">Input file Excel</label>
                  		<div class="col-md-7">
                    		<input type="file" name="produk" id="produk" class="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                    	</div>
                    	<div class="col-md-12"><button class="btn btn-primary">Upload</button></div>
                	</form>
                </div>
            </div>        
        </div>


<!-- End Container -->
    </div>
</div>