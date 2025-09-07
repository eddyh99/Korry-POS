<script>
$(document).ready(function(){
    let bahanIndex = 0;

    // simpan semua bahan dari PHP ke JS
    let allBahan = <?php echo json_encode($bahanbaku); ?>;

    function refreshAddButton() {
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
            <div class="form-group row align-items-center" id="row-${bahanIndex}">
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
                    <button type="button" class="btn btn-danger btn-sm btnRemove" data-id="${bahanIndex}">X</button>
                </div>
            </div>
        `;
        $("#bahan-container").append(row);

        refreshOptions();
        refreshAddButton();
    });

    // hapus baris bahan
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

    // filter bahan supaya tidak bisa dipilih dua kali
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
