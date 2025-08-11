<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script>
$("#barcode").on("keyup",function(e){
	$.get( "<?=base_url()?>admin/stok/getDetail/"+$(this).val(), function( data ) {
		var res=JSON.parse(data);
		$("#produk").val(res.namaproduk);
		$("#brand").val(res.namabrand);
	});
	e.preventDefault();
})

$("#store").select2();

 $('#produk').on('input', function() {
	var val = $(this).val()
	var xyz = $('#dataproduk option').filter(function() {
		return this.value == val;
	}).data('barcode');
	$("#barcode").val(xyz);
	
    var e = jQuery.Event("keyup");
    e.which = 17;
    $("#barcode").trigger(e);

 }) 

</script>