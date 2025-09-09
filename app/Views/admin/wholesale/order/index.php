<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/wholesale/ordertambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                            <tr>
                              <th>Nota Order</th>
                              <th>Partner</th>
                              <th>Tanggal</th>
                              <th>Lama</th>
                              <th>Subtotal</th>
                              <th>DP</th>
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

<!-- Modal Confirm Delete Order Wholesale -->
<div class="modal fade" id="modal_deleteOrder" tabindex="-1" role="dialog" aria-labelledby="deleteOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteOrderLabel">Konfirmasi Hapus</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus Order Wholesale dengan No. Order:
          <strong id="notaOrderToDelete"></strong>?
        </p>
        <input type="hidden" id="notaOrderHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteOrderBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Nota Wholesale Order -->
<div class="modal fade" id="modal_detailOrder" tabindex="-1" role="dialog" aria-labelledby="detailOrderLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="detailOrderLabel">Detail Nota Wholesale Order</h5>
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
              <p><b>Lama (hari):</b> <span id="detail_lama"></span></p>
            </div>
            <div class="col-md-6">
              <p><b>Wholesaler:</b> <span id="detail_partner"></span></p>
              <p><b>Alamat:</b> <span id="detail_alamat"></span></p>
              <p><b>Kontak:</b> <span id="detail_kontak"></span></p>
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
                  <th>Brand</th>
                  <th>Kategori</th>
                  <th>Fabric</th>
                  <th>Size</th>
                  <th>Warna</th>
                  <th class="text-center">Jumlah</th>
                  <th class="text-right">Harga</th>
                  <th class="text-right">Potongan</th>
                  <th class="text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody id="detail_tableBody"></tbody>
              <tfoot>
                <tr>
                  <th colspan="8" class="text-right">Total</th>
                  <th id="detail_totalJumlah" class="text-center"></th>
                  <th></th>
                  <th></th>
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
