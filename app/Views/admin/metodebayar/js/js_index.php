<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Metode Bayar...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/metodebayar/listdata",
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
				{ "data": "namaakun" },
				{ "data": "noakun" },
				{ "data": "namabank" },
				{ "data": "cabangbank" },
				{ "data": "kodeswift" },
				{ "data": "matauang" },
				{ "data": "negara" },
				{ "data": "noakun",
					"render": function (data, type, full, meta){
						console.log("Render tombol aksi untuk no. akun:", data, "Row data:", full);
						
						let button = '';
						button += '<a href="<?=base_url()?>admin/metodebayar/ubah/' + encodeURI(btoa(full.noakun)) + '" class="btn btn-simple btn-warning btn-icon" title="Ubah"><i class="material-icons">create</i></a>';
						
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
