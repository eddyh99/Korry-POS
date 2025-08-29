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
            { targets: [3,4,5], className: "text-center" }
        ]
    });

    // === format angka ribuan
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // === ketika DO dipilih, ambil produk by DO
    $("#do_konsinyasi").change(function(){
        let do_id = $(this).val();
        $("#produk").html('<option value="" disabled selected>--Pilih Produk--</option>'); 
        $("#jumlah").val("");
        $("#harga").val("");

        if(!do_id) return;

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/listprodukbydo')?>",
            type: "POST",
            data: { do_id: do_id },
            dataType: "json",
            success: function(res){
                if(res.length === 0){
                    alert("Produk untuk DO ini tidak tersedia / sudah habis.");
                    return;
                }

                res.forEach(function(item){
                    $("#produk").append(
                        `<option value="${item.barcode}" 
                                 data-harga="${item.harga}" 
                                 data-sisa="${item.sisa}">
                            ${item.nama} (Max: ${item.sisa})
                        </option>`
                    );
                });
            },
            error: function(xhr){
                alert("Gagal load produk!\n" + xhr.responseText);
            }
        });
    });

    // === cek tombol tambah bisa dipakai atau tidak
    function checkProdukAvailable() {
        let selected = $("#produk").find(":selected");
        let harga = selected.data("harga");
        let maxJumlah = selected.data("sisa");

        if (!harga || !maxJumlah || maxJumlah <= 0) {
            $("#btnAdd").hide();
        } else {
            $("#btnAdd").show();
        }
    }

    // === isi jumlah & harga otomatis ketika pilih produk
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let harga = selected.data("harga");
        let maxJumlah = selected.data("sisa");

        if (!harga || !maxJumlah) {
            $("#jumlah").val("").removeAttr("max");
            $("#harga").val("");
            checkProdukAvailable();
            return;
        }

        $("#jumlah").val(1).attr("max", maxJumlah);
        $("#harga").val(harga);

        checkProdukAvailable();
    });

    // === validasi jumlah
    $("#jumlah").on("input", function(){
        let max = parseInt($(this).attr("max")) || 0;
        let val = parseInt($(this).val()) || 0;
        let namaProduk = $("#produk option:selected").text();

        if(val > max){
            alert("Maksimal " + namaProduk + " hanya " + max);
            $(this).val(max);
        } else if(val < 1){
            $(this).val(1);
        }
    });

    // === tambah ke grid
    $("#btnAdd").click(function(){
        let do_no   = $("#do_konsinyasi").val();
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let jumlah  = parseInt($("#jumlah").val());
        let harga   = parseInt($("#harga").val());
        let maxJumlah = parseInt($("#produk option:selected").data("sisa"));

        if(!do_no || !barcode || !jumlah || !harga){
            alert("DO, Produk, jumlah & harga wajib diisi!");
            return;
        }

        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[1]).filter("input").val();
            if(existingBarcode === barcode){
                rowFound = this;
            }
        });

        if(rowFound){
            let oldJumlah = parseInt($(rowFound.data()[3]).filter("input").val());
            let newJumlah = oldJumlah + jumlah;

            if(newJumlah > maxJumlah){
                alert("Jumlah melebihi sisa, dibatasi " + maxJumlah);
                newJumlah = maxJumlah;
            }

            let total = newJumlah * harga;
            rowFound.data([
                do_no,
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }else{
            let total = jumlah * harga;
            table.row.add([
                do_no,
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        $("#produk").val("").trigger("change");
        $("#jumlah").val("");
        $("#harga").val("");
        updateSubtotal();
        checkProdukAvailable();
    });

    // === hapus baris
    $("#table_data tbody").on("click", ".btnDelete", function(){
        let row = $(this).closest("tr");
        table.row(row).remove().draw(false);
        updateSubtotal();
    });

    // === hitung subtotal
    function updateSubtotal(){
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val());
        });
        $("#subtotal").text(formatNumber(subtotal));
    }

    // === submit form
    $("#form_nota").submit(function(e){
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/add-data-nota')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Nota Konsinyasi berhasil disimpan!");
                    window.location.href = "<?=base_url('admin/konsinyasi/nota')?>";
                }else{
                    alert(res.message);
                }
            },
            error: function(xhr){
                alert("Terjadi kesalahan server!\n" + xhr.responseText);
            }
        });
    });

    checkProdukAvailable();
});
</script>
