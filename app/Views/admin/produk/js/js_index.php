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
					"url": "<?=base_url()?>admin/produk/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data["produk"];							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [6],
				"mData": "barcode",
				"mRender": function (data, type, full, meta){
				        button='<a href="<?=base_url()?>admin/produk/ubah/'+encodeURI(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Ubah"><i class="fas fa-pen"></i></a>';
				        if (full.status==0){
    				        button=button+'<a href="<?=base_url()?>admin/produk/DelData/'+encodeURI(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Hapus"><i class="fas fa-times"></i></a>';
				        }else{
    				        button=button+'<a href="<?=base_url()?>admin/produk/callData/'+encodeURI(btoa(full.barcode))+'" class="btn btn-simple btn-danger btn-icon remove" title="Aktifkan"><i class="fas fa-history"></i></a>';
				        }
    			        return button;				    
				}
			}],
            "columns": [
				  { "data": "barcode"},
				  { "data": "namaproduk"},
                  { "data": "namabrand" },
                  { "data": "namakategori" },
                  { "data": "harga",render:$.fn.dataTable.render.number('.', ',', 0, '') },
                  { "data": "diskon",render:$.fn.dataTable.render.number('.', ',', 0, '') },
			]
	});
})
</script>