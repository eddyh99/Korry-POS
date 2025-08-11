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
            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>staff/kas/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
            "columns": [
				  { "data": "tanggal"},
                  { "data": "nominal","render":$.fn.dataTable.render.number( ',', '.', 0, '' ) },
                  { "data": "keterangan" },
			]
	});
})
</script>