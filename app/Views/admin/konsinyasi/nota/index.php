<div class="content">
    <div class="container-fluid">
<!-- Start -->

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary" href="<?=base_url()?>admin/konsinyasi/notatambah">Tambah</a>
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
                                    <th>Nota Jual</th>
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

<!-- Modal Confirm Delete Nota Jual Konsinyasi -->
<div class="modal fade" id="modal_deleteNotajual" tabindex="-1" role="dialog" aria-labelledby="deleteNotajualLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteNotajualLabel">Konfirmasi Hapus</h5>
      </div>
      <!-- Body -->
      <div class="modal-body">
        <p>
          Apakah Anda yakin ingin menghapus Nota Jual Konsinyasi dengan No. Nota:
          <strong id="notajualToDelete"></strong>?
        </p>
        <input type="hidden" id="notajualHidden">
      </div>
      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteNotajualBtn">Ya, Hapus</button>
      </div>
    </div>
  </div>
</div>


