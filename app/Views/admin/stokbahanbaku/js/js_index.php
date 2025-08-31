<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	console.log("Inisialisasi DataTable untuk Stok Bahan Baku...");

	table = $('#table_data').DataTable({
		"order": [[ 0, "asc" ]],
		"scrollX": true,
		"ajax": {
			"url": "<?=base_url()?>admin/stokbahanbaku/listdata",
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
			{ "data": "namabahan" },
			{ "data": "stok_akhir", render: $.fn.dataTable.render.number(".", ",", 2, "", "")},
			{ "data": "satuan" },
			{ "data": "harga_rata2", render: $.fn.dataTable.render.number(".", ",", 2, "", "") },
			{ "data": "harga_terakhir", render: $.fn.dataTable.render.number(".", ",", 2, "", "") }
		],
		"createdRow": function (row, data, dataIndex) {
			if (parseFloat(data.stok_akhir) < parseFloat(data.min)) {
				$(row).css("background-color", "#f8d7da");  // merah muda (seperti alert bootstrap)
				// atau pakai class biar lebih rapih:
				// $(row).addClass("table-danger");
			}
		}		
	});

	table.on('error.dt', function(e, settings, techNote, message) {
		console.error("DataTables error:", message);
	});
</script>
