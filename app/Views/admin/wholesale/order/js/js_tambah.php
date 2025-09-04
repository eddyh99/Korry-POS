<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function(){
    // init select2
    $(".select2").select2();

    const table = $("#table_data").DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        columnDefs: [
            { targets: "_all", className: "align-middle" },
            { targets: [3,4,5], className: "text-right" }
        ],
        drawCallback: function(){
            hitungSubtotal();
        }
    });

    function formatNumber(num){
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function hitungTotal(jumlah, harga, potonganInput){
        let total = jumlah * harga;
        let potongan = 0;

        if(potonganInput.toString().includes("%")){
            const percent = parseFloat(potonganInput.replace("%","")) || 0;
            potongan = Math.round(total * percent / 100);
        }else{
            potongan = parseFloat(potonganInput) || 0;
        }
        return total - potongan;
    }

    function hitungSubtotal(){
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val()) || 0;
        });
        $("#subtotal").text(formatNumber(subtotal));
    }

    // ambil harga ketika pilih produk
    $("#produk").change(function(){
        let harga = $(this).find(":selected").data("harga") || 0;
        let sizeStr    = $(this).find(":selected").data("size");   // contoh: "S,M,L"
        $("#harga").val(harga);
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
        if (sizeStr) {
            sizeStr.split(",").forEach(sz => {
                $("#size").append(`<option value="${sz}">${sz}</option>`);
            });
        }    });

    // tambah produk ke tabel
    $("#btnAdd").on("click", function(){
        let barcode  = $("#produk").val();
        let nama     = $("#produk option:selected").text();
        let size     = $("#size").val();
        let jumlah   = parseFloat($("#jumlah").val()) || 0;
        let harga    = parseFloat($("#harga").val()) || 0;
        let potonganRaw = $("#potongan").val().trim(); // bisa angka atau %

        if(!barcode || !size || jumlah <= 0 || harga <= 0){
            alert("Produk, size, jumlah, dan harga wajib diisi!");
            return;
        }

        // cek apakah row dengan barcode + size sudah ada
        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[0]).filter("input").val();
            let existingSize    = $(row[2]).filter("input").val();
            if(existingBarcode === barcode && existingSize === size){
                rowFound = this;
            }
        });

        if(rowFound){
            // ambil jumlah lama
            let jumlahLama = parseFloat($(rowFound.data()[3]).filter("input").val()) || 0;
            let jumlahBaru = jumlahLama + jumlah;

            let total = hitungTotal(jumlahBaru, harga, potonganRaw);

            // update row
            rowFound.data([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                `<input type="hidden" name="jumlah[]" value="${jumlahBaru}">${jumlahBaru}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="potongan[]" value="${potonganRaw}">${potonganRaw}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }else{
            // row baru
            let total = hitungTotal(jumlah, harga, potonganRaw);

            table.row.add([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="potongan[]" value="${potonganRaw}">${potonganRaw}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        // reset input
        $("#produk").val("").trigger("change");
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
        $("#jumlah").val(1);
        $("#harga").val("");
        $("#potongan").val(0);

        hitungSubtotal();
    });


    // hapus produk dari tabel
    $("#table_data").on("click", ".btnDelete", function(){
        const tr = $(this).closest("tr");
        const row = table.row(tr);
        const barcode = tr.find("input[name='barcode[]']").val();
        const nama    = tr.find("td:eq(1)").text();
        const harga   = tr.find("input[name='harga[]']").val();

        $("#produk").append(
            `<option value="${barcode}" data-harga="${harga}">${nama}</option>`
        );

        row.remove().draw();
        hitungSubtotal();
    });

    // submit form via ajax
    $("#form_order").submit(function(e){
        e.preventDefault();

        // hitung total jumlah barang
        let totalQty = 0;
        $("input[name='jumlah[]']").each(function(){
            totalQty += parseFloat($(this).val()) || 0;
        });

        if(totalQty < 100){
            alert("Jumlah total barang minimal 100!");
            return; // hentikan submit
        }

        let formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('admin/wholesale/add-data-order')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Order wholesale berhasil disimpan!");
                    window.open("<?=base_url('admin/wholesale/cetaknotaorder')?>/" + res.notaorder, "_blank");
                    window.location.href = "<?=base_url('admin/wholesale/order')?>";
                }else{
                    alert(res.message);
                }
            },
            error: function(xhr){
                alert("Terjadi kesalahan server!\n" + xhr.responseText);
            }
        });
    });

    function parseInputValue(inputStr, baseAmount){
        let val = 0;
        inputStr = inputStr.toString().trim();
        if(inputStr.includes("%")){
            const percent = parseFloat(inputStr.replace("%","")) || 0;
            val = Math.round(baseAmount * percent / 100);
        }else{
            val = parseFloat(inputStr) || 0;
        }
        return val;
    }

    function formatNumber(num){
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function updateGrandTotal(){
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val()) || 0;
        });

        // tampilkan subtotal ke input
        $("#subtotal").val(formatNumber(subtotal));

        // ambil diskon & ppn
        let diskonRaw = $("#diskon").val();
        let ppnRaw    = $("#ppn").val();

        let diskon = parseInputValue(diskonRaw, subtotal);
        let setelahDiskon = subtotal - diskon;

        let ppn = parseInputValue(ppnRaw, setelahDiskon);

        let grandTotal = setelahDiskon + ppn;

        $("#total").val(formatNumber(grandTotal));
    }

    // event saat diskon/ppn diubah
    $("#diskon, #ppn").on("input", function(){
        updateGrandTotal();
    });

    // panggil juga setelah tabel berubah
    function hitungSubtotal(){
        updateGrandTotal();
    }


});


</script>
