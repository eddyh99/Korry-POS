<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Order Wholsale...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/wholesale/orderlistdata",
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
				{ "data": "notaorder" },
				{ "data": "id_wholesaler" },   // sudah join, bukan id lagi
				{ "data": "tanggal" },
				{ "data": "lama" },
				{ "data": "diskon" },		
				{ "data": "ppn" },						
				{ "data": "notaorder",
					"render": function (data, type, full, meta){
						let button = '';
						button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-notaorder="' + data + '"><i class="material-icons">close</i></button>';
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
				const notaorder = $(this).data("notaorder");
				const encoded = btoa(notaorder); // base64 encode

				// set ke modal
				$("#notaOrderToDelete").text(notaorder);
				$("#notaOrderHidden").val(encoded);

				// munculkan modal
				$("#modal_deleteOrder").modal("show");
			});

			// Saat konfirmasi hapus ditekan
			$("#confirmDeleteOrderBtn").on("click", function () {
				const encodedNota = $("#notaOrderHidden").val();
				if (encodedNota) {
					window.location.href = "<?=base_url()?>admin/wholesale/orderhapus/" + encodedNota;
				}
			});
	});

</script>
