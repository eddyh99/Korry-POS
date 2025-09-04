<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
<script>   
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
		],
		"createdRow": function (row, data, dataIndex) {
			if (parseFloat(data.stok_akhir) < parseFloat(data.min)) {
				$(row).css("background-color", "#f8d7da");  // merah muda (seperti alert bootstrap)
				// atau pakai class biar lebih rapih:
				// $(row).addClass("table-danger");
			}
		}		
	});
    
     table = $('#table_produksi').DataTable({
		"order": [[ 0, "asc" ]],
		"scrollX": true,
		"ajax": {
			"url": "<?=base_url()?>produksi/listdatadeadline",
			"type": "GET",
			"dataSrc": function (data){
				console.log("Data diterima dari server:", data);
				return data;                            
			},
			"error": function (xhr, error, code) {
				console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
			}
		},
		"columns": [
			{ "data": "nonota" },
			{ "data": "vendor_nama" },
			{ "data": "tanggal"},
			{ "data": "deadline" },
		],
	});

	table = $('#table_topten').DataTable({
		"order": [[ 0, "asc" ]],
		"scrollX": true,
		"ajax": {
			"url": "<?=base_url()?>dashboard/alltimetopten",
			"type": "GET",
			"dataSrc": function (data){
				console.log("Data diterima dari server:", data);
				return data;                            
			},
			"error": function (xhr, error, code) {
				console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
			}
		},
		"columns": [
			{ "data": "namaproduk" },
			{ "data": "total_qty" },
			{ "data": "avg_jual", render: $.fn.dataTable.render.number(".", ",", 2, "", "")},
			{ "data": "avg_modal", render: $.fn.dataTable.render.number(".", ",", 2, "", "") },
			{ "data": "avg_profit", render: $.fn.dataTable.render.number(".", ",", 2, "", "") },
		],
	});
</script>