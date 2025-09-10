<script>
$(document).ready(function(){
    let bahanIndex = 0;

    // simpan semua bahan dari PHP ke JS
    let allBahan = <?php echo json_encode($bahanbaku); ?>;

    function refreshAddButtonBahan() {
        // kalau semua bahan sudah dipakai, tombol tambah hilang
        if ($(".bahan-select option:selected").length >= allBahan.length) {
            $("#btnAddBahan").hide();
        } else {
            $("#btnAddBahan").show();
        }
    }

    // klik tombol tambah bahan
    $("#btnAddBahan").click(function(e){
        e.preventDefault();
        bahanIndex++;

        let row = `
            <div class="form-group row align-items-center" id="row-bahan-${bahanIndex}">
                <label class="col-sm-3 col-form-label lbl-bahan">Bahan</label>
                <div class="col-sm-4">
                    <select class="form-control bahan-select" name="bahanbaku[${bahanIndex}]" required>
                        <option value="">-- Pilih Bahan --</option>
                        <?php foreach($bahanbaku as $dt): ?>
                            <option value="<?= $dt["id"] ?>"><?= $dt["namabahan"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="number" class="form-control jumlah-input" name="jumlah[${bahanIndex}]" 
                           placeholder="-- Input Jumlah --" min="0" step="0.01" required>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm btnRemoveBahan" data-id="${bahanIndex}">X</button>
                </div>
            </div>
        `;
        $("#bahan-container").append(row);

        refreshOptionsBahan();
        refreshAddButtonBahan();
    });

    // hapus baris bahan
    $(document).on("click", ".btnRemoveBahan", function(){
        let id = $(this).data("id");
        $("#row-bahan-"+id).remove();
        refreshOptionsBahan();
        refreshAddButtonBahan();
    });

    // filter bahan supaya tidak bisa dipilih dua kali
    function refreshOptionsBahan() {
        let selectedValues = $(".bahan-select").map(function(){ return $(this).val(); }).get();

        $(".bahan-select").each(function(){
            let currentVal = $(this).val();
            $(this).find("option").each(function(){
                if ($(this).val() !== "" && selectedValues.includes($(this).val()) && $(this).val() !== currentVal) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }


    /* ==============================
       BAGIAN INPUT DINAMIS BIAYA PRODUKSI
       ============================== */
    let biayaIndex = 0;

    // simpan semua biaya produksi dari PHP ke JS
    let allBiaya = <?php echo json_encode($biayaproduksi); ?>;

    function refreshAddButtonBiaya() {
        // kalau semua biaya produksi sudah dipakai, tombol tambah hilang
        if ($(".biaya-select option:selected").length >= allBiaya.length) {
            $("#btnAddBiaya").hide();
        } else {
            $("#btnAddBiaya").show();
        }
    }

    // klik tombol tambah biaya produksi
    $("#btnAddBiaya").click(function(e){
        e.preventDefault();
        biayaIndex++;

        let row = `
            <div class="form-group row align-items-center" id="row-biaya-${biayaIndex}">
                <label class="col-sm-3 col-form-label lbl-biaya">Biaya</label>
                <div class="col-sm-4">
                    <select class="form-control biaya-select" name="biayaproduksi[${biayaIndex}]" required>
                        <option value="">-- Pilih Jenis Biaya --</option>
                        <?php foreach($biayaproduksi as $dt): ?>
                            <option value="<?= $dt["namabiayaproduksi"] ?>"><?= $dt["namabiayaproduksi"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="number" class="form-control biaya-harga" name="hargaproduksi[${biayaIndex}]" 
                           placeholder="-- Input Harga --" min="0" step="0.01" required>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm btnRemoveBiaya" data-id="${biayaIndex}">X</button>
                </div>
            </div>
        `;
        $("#biayaproduksi-container").append(row);

        refreshOptionsBiaya();
        refreshAddButtonBiaya();
    });

    // hapus baris biaya produksi
    $(document).on("click", ".btnRemoveBiaya", function(){
        let id = $(this).data("id");
        $("#row-biaya-"+id).remove();
        refreshOptionsBiaya();
        refreshAddButtonBiaya();
    });

    // filter biaya supaya tidak bisa dipilih dua kali
    function refreshOptionsBiaya() {
        let selectedValues = $(".biaya-select").map(function(){ return $(this).val(); }).get();

        $(".biaya-select").each(function(){
            let currentVal = $(this).val();
            $(this).find("option").each(function(){
                if ($(this).val() !== "" && selectedValues.includes($(this).val()) && $(this).val() !== currentVal) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

});
</script>
