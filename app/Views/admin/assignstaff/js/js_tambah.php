<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
 $('#nama').on('input', function() {
	var val = $('#nama').val()
	var xyz = $('#stafflist option').filter(function() {
		return this.value == val;
	}).data('uname');
	$("#username").val(xyz);
  })
 $('#store').on('input', function() {
	var val = $('#store').val()
	var xyz = $('#storelist option').filter(function() {
		return this.value == val;
	}).data('storeid');
	$("#storeid").val(xyz);
  })

</script>