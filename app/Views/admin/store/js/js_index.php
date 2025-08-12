<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Store...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/store/listdata",
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
				{ "data": "store" },
				{ "data": "alamat" },
				{ "data": "kontak" },
				{ "data": "keterangan" },
				{ 
					"data": "storeid",
					"render": function (data, type, full, meta){
						console.log("Render tombol aksi untuk storeid:", data, "Row data:", full);
						
						let button = '';
						button += '<a href="<?=base_url()?>admin/store/ubah/' + encodeURI(btoa(full.storeid)) + '" class="btn btn-simple btn-warning btn-icon" title="Ubah"><i class="material-icons">update</i></a>';
						
						if (full.role !== "Admin") {
							button += '<a href="<?=base_url()?>admin/store/hapus/' + encodeURI(btoa(full.storeid)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
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
