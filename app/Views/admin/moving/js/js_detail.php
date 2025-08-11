<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>
<script>
var table;
$(function(){
	table = $('#table_data').DataTable({
            "responsive" : true,
            "pageLength": 50,
			"order"		 : [[ 0, "asc" ]],
            "scrollX"	 : true,
			"ajax"		 : {
								"url"		: "<?=base_url()?>admin/moving/listdetail",
								"type"		: "POST",
								"data"      : {
                                    mutasi_id : $("#mutasi").val()
                                },
								"dataSrc"	: function (data){
									return data;							
								}
						   },
    		"drawCallback": function () {
    			  var api = this.api();
    			  var total=api.column( 4,{filter:'applied'} ).data().sum();
    			  $( api.column( 4 ).footer() ).html(
    				total.toLocaleString("en")
    			  );
    		},
            "columns"	: [
				  { "data" : "barcode"},
				  { "data" : "namaproduk"},
                  { "data" : "size" },
                  { "data" : "namabrand"},
                  { "data" : "jumlah"},
			]
	});
})
</script>