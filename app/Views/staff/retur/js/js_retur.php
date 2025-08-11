<!-- Select2 -->
<link rel="stylesheet" href="<?=base_url()?>assets/bootstrap/plugins/select2/css/select2.min.css">
<style>
#table_retur tbody tr{
  cursor: pointer;
}
.hanaka-row {
  width:100%;
  height: 100%;
}

.hanaka-button{
	height:100px;
	width:100px;
	border-radius: 10px;
}

.hanaka-col{
	height:120px;
	width:120px;
	float: left;
}

.hanaka-space{
	padding-left: 5px;
}
</style>

<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<link href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
	var rows_selected=[];
	var barangretur=[];
	var tableretur = $('#table_retur').DataTable({
            "scrollX": true,
            "order": [[ 1, 'asc' ]],
			"ajax": {
					"url": "<?=base_url()?>staff/retur/listretur",
					"type": "POST",
					"data" : {
					    key: $("#key").val()
					},
					"dataSrc":function (data){
							return data;							
						  }
			},
            "columnDefs": [ 
                    {
                        "orderable" : false,
                        "targets"   : 0,
                        "render"    : function (){
                            return "<input type='checkbox'>"
                        }
                    },
                    {
                        "data"      : "barcode",
                        "targets"   : 1
                    },
                    { 
                        "data"      : "namaproduk",
                        "targets"   : 2
                    },                    
				    { 
				        "data"      : "namabrand",
				        "targets"   : 3
				    },
				    { 
				        "data"      : "size",
				        "targets"   : 4
				    },
				    {   
				        "data"      : "jumlah",
				        "targets"   : 5
				    },
				    { 
				        "data"      : "harga",
				        "targets"   : 6,
				        "render"    : $.fn.dataTable.render.number( ',', '.', 0, '' )
				    },
                    { 
                        "data"      : "total",
                        "targets"   : 7,
                        "render"    : $.fn.dataTable.render.number( ',', '.', 0, '' ) 
                    },
            ],
	});
    
    var hasil=0;
    $('#table_retur tbody').on('click', 'input[type="checkbox"]', function(e){
        var $row = $(this).closest('tr');
        var data = tableretur.row($row).data();
        if(this.checked){
            hasil=hasil+Number(data.total)
            barangretur.push([data.barcode,data.size,data.jumlah]);
            $row.addClass('selected');
        } else {
            hasil=hasil-Number(data.total)
            for (i=0;i<barangretur.length;i++){
                if (barangretur[i][0]==data.barcode){
                    barangretur.splice(i,1);
                }
            }
            $row.removeClass('selected');
        }
        $("#total").val(hasil.toLocaleString("en"));
        $("#ttlretur").val(hasil);
		localStorage.setItem('returbrg', JSON.stringify(barangretur));
        e.stopPropagation();
    });
    
    $('#table_retur').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
    });

	$("#chargecard").hide();
    
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
			  var total=api.column( 7 ).data().sum();
			  $("#totalbelanja").text(total.toLocaleString("en"));
			  $( api.column( 7 ).footer() ).html(
				total.toLocaleString("en")
			  );
		},
		"columnDefs": [
			{ targets: [4] ,render:$.fn.dataTable.render.number('.', ',', 0, '')},
			{ targets: [5] ,render:$.fn.dataTable.render.number('.', ',', 0, '')},
			{ targets: [6] ,render:$.fn.dataTable.render.number('.', ',', 0, '')},
			{ targets: [7] ,render:$.fn.dataTable.render.number('.', ',', 0, '')},
	    ]
	});

	//pencarian
	var tbcari= $('#table_cari').DataTable({
					"processing": true,
					"serverSide": true,
					"scrollX": true,
					"ajax": {
							"url": "<?=base_url()?>staff/cashier/listdata",
							"type": "POST",
							"dataSrc":function (data){
									return data["produk"];							
								  }
					},
					"columns": [
						  { "data": "barcode"},
						  { "data": "namaproduk" },
						  { "data": "namabrand" },
						  { "data": "size" },
						  { "data": "stok" },
						  { "data": "store" },
					]
			})
	

	//barcode enter
	$("#barcode").on("keypress",function(e){

		if(e.which == 13) {
			if ($(this).val().length==0)
			{
				return;
			}
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

			$.ajax({
				url: "<?=base_url()?>staff/cashier/getharga",
				type: "post",
				data: "barcode="+$(this).val() ,
				success: function (data) {
					data=JSON.parse(data);
					$("#produk").val(data.namaproduk);
					$("#harga").val(data.harga);
				},
				error: function(jqXHR, textStatus, errorThrown) {
				   console.log(textStatus, errorThrown);
				}
			});
		}
	})

	$('#namaproduk').select2();

    $('#namaproduk').on('change', function() {
    	$("#barcode").val($(this).val());
        var e = jQuery.Event("keypress");
        e.which = 13;
        $("#barcode").trigger(e);
     }) 
	
	$("#simpan").on("click",function (e){
		var barcode	= $("#barcode").val();
		var produk	= $("#produk").val();
		var size	= $("#size").val();
		var harga	= $("#harga").val();
		var diskon	= Number($("#diskon").val());
		var alasan  = $("#keterangan").val().trim();
		var bsrnominal	= 0;
		var bsrpersen	= 0;
		var total		= $("#harga").val();

		if ($('#potongan:checked').val()=="persen")
		{
		    if ($("#keterangan").val().length==0){
		        alert("Keterangan Diskon Wajib Diisi");
		        return
		    }
			bsrpersen	= harga*(diskon/100);
			total		= harga-bsrpersen;
		}else if ($('#potongan:checked').val()=="fixed"){
		    if ($("#keterangan").val().length==0){
		        alert("Keterangan Diskon Wajib Diisi");
		        return
		    }
			bsrnominal	= diskon;
			total		= harga-bsrnominal;
		}

		var button	= '<button class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></button>';

        if (alasan==""){
    		var found=0;
    		for (i=0;i<dataSet.length;i++)
    		{
    			if ((dataSet[i][0]==barcode) && (dataSet[i][2]==size))
    			{
    			    if (dataSet[i][9]=="") {
        				dataSet[i][3]=Number(dataSet[i][3])+1;
    					dataSet[i][7]=Number(dataSet[i][3])*harga;
            			break;
    			    }else{
    			        found++;
    			    }
    			}else{
    				found++;
    			}
    		}
    		if (found>=dataSet.length)
    		{
    			dataSet.push([barcode, produk, size, "1", harga, bsrnominal, bsrpersen, total, button, alasan]);
    		}
        }else{
    		dataSet.push([barcode, produk, size, "1", harga, bsrnominal, bsrpersen, total, button, alasan]);
        }

		table.clear();
        table.rows.add(dataSet);
        table.draw();

		localStorage.setItem('dataSet', JSON.stringify(dataSet));

		$("#diskon").val("");
		$("#barcode").val("");
		$("#keterangan").val("");
		$("#produk").val("");
		$("#potongan:checked").prop('checked', false);
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
		if (dataSet.length>0)
		{
    		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''))-$("#ttlretur").val();
		    if ($("#ttlretur").val()>0){
    			$("#grandtotal").text(ttlbelanja.toLocaleString('en'));
    			$("#totalretur").text($("#total").val());
    			$("#modalpayment").modal("show");
		    }else{
		        alert("Barang Retur Belum dipilih");
		    }
		}
	})

	$("#carabayar").on("change",function(e){
		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''))-$("#ttlretur").val();
		if ($(this).val()=="credit")
		{
			$("#chargefee").val("");
			$("#chargecard").show();
			$("#grandtotal").text(ttlbelanja.toLocaleString('en'));
			$("#amountpay").prop("readonly", true);
		}else{
			$("#chargefee").val("");
			$("#chargecard").hide();
			$("#grandtotal").text(ttlbelanja.toLocaleString('en'));
			$("#amountpay").removeAttr("readonly");
			$("#amountpay").val("");
			$("#exchange").text("");
		}
	})


	$("#chargefee").on("keyup",function(){
		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''))-$("#ttlretur").val();
		ttlbelanja = ttlbelanja+(ttlbelanja*Number($("#chargefee").val())/100);
		$("#grandtotal").text(ttlbelanja.toLocaleString("en"));
		$("#amountpay").val(ttlbelanja.toLocaleString("en"));
		$("#exchange").text("0");
	});

	$("#amountpay").on("keyup",function(){
		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''))-$("#ttlretur").val();
		var kembalian = $(this).val()-ttlbelanja;
		$("#exchange").text(kembalian);
	});

	$("#simpaninvoice").on("click",function(){
	    var retur = $("#ttlretur").val();
	    var belanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''));
	    if (belanja-retur<0){
	        alert("Total retur masih lebih besar daripada belanja");
	        return;
	    }
	    console.log($("#amountpay").val()-(belanja-retur));
		if ($("#amountpay").val()-(belanja-retur)>=0)
		{
			var memberid=$("#memberid").val();
			var method=$("#carabayar").val();
			var fee=$("#chargefee").val();
			var Object = JSON.parse(localStorage.getItem('dataSet'));
			var Objectretur = JSON.parse(localStorage.getItem('returbrg'));
            console.log(Objectretur);
			for (i=0;i<Object.length ;i++ )
			{
			    temp=Object[i].pop();
				Object[i].pop();
				Object[i].push(temp);
			}
			var barang=JSON.stringify(Object);
			var barangretur = JSON.stringify(Objectretur);
			$.ajax({
				url: "<?=base_url()?>staff/retur/addretur",
				type: "post",
				data: "memberid="+memberid+"&method="+method+"&fee="+fee+"&barang="+barang+"&brgretur="+barangretur+"&id="+$("#key").val(),
				success: function (data) {
					location.reload();
				},
				error: function(jqXHR, textStatus, errorThrown) {
				   console.log(textStatus, errorThrown);
				}
			});
		}else{
		    alert("Harap masukkan pembayaran/Periksa pembayaran");
		}
	});


</script>