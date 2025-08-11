<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>

<script>
$(function(){
	$('#table_detail').on('shown.bs.modal', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});

$('#tanggal').datetimepicker({
    format: 'MM/DD/YYYY',
    icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove',
        inline: true
    }
 });

$("#store").select2();

var	table = $('#table_data').DataTable({
        "scrollX": true,
		"ajax": {
				"url": "<?=base_url()?>admin/transaksi/Listdata",
				"type": "POST",
                "data": function(d){
				    d.tanggal=$("#tanggal").val();
				    d.storeid=$("#store").val();
				},					
				"dataSrc":function (data){
						return data;		
				}
		},
        "columns": [
			  { "data": "nonota"},
              { "data": "tanggal" },
              { "data": "member_id" },
              { "data": "total",render:$.fn.dataTable.render.number('.', ',', 0, '') },
              { "data": "method" },
		]
});

$("#cari").on("click",function(){
    table.ajax.reload();
})

var tbdetail= $('#table_detail').DataTable({
		"scrollX": true,
		"ajax": {
				"url": "<?=base_url()?>admin/transaksi/listdetail",
				"type": "POST",
                "data": function(d){
				    d.nonota=$("#nonota").val();
				    d.tanggal=$("#tglnota").val();
				},					
				"dataSrc":function (data){
						return data;							
					  }
		},
		"columns": [
			  { "data": "barcode"},
			  { "data": "namaproduk" },
			  { "data": "size" },
			  { "data": "jumlah" },
			  { "data": "harga",render:$.fn.dataTable.render.number('.', ',', 0, '')  },
			  { "data": "diskonn",render:$.fn.dataTable.render.number('.', ',', 0, '')  },
			  { "data": "diskonp",render:$.fn.dataTable.render.number('.', ',', 0, '')  },
			  { "data": "total",render:$.fn.dataTable.render.number('.', ',', 0, '')  },
		]
})


$('#table_data tbody').on( 'click', 'td', function () 
{
	var data=table.row( this ).data();
	$("#nonota").val(data.nonota);
	$("#tglnota").val(data.tanggal);
	$("#carabayar").val(data.method)
	
    tbdetail.ajax.reload();
	$("#gantipayment").modal("show");

});

$("#simpanpembayaran").on("click",function(){
    var nonota=$("#nonota").val();
    var carabayar=$("#carabayar").val();

    $.ajax({
        type    : "POST",
        url     : "<?=base_url()?>admin/transaksi/simpanbayar",
        data    : "nonota="+nonota+"&bayar="+carabayar,
        success : function (data) {
            table.ajax.reload();
    	    $("#gantipayment").modal("hide");
        }
    });
})
</script>