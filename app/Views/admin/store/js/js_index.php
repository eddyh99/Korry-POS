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
			"ajax": {
					"url": "<?=base_url()?>admin/store/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [4],
				"mData": "storeid",
				"mRender": function (data, type, full, meta){
				    if (full.role!="Admin"){
				        button='<a href="<?=base_url()?>admin/store/ubah/'+encodeURI(btoa(full.storeid))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        button=button+'<a href="<?=base_url()?>admin/store/DelData/'+encodeURI(btoa(full.storeid))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
    			        return button;
				    }else{
				        button='<a href="<?=base_url()?>admin/store/ubah/'+encodeURI(btoa(full.storeid))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        return button;
				    }
				}
			}],
            "columns": [
				  { "data": "store"},
				  { "data": "alamat"},
				  { "data": "kontak"},
                  { "data": "keterangan" },
			]
	});
})
</script>