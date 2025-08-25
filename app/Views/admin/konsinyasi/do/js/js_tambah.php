<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function(){
    $(".select2").select2();

    let table = $("#table_data tbody");

    // isi harga otomatis saat pilih produk
    $("#produk").change(function(){
        let harga = $(this).find(':selected').data('harga') || 0;
        $("#harga").val(harga);
    });

    function checkBtnVisibility() {
        if ($("#produk option").length <= 1) {
            // hanya tersisa --Pilih Produk--
            $("#btnAdd").hide();
        } else {
            $("#btnAdd").show();
        }
    }

    // Tambah ke grid sementara
    $("#btnAdd").click(function(){
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let jumlah  = $("#jumlah").val();
        let harga   = $("#harga").val();

        if(!barcode || !jumlah){
            alert("Produk & jumlah wajib diisi!");
            return;
        }

        // Tambah baris ke tabel

		// Button Hapus spt index: <td><button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus"><i class="material-icons">close</i></button></td>

        let row = `
            <tr data-barcode="${barcode}">
                <td><input type="hidden" name="barcode[]" value="${barcode}">${barcode}</td>
                <td>${nama}</td>
                <td><input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}</td>
                <td>${harga}</td>
                <td><button type="button" class="btn btn-danger btn-sm btnDelete">x</button></td>
            </tr>`;
        table.append(row);

        // Hapus produk dari pilihan select
        $("#produk option[value='"+barcode+"']").remove();

        // reset input
        $("#produk").val("").trigger("change");
        $("#jumlah").val(1);
        $("#harga").val("");

        checkBtnVisibility();
    });

    // Hapus baris
    $(document).on("click", ".btnDelete", function(){
        let tr = $(this).closest("tr");
        let barcode = tr.data("barcode");
        let nama    = tr.find("td:eq(1)").text();
        let harga   = tr.find("td:eq(3)").text();

        // kembalikan ke dropdown
        $("#produk").append(
            `<option value="${barcode}" data-harga="${harga}">${nama}</option>`
        );

        tr.remove();

        checkBtnVisibility();
    });

    // cek saat load awal
    checkBtnVisibility();

    // Submit form
    $("#form_do").submit(function(e){
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/add-data-do')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("DO Konsinyasi berhasil disimpan!");
                    window.location.href = "<?=base_url('admin/konsinyasi/do')?>";
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

