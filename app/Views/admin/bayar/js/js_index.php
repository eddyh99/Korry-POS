<script>
var table;
$(function(){
	table = $('#table_data').DataTable({
	        "order": [[ 0, "desc" ]],
            "scrollX": true,
            "pageLength": 50,
			"ajax": {
					"url": "<?=base_url()?>admin/bayar/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [5],
				"mRender": function (data, type, full, meta){
				    var button = "<a href='<?=base_url()?>admin/bayar/ganti/"+btoa(full.id)+"' class='btn btn-sm'>Ganti</a>";
				    var order=moment(full.tanggal).format("YYYY-MM-DD");
				    var maxtgl = moment().subtract(3, "days").format("YYYY-MM-DD");
				    if (order>maxtgl){
    				    return button;
				    }else{
				        return "";
				    }
				}
			}],
            "columns": [
				  { "data": "nonota"},
				  { "data": "tanggal"},
				  { "data": "nama"},
                  { "data": "total","render":$.fn.dataTable.render.number( ',', '.', 0, '' ) },
                  { "data": "method" },
			]
	});
})
</script>