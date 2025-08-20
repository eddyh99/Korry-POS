<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Produksi...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>produksi/listdata",
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
				{ "data": "nonota" },
				{ "data": "tanggal" },
				{ "data": "idvendor" },
				{ "data": "estimasi" },
				{ "data": "dp" },
				{ "data": "total" },
				{ 
					"data": "nonota",
					"render": function (data, type, full, meta){
						console.log("Render tombol aksi untuk nonota produksi:", data, "Row data:", full);
						
						let button = '';

						if (full.role !== "Admin") {
							button += '<a href="<?=base_url()?>produksi/hapus/' + encodeURI(btoa(full.nonota)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
						}
						return button;
					}
				}
			]
		});

		table.on('error.dt', function(e, settings, techNote, message) {
			console.error("DataTables error:", message);
		});
	});
</script>