<script>
	var table = $('#table_data').DataTable({
            "responsive": true,
			"order": [[ 1, "asc" ]],
            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>admin/opname/liststokopname",
					"type": "POST",
					"data": {
					    "storeid":function(){return $("#store").val()}
					},
					"dataSrc":function (data){
							return data.filter(function(row){
								return row.old != row.baru;
							});							
					}
			},
            "columns": [
				  { "data": "barcode"},
				  { "data": "produk"},
                  { "data": "size" },
                  { "data": "old"},
                  { "data": "baru"},
                  { "data": "keterangan"},
			]
	});
	
	$("#store").on("change",function(){
	    table.ajax.reload();
	});

</script>