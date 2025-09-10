<script>
$(document).ready(function(){
    let bahanIndex = 0;

    // semua bahan dari PHP
    let allBahan = <?php echo json_encode($bahanbaku); ?>;
    // bahan produk yang sudah tersimpan
    let produkBahan = <?php echo json_encode($produkBahan); ?>;

    function refreshAddButton() {
        if ($(".bahan-select option:selected").length >= allBahan.length) {
            $("#btnAddBahan").hide();
        } else {
            $("#btnAddBahan").show();
        }
    }

    // render row
    function renderRow(index, selectedId = "", jumlah = "") {
        let row = `
            <div class="form-group row align-items-center" id="row-${index}">
                <label class="col-sm-3 col-form-label lbl-bahan">Bahan</label>
                <div class="col-sm-4">
                    <select class="form-control bahan-select" name="bahanbaku[${index}]" required>
                        <option value="">-- Pilih Bahan --</option>
                        ${allBahan.map(b => 
                            `<option value="${b.id}" ${b.id == selectedId ? "selected" : ""}>${b.namabahan}</option>`
                        ).join("")}
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="number" class="form-control jumlah-input" 
                           name="jumlah[${index}]" 
                           placeholder="-- Input Jumlah --"
                           min="0" step="0.01" 
                           value="${jumlah}" required>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm btnRemove" data-id="${index}">X</button>
                </div>
            </div>
        `;
        $("#bahan-container").append(row);

        // update label kalau ada pilihan
        let $select = $(`#row-${index} .bahan-select`);
        let selectedText = $select.find("option:selected").text();
        if (selectedText && selectedText !== "-- Pilih Bahan --") {
            $(`#row-${index} .lbl-bahan`).text("Bahan");
        }
    }

    // preload dari DB
    if (produkBahan.length > 0) {
        produkBahan.forEach((pb) => {
            bahanIndex++;
            renderRow(bahanIndex, pb.idbahan, pb.jumlah);
        });
    }

    refreshOptions();
    refreshAddButton();

    // tambah baru
    $("#btnAddBahan").click(function(e){
        e.preventDefault();
        bahanIndex++;
        renderRow(bahanIndex);
        refreshOptions();
        refreshAddButton();
    });

    // hapus
    $(document).on("click", ".btnRemove", function(){
        let id = $(this).data("id");
        $("#row-"+id).remove();
        refreshOptions();
        refreshAddButton();
    });

    // ubah label sesuai bahan yang dipilih
    $(document).on("change", ".bahan-select", function(){
        let selectedText = $(this).find("option:selected").text();
        if(selectedText && selectedText !== "-- Pilih Bahan --") {
            $(this).closest(".form-group").find(".lbl-bahan").text("Bahan");
        } else {
            $(this).closest(".form-group").find(".lbl-bahan").text("Bahan");
        }
        refreshOptions();
        refreshAddButton();
    });

    // filter opsi supaya tidak dobel
    function refreshOptions() {
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

    /* ==========================
       BAGIAN UBAH DINAMIS BIAYA-PRODUKSI
       ========================== */
    let biayaIndex = 0;

    // semua biaya produksi dari PHP
    let allBiaya = <?php echo json_encode($biayaproduksi); ?>;
    // biaya produksi produk yang sudah tersimpan
    let produkBiaya = <?php echo json_encode($produkBiaya); ?>;

    function refreshAddButtonBiaya() {
        // kalau semua biaya produksi sudah dipakai, tombol tambah hilang
        if ($(".biaya-select option:selected").length >= allBiaya.length) {
            $("#btnAddBiaya").hide();
        } else {
            $("#btnAddBiaya").show();
        }
    }

    // render row
    function renderBiayaRow(index, selectedNama = "", nominal = "") {
        let row = `
            <div class="form-group row align-items-center" id="biaya-row-${index}">
                <label class="col-sm-3 col-form-label lbl-biaya">Biaya Produksi</label>
                <div class="col-sm-4">
                    <select class="form-control biaya-select" name="biayaproduksi[${index}]" required>
                        <option value="">-- Pilih Biaya --</option>
                        ${allBiaya.map(b => 
                            `<option value="${b.namabiayaproduksi}" ${b.namabiayaproduksi == selectedNama ? "selected" : ""}>${b.namabiayaproduksi}</option>`
                        ).join("")}
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="number" class="form-control nominal-input" 
                           name="hargaproduksi[${index}]" 
                           placeholder="-- Input Nominal --"
                           min="0" step="0.01" 
                           value="${nominal}" required>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger btn-sm btnRemoveBiaya" data-id="${index}">X</button>
                </div>
            </div>
        `;
        $("#biayaproduksi-container").append(row);

        // update label kalau ada pilihan
        let $select = $(`#biaya-row-${index} .biaya-select`);
        let selectedText = $select.find("option:selected").text();
        if (selectedText && selectedText !== "-- Pilih Biaya --") {
            $(`#biaya-row-${index} .lbl-biaya`).text("Biaya Produksi");
        }
    }

    // preload dari DB
    if (produkBiaya.length > 0) {
        produkBiaya.forEach((pb) => {
            biayaIndex++;
            renderBiayaRow(biayaIndex, pb.namabiayaproduksi, pb.nominal);
        });
    }

    refreshBiayaOptions();
    refreshAddButtonBiaya();

    // tambah baru
    $("#btnAddBiaya").click(function(e){
        e.preventDefault();
        biayaIndex++;
        renderBiayaRow(biayaIndex);
        refreshBiayaOptions();
        refreshAddButtonBiaya();
    });

    // hapus
    $(document).on("click", ".btnRemoveBiaya", function(){
        let id = $(this).data("id");
        $("#biaya-row-"+id).remove();
        refreshBiayaOptions();
        refreshAddButtonBiaya();
    });

    // ubah label sesuai biaya produksi yang dipilih
    $(document).on("change", ".biaya-select", function(){
        let selectedText = $(this).find("option:selected").text();
        if(selectedText && selectedText !== "-- Pilih Biaya --") {
            $(this).closest(".form-group").find(".lbl-biaya").text("Biaya Produksi");
        } else {
            $(this).closest(".form-group").find(".lbl-biaya").text("Biaya Produksi");
        }
        refreshBiayaOptions();
        refreshAddButtonBiaya();
    });

    // filter opsi supaya tidak dobel
    function refreshBiayaOptions() {
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
