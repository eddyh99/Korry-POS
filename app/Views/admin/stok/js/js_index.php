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

            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>admin/stok/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data["produk"];							
						  }
			},
            "columns": [
				  { "data": "barcode"},
				  { "data": "namaproduk"},
                  { "data": "namabrand" },
                  { "data": "size" },
                  { "data": "stok",render:$.fn.dataTable.render.number('.', ',', 0, '') },
                  { "data": "store" },
			]
	});
})
</script>