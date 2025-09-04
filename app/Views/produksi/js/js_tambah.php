<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function () {
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

    // === format angka ribuan
    function formatNumber(num) {
        num = Number(num || 0);
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // === tampilkan/sembunyikan tombol +Tambah
    function checkProdukAvailable() {
        let harga = $("#produk option:selected").data("harga");
        if (!harga) $("#btnAdd").hide();
        else $("#btnAdd").show();
    }

    // === render preview bahan
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
                    <label class="col-sm-12 control-label">
                        ${item.namabahan} jumlah ${item.jumlah} ${item.satuan}
                        <span class="text-muted">(stok: ${item.stok})</span>
                    </label>
                </div>
            `);
        });
    }

    // === isi harga + preview + size saat pilih produk
    $("#produk").change(function(){
        let selected   = $(this).find(":selected");
        let harga      = selected.data("harga");
        let sizeStr    = selected.data("size");   // contoh: "S,M,L"
        let bahanJson  = selected.attr("data-bahan");

        if (!harga) {
            $("#harga").val("");
            $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
            $("#preview_bahan").empty();
            checkProdukAvailable();
            return;
        }

        $("#harga").val(harga);

        // isi size
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
        if (sizeStr) {
            sizeStr.split(",").forEach(sz => {
                $("#size").append(`<option value="${sz}">${sz}</option>`);
            });
        }

        // isi preview bahan
        let bahanArr = [];
        if (bahanJson) {
            try {
                bahanArr = JSON.parse(bahanJson);
            } catch(e) {
                console.error("Format data-bahan salah:", e);
                bahanArr = [];
            }
        }
        renderPreviewBahan(bahanArr);

        checkProdukAvailable();
    });

    // === hitung subtotal
    function updateSubtotal(){
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val() || 0);
        });
        $("#subtotal").text(formatNumber(subtotal));
    }

    // === tambah ke grid
    $("#btnAdd").click(function(){
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let jumlah  = parseInt($("#jumlah").val());
        let harga   = parseInt($("#harga").val());
        let size    = $("#size").val();

        // ambil komposisi bahan
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

        if (!barcode || !jumlah || !harga || !size) {
            alert("Produk, size, jumlah & harga wajib diisi!");
            return;
        }

        // cek existing row (barcode + size)
        let existingJumlah = 0;
        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[0]).filter("input").val();
            let existingSize    = $(row[3]).filter("input").val();
            if (existingBarcode === barcode && existingSize === size) {
                existingJumlah = parseInt($(row[2]).filter("input").val()) || 0;
                rowFound = this;
            }
        });

        let totalJumlah = existingJumlah + jumlah;

        // cek stok bahan
        let kurang = [];
        komposisi.forEach(function(item){
            let kebutuhan = totalJumlah * item.jumlah;
            if (kebutuhan > item.stok) {
                kurang.push(`${item.namabahan}: butuh ${kebutuhan} ${item.satuan}, stok hanya ${item.stok} ${item.satuan}`);
            }
        });

        if (kurang.length > 0) {
            alert("Stok bahan tidak mencukupi:\n" + kurang.join("\n"));
            return;
        }

        let total = totalJumlah * harga;

        if (rowFound) {
            // update row
            rowFound.data([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="jumlah[]" value="${totalJumlah}">${totalJumlah}`,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        } else {
            // tambah row
            table.row.add([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${jumlah * harga}">${formatNumber(jumlah * harga)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        // reset input
        $("#produk").val("").trigger("change");
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
        $("#jumlah").val(1);
        $("#harga").val("");
        $("#preview_bahan").empty();

        updateSubtotal();
        checkProdukAvailable();
    });

    // === hapus baris
    $("#table_data tbody").on("click", ".btnDelete", function(){
        table.row($(this).closest("tr")).remove().draw(false);
        updateSubtotal();
    });

    // === submit form
    $("#form_produksi").submit(function(e){
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: "<?=base_url('produksi/add-data')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if (res.status) {
                    alert("Produksi berhasil disimpan!");
                    window.location.href = "<?=base_url('produksi')?>";
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr){
                alert("Terjadi kesalahan server!\n" + xhr.responseText);
            }
        });
    });

    // init awal
    checkProdukAvailable();
    updateSubtotal();
});
</script>
