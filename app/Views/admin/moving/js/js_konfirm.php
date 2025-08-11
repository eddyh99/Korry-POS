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
            "responsive" : true,
            "processing" : true,
            "serverSide" : true,
            "pageLength": 50,

			"order"		 : [[ 1, "desc" ]],
            "scrollX"	 : true,
			"ajax"		 : {
								"url"		: "<?=base_url()?>admin/moving/Listdatakonfirm",
								"type"		: "POST",
								"dataSrc"	: function (data){
									return data["produk"];							
								}
						   },
		    "aoColumnDefs"	: [{	
				"aTargets"	: [4],
				"mData"		: "mutasi_id",
				"mRender"	: function (data, type, full, meta){
					var button='';
					<?php if($_SESSION["logged_status"]["role"]!="Office Staff"){?>
    					if (full.status=="Belum")
    					{
    						button	= '<a href="<?=base_url()?>admin/moving/terimakonfirm/'+encodeURI(btoa(full.mutasi_id))+'" class="btn btn-simple btn-success btn-icon"><i class="material-icons" title="Kirim">check_circle</i></a>';
    				        button	= button+'<a href="<?=base_url()?>admin/moving/batalkonfirm/'+encodeURI(btoa(full.mutasi_id))+'" class="btn btn-simple btn-danger btn-icon" title="Tolak"><i class="material-icons">close</i></a>';
    					}else{
    						button	= "";
    					}
				    <?php } ?>
    				button=button+'<a href="<?=base_url()?>admin/moving/detail/'+encodeURI(btoa(full.mutasi_id))+'/1" class="btn btn-info btn-sm btn-icon"><i class="fas fa-info"></i> Detail</a>';
				    return button;			    
				}
			}],
            "columns"	: [
				  { "data" : "mutasi_id"},
				  { "data" : "tanggal"},
                  { "data" : "tujuan" },
                  { "data" : "status"},
			]
	});
})
</script>