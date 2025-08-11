<script>
var table;
	table = $('#table_data').DataTable({
            "scrollX": true,
            "pageLength": 50,
			"ajax": {
					"url": "<?=base_url()?>admin/pinjam/Listdata",
					"type": "POST",
					"data": {
					    store:function(){return $("#store").val()}
					},
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [3],
				"mRender": function (data, type, full, meta){
				    var button='';
				    <?php if ($_SESSION["logged_status"]["role"]!="Staff"){?>
        				    button = "<a href='<?=base_url()?>admin/pinjam/tutup/"+full.id+"' class='btn btn-sm'> <i class='fas fa-times-circle'></i> Tutup</a>";
        				    button = button+"<a href='<?=base_url()?>admin/pinjam/detailpinjam/"+full.id+"' class='btn btn-sm'> <i class='fas fa-info'></i> Detail</a>";
				    <?php }?>
    		        return button;
				}
			}],
            "columns": [
				  { "data": "tanggal"},
				  { "data": "nama"},
                  { "data": "keterangan" },
			]
	});

$("#store").on("change",function(){
    table.ajax.reload();
})	
</script>

