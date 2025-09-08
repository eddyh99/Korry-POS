<div class="content">
    <div class="container-fluid">
<!-- Start -->

		<div class="row">
			<div class="card">
				<div class="card-body">

                    <div class="col-sm-12">

<form id="form_cicilan" method="post" action="<?=base_url()?>admin/wholesale/add-data-cicilan">

    <div class="col-lg-6">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nota Order</label>
                <div class="col-sm-7">
                    <input type="text" id="notaorder" name="notaorder" class="form-control" readonly value="<?=$notaorder?>">
                </div>
            </div>                                   
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Customer</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="customer" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Tanggal Order</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="tanggal" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Total Tagihan</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="total" maxlength="12" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Kekurangan</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="sisa" maxlength="12" readonly>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-12 mt-5">
        <hr>
        <table id="table_data" class="table table-striped nowrap" width="100%">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Besar Cicilan</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-lg-12 text-right">
        <a name="btnBack" href="<?=base_url()?>admin/wholesale/cicilan" class="btn btn-warning">
            <i class="material-icons">reply</i> Kembali
        </a>
    </div>

</form>


                    </div>

				</div>
			</div>
		</div>
<!-- End Container -->
    </div>
</div>