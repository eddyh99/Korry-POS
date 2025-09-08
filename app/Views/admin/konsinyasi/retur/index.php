<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/konsinyasi/returtambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                                <tr>
                                    <th>No. Retur</th>
                                    <th>No. Nota</th>
                                    <th>Tanggal</th>
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

<!-- Modal Confirm Delete Retur Konsinyasi -->
<div class="modal fade" id="modal_deleteRetur" tabindex="-1" role="dialog" aria-labelledby="deleteReturLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteReturLabel">Konfirmasi Hapus</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus Retur Konsinyasi dengan No. Retur:
          <strong id="noreturToDelete"></strong>?
        </p>
        <input type="hidden" id="noreturHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Retur Konsinyasi -->
<div class="modal fade" id="modal_detailRetur" tabindex="-1" role="dialog" aria-labelledby="detailReturLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:95%;">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="detailReturLabel">Detail Retur Konsinyasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <p><b>No Retur:</b> <span id="detail_noretur"></span></p>
            <p><b>Tanggal:</b> <span id="detail_tanggal"></span></p>
          </div>
          <div class="col-md-6">
            <p><b>No DO Konsinyasi:</b> <span id="detail_nokonsinyasi"></span></p>
            <p><b>User:</b> <span id="detail_user"></span></p>
          </div>
        </div>

        <hr>
        <h6>Detail Barang Retur</h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered" style="width:100%;">
            <thead class="thead-light">
              <tr>
                <th>Barcode</th>
                <th>Produk</th>
                <th>SKU</th>
                <th>Size</th>
                <th>Warna</th>
                <th>Jumlah</th>
                <th>Alasan Retur</th>
              </tr>
            </thead>
            <tbody id="detail_tableBody"></tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
