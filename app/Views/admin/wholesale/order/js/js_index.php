<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
var table;
$(function(){
    console.log("Inisialisasi DataTable untuk Order Wholesale...");

    table = $('#table_data').DataTable({
        "order": [[ 0, "asc" ]],
        "scrollX": true,
        "ajax": {
            "url": "<?=base_url()?>admin/wholesale/orderlistdata",
            "type": "POST",
            "dataSrc": function (data){
                console.log("Data diterima dari server:", data);
                return data;                            
            },
            "error": function (xhr, error, code) {
                console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
            }
        },
        "columns": [
            { "data": "notaorder" },
            { "data": "nama_partner" },
            { "data": "tanggal" },
            { "data": "lama" },
            { "data": "subtotal", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); } },
            { "data": "dp", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); }},
            { "data": "notaorder",
                "render": function (data, type, full, meta){
                    let button = '';
                    button += '<button type="button" class="btn btn-simple btn-info btn-icon btnDetail" title="Detail Nota" data-notaorder="' + data + '"><i class="material-icons">info_outline</i></button>';
                    if (full.is_complete!=="1"){
                        button += '<button type="button" class="btn btn-simple btn-info btn-icon btnPrint" title="Print Ulang" data-notaorder="' + data + '"><i class="material-icons">print</i></button>';
                        button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-notaorder="' + data + '"><i class="material-icons">close</i></button>';
                    }
                    return button;
                }
            }
        ]
    });

    // === Handle Detail Nota ===
    $('#table_data').on("click", ".btnDetail", function () {
        const notaorder = $(this).data("notaorder");
        if (!notaorder) return;

        $.ajax({
            url: "<?=base_url()?>admin/wholesale/notaorderdetail/" + notaorder,
            type: "GET",
            success: function (res) {
                console.log("Detail Nota diterima:", res);

                // Header
                $("#detail_nonota").text(res.header.notaorder);
                $("#detail_tanggal").text(res.header.tanggal);
                $("#detail_lama").text(res.header.lama);
                $("#detail_user").text(res.header.nama_user);
                $("#detail_partner").text(res.header.nama_wholesaler);
                $("#detail_alamat").text(res.header.alamat_wholesaler);
                $("#detail_kontak").text(res.header.kontak_wholesaler);

                // Detail Barang
                let rows = "";
                let totalJumlah = 0;
                let totalHarga = 0;

                res.detail.forEach(function (item) {
                    const subtotal = (item.jumlah * item.harga) - item.potongan;
                    totalJumlah += parseInt(item.jumlah);
                    totalHarga += subtotal;

                    rows += `
                        <tr>
                          <td>${item.barcode}</td>
                          <td>${item.namaproduk}</td>
                          <td>${item.sku}</td>
                          <td>${item.brand}</td>
                          <td>${item.kategori}</td>
                          <td>${item.fabric}</td>
                          <td>${item.size ?? "-"}</td>
                          <td>${item.warna}</td>
                          <td class="text-center">${item.jumlah}</td>
                          <td class="text-right">${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                          <td class="text-right">${new Intl.NumberFormat('id-ID').format(item.potongan)}</td>
                          <td class="text-right">${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                        </tr>`;
                });

                $("#detail_tableBody").html(rows);
                $("#detail_totalJumlah").text(totalJumlah);
                $("#detail_totalHarga").text(new Intl.NumberFormat('id-ID').format(totalHarga));

                // Informasi Pembayaran
                $("#detail_diskon").text(new Intl.NumberFormat('id-ID').format(res.header.diskon));
                $("#detail_ppn").text(new Intl.NumberFormat('id-ID').format(res.header.ppn));
                $("#detail_dp").text(new Intl.NumberFormat('id-ID').format(res.header.dp));

                // Show modal
                $("#modal_detailOrder").modal("show");
            },
            error: function (xhr, status, error) {
                console.error("Gagal load detail:", error);
                alert("Gagal load detail nota order!");
            }
        });
    });

    // === Handle Print Ulang ===
    $('#table_data').on("click", ".btnPrint", function () {
        const notaorder = $(this).data("notaorder");
        if (notaorder) {
            window.open("<?=base_url('admin/wholesale/cetaknotaorder')?>/" + notaorder, "_blank");
        }
    });

    // === Handle Delete ===
    $('#table_data').on("click", ".btnDelete", function () {
        const notaorder = $(this).data("notaorder");
        const encoded = btoa(notaorder);

        $("#notaOrderToDelete").text(notaorder);
        $("#notaOrderHidden").val(encoded);
        $("#modal_deleteOrder").modal("show");
    });

    $("#confirmDeleteOrderBtn").on("click", function () {
        const encodedNota = $("#notaOrderHidden").val();
        if (encodedNota) {
            window.location.href = "<?=base_url()?>admin/wholesale/orderhapus/" + encodedNota;
        }
    });
});
</script>

