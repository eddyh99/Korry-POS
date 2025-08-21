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
			"responsive": true,
			"processing": true,
			"serverSide": true,
			"pageLength": 50,
			"order": [[ 1, "asc" ]],
			"scrollX": true,

			"ajax": {
				"url": "<?= base_url('admin/produk/listdata') ?>",
				"type": "POST",
				"dataSrc": function (data) {
					console.log("[DEBUG] Response dari server:", data); // Logging hasil AJAX
					if (!data || !data.produk) {
						console.error("[ERROR] Data produk tidak ditemukan di response!");
						return [];
					}
					return data.produk;                          
				},
				"error": function (xhr, error, thrown) {
					console.error("[AJAX ERROR]", error, thrown);
					console.error("[AJAX RESPONSE]", xhr.responseText);
				}
			},

			"aoColumnDefs": [{  
				"aTargets": [7], // kolom aksi
				"mData": "barcode",
				"mRender": function (data, type, full, meta){
					let button = '<a href="<?= base_url('admin/produk/ubah/') ?>'+encodeURIComponent(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Ubah"><i class="fas fa-pen"></i></a>';
					if (full.status == 0){
						button += '<a href="<?= base_url('admin/produk/hapus/') ?>'+encodeURIComponent(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Hapus"><i class="fas fa-times"></i></a>';
					} else {
						button += '<a href="<?= base_url('admin/produk/panggil/') ?>'+encodeURIComponent(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Aktifkan"><i class="fas fa-history"></i></a>';
					}
					return button;                  
				}
			}],

			"columns": [
				{ "data": "barcode" },
				{ "data": "namaproduk" },
				{ "data": "sku" },
				{ "data": "harga", render: $.fn.dataTable.render.number('.', ',', 0, '') },
				{ "data": "harga_konsinyasi", render: $.fn.dataTable.render.number('.', ',', 0, '') },
				{ "data": "harga_wholesale", render: $.fn.dataTable.render.number('.', ',', 0, '') },
				{ "data": "diskon", render: $.fn.dataTable.render.number('.', ',', 0, '') },
				{ "data": null } // kolom aksi
			]
		});
	});
</script>
