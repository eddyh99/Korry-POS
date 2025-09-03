<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable untuk Cicilan Wholesale...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/wholesale/cicilanlistdata",
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
				{ "data": "notaorder" },  
				{ "data": "tanggal" },
				{ "data": "bayar", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); }},
				{ "data": "nonota",
					"render": function (data, type, full, meta){
						let button = '';
						button += '<button type="button" class="btn btn-simple btn-info btn-icon btnBP" title="Cetak BP" data-nonota="' + data + '"><i class="material-icons">account_balance</i></button>';
						// button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-nonota="' + data + '"><i class="material-icons">close</i></button>';
						return button;
					}
				}
			]
		});

		table.on('error.dt', function(e, settings, techNote, message) {
			console.error("DataTables error:", message);
		});

		// === Handle Print Balance ===
		$('#table_data').on("click", ".btnBP", function () {
			const nonota = $(this).data("nonota");
			if (nonota) {
				// langsung buka jendela cetak
				window.open("<?=base_url('admin/wholesale/cetakbalancepaymentcicilan')?>/" + nonota, "_blank");
			}
		});

		// === Handle Hapus Cicilan (Bootstrap 4) ===
		$('#table_data').on("click", ".btnDelete", function () {
			const nonota = $(this).data("nonota");
			const encoded = btoa(nonota); // base64 encode

			// set ke modal
			$("#nonotaCicilanToDelete").text(nonota);
			$("#nonotaCicilanHidden").val(encoded);

			// munculkan modal
			$("#modal_deleteCicilan").modal("show");
		});

		// Saat konfirmasi hapus ditekan
		$("#confirmDeleteCicilanBtn").on("click", function () {
			const encodedNota = $("#nonotaCicilanHidden").val();
			if (encodedNota) {
				window.location.href = "<?= base_url() ?>admin/wholesale/cicilanhapus/" + encodedNota;
			}
		});
	});

</script>
