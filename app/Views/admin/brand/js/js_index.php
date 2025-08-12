<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
	var table;
	$(function(){
		console.log("Inisialisasi DataTable...");

		table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
			"scrollX": true,
			"pageLength": 50,
			"ajax": {
				"url": "<?= base_url() ?>admin/brand/listdata",
				"type": "POST",
				"dataSrc": function (data) {
					console.log("Data diterima dari server:", data);

					if (!Array.isArray(data)) {
						console.error("Format data tidak sesuai, seharusnya array:", data);
						return [];
					}
					return data;
				},
				"error": function (xhr, error, thrown) {
					console.error("AJAX Error:", error, thrown);
					console.error("Response Text:", xhr.responseText);
				}
			},
			"aoColumnDefs": [
				{  
					"aTargets": [2],
					"mData": "namabrand",
					"mRender": function (data, type, full, meta){
						console.log("Render kolom aksi untuk:", full);

						let button = '';
						if (full.role != "Admin") {
							button += '<a href="<?= base_url() ?>admin/brand/ubah/' + encodeURI(btoa(full.namabrand)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
							button += '<a href="<?= base_url() ?>admin/brand/hapus/' + encodeURI(btoa(full.namabrand)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
						} else {
							button += '<a href="<?= base_url() ?>admin/brand/ubah/' + encodeURI(btoa(full.namabrand)) + '" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
						}
						return button;
					}
				}
			],
			"columns": [
				{ "data": "namabrand" },
				{ "data": "keterangan" },
				{ "data": null } // kolom aksi di-handle oleh mRender
			]
		});

		console.log("DataTable siap dipakai.");
	});
</script>
