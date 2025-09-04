<style>
    tr { height: 50px; }
    #table_data tbody tr {
        cursor: pointer;
    }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
let table;
table = $('#table_data').DataTable({
    "order": [[0, "asc"]],
    "pageLength": 50,
    "dom": 'Bfrtip',
    // "buttons": ['excel', 'pdf', 'print'],
	"buttons": [
        {
            extend: 'excel',
            footer: true
        },
        {
            extend: 'pdf',
            footer: true
        },
        {
            extend: 'print',
            footer: true
        }
    ],
    "scrollX": true,
    "ajax": {
        "url": "<?= base_url() ?>admin/laporan/listpospengeluaran",
        "type": "POST",
        "data": function(d) {
            d.bulan       = $("#bulan").val();
            d.tahun       = $("#tahun").val();
            d.storeid     = $("#store").val();
            d.pengeluaran = $("#pengeluaran").val();
        },
        "dataSrc": function(data) {
            return data;
        }
    },
    "drawCallback": function () {
        var api = this.api();
        var total = api.column(2, {filter:'applied'} ).data().sum();

        // $( api.column(2).footer() ).html(
        //     total.toLocaleString("id-ID")
        // );

		// isi footer Subtotal (col index 1) dan Total (col index 2)
		$( api.column(1).footer() ).html('<b>Subtotal</b>');
		$( api.column(2).footer() ).html('<b>'+ total.toLocaleString("en") +'</b>');
    },
    "columns": [
        { "data": "store" },
        { "data": "namapengeluaran" },
        { "data": "total", 
            "className": "text-right", 
            "render": $.fn.dataTable.render.number('.', ',', 0, '') 
        }
    ]
});

$("#lihat").on("click", function(){
    table.ajax.reload();
});
</script>
