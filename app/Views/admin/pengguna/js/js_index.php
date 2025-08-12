<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		table = $('#table_data').DataTable({
			"order": [[ 1, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/pengguna/listdata",
				"type": "POST",
				"dataSrc": function (data) {
					console.log("Raw Ajax response:", data); // Logging response mentah
					if (!Array.isArray(data)) {
						console.error("Expected an array, got:", data);
						return [];
					}
					return data;                            
				},
				"error": function (xhr, error, code) {
					console.error("AJAX Error:", error, code, xhr.responseText);
					alert("Terjadi kesalahan saat memuat data. Cek console untuk detail.");
				}
			},
			"aoColumnDefs": [{  
				"aTargets": [3],
				"mData": "username",
				"mRender": function (data, type, full, meta) {
					var button = '';
					if (full.role != "Admin") {
						button += '<a href="<?=base_url()?>admin/pengguna/ubah/' + encodeURI(btoa(full.username)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
						button += '<a href="<?=base_url()?>admin/pengguna/hapus/' + encodeURI(btoa(full.username)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
					} else {
						button += '<a href="<?=base_url()?>admin/pengguna/ubah/' + encodeURI(btoa(full.username)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
					}
					return button;
				}
			}],
			"columns": [
				{ "data": "username" },
				{ "data": "nama" },
				{ "data": "role" },
				{ "data": null, "defaultContent": "" } // kolom aksi
			]
		});
	});
</script>