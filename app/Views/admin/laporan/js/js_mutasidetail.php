<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
var table;
	table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
            "pageLength": 50,
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/laporan/Listmutasidetail",
				"type": "POST",
				"data": {
				    bulan   : function(){return $("#bulan").val()},
				    tahun   : function(){return $("#tahun").val()},
				    brand : function(){return $("#brand").val()},
				    kategori : function(){return $("#kategori").val()},
				    storeid : function(){return $("#store").val()}
				},
				"dataSrc":function (data){
						return data;
					  }
			},
    		"drawCallback": function () {
    			  var api = this.api();
    			  var awal=api.column( 3,{filter:'applied'} ).data().sum();
    			  var masuk=api.column( 4,{filter:'applied'} ).data().sum();
    			  var keluar=api.column( 5,{filter:'applied'} ).data().sum();
    			  var jual=api.column( 6,{filter:'applied'} ).data().sum();
    			  var sesuai=api.column( 7,{filter:'applied'} ).data().sum();
    			  var sisa=api.column( 8,{filter:'applied'} ).data().sum();
    			  $( api.column( 3 ).footer() ).html(
    			    awal.toLocaleString("en")
    			  );
    			  $( api.column( 4 ).footer() ).html(
                    masuk.toLocaleString("en")
    			  );
    			  $( api.column( 5 ).footer() ).html(
    				keluar.toLocaleString("en")
    			  );
    			  $( api.column( 6 ).footer() ).html(
    				jual.toLocaleString("en")
    			  );
    			  $( api.column( 7 ).footer() ).html(
    				sesuai.toLocaleString("en")
    			  );
    			  $( api.column( 8 ).footer() ).html(
    				sisa.toLocaleString("en")
    			  );
    		},
            "columns": [
				  { "data": "namaproduk"},
				  { "data": "namabrand"},
				  { "data": "size"},
                  { "data": "awal","className": "text-right" },
                  { "data": "masuk","className": "text-right" },
                  { "data": "keluar","className": "text-right" },
                  { "data": "jual","className": "text-right" },
                  { "data": "sesuai","className": "text-right" },
                  { "data": "sisa","className": "text-right" },
			]
	});
	
	$("#lihat").on("click",function(){
	    table.ajax.reload();
	})
</script>