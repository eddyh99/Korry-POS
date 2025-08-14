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
				"url": "<?=base_url()?>admin/laporan/listdetailpenjualan",
				"type": "POST",
				"data": {
				    key     : function(){return $("#key").val()},
				},
				"dataSrc":function (data){
						return data;
					  }
			},
            "columns": [
				  { "data": "nonota"},
                  { "data": "namaproduk" },
                  { "data": "namabrand" },
                  { "data": "size" },
                  { "data": "jumlah" },
                  { "data": "harga" },
                  { "data": "diskonn","render":$.fn.dataTable.render.number( ',', '.', 0, '' )  },
                  { "data": "diskonp","render":$.fn.dataTable.render.number( ',', '.', 0, '' )  },
                  { "data": "total","render":$.fn.dataTable.render.number( ',', '.', 0, '' )  },
    			<?php if (($_SESSION["logged_status"]["role"]=="Office Manager") || ($_SESSION["logged_status"]["role"]=="Owner")) {?>
                  { "data": "alasan" },
                <?php }  ?>
			]
	});
	
	$("#lihat").on("click",function(){
	    table.ajax.reload();
	})
</script>