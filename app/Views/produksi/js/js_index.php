<style>
    tr { height: 50px; }
    #table_data tbody tr {
        cursor: pointer;
    }
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
                        console.log("Render tombol aksi untuk nonota produksi:", data, "Row data:", full);

                        let button = '';

                        if (full.role !== "Admin" && full.is_complete==0) {
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

        table.on('error.dt', function (e, settings, techNote, message) {
            console.error("DataTables error:", message);
        });

        // === Handle Hapus Modal (Produksi) ===
        $('#table_data').on("click", ".btnDeleteProduksi", function () {
            const nonota = $(this).data("nonota");
            const encoded = btoa(nonota); // base64 encode

            // set ke modal
            $("#nonotaProduksiToDelete").text(nonota);
            $("#nonotaProduksiHidden").val(encoded);

            // munculkan modal
            $("#modal_deleteProduksi").modal("show");
        });

         $('#table_data').on("click", ".btncomplete", function () {
            const nonota = $(this).data("nonota");
            $.ajax({
                url: "<?=base_url()?>/produksi/complete/"+nonota,  // ganti sesuai route kamu
                type: "GET",                          // biasanya POST untuk update
                data: { nonota: nonota },
                success: function (response) {
                    // misalnya refresh tabel setelah update
                    alert("Produksi " + nonota + " sudah complete!");
                    $('#table_data').DataTable().ajax.reload(); 
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert("Gagal update status!");
                }
            });
        });


        // Saat konfirmasi hapus ditekan
        $("#confirmDeleteProduksiBtn").on("click", function () {
            const encodedNota = $("#nonotaProduksiHidden").val();
            if (encodedNota) {
                window.location.href = "<?=base_url()?>produksi/hapus/" + encodedNota;
            }
        });

    });
</script>
