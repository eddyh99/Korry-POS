<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Bahan Baku...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/bahanbaku/listdata",
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
				{ "data": "min" },
				{ 
					"data": "stok",
					"render": function (data, type, full, meta){
						if (parseInt(data) < parseInt(full.min)) {
							return '<span style="color:red;font-weight:bold;">' + data + '</span>';
						} else {
							return data;
						}
					}
				},
				{ 
					"data": "id",
					"render": function (data, type, full, meta){
						console.log("Render tombol aksi untuk id bahanbaku:", data, "Row data:", full);
						
						let button = '';
						button += '<a href="<?=base_url()?>admin/bahanbaku/ubah/' + encodeURI(btoa(full.id)) + '" class="btn btn-simple btn-warning btn-icon" title="Ubah"><i class="material-icons">update</i></a>';
						
						if (full.role !== "Admin") {
							button += '<a href="<?=base_url()?>admin/bahanbaku/hapus/' + encodeURI(btoa(full.id)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
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
