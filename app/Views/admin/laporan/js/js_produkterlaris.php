<style>
    tr { height: 45px; }
    #table_data tbody tr { cursor: pointer; }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
let table;
table = $('#table_data').DataTable({
    "order": [[1, "desc"]],
    "pageLength": 10,
    "dom": 'Bfrtip',
    "buttons": [
        {extend: 'excel'},
        {extend: 'pdf'},
        {extend: 'print'}
    ],
    "scrollX": true,
    "ajax": {
        "url": "<?= base_url('admin/laporan/listprodukterlaris') ?>",
        "type": "POST",
        "data": function(d) {
            d.bulan = $("#bulan").val();
            d.tahun = (d.bulan === "all-time") ? "-" : $("#tahun").val();
        },
        "dataSrc": function(data) {
            return data;
        }
    },
    "columns": [
        { "data": "namaproduk" },
        { "data": "total_qty", "className": "text-right" },
        { "data": "avg_jual", "className": "text-right", 
          "render": $.fn.dataTable.render.number('.', ',', 0, '') },
        { "data": "avg_modal", "className": "text-right",
          "render": $.fn.dataTable.render.number('.', ',', 0, '') },
        { "data": "avg_profit", "className": "text-right",
          "render": $.fn.dataTable.render.number('.', ',', 0, '') }
    ]
});

$("#lihat").on("click", function(){
    table.ajax.reload();
});
</script>
