<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Nota Konsinyasi...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/konsinyasi/notalistdata",
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
				{ "data": "notajual" },
				{ "data": "partner" },  
				{ "data": "tanggal" },
				{ "data": "total",
					"render": function(data){
						return new Intl.NumberFormat('id-ID').format(data); 
					}
				},
				{ 
					"data": "notajual",
					"render": function (data, type, full, meta){
						let button = '';
						// button += '<a href="<?=base_url()?>admin/konsinyasi/dohapus/' + encodeURI(btoa(data)) + '" class="btn btn-simple btn-danger btn-icon" title="Hapus"><i class="material-icons">close</i></a>';
						button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-notajual="' + data + '"><i class="material-icons">close</i></button>';
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
			const notajual = $(this).data("notajual");
			const encoded = btoa(notajual); // base64 encode

			// set ke modal
			$("#notajualDoToDelete").text(notajual);
			$("#notajualDoHidden").val(encoded);

			// munculkan modal
			$("#modal_deleteDo").modal("show");
		});

		// Saat konfirmasi hapus ditekan
		$("#confirmDeleteBtn").on("click", function () {
			const encodedNota = $("#notajualDoHidden").val();
			if (encodedNota) {
				window.location.href = "<?=base_url()?>admin/konsinyasi/notahapus/" + encodedNota;
			}
		});
	});

</script>
