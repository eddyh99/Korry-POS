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

    // === ketika DO dipilih / kosong
    $("#do_konsinyasi").change(function(){
        let do_id = $(this).val();
        $("#produk").html('<option value="" disabled selected>--Pilih Produk--</option>'); 
        $("#jumlah").val("");
        $("#harga").val("");

        if(!do_id){
            // === fallback tanpa DO → load semua produk master
            $.ajax({
                url: "<?=base_url('admin/konsinyasi/listproduktanpado')?>",
                type: "GET",
                dataType: "json",
                success: function(res){
                    res.forEach(function(item){
                        $("#produk").append(
                            `<option value="${item.barcode}" data-harga="${item.harga}">
                                ${item.nama}
                            </option>`
                        );
                    });
                },
                error: function(xhr){
                    alert("Gagal load produk!\n" + xhr.responseText);
                }
            });
            return;
        }

        // === kalau ada DO → load produk by DO
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

    // === isi jumlah & harga otomatis
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let harga = selected.data("harga") || "";
        let maxJumlah = selected.data("sisa");

        $("#harga").val(harga);

        if(maxJumlah){
            $("#jumlah").val(1).attr("max", maxJumlah);
        } else {
            $("#jumlah").val(1).removeAttr("max");
        }
    });

    // === validasi jumlah
    $("#jumlah").on("input", function(){
        let max = parseInt($(this).attr("max")) || 0;
        let val = parseInt($(this).val()) || 0;
        let namaProduk = $("#produk option:selected").text();

        if(max > 0 && val > max){
            alert("Maksimal " + namaProduk + " hanya " + max);
            $(this).val(max);
        } else if(val < 1){
            $(this).val(1);
        }
    });

    // === tambah ke grid
    $("#btnAdd").click(function(){
        let do_no   = $("#do_konsinyasi").val() || "-"; 
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let jumlah  = parseInt($("#jumlah").val());
        let harga   = parseInt($("#harga").val());
        let maxJumlah = parseInt($("#produk option:selected").data("sisa")) || null;

        if(!barcode || !jumlah || !harga){
            alert("Produk, jumlah & harga wajib diisi!");
            return;
        }

        // validasi batas DO
        if(maxJumlah && jumlah > maxJumlah){
            alert("Jumlah melebihi sisa, dibatasi " + maxJumlah);
            jumlah = maxJumlah;
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
            if(maxJumlah && newJumlah > maxJumlah){
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

    // === trigger change sekali pas halaman pertama kali dibuka
    $("#do_konsinyasi").trigger("change");

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
                    window.open("<?=base_url('admin/konsinyasi/cetaknotajualnota')?>/" + res.notajual, "_blank");
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
});
</script>
