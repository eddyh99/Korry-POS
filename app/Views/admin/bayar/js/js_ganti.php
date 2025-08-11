<script>
    $("#charge").hide();
    $("#carabayar").on("change",function(){
        if ($(this).val()=="credit"){
            $("#charge").show();
        }
    })
</script>