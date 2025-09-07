<script>
$(document).ready(function(){
    let allBahan = <?php echo json_encode($bahanbaku); ?>;

    // ====== Fungsi bantu ======

    // Refresh nomor label (Bahan 1, Bahan 2, dst)
    function refreshLabels() {
        $("#bahan-container .form-group").each(function(index){
            $(this).find("label").text("Bahan " + (index+1));
        });
    }

    // Supaya bahan tidak bisa dipilih dobel
    function refreshOptions() {
        let selectedValues = $(".bahan-select").map(function(){ return $(this).val(); }).get();

        $(".bahan-select").each(function(){
            let currentVal = $(this).val();
            $(this).find("option").each(function(){
                if ($(this).val() !== "" && selectedValues.includes($(this).val()) && $(this).val() !== currentVal) {
                    $(this).hide(); // sembunyikan kalau sudah dipilih di select lain
                } else {
                    $(this).show();
                }
            });
        });
    }

    // Kontrol tombol tambah bahan
    function refreshAddButton() {
        let selectedValues = $(".bahan-select").map(function(){ return $(this).val(); }).get();
        let rowCount = $("#bahan-container .form-group").length;
        let filledCount = selectedValues.filter(v => v !== "").length;

        // Aturan:
        // - Kalau semua bahan sudah dipakai => hide
        // - Kalau masih ada select kosong => hide
        if (filledCount >= allBahan.length || filledCount < rowCount) {
            $("#btnAddBahan").hide();
        } else {
            $("#btnAddBahan").show();
        }
    }

    // ====== Event Handler ======

    // Klik tambah bahan
    $("#btnAddBahan").click(function(e){
        e.preventDefault();

        let row = `
            <div class="form-group row align-items-center">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-3">
                    <select class="form-control bahan-select" name="idbahan[]" required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        <?php foreach($bahanbaku as $dt): ?>
                            <option value="<?= $dt['id']; ?>"><?= $dt['namabahan']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="number" class="form-control jumlah-input" name="jumlah[]" 
                           min="0" step="0.01" placeholder="Jumlah" required>
                </div>
                <div class="col-sm-2">
                    <select class="form-control satuan-select" name="satuan[]" required>
                        <option value="">-- Satuan --</option>
                        <option value="yard">Yard</option>
                        <option value="meter">Meter</option>
                        <option value="pcs">Pcs</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control harga-input" name="harga[]" 
                           placeholder="Harga Satuan" maxlength="11" required onkeypress="return isNumber(event)">
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-danger btn-sm btnRemove">X</button>
                </div>
            </div>
        `;

        $("#bahan-container").append(row);

        refreshLabels();
        refreshOptions();
        refreshAddButton();
    });

    // Klik hapus row
    $(document).on("click", ".btnRemove", function(){
        $(this).closest(".form-group").remove();
        refreshLabels();
        refreshOptions();
        refreshAddButton();
    });

    // Kalau user ganti select bahan
    $(document).on("change", ".bahan-select", function(){
        refreshOptions();
        refreshAddButton();
    });

    // Inisialisasi awal
    refreshLabels();
    refreshOptions();
    refreshAddButton();
});
</script>
