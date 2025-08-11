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
            "pageLength": 50,
            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>admin/produksize/Listdata",
					"type": "POST",
					"dataSrc":function (data){
						console.log(data);
							return data;				
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [4],
				"mData": "barcode",
				"mRender": function (data, type, full, meta){
				        button='<a href="<?=base_url()?>admin/produksize/DelData/'+encodeURI(btoa(full.barcode))+'/'+encodeURI(btoa(full.size))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
    			        return button;
				}
			}],
            "columns": [
				  { "data": "barcode"},
				  { "data": "namaproduk"},
                  { "data": "namabrand" },
                  { "data": "size" },
			]
	});
})
</script>