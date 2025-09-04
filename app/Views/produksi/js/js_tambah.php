<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function(){
    $(".select2").select2();

    // Init DataTable
    let table = $("#table_data").DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        columnDefs: [
            { targets: [0,2,3,4], className: "text-center" }
        ]
    });

    // === fungsi format angka dengan ribuan (Indonesia pakai ".")
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // === fungsi cek apakah produk tersedia (kalau tidak, sembunyikan tombol +Tambah)
    function checkProdukAvailable() {
        let selected = $("#produk").find(":selected");
        let harga = selected.data("harga");

        if (!harga) {
            $("#btnAdd").hide();   // sembunyikan tombol
        } else {
            $("#btnAdd").show();   // tampilkan tombol
        }
    }

    // === fungsi render preview bahan baku
    function renderPreviewBahan(bahanList) {
        let container = $("#preview_bahan");
        container.empty();

        if (!bahanList || bahanList.length === 0) {
            container.append(`<div class="form-group"><label class="col-sm-12 control-label">Tidak ada detail bahan</label></div>`);
            return;
        }

        bahanList.forEach(function(item){
            container.append(`
                <div class="form-group">
                    <label class="col-sm-6 control-label">
                        ${item.namabahan} jumlah ${item.jumlah} ${item.satuan}
                        <span class="text-muted">(stok: ${item.stok})</span>
                    </label>
                </div>
            `);
        });
    }

    // === Isi harga & jumlah otomatis saat pilih produk + tampilkan preview bahan
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let harga = selected.data("harga");

        if (!harga) {
            $("#harga").val("");
            checkProdukAvailable();
            $("#preview_bahan").empty(); // reset preview
            return;
        }

        $("#harga").val(harga);
        checkProdukAvailable(); // cek tombol +Tambah

        // tampilkan preview bahan
        let bahanJson = selected.attr("data-bahan");
        if (bahanJson) {
            try {
                let bahanArr = JSON.parse(bahanJson);
                renderPreviewBahan(bahanArr);
            } catch(e) {
                console.error("Format data-bahan salah:", e);
                $("#preview_bahan").html(`<div class="form-group"><label class="col-sm-12 control-label text-danger">Data bahan tidak valid</label></div>`);
            }
        }
    });

    // === Tambah ke grid
    $("#btnAdd").click(function(){
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let jumlah  = parseInt($("#jumlah").val());
        let harga   = parseInt($("#harga").val());

        // --- Ambil komposisi bahan (JSON dari atribut data-bahan)
        let bahanJson = $("#produk option:selected").attr("data-bahan");
        let komposisi = [];
        if (bahanJson) {
            try {
                komposisi = JSON.parse(bahanJson);
            } catch(e) {
                console.error("Format data-bahan salah:", e);
                alert("Data bahan tidak valid, hubungi admin.");
                return;
            }
        }

        if(!barcode || !jumlah || !harga){
            alert("Produk, jumlah & harga wajib diisi!");
            return;
        }

        // --- Hitung jumlah produk yang sudah ada di tabel
        let existingJumlah = 0;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[0]).filter("input").val();
            if(existingBarcode === barcode){
                existingJumlah = parseInt($(row[2]).filter("input").val()) || 0;
            }
        });

        let totalJumlah = existingJumlah + jumlah; // jumlah total setelah ditambah

        // --- Cek stok bahan berdasarkan totalJumlah
        let kurang = [];
        komposisi.forEach(function(item){
            let kebutuhan = totalJumlah * item.jumlah;
            if(kebutuhan > item.stok){
                kurang.push(
                    `${item.namabahan}: butuh ${kebutuhan} ${item.satuan}, stok hanya ${item.stok} ${item.satuan}`
                );
            }
        });

        if(kurang.length > 0){
            alert("Stok bahan tidak mencukupi:\n" + kurang.join("\n"));
            return;
        }

        // tampilkan daftar bahan dalam bentuk string untuk masuk tabel
        let bahanText = komposisi.map(item => `${item.namabahan} ${item.jumlah} ${item.satuan}`).join(", ");

        // cek apakah produk sudah ada di DataTable
        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[0]).filter("input").val();
            if(existingBarcode === barcode){
                rowFound = this;
            }
        });

        if(rowFound){
            let newJumlah = totalJumlah;
            let total = newJumlah * harga;

            rowFound.data([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                bahanText,
                `<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);

        }else{
            let total = jumlah * harga;
            table.row.add([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                bahanText,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        // Reset input + preview
        $("#produk").val("").trigger("change");
        $("#jumlah").val(1);
        $("#harga").val("");
        $("#preview_bahan").empty();

        updateSubtotal();
        checkProdukAvailable();
    });

    // === Hapus baris
    $("#table_data tbody").on("click", ".btnDelete", function(){
        let row    = $(this).closest("tr");
        table.row(row).remove().draw(false);
        updateSubtotal();
    });

    // === Hitung subtotal
    function updateSubtotal(){
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val());
        });
        $("#subtotal").text(formatNumber(subtotal));
    }

    // === Submit form
    $("#form_produksi").submit(function(e){
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('produksi/add-data')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Produksi berhasil disimpan!");
                    window.location.href = "<?=base_url('produksi')?>";
                }else{
                    alert(res.message);
                }
            },
            error: function(xhr){
                alert("Terjadi kesalahan server!\n" + xhr.responseText);
            }
        });
    });		

    // jalankan sekali di awal
    checkProdukAvailable();
});
</script>
