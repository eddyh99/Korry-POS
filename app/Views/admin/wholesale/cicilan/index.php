<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/wholesale/cicilantambah">Tambah</a>
            </div>
            <div class="card">
                <?php if (isset($_SESSION["message"])){?>
                <div class="alert alert-success"><?=$_SESSION["message"]?></div>
                <?php } ?>
                <div class="card-content">
                    	<table id="table_data" class="table table-striped nowrap" width="100%">
                    	    <thead>
                                <tr>
                                    <th>No. Cicilan</th>
                                    <th>No. Order</th>
                                    <th>Tanggal</th>
                                    <th>Bayar</th>
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

<!-- Modal Confirm Delete Cicilan Wholesale -->
<div class="modal fade" id="modal_deleteCicilan" tabindex="-1" role="dialog" aria-labelledby="deleteCicilanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCicilanLabel">Konfirmasi Hapus Cicilan</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus Cicilan Wholesale dengan No. Nota:
          <strong id="nonotaCicilanToDelete"></strong>?
        </p>
        <input type="hidden" id="nonotaCicilanHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteCicilanBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>
