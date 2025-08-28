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

                        if (full.role !== "Admin") {
                            button += `
                                <button 
                                    type="button" 
                                    class="btn btn-simple btn-danger btn-icon btnDeleteProduksi" 
                                    title="Hapus" 
                                    data-nonota="${full.nonota}">
                                    <i class="material-icons">close</i>
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

        // Saat konfirmasi hapus ditekan
        $("#confirmDeleteProduksiBtn").on("click", function () {
            const encodedNota = $("#nonotaProduksiHidden").val();
            if (encodedNota) {
                window.location.href = "<?=base_url()?>produksi/hapus/" + encodedNota;
            }
        });

    });
</script>
