<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
<script>
    // =========================
    // Omzet Bulanan Per Store (Pie)
    // =========================
    var incomepoint = [];
    var netincome = new CanvasJS.Chart("netincome", {
        animationEnabled: true,
        title: { text: "Omzet Bulanan Per Store" },
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0.00\"%\"",
            indexLabel: "{label} {y}",
            dataPoints: incomepoint
        }]
    });

    $.ajax({
        url: "<?= base_url('staff/dashboard/penjualan'); ?>",
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (Array.isArray(data)) {
                data.forEach(function (row) {
                    incomepoint.push({ y: parseFloat(row[0]) || 0, label: row[1] });
                });
            }
            netincome.render();
        },
        error: function (xhr, status, err) {
            console.error("penjualan AJAX error:", status, err);
            netincome.render();
        }
    });

    // =========================
    // Penjualan Bulanan Per Brand (Column)
    // =========================
    var brandpoint = [];
    var brand = new CanvasJS.Chart("brand", {
        animationEnabled: true,
        title: { text: "Penjualan Bulanan Per Brand" },
        axisY: { title: "Penjualan" },
        data: [{
            type: "column",
            dataPoints: brandpoint
        }]
    });

    $.ajax({
        url: "<?= base_url('staff/dashboard/jualbrand'); ?>",
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (Array.isArray(data)) {
                data.forEach(function (row) {
                    brandpoint.push({ y: parseFloat(row[0]) || 0, label: row[1] });
                });
            }
            brand.render();
        },
        error: function (xhr, status, err) {
            console.error("jualbrand AJAX error:", status, err);
            brand.render();
        }
    });

    // =========================
    // Penjualan Per Brand Per Store (Multi-Series)
    // =========================
    $.ajax({
        url: "<?= base_url('staff/dashboard/brandstore'); ?>",
        type: "GET",
        dataType: "json",
        success: function (seriesData) {
            var brandstore = new CanvasJS.Chart("brandstore", {
                animationEnabled: true,
                title: { text: "Penjualan Bulanan Per Brand Per Store" },
                axisY: { title: "Penjualan" },
                data: Array.isArray(seriesData) ? seriesData : []
            });
            brandstore.render();
        },
        error: function (xhr, status, err) {
            console.error("brandstore AJAX error:", status, err);
            // render chart kosong biar halaman tetap jalan
            var brandstore = new CanvasJS.Chart("brandstore", {
                animationEnabled: true,
                title: { text: "Penjualan Bulanan Per Brand Per Store" },
                axisY: { title: "Penjualan" },
                data: []
            });
            brandstore.render();
        }
    });

    // =========================
    // Top 10 Produk Terlaris (Bar) — opsional kalau ada <div id="topten"></div>
    // =========================
    if (document.getElementById('topten')) {
        var toptenpoint = [];
        var topten = new CanvasJS.Chart("topten", {
            animationEnabled: true,
            title: { text: "Top 10 Produk Terlaris (Qty)" },
            axisY: { title: "Jumlah Terjual" },
            data: [{
                type: "bar",
                dataPoints: toptenpoint
            }]
        });

        $.ajax({
            url: "<?= base_url('staff/dashboard/toptenpenjualan'); ?>",
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (Array.isArray(data)) {
                    data.forEach(function (row) {
                        toptenpoint.push({ y: parseInt(row.total_qty) || 0, label: row.namaproduk });
                    });
                }
                topten.render();
            },
            error: function (xhr, status, err) {
                console.error("toptenpenjualan AJAX error:", status, err);
                topten.render();
            }
        });
    }
</script>
