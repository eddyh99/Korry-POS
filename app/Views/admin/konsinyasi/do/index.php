<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/konsinyasi/dotambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                    		<tr>
                    			<!-- <th>No. Konsinyasi</th>
                    			<th>Partner</th>
                    			<th>Tanggal</th>
                                <th>Total</th>
                    			<th>Aksi</th> -->
                                <th>No. Konsinyasi</th>
                    			<th>Partner</th>
                                <th>Tanggal</th>
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

<!-- Modal Confirm Delete DO Konsinyasi -->
<div class="modal fade" id="modal_deleteDo" tabindex="-1" role="dialog" aria-labelledby="deleteDoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteDoLabel">Konfirmasi Hapus</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus DO Konsinyasi dengan No. Nota:
          <strong id="nonotaDoToDelete"></strong>?
        </p>
        <input type="hidden" id="nonotaDoHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Nota DO Konsinyasi -->
<div class="modal fade" id="modal_detailDo" tabindex="-1" role="dialog" aria-labelledby="detailDoLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="detailDoLabel">Detail Nota DO Konsinyasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <div id="detailNotaContent">
          <div class="row">
            <div class="col-md-6">
              <p><b>No Nota:</b> <span id="detail_nonota"></span></p>
              <p><b>Tanggal:</b> <span id="detail_tanggal"></span></p>
            </div>
            <div class="col-md-6">
              <p><b>Partner:</b> <span id="detail_partner"></span></p>
              <p><b>User:</b> <span id="detail_user"></span></p>
            </div>
          </div>

          <hr>
          <h6>Detail Barang</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>Barcode</th>
                  <th>Produk</th>
                  <th>SKU</th>
                  <th>Size</th>
                  <th>Warna</th>
                  <th>Jumlah</th>
                  <th>Harga</th>
                </tr>
              </thead>
              <tbody id="detail_tableBody"></tbody>
              <tfoot>
                <tr>
                  <th colspan="5" class="text-right">Total</th>
                  <th id="detail_totalJumlah" class="text-center"></th>
                  <th id="detail_totalHarga" class="text-right"></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


