<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
    var table;
    $(function(){
        console.log("Inisialisasi DataTable untuk Retur Konsinyasi...");

        table = $('#table_data').DataTable({
            "order": [[ 0, "asc" ]],
            "scrollX": true,
            "ajax": {
                "url": "<?=base_url()?>admin/konsinyasi/returlistdata",
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
                { "data": "noretur" },
                { "data": "nokonsinyasi" },  
                { "data": "tanggal" },
                { "data": "noretur",
                    "render": function (data, type, full, meta){
                        let button = '';
                        button += '<button type="button" class="btn btn-simple btn-info btn-icon btnDetail" title="Detail Retur" data-noretur="' + data + '"><i class="material-icons">info_outline</i></button>';
                        button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-noretur="' + data + '"><i class="material-icons">close</i></button>';
                        return button;
                    }
                }
            ]
        });

        table.on('error.dt', function(e, settings, techNote, message) {
            console.error("DataTables error:", message);
        });

        // === Handle Detail Retur ===
        $('#table_data').on("click", ".btnDetail", function () {
            const noretur = $(this).data("noretur");

            $.ajax({
                url: "<?=base_url()?>admin/konsinyasi/returdetail/" + noretur,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    console.log("Detail Retur diterima:", data);

                    // isi header
                    $("#detail_noretur").text(data.header.noretur);
                    $("#detail_tanggal").text(data.header.tanggal);
                    $("#detail_nokonsinyasi").text(data.header.nokonsinyasi);
                    $("#detail_user").text(data.header.nama_user);

                    // isi tabel
                    let rows = "";
                    data.detail.forEach(function (row) {
                        rows += `
                        <tr>
                            <td>${row.barcode}</td>
                            <td>${row.namaproduk}</td>
                            <td>${row.sku}</td>
                            <td>${row.size}</td>
                            <td>${row.warna}</td>
                            <td class="text-center">${row.jumlah}</td>
                            <td>${row.alasan}</td>
                        </tr>
                        `;
                    });
                    $("#detail_tableBody").html(rows);

                    // munculkan modal
                    $("#modal_detailRetur").modal("show");
                },
                error: function (xhr, status, error) {
                    console.error("Gagal ambil detail retur:", error, xhr.responseText);
                    alert("Gagal ambil detail retur!");
                }
            });
        });

        // === Handle Hapus Modal (Bootstrap 4) ===
        $('#table_data').on("click", ".btnDelete", function () {
            const noretur = $(this).data("noretur");
            const encoded = btoa(noretur);

            $("#noreturToDelete").text(noretur);
            $("#noreturHidden").val(encoded);

            $("#modal_deleteRetur").modal("show");
        });

        $("#confirmDeleteBtn").on("click", function () {
            const encodedNota = $("#noreturHidden").val();
            if (encodedNota) {
                window.location.href = "<?=base_url()?>admin/konsinyasi/returhapus/" + encodedNota;
            }
        });
    });
</script>

