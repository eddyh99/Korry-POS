<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css" />
<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
    .show-calendar tr{
        height:10px;
    }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
  $('#tgl').daterangepicker({
    startDate: moment(),
    endDate: moment().add(1, 'days'),
    opens:'right',
    locale: {
      format: 'DD MMM YYYY'
    }
  });

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
				"url": "<?=base_url()?>admin/laporan/Listbrand",
				"type": "POST",
				"data": {
				    tgl     : function(){return $("#tgl").val()},
				    storeid : function(){return $("#store").val()},
				    brand   : function(){return $("#brand").val()},
				    kategori   : function(){return $("#kategori").val()},
				},
				"dataSrc":function (data){
						return data;
					  }
			},
    		"drawCallback": function () {
    			  var api = this.api();
    			  var total=api.column( 6,{filter:'applied'}).data().sum();
    			  var harga=api.column( 7,{filter:'applied'}).data().sum();
    			  $( api.column( 6 ).footer() ).html(
    				total.toLocaleString("en")
    			  );
    			  $( api.column( 7 ).footer() ).html(
    				harga.toLocaleString("en")
    			  );
    		},
            "columns": [
				  { "data": "nonota"},
                  { "data": "tanggal" },
                  { "data": "barcode" },
                  { "data": "namabrand" },
                  { "data": "namaproduk"},
                  { "data": "size"},
                  { "data": "jumlah"},
                  { "data": "total","render":$.fn.dataTable.render.number( ',', '.', 0, '' )  },
			]
	});
	
	$("#lihat").on("click",function(){
	    table.ajax.reload();
	})
</script>