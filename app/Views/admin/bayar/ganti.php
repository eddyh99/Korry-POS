<div class="content">
    <div class="container-fluid">
<!-- Start -->

    <div class="card">
        <div class="card-content">
            <form action="<?=base_url()?>admin/bayar/gantibayar" method="post">
                <input type="hidden" name="key" value="<?=$detail->id?>">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-form-label col-sm-3">No. Nota</label>
                            <label class="col-form-label col-sm-2 text-right"><?=date("d M Y",strtotime($detail->tanggal))?></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label col-sm-3">Cata Pembayaran</label>
                            <label class="col-form-label col-sm-2 text-right"><?=$detail->method?></label>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Cara Bayar Baru</label>
                            <div class="col-sm-3">
                                <select name="carabayar" id="carabayar" class="form-control">
                                    <option value="" disabled>--- CARA BAYAR ---</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Kartu Kredit</option>
                                    <option value="debit">Kartu Debit</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="charge">
                            <label class="col-form-label col-sm-3">Fee</label>
                            <div class="col-sm-3">
                                <input type="text" name="fee" class="form-control" value="0">
                            </div>
                        </div> 
                        <div class="col-sm-6 text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<!-- End Container -->
    </div>
</div>