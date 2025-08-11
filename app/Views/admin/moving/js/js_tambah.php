<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>
<script>
	$('.asal').select2();
	$("#produk").select2();
	var tujuan=$('#tujuan').select2();

	var dataSet=[];
	//pesanan
	var table=$('#table_data').DataTable({
		"targets": 'no-sort',
		"bSort": false,
		"order": [],
		"pageLength": 50,
		"bPaginate": false,
	    "bInfo": false,
		"lengthChange": false,
		"scrollY":        "200px",
        "scrollCollapse": true,
		"drawCallback": function () {
			  var api = this.api();
			  var total=api.column( 3 ).data().sum();
			  $( api.column( 3 ).footer() ).html(
				total.toLocaleString("en")
			  );
		},
	});

	$("#asal").on("change",function(e){
	    dataSet=[];
        $('#table_data').DataTable().clear().draw();
        localStorage.clear();
        $("#jumlah").val("");
	})
	
	$("#tujuan").on("change",function(e){
	    dataSet=[];
        $('#table_data').DataTable().clear().draw();
        localStorage.clear();
        $("#jumlah").val("");
	})

	$("#btlbeli").on("click",function(){
	    $("#size").val("").trigger('change')
	    $("#produk").val("").trigger('change')
	});

	//barcode enter
	$("#barcode").on("keypress",function(e){

		if(e.which == 13) {
			if ($(this).val().length==0)
			{
				return;
			}
			
			if ($("#asal").val()==$("#tujuan").val()){
			    alert("Store dan Request Store tidak boleh sama");
        	    $("#produk").val("").trigger('change')
			    return;
			}
            
            $("#jumlah").val("");
			$.ajax({
				url: "<?=base_url()?>staff/cashier/readbarcode",
				type: "post",
				data: "barcode="+$(this).val() ,
				success: function (data) {
					data=JSON.parse(data);
    					results=$.map(data, function (item) {
    								return {
    							   id: item.size,
    							   text: item.size
    								};
    							})
    					$('#size').select2({data:results});
    					$("#modalsize").modal("show");
				},
				error: function(jqXHR, textStatus, errorThrown) {
				   console.log(textStatus, errorThrown);
				}
			});
		}
	})
	
	$("#simpan").on("click",function (e){
		var barcode	= $("#barcode").val();
		var produk	= $('#produk').select2('data')[0].text;
		var size	= $("#size").val();
		var jumlah	= Number($("#jumlah").val());
        
        if (jumlah==0){
            jumlah=1;
        }
        
        var stok;
		$.ajax({
			url: "<?=base_url()?>admin/opname/cekstok",
            async: false,
			type: "post",
			data: "barcode="+barcode+"&tujuan="+$("#tujuan").val()+"&size="+size,
			success: function (data) {
                stok = data;
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
        
        if (stok==0){
            alert("Stok pada cabang sedang kosong");
            return;
        }else if (stok-jumlah<0) {
            alert("Stok pada cabang "+$("#tujuan").find(':selected').text()+" tersisa : "+stok);
            return;
        }
    
		var button	= '<button class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></button>';

		var found=0;
		for (i=0;i<dataSet.length;i++)
		{
			if ((dataSet[i][0]==barcode) && (dataSet[i][2]==size))
			{
				dataSet[i][3]=Number(dataSet[i][3])+jumlah;
				break;
			}else{
				found++;
			}
		}
		if (found>=dataSet.length)
		{
			dataSet.push([barcode, produk, size, jumlah, button]);
		}


		table.clear();
        table.rows.add(dataSet);
        table.draw();

		localStorage.setItem('dataSet', JSON.stringify(dataSet));

		$("#barcode").val("");
		$("#modalsize").modal("hide");
	    $("#produk").val("").trigger('change')
	})

	$('#table_data tbody').on( 'click', 'td', function () 
	{
		var tr = $(this).closest("tr");
		var rowindex = tr.index();
		dataSet.splice(rowindex,1);
		localStorage.setItem('dataSet', JSON.stringify(dataSet));

		table.clear();
        table.rows.add(dataSet);
        table.draw();
	});

	$("#btnpayment").on("click",function (){
		var asal=$("#asal").val();
		var tujuan=$("#tujuan").val();

		if (asal==tujuan){
		    alert("Store dan Request Store tidak boleh sama");
		    return;
		}
		

		var Object = JSON.parse(localStorage.getItem('dataSet'));

		if (!Object){
		   alert("Daftar barang masih kosong"); 
		    return;
		}
		
		for (i=0;i<Object.length ;i++ )
		{
			Object[i].pop();
		}
		var barang=JSON.stringify(Object);

		$.ajax({
			url: "<?=base_url()?>admin/moving/addData",
			type: "post",
			data: "asal="+asal+"&tujuan="+tujuan+"&barang="+barang,
			success: function (data) {
                localStorage.clear();
				location.href="<?=base_url()?>admin/moving";
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});

	})

 $('#produk').on('change', function() {
	$("#barcode").val($(this).val());

    var e = jQuery.Event("keypress");
    e.which = 13;
    $("#barcode").trigger(e);
 }) 

</script>

