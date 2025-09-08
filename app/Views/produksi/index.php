<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>produksi/tambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<th>No. Nota</th>
                    			<th>Tgl</th>
                    			<th>Vendor</th>
                    			<th>Estimasi</th>
                    			<th>DP</th>
                                <th>Total</th>
                                <th>Aksi</th>
                    		</tr>
                    	    </thead>
                    	    <tbody>
                    	    </tbody>
                    	</table>
                </div>
            </div>
        </div>
        
<!-- End Container -->
    </div>
</div>

<!-- Modal Confirm Delete Produksi -->
<div class="modal fade" id="modal_deleteProduksi" tabindex="-1" role="dialog" aria-labelledby="deleteProduksiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteProduksiLabel">Konfirmasi Hapus</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus Produksi dengan No. Nota:
          <strong id="nonotaProduksiToDelete"></strong>?
        </p>
        <input type="hidden" id="nonotaProduksiHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteProduksiBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Produksi -->
<!-- Modal Detail Produksi -->
<div class="modal fade" id="modal_detailProduksi" tabindex="-1" role="dialog" aria-labelledby="detailProduksiLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:95%;">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="detailProduksiLabel">Detail Produksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <p><b>No Nota:</b> <span id="detail_nonota"></span></p>
            <p><b>Tanggal:</b> <span id="detail_tanggal"></span></p>
            <p><b>Estimasi (hari):</b> <span id="detail_estimasi"></span></p>
          </div>
          <div class="col-md-6">
            <p><b>Vendor:</b> <span id="detail_vendor"></span></p>
            <p><b>Tipe Vendor:</b> <span id="detail_tipevendor"></span></p>
            <p><b>User:</b> <span id="detail_user"></span></p>
          </div>
        </div>

        <hr>
        <h6>Detail Barang Produksi</h6>
        <table class="table table-sm table-bordered" style="width:100%; table-layout:auto;">
          <thead class="thead-light">
            <tr>
              <th>Barcode</th>
              <th>Produk</th>
              <th>SKU</th>
              <th>Size</th>
              <th>Jumlah</th>
              <th>Harga</th>
              <th>Biaya</th>
            </tr>
          </thead>
          <tbody id="detailProduksi_tableBody"></tbody>
        </table>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
