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
let table = $('#table_data').DataTable({
    "order": [[0, "asc"]], // urutkan akun
    "pageLength": 10,
    "dom": 'Bfrtip',
    "buttons": [
        { extend: 'excel', text: 'Export Excel' },
        { extend: 'pdf', text: 'Export PDF' },
        { extend: 'print', text: 'Print' }
    ],
    "scrollX": true,
    "ajax": {
        "url": "<?= base_url('admin/laporan/listneraca') ?>",
        "type": "POST",
        "data": function(d) {
            d.tahun = $("#tahun").val();
        },
        "dataSrc": function(data) {
            return data;
        }
    },
    "columns": [
        { "data": "akun" },
        { "data": "saldo", 
            "className": "text-right",
            "render": $.fn.dataTable.render.number('.', ',', 0, 'Rp ')
        }
    ]
});

// tombol lihat untuk reload berdasarkan tahun
$("#lihat").on("click", function(){
    table.ajax.reload();
});
</script>

