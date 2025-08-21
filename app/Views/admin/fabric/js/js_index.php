<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Fabric...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/fabric/listdata",
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
				{ "data": "nama" },
				{ 
					"data": "id",
					"render": function (data, type, full, meta){
						console.log("Render tombol aksi untuk id fabric:", data, "Row data:", full);
						
						let button = '';
						button += '<a href="<?=base_url()?>admin/fabric/ubah/' + encodeURI(btoa(full.id)) + '" class="btn btn-simple btn-warning btn-icon" title="Ubah"><i class="material-icons">update</i></a>';
						
						if (full.role !== "Admin") {
							button += '<a href="<?=base_url()?>admin/fabric/hapus/' + encodeURI(btoa(full.id)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
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
