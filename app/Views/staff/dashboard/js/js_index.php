<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
<script>
    var incomepoint =[];
    var netincome = new CanvasJS.Chart("netincome",{
        animationEnabled: true,
        title:{
            text: "Omzet Bulanan Per Store"
        },
        data: [{
            type: "pie",
    		startAngle: 240,
    		yValueFormatString: "##0.00\"%\"",
    		indexLabel: "{label} {y}",
            dataPoints: incomepoint,
        }]
    });
    
    $.getJSON("<?=base_url()?>dashboard/penjualan", function(data) { 
        $.each(data, function(key, value){
            incomepoint.push({y: value[0], label: value[1]});
        });
        netincome.render();
    });

    var brandpoint =[];
    var brand = new CanvasJS.Chart("brand",{
        animationEnabled: true,
        title:{
            text: "Penjualan Bulanan Per Brand"
        },
        axisY: {
    		title: "Penjualan"
    	},
        data: [{
            type: "column",  
            dataPoints: brandpoint,
        }]
    });
    
    $.getJSON("<?=base_url()?>dashboard/jualbrand", function(data) { 
        $.each(data, function(key, value){
            brandpoint.push({y: value[0], label: value[1]});
        });
        brand.render();
    });
    
    var brandstorepoint;

    $.ajax({
        url: "<?=base_url()?>dashboard/brandstore", 
        async: false,
        success: function(data){
            brandstorepoint=JSON.parse(data);
        }
    });

    console.log(brandstorepoint);
    
    var brandstore = new CanvasJS.Chart("brandstore",{
        animationEnabled: true,
        title:{
            text: "Penjualan Bulanan Per Brand Per Store"
        },
        axisY: {
    		title: "Store"
    	},
        data: brandstorepoint
    });
    brandstore.render();
    
    

</script>