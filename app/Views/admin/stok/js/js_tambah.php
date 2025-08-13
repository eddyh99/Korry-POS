<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script>
$("#barcode").on("keyup",function(e){
	$.get( "<?=base_url()?>admin/stok/get-detail/"+$(this).val(), function( data ) {
		var res=JSON.parse(data);
		$("#produk").val(res.namaproduk);
		$("#brand").val(res.namabrand);
	});
	e.preventDefault();
})

$("#store").select2();

$('#produk').on('input', function() {
    var val = $(this).val();
    var opt = $('#dataproduk option').filter(function() {
        return this.value == val;
    });

    var xyz = opt.data('barcode');
    var brand = opt.data('brand');

    $("#barcode").val(xyz);
    $("#brand").val(brand); // langsung isi brand tanpa Ajax

    // Kalau mau tetap load detail lain lewat Ajax, tetap panggil ini
    var e = jQuery.Event("keyup");
    e.which = 17;
    $("#barcode").trigger(e);
});

</script>