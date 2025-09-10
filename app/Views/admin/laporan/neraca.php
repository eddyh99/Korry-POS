<div class="content">
    <div class="container-fluid">
<!-- Start -->
<div class="col-sm-12 mb-3">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Laporan Neraca</h5>
        </div>
        <div class="card-content">
            <div class="row form-group">
                <label class="col-form-label col-sm-1">Tanggal</label>
                <div class="col-sm-2">
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?=date("Y-m-d")?>">
                </div>
                <div class="col-sm-1">
                    <button id="lihat" class="btn btn-primary">Lihat</button>
                </div>
            </div>
            <hr>
            <table id="table_data" class="mt-3 table table-striped nowrap" width="100%">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th>Saldo (Rp)</th>
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