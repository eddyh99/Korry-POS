<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>

<script>
$("#produk").select2();
$("#size").select2();

$("#produk").on("change",function(){
	var barcode	= $(this).select2('data')[0].id;
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
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});
    $('#size').val("");
	$('#size').select2().trigger('change');
})

$("#size").on("change",function(){
	var barcode	= $("#produk").select2('data')[0].id;
	var size	= $("#size").select2('data')[0].id;
	console.log(barcode+" "+size);
	$.ajax({
		url: "<?=base_url()?>admin/opname/cekstok",
        async: false,
		type: "post",
		data: "barcode="+barcode+"&tujuan="+$("#tujuan").val()+"&size="+size,
		success: function (data) {
            $("#stok").val(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		   console.log(textStatus, errorThrown);
		}
	});    
})
</script>