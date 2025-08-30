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
            { targets: [3,4], className: "text-center" }
        ]
    });

    // === ketika DO dipilih, ambil produk by DO
    $("#do_konsinyasi").change(function(){
        let do_id = $(this).val();
        $("#produk").html('<option value="" disabled selected>--Pilih Produk--</option>'); 
        $("#jumlah").val("");
        $("#alasan").val("");

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
                        `<option value="${item.barcode}" data-sisa="${item.sisa}">
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

    // === isi jumlah otomatis ketika pilih produk
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let maxJumlah = selected.data("sisa");

        if (!maxJumlah) {
            $("#jumlah").val("").removeAttr("max");
            return;
        }

        $("#jumlah").val(1).attr("max", maxJumlah);
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
        let alasan  = $("#alasan").val();
        let maxJumlah = parseInt($("#produk option:selected").data("sisa"));

        if(!do_no || !barcode || !jumlah || !alasan){
            alert("DO, Produk, jumlah & alasan wajib diisi!");
            return;
        }

        // Cek duplikat barcode
        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[1]).filter("input").val();
            if(existingBarcode === barcode){
                rowFound = this;
            }
        });

        if(rowFound){
            let oldJumlah = parseInt($(rowFound.data()[2]).filter("input").val());
            let newJumlah = oldJumlah + jumlah;

            if(newJumlah > maxJumlah){
                alert("Jumlah melebihi sisa, dibatasi " + maxJumlah);
                newJumlah = maxJumlah;
            }

            rowFound.data([
                do_no,
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                `<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`,
                `<input type="hidden" name="alasan[]" value="${alasan}">${alasan}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }else{
            table.row.add([
                do_no,
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="alasan[]" value="${alasan}">${alasan}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        $("#produk").val("").trigger("change");
        $("#jumlah").val("");
        $("#alasan").val("");
    });

    // === hapus baris
    $("#table_data tbody").on("click", ".btnDelete", function(){
        let row = $(this).closest("tr");
        table.row(row).remove().draw(false);
    });

    // === submit form
    $("#form_retur").submit(function(e){
        e.preventDefault();

        // Ambil data detail grid
        let details = [];
        table.rows().every(function(){
            let row = this.data();
            let barcode = $(row[1]).filter("input").val();
            let jumlah = $(row[2]).filter("input").val();
            let alasan = $(row[3]).filter("input").val();

            if(barcode && jumlah){
                details.push({ barcode: barcode, jumlah: jumlah, alasan: alasan });
            }
        });

        if(details.length === 0){
            alert("Detail retur belum diisi!");
            return;
        }

        let payload = {
            noretur: $("#noretur").val(),
            do_konsinyasi: $("#do_konsinyasi").val(),
            details: details
        };

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/add-data-retur')?>",
            type: "POST",
            data: payload,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Retur Konsinyasi berhasil disimpan!");
                    window.location.href = "<?=base_url('admin/konsinyasi/retur')?>";
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
