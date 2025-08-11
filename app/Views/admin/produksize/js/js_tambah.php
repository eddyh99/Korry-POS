<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>

 $('#barcode').on('keypress', function(e) {
	e.preventDefault();
	if (e.which==13)
	{
		$.ajax({
			url: "<?=base_url()?>admin/produksize/getprodukname",
			type: "post",
			data: "barcode="+$(this).val() ,
			success: function (data) {
				$("#namaproduk").val(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});

	}
 }) 
 
 $('#namaproduk').on('input', function() {
	var val = $(this).val()
	var xyz = $('#dataproduk option').filter(function() {
		return this.value == val;
	}).data('barcode');
	$("#barcode").val(xyz);

 }) 

</script>