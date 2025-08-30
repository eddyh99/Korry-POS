<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function(){
    $(".select2").select2();

    let table = $("#table_data").DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        columnDefs: [
            { targets: [2,3,4,5], className: "text-center" }
        ]
    });

    // === ketika DO dipilih, load produk
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
                        `<option value="${item.barcode}" 
                                 data-sisa="${item.sisa}" 
                                 data-size="${item.size}">
                            ${item.nama} [Size: ${item.size}] (Max: ${item.sisa})
                        </option>`
                    );
                });
            },
            error: function(xhr){
                alert("Gagal load produk!\n" + xhr.responseText);
            }
        });
    });

    // === isi jumlah otomatis saat produk dipilih
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let maxJumlah = selected.data("sisa");

        if (!maxJumlah) {
            $("#jumlah").val("").removeAttr("max");
            return;
        }

        $("#jumlah").val(1).attr("max", maxJumlah);
    });

    // === validasi jumlah input manual
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

    // === tombol Tambah ke tabel
    $("#btnAdd").click(function(){
        let do_no   = $("#do_konsinyasi").val();
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let size    = $("#produk option:selected").data("size");
        let jumlah  = parseInt($("#jumlah").val());
        let alasan  = $("#alasan").val();
        let maxJumlah = parseInt($("#produk option:selected").data("sisa"));

        if(!do_no || !barcode || !jumlah || !alasan || !size){
            alert("DO, Produk, size, jumlah & alasan wajib diisi!");
            return;
        }

        // cek stok ke server dulu
        let stok = 0;
        $.ajax({
            url: "<?=base_url()?>admin/konsinyasi/cekstokreturkonsinyasi",
            async: false,
            type: "POST",
            data: { 
                barcode: barcode, 
                tujuan: $("#tujuan").val(), 
                size: size 
            },
            success: function (data) {
                stok = parseInt(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

        if (stok <= 0) {
            alert("Stok sudah habis");
            return;
        }

        // cek duplikat berdasarkan barcode+size
        let rowFound = null;
        table.rows().every(function(){
            let row = this.data();
            let existingBarcode = $(row[0]).filter("input[name='barcode[]']").val();
            let existingSize    = $(row[1]).filter("input[name='size[]']").val();
            if(existingBarcode === barcode && existingSize === size){
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

            rowFound.data([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                nama,
                `<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`,
                `<input type="hidden" name="alasan[]" value="${alasan}">${alasan}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }else{
            table.row.add([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                nama,
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

        let details = [];
        table.rows().every(function(){
            let row = this.data();
            let barcode = $(row[0]).filter("input[name='barcode[]']").val();
            let size    = $(row[1]).filter("input[name='size[]']").val();
            let jumlah  = $(row[3]).filter("input").val();
            let alasan  = $(row[4]).filter("input").val();

            if(barcode && size && jumlah){
                details.push({ barcode: barcode, size: size, jumlah: jumlah, alasan: alasan });
            }
        });

        if(details.length === 0){
            alert("Detail retur belum diisi!");
            return;
        }

        let payload = {
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
