<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script>
    $("#produk").select2();
    
	$("#btlbeli").on("click",function(){
	    $("#size").val("").trigger('change')
	    $("#produk").val("").trigger('change')
	});
    
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
	});


	//barcode enter
	$("#barcode").on("keypress",function(e){

		if(e.which == 13) {
			if ($(this).val().length==0)
			{
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
        console.log(jumlah);
        
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

        if (stok-jumlah<0) {
            alert("Stok tersisa : "+stok);
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
	    $("#produk").val("").trigger('change')
		$("#modalsize").modal("hide");
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
			url: "<?=base_url()?>admin/pinjam/addData",
			type: "post",
			data: "nama="+$("#nama").val()+"&keterangan="+$("#keterangan").val()+"&barang="+barang,
			success: function (data) {
                localStorage.clear();
				location.href="<?=base_url()?>admin/pinjam";
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

