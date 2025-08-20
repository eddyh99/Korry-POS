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
                    <button type="button" class="btn btn-danger btn-sm btnRemove" data-id="${index}">❌</button>
                </div>
            </div>
        `;
        $("#bahan-container").append(row);

        // update label kalau ada pilihan
        let $select = $(`#row-${index} .bahan-select`);
        let selectedText = $select.find("option:selected").text();
        if (selectedText && selectedText !== "-- Pilih Bahan --") {
            $(`#row-${index} .lbl-bahan`).text(selectedText);
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
            $(this).closest(".form-group").find(".lbl-bahan").text(selectedText);
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
});
</script>
