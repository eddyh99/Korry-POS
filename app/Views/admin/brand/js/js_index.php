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
			"order": [[ 0, "asc" ]],
            "scrollX": true,
            "pageLength": 50,
			"ajax": {
					"url": "<?=base_url()?>admin/brand/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [2],
				"mData": "namabrand",
				"mRender": function (data, type, full, meta){
				    if (full.role!="Admin"){
				        button='<a href="<?=base_url()?>admin/brand/ubah/'+encodeURI(btoa(full.namabrand))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        button=button+'<a href="<?=base_url()?>admin/brand/DelData/'+encodeURI(btoa(full.namabrand))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
    			        return button;
				    }else{
				        button='<a href="<?=base_url()?>admin/brand/ubah/'+encodeURI(btoa(full.namabrand))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        return button;
				    }
				}
			}],
            "columns": [
				  { "data": "namabrand"},
                  { "data": "keterangan" },
			]
	});
})
</script>