<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
$(function(){
    table = $('#table_data').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 50,
        scrollX: true,

        ajax: {
            url: "<?= base_url('admin/stok/listdata') ?>",
            type: "POST",
            dataSrc: function (json) {
                console.log("📦 [DEBUG] DataTables AJAX Response:", json); // Debug respon server
                if (!json || typeof json !== 'object') {
                    console.error("❌ Response bukan object:", json);
                    return [];
                }
                if (!Array.isArray(json.produk)) {
                    console.error("❌ json.produk bukan array:", json.produk);
                    return [];
                }
                return json.produk; // Karena controller return 'produk'
            },
            error: function (xhr, error, thrown) {
                console.error("❌ [AJAX ERROR]", {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
            }
        },

        columns: [
            { data: "barcode" },
            { data: "namaproduk" },
            { data: "namabrand" },
            { data: "size" },
            { data: "stok", render: $.fn.dataTable.render.number('.', ',', 0, '') },
            { data: "store" }
        ]
    });
});
</script>
