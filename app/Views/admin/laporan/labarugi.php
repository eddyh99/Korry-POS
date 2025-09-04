<div class="content">
    <div class="container-fluid">
<!-- Start -->
<div class="col-sm-12 mb-3">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Laporan Laba-Rugi</h5>
        </div>
        <div class="card-content">
            <div class="row form-group">
                <label class="col-form-label col-sm-1">Tahun</label>
                <div class="col-sm-2">
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
                        <th>Keterangan</th>
                        <th>Jumlah (Rp)</th>
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