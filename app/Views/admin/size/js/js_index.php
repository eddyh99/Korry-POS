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
			"order": [[ 1, "asc" ]],
            "scrollX": true,
            "pageLength": 50,
			"ajax": {
					"url": "<?=base_url()?>admin/size/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [1],
				"mData": "nama",
				"mRender": function (data, type, full, meta){
				    if (full.role!="Admin"){
				        button='<a href="<?=base_url()?>admin/size/ubah/'+encodeURI(btoa(full.nama))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        button=button+'<a href="<?=base_url()?>admin/size/DelData/'+encodeURI(btoa(full.nama))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
    			        return button;
				    }else{
				        button='<a href="<?=base_url()?>admin/size/ubah/'+encodeURI(btoa(full.nama))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        return button;
				    }
				}
			}],
            "columns": [
				  { "data": "nama"},
			]
	});
})
</script>