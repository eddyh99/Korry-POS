<script>
var table;
$(function(){
	table = $('#table_data').DataTable({
	        "order":[[1,"desc"]],
            "scrollX": true,
            "pageLength": 50,
			"ajax": {
					"url": "<?=base_url()?>staff/retur/Listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data;							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [5],
				"mRender": function (data, type, full, meta){
				    var button = "<a href='#'  onclick=\"window.open('<?=base_url()?>staff/cashier/cetaknota/"+full.id+"')\" class='btn btn-sm'>Reprint</a>";
				    var button = button+"<a href='<?=base_url()?>staff/retur/detailretur/"+full.id+"/"+full.member_id+"' class='btn btn-sm'>Retur</a>";
				    var batal="";
                    <?php if ($_SESSION["logged_status"]["role"]!="Staff"){?>
				        batal = "<a href='<?=base_url()?>staff/retur/batalnota/"+full.id+"' class='btn btn-sm'>Batal</a>"
				    <?php }?>
				    var order=moment(full.tanggal).format("YYYY-MM-DD");
				    var maxtgl = moment().subtract(4, "days").format("YYYY-MM-DD");
				    var btltgl = moment().subtract(4, "days").format("YYYY-MM-DD");
				    
				    if (order>maxtgl){
				        if (full.jual_id==0){
				            return '';
				        }else{
    				        if (order>btltgl){
    				            return button+" "+batal;
    				        }else{
            				    return button;
    				        }
				        }
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