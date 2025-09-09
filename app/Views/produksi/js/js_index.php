<style>
    tr { height: 50px; }
    #table_data tbody tr { cursor: pointer; }
</style>

<script>
var table;
$(function () {
    console.log("Inisialisasi DataTable untuk Produksi...");

    table = $('#table_data').DataTable({
        "order": [[0, "asc"]],
        "scrollX": true,
        "ajax": {
            "url": "<?=base_url()?>produksi/listdata",
            "type": "POST",
            "dataSrc": function (data) {
                console.log("Data diterima dari server:", data);
                return data;
            },
            "error": function (xhr, error, code) {
                console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
            }
        },
        "columns": [
            { "data": "nonota" },
            { "data": "tanggal" },
            { "data": "vendor_nama" },
            { "data": "estimasi" },
            { "data": "dp" },
            { "data": "total" },
            { "data": "nonota",
                "render": function (data, type, full, meta) {
                    let button = '';
                    button += '<button type="button" class="btn btn-simple btn-info btn-icon btnDetail" title="Detail Produksi" data-nonota="' + data + '"><i class="material-icons">info_outline</i></button>';
                    
                    if (full.role !== "Admin" && full.is_complete == 0) {
                        button += `
                            <button 
                                type="button" 
                                class="btn btn-simple btn-danger btn-icon btnDeleteProduksi" 
                                title="Hapus" 
                                data-nonota="${full.nonota}">
                                <i class="material-icons">close</i>
                            </button>
                            <button 
                                type="button" 
                                class="btn btn-simple btn-success btn-icon btncomplete" 
                                title="Complete"
                                data-nonota="${full.nonota}">
                                <i class="material-icons">check</i>
                            </button>
                        `;
                    }
                    return button;
                }
            }
        ]
    });

    // === Handle Detail Produksi ===
    $('#table_data').on("click", ".btnDetail", function () {
        const nonota = $(this).data("nonota");

        $.ajax({
            url: "<?=base_url()?>produksi/produksidetail/" + nonota,
            type: "GET",
            dataType: "json",
            success: function (data) {
                console.log("Detail Produksi diterima:", data);

                // isi header
                $("#detail_nonota").text(data.header.nonota);
                $("#detail_tanggal").text(data.header.tanggal);
                $("#detail_estimasi").text(data.header.estimasi);
                $("#detail_vendor").text(data.header.vendor_nama);
                $("#detail_tipevendor").text(data.header.vendor_tipe);
                $("#detail_user").text(data.header.user_id);

                // isi tabel detail
                let rows = "";
                data.detail.forEach(function (row) {
                    rows += `
                    <tr>
                        <td>${row.barcode}</td>
                        <td>${row.namaproduk}</td>
                        <td>${row.sku}</td>
                        <td>${row.size}</td>
                        <td class="text-center">${row.jumlah}</td>
                        <td class="text-right">${row.harga}</td>
                        <td class="text-right">${row.biaya}</td>
                    </tr>
                    `;
                });
                $("#detailProduksi_tableBody").html(rows);

                // tampilkan modal
                $("#modal_detailProduksi").modal("show");
            },
            error: function (xhr, status, error) {
                console.error("Gagal ambil detail produksi:", error, xhr.responseText);
                alert("Gagal ambil detail produksi!");
            }
        });
    });

    // === Handle Hapus Produksi ===
    $('#table_data').on("click", ".btnDeleteProduksi", function () {
        const nonota = $(this).data("nonota");
        const encoded = btoa(nonota);

        $("#nonotaProduksiToDelete").text(nonota);
        $("#nonotaProduksiHidden").val(encoded);

        $("#modal_deleteProduksi").modal("show");
    });

    // === Handle Complete Produksi ===
    $('#table_data').on("click", ".btncomplete", function () {
        const nonota = $(this).data("nonota");
        $.ajax({
            url: "<?=base_url()?>produksi/complete/" + nonota,
            type: "GET",
            success: function (response) {
                alert("Produksi " + nonota + " sudah complete!");
                $('#table_data').DataTable().ajax.reload(); 
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert("Gagal update status!");
            }
        });
    });

    // === Handle Konfirmasi Hapus ===
    $("#confirmDeleteProduksiBtn").on("click", function () {
        const encodedNota = $("#nonotaProduksiHidden").val();
        if (encodedNota) {
            window.location.href = "<?=base_url()?>produksi/hapus/" + encodedNota;
        }
    });
});
</script>
