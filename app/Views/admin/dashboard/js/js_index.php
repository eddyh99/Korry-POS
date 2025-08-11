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
            text: "Penjualan Bulanan Per Brand Global"
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

    var brandstore = new CanvasJS.Chart("brandstore",{
        animationEnabled: true,
        title:{
            text: "Penjualan Bulanan Per Brand Per Store"
        },
        axisY: {
    		title: "Store"
    	},
    	toolTip: {
    		shared: true,
    		content: toolTipFormatter
    	},
    	legend: {
    		cursor:"pointer",
    		itemclick : toggleDataSeries
    	},
        data: brandstorepoint
    });
    brandstore.render();
    
    function toolTipFormatter(e) {
    	var str = "";
    	var total = 0 ;
    	var str3;
    	var str2 ;
    	for (var i = 0; i < e.entries.length; i++){
    		var str1 = "<span style= \"color:"+e.entries[i].dataSeries.color + "\">" + e.entries[i].dataSeries.name + "</span>: <strong>"+  e.entries[i].dataPoint.y + "</strong> <br/>" ;
    		total = e.entries[i].dataPoint.y + total;
    		str = str.concat(str1);
    	}
    	str2 = "<strong>" + e.entries[0].dataPoint.label + "</strong> <br/>";
    	str3 = "<span style = \"color:Tomato\">Total: </span><strong>" + total + "</strong><br/>";
    	return (str2.concat(str)).concat(str3);
    }
    
    function toggleDataSeries(e) {
    	if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
    		e.dataSeries.visible = false;
    	}
    	else {
    		e.dataSeries.visible = true;
    	}
    	brandstore.render();
    }
        
</script>