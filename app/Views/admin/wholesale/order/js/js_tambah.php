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
        $("#harga").val(harga);
    });

    // tambah produk ke tabel
    $("#btnAdd").on("click", function(){
        let barcode  = $("#produk").val();
        let nama     = $("#produk option:selected").text();
        let jumlah   = parseFloat($("#jumlah").val()) || 0;
        let harga    = parseFloat($("#harga").val()) || 0;
        let potonganRaw = $("#potongan").val().trim(); // bisa "10000" atau "10%"

        if(!barcode || jumlah <= 0 || harga <= 0){
            alert("Produk, jumlah, dan harga wajib diisi!");
            return;
        }

        let total = hitungTotal(jumlah, harga, potonganRaw);

        table.row.add([
            `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
            nama,
            `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
            `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
            `<input type="hidden" name="potongan[]" value="${potonganRaw}">${potonganRaw}`,
            `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
            `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
        ]).draw(false);

        // hapus produk yg sudah dipakai dari select
        $("#produk option[value='"+barcode+"']").remove();
        $("#produk").val("").trigger("change");
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
});
</script>
