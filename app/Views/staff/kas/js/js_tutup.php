<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- pakai jsPDF versi terbaru + html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    // Date Picker
    $('#tgl').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 2021,
        maxYear: parseInt(moment().format('YYYY'), 10),
        locale: {
            format: 'DD MMM YYYY'
        }
    });

    // Print hanya area tertentu, tidak overwrite body
    function printDiv(divName) {
        var content = document.getElementById(divName).innerHTML;
        var printWindow = window.open('', 'PRINT', 'height=600,width=800');

        printWindow.document.write('<html><head><title>Laporan Harian</title>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    // Export PDF pakai jsPDF + html2canvas
    function printPDF(divName) {
        var { jsPDF } = window.jspdf;
        var doc = new jsPDF('p', 'pt', 'a4');
        var element = document.getElementById(divName);

        doc.html(element, {
            callback: function (doc) {
                doc.save('rekapharian.pdf');
            },
            x: 10,
            y: 10,
            width: 500,       // lebar konten di PDF
            windowWidth: 900  // lebar "virtual window" supaya layout tidak rusak
        });
    }
</script>
