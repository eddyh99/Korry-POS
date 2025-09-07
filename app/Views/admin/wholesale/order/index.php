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
