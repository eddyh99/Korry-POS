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
				{ "data": "notaorder" },
				{ "data": "nama_partner" },   // sudah join, bukan id lagi
				{ "data": "tanggal" },
				{ "data": "subtotal", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); } },
				{ "data": "total_cicilan", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); }},		
				{ "data": null, "render": function(data, type, full, meta){
						let sisa = (full.subtotal ?? 0) - (full.total_cicilan ?? 0);
					 	return new Intl.NumberFormat('id-ID').format(sisa); 
					}
				},		
				{ "data": "notaorder", "render": function (data, type, full, meta){
						let button = '';

						// cek selisih subtotal - total_cicilan
						let sisa = (Number(full.subtotal) ?? 0) - (Number(full.total_cicilan) ?? 0);
						if (sisa >0) {
							button += '<button type="button" class="btn btn-simple btn-info btn-icon btnBP" title="Cetak BP" data-notaorder="' + data + '"><i class="material-icons">account_balance</i></button>';
							button += '<a href="<?=base_url()?>admin/wholesale/detailcicilan/'+data+'" class="btn btn-simple btn-danger btn-icon btnDelete" title="Detail">detail</button>';
						}
						
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
			const notaorder = $(this).data("notaorder");
            $.ajax({
                url: "<?=base_url()?>/admin/wholesale/complete/"+notaorder,  // ganti sesuai route kamu
                type: "GET",                          // biasanya POST untuk update
                success: function (response) {
                    $('#table_data').DataTable().ajax.reload(); 
					if (notaorder) {
						// langsung buka jendela cetak
						window.open("<?=base_url('admin/wholesale/cetakbalancepayment')?>/" + notaorder, "_blank");
					}
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert("Gagal update status!");
                }
            });

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
