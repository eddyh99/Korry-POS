<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
    $("#btnpayment").prop("disabled",true);
	$(function() {
		$("#chargecard").hide();
        $("input[name='potongan']").on("click", function(){
			$("#diskonsale").show();
			$("#ketsale").show();
    	    if ($(this).val()=="sale"){
    			$.ajax({
    				url: "<?=base_url()?>staff/cashier/getharga",
    				type: "post",
    				data: "barcode="+$("#barcode").val() ,
    				success: function (data) {
    					data=JSON.parse(data);
    					$("#diskonsale").hide();
    					$("#ketsale").hide();
    					$("#diskon").val(data.diskon);
    					$("#keterangan").val("Sale");
    				},
    				error: function(jqXHR, textStatus, errorThrown) {
    				   console.log(textStatus, errorThrown);
    				}
    			});

        	    $("#diskon").prop("disabled",true);
        	    $("#keterangan").prop("disabled",true);
        	    return;
    	    }
    	    if ($(this).val()=="nodisk"){
    			$("#diskon").val('');
    			$("#keterangan").val("");
        	    $("#diskon").prop("disabled",true);
        	    $("#keterangan").prop("disabled",true);
        	    return;
    	    }
			$("#diskon").val('');
			$("#keterangan").val("");
    	    $("#diskon").prop("disabled",false);
    	    $("#keterangan").prop("disabled",false);
        })
	});
	
	$("#btnnew").on("click",function(){
        $.get("<?=base_url()?>staff/cashier/newnota", function(data, status){
            $("#btnpayment").prop("disabled",false);
        });
	})
  
	$("#amountpay").on("keyup",function(){
        var val = this.value;
        val = val.replace(/[^0-9\.]/g,'');
        
        if(val != "") {
            valArr = val.split('.');
            valArr[0] = (parseInt(valArr[0],10)).toLocaleString();
            val = valArr.join('.');
        }
        
        this.value = val;
    });
    

	$("#btlbeli").on("click",function(){
	    $("#diskon").val("");
	    $("#keterangan").val("");
	    $("#size").val("").trigger('change')
	    $("#namaproduk").val("").trigger('change')
        
        $('input[name="potongan"]').prop("checked",false);
	    $("#diskon").prop("disabled",true);
	    $("#keterangan").prop("disabled",true);
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
		"drawCallback": function () {
			  var api = this.api();
			  var qty=api.column( 3 ).data().sum();
			  $( api.column( 3 ).footer() ).html(
				qty.toLocaleString("en")
			  );
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
            "responsive": true,
            "processing": true,
            "serverSide": true,

            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>admin/stok/listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data["produk"];							
						  }
			},
            "columns": [
				  { "data": "barcode"},
				  { "data": "namaproduk"},
                  { "data": "namabrand" },
                  { "data": "harga",render:$.fn.dataTable.render.number('.', ',', 0, '') },
                  { "data": "size" },
                  { "data": "stok",render:$.fn.dataTable.render.number('.', ',', 0, '') },
                  { "data": "store" },
			]
			})
	

	//barcode enter
	// $("#barcode").on("keypress",function(e){
	// 	if(e.which == 13) {
	// 		if ($(this).val().length==0)
	// 		{
	// 			return;
	// 		}
	// 		$.ajax({
	// 			url: "<?=base_url()?>staff/cashier/readbarcode",
	// 			type: "post",
	// 			data: "barcode="+$(this).val() ,
	// 			success: function (data) {
	// 				data=JSON.parse(data);
	// 				results=$.map(data, function (item) {
	// 							return {
	// 						   id: item.size,
	// 						   text: item.size
	// 							};
	// 						})
	// 				$('#size').select2({data:results});
	// 				$("#modalsize").modal("show");
	// 			},
	// 			error: function(jqXHR, textStatus, errorThrown) {
	// 			   console.log(textStatus, errorThrown);
	// 			}
	// 		});

	// 		$.ajax({
	// 			url: "<?=base_url()?>staff/cashier/getharga",
	// 			type: "post",
	// 			data: "barcode="+$(this).val() ,
	// 			success: function (data) {
	// 				data=JSON.parse(data);
	// 				$("#produk").val(data.namaproduk);
	// 				$("#harga").val(data.harga);
	// 				$("#ketsale").show();
	// 				$("#diskonsale").show();
					
	// 				if (data.diskon>0){
	// 				    $("#potongan3").prop("checked",true);
    // 					$("#diskon").val(data.diskon);
    // 					$("#ketsale").hide();
    // 					$("#diskonsale").hide();
    // 					$("#keterangan").val("Sale");
	// 				}
	// 			},
	// 			error: function(jqXHR, textStatus, errorThrown) {
	// 			   console.log(textStatus, errorThrown);
	// 			}
	// 		});
	// 	}
	// })

	// barcode enter
	$("#barcode").on("keypress", function (e) {
		if (e.which == 13) {
			if ($(this).val().length == 0) {
				return;
			}

			// === AJAX 1: readbarcode ===
			$.ajax({
				url: "<?=base_url()?>staff/cashier/readbarcode",
				type: "post",
				data: { barcode: $(this).val() },   // ✅ ganti jadi object biar aman
				dataType: "json",                   // ✅ tambahkan ini, otomatis parse JSON
				success: function (data) {
					// ❌ tidak perlu JSON.parse(data) lagi
					results = $.map(data, function (item) {
						return {
							id: item.size,
							text: item.size
						};
					});
					$('#size').select2({ data: results });
					$("#modalsize").modal("show");
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});

			// === AJAX 2: getharga ===
			$.ajax({
				url: "<?=base_url()?>staff/cashier/getharga",
				type: "post",
				data: { barcode: $(this).val() },   // ✅ ganti jadi object
				dataType: "json",                   // ✅ tambahkan ini
				success: function (data) {
					// ❌ tidak perlu JSON.parse(data)
					$("#produk").val(data.namaproduk);
					$("#harga").val(data.harga);
					$("#ketsale").show();
					$("#diskonsale").show();

					if (data.diskon > 0) {
						$("#potongan3").prop("checked", true);
						$("#diskon").val(data.diskon);
						$("#ketsale").hide();
						$("#diskonsale").hide();
						$("#keterangan").val("Sale");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});
		}
	});

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

        if (stok<=0) {
            alert("Stok sudah habis");
            return;
        }

		if ($('input[name="potongan"]:checked').val()=="persen")
		{
		    if ($("#keterangan").val().length==0){
		        alert("Keterangan Diskon Wajib Diisi");
		        return
		    }
			bsrpersen	= harga*(diskon/100);
			total		= harga-bsrpersen;
		}else if (($('input[name="potongan"]:checked').val()=="fixed") || ($('input[name="potongan"]:checked').val()=="sale")){
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
        $("#namaproduk").val("").trigger('change')
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

	$("#btnmember").on("click",function (e){
		$("#modalmember").modal("show");
	})

	$("#batalmember").on("click",function (e){
		$("#memberid").val("");
		$("#membername").text("");
		$("#modalmember").modal("hide");
	})

	// $("#simpanmember").on("click",function (e){
	// 	$.ajax({
	// 		url: "<?=base_url()?>staff/cashier/getdetail",
	// 		dataType: "json",  // penting
	// 		type: "post",
	// 		data: "memberid="+$("#memberid").val() ,
	// 		success: function (data) {
	// 			if (data==404)
	// 			{
	// 				alert("Member tidak Ditemukan");
	// 				$(".membername").text("");
	// 			}else{
	// 				data=JSON.parse(data);
	// 				$(".membername").text(data.nama);
	// 				$("#modalmember").modal("hide");
	// 			}
	// 		},
	// 		error: function(jqXHR, textStatus, errorThrown) {
	// 		   console.log(textStatus, errorThrown);
	// 		}
	// 	});		
	// })

	// $("#memberid").on("keypress",function (e){
	// 	if (e.which==13)
	// 	{
	// 		$.ajax({
	// 			url: "<?=base_url()?>staff/cashier/getdetail",
	// 			dataType: "json",  // penting
	// 			type: "post",
	// 			data: "memberid="+$(this).val() ,
	// 			success: function (data) {
	// 				if (data==404)
	// 				{
	// 					alert("Member tidak Ditemukan");
	// 					$(".membername").text("");
	// 				}else{
	// 					data=JSON.parse(data);
	// 					$(".membername").text(data.nama);
	// 				}
	// 			},
	// 			error: function(jqXHR, textStatus, errorThrown) {
	// 			   console.log(textStatus, errorThrown);
	// 			}
	// 		});
	// 	}
	// })

	// Klik tombol Simpan
	$("#simpanmember").on("click", function (e) {
		$.ajax({
			url: "<?=base_url()?>staff/cashier/getdetail",
			type: "post",
			data: { memberid: $("#memberid").val() }, // pakai object biar aman
			dataType: "json", // otomatis parse JSON
			success: function (data) {
				$(".membername").text(data.nama);
				$("#modalmember").modal("hide");
			},
			error: function (xhr, textStatus, errorThrown) {
				if (xhr.status === 404) {
					alert("Member tidak Ditemukan");
					$(".membername").text("");
				} else {
					console.log("Error:", textStatus, errorThrown);
				}
			}
		});
	});

	// Tekan Enter di input memberid
	$("#memberid").on("keypress", function (e) {
		if (e.which == 13) {
			$.ajax({
				url: "<?=base_url()?>staff/cashier/getdetail",
				type: "post",
				data: { memberid: $(this).val() },
				dataType: "json",
				success: function (data) {
					$(".membername").text(data.nama);
					$("#modalmember").modal("hide");
				},
				error: function (xhr, textStatus, errorThrown) {
					if (xhr.status === 404) {
						alert("Member tidak Ditemukan");
						$(".membername").text("");
					} else {
						console.log("Error:", textStatus, errorThrown);
					}
				}
			});
		}
	});

	$("#btncari").on("click",function (){
		$("#cariproduk").modal("show");
	})

	$('#cariproduk').on('shown.bs.modal', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

	$("#btnpayment").on("click",function (){
		if (dataSet.length>0)
		{
			console.log(dataSet);
			$("#grandtotal").text($("#totalbelanja").text());
			$("#amountpay").prop("readonly", true);
			$("#amountpay").val($("#totalbelanja").text());
			$("#modalpayment").modal("show");
		}
	})


	$("#carabayar").on("change",function(e){
		if ($(this).val()=="credit")
		{
			$("#chargefee").val("");
			$("#chargecard").show();
			$("#grandtotal").text($("#totalbelanja").text());
			$("#amountpay").prop("readonly", true);
		}else if ($(this).val()=="debit"){
			$("#chargefee").val("");
			$("#chargecard").hide();
			$("#grandtotal").text($("#totalbelanja").text());
			$("#amountpay").prop("readonly", true);
			$("#amountpay").val($("#totalbelanja").text());
			$("#exchange").text(0);
		}else{
			$("#chargefee").val("");
			$("#chargecard").hide();
			$("#grandtotal").text($("#totalbelanja").text());
			$("#amountpay").prop("readonly", true);
			$("#amountpay").val($("#totalbelanja").text());
			$("#exchange").text(0);
		}
	})


	$("#chargefee").on("keyup",function(){
		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''));
		ttlbelanja = ttlbelanja+(ttlbelanja*Number($("#chargefee").val())/100);
		$("#grandtotal").text(ttlbelanja.toLocaleString("en"));
		$("#amountpay").val(ttlbelanja.toLocaleString("en"));
		$("#exchange").text("0");
	});

	$("#amountpay").on("keyup",function(){
		var ttlbelanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''));
		var kembalian = $(this).val().replace(/,/g, '')-ttlbelanja;
		$("#exchange").text(kembalian.toLocaleString('en'));
	});

	$("#simpaninvoice").on("click",function(){
	    var belanja = parseFloat($("#totalbelanja").text().replace(/,/g, ''));
	    var amountpay=$("#amountpay").val().replace(/,/g, '');
		if (amountpay-belanja>=0)
		{
			var memberid=$("#memberid").val();
			var method=$("#carabayar").val();
			var fee=$("#chargefee").val();
			
			var Object = JSON.parse(localStorage.getItem('dataSet'));
			for (i=0;i<Object.length ;i++ )
			{
			    temp=Object[i].pop();
				Object[i].pop();
				Object[i].push(temp);
			}
			var barang=JSON.stringify(Object);
			$.ajax({
				url: "<?=base_url()?>staff/cashier/add-data",
				type: "post",
				data: "memberid="+memberid+"&method="+method+"&fee="+fee+"&barang="+barang,
				success: function (data) {
				    if (data==511){
				        alert("ada barang yang habis");   
				    }else if (data=="611") {
				        alert("Nota sudah disimpan, silahkan cetak ulang nota ");
				    }else{
                        window.open('<?=base_url()?>staff/cashier/cetaknota/'+data);
                        location.reload();
				    }
				},
				error: function(jqXHR, textStatus, errorThrown) {
				   console.log(textStatus, errorThrown);
				}
			});
		}
	});
</script>

