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