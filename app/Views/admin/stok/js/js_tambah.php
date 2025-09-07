<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script>
$("#barcode").on("keypress",function(e){
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
    let sizeStr = opt.data("size"); 
    // -------- TAMPILKAN SIZE KE SELECT --------
    $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
    if (sizeStr) {
        let sizeArr = sizeStr.split(",");
        sizeArr.forEach(function (sz) {
            $("#size").append(`<option value="${sz}">${sz}</option>`);
        });
    }
    $("#barcode").val(xyz);
    $("#brand").val(brand); // langsung isi brand tanpa Ajax

    // Kalau mau tetap load detail lain lewat Ajax, tetap panggil ini
    var e = jQuery.Event("keyup");
    e.which = 17;
    $("#barcode").trigger(e);
});

</script>