<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Retur Konsinyasi...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/konsinyasi/returlistdata",
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
				{ "data": "noretur" },
				{ "data": "nokonsinyasi" },  
				{ "data": "tanggal" },
				{ "data": "noretur",
					"render": function (data, type, full, meta){
						let button = '';
						// button += '<a href="<?=base_url()?>admin/konsinyasi/dohapus/' + encodeURI(btoa(data)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
						button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-noretur="' + data + '"><i class="material-icons">close</i></button>';
						return button;
					}
				}
			]
		});

		table.on('error.dt', function(e, settings, techNote, message) {
			console.error("DataTables error:", message);
		});

		// === Handle Hapus Modal (Bootstrap 4) ===
		$('#table_data').on("click", ".btnDelete", function () {
			const noretur = $(this).data("noretur"); // ambil noretur dari tombol
			const encoded = btoa(noretur); // base64 encode biar aman di URL

			// set ke modal
			$("#noreturToDelete").text(noretur);
			$("#noreturHidden").val(encoded);

			// munculkan modal
			$("#modal_deleteRetur").modal("show");
		});

		// Saat konfirmasi hapus ditekan
		$("#confirmDeleteBtn").on("click", function () {
			const encodedNota = $("#noreturHidden").val();
			if (encodedNota) {
				window.location.href = "<?=base_url()?>admin/konsinyasi/returhapus/" + encodedNota;
			}
		});
	});
</script>
