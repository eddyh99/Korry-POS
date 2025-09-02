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
            $("#btnAdd").hide();
        } else {
            $("#btnAdd").show();
        }
    }

    // === fungsi format angka dengan ribuan (Indonesia pakai ".")
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // fungsi update subtotal
    function updateSubtotal() {
        let subtotal = 0;
        $("input[name='total[]']").each(function(){
            subtotal += parseFloat($(this).val()) || 0;
        });
        $("#subtotal").text(formatNumber(subtotal));
    }

    // Tambah ke grid sementara
    $("#btnAdd").click(function(){
        let barcode  = $("#produk").val();
        let nama     = $("#produk option:selected").text();
        let jumlah   = parseFloat($("#jumlah").val());
        let harga    = parseFloat($("#harga").val());
        let potongan = parseFloat($("#potongan").val());

        if(!barcode || !jumlah){
            alert("Produk & jumlah wajib diisi!");
            return;
        }

        // hitung total per baris
        let total = jumlah * harga * (1 - (potongan/100));

        let row = `
            <tr data-barcode="${barcode}">
                <td><input type="hidden" name="barcode[]" value="${barcode}">${barcode}</td>
                <td>${nama}</td>
                <td><input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}</td>
                <td><input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}</td>
                <td><input type="hidden" name="potongan[]" value="${potongan}">${potongan}%</td>
                <td>
                    <input type="hidden" name="total[]" value="${total}">
                    ${formatNumber(total)}
                </td>
                <td><button type="button" class="btn btn-danger btn-sm btnDelete">x</button></td>
            </tr>`;
        table.append(row);

        // update subtotal
        updateSubtotal();

        // hapus produk dari pilihan select
        $("#produk option[value='"+barcode+"']").remove();

        // reset input
        $("#produk").val("").trigger("change");
        $("#jumlah").val(1);
        $("#harga").val("");
        $("#potongan").val(0);

        checkBtnVisibility();
    });

    // Hapus baris
    $(document).on("click", ".btnDelete", function(){
        let tr      = $(this).closest("tr");
        let barcode = tr.data("barcode");
        let nama    = tr.find("td:eq(1)").text();
        let harga   = tr.find("input[name='harga[]']").val();

        // kembalikan ke dropdown (harga asli tetap ikut)
        $("#produk").append(
            `<option value="${barcode}" data-harga="${harga}">${nama}</option>`
        );

        tr.remove();
        updateSubtotal();
        checkBtnVisibility();
    });

    // cek saat load awal
    checkBtnVisibility();

    // Submit form
    $("#form_order").submit(function(e){
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('admin/wholesale/add-data-order')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Order wholesale berhasil disimpan!");
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

});
</script>
