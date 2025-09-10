<style>
    tr { height: 45px; }
    #table_data tbody tr { cursor: pointer; }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
 $('#tanggal').daterangepicker({
    opens: 'right',
    autoUpdateInput: true,
    locale: {
      format: 'DD MMM YYYY'
    }
  });
let table = $('#table_data').DataTable({
            "ordering": false,
            "searching": false,
            "paging": false,
            "info": false,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    title: 'Laporan Laba Rugi',
                    className: 'btn-export'
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    title: 'Laporan Laba Rugi',
                    className: 'btn-export'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    title: 'Laporan Laba Rugi',
                    className: 'btn-export'
                }
            ],
            "ajax": {
                "url": "<?= base_url('admin/laporan/listlabarugi') ?>", // Ganti dengan URL endpoint Anda
                "type": "POST",
                "data": function(d) {
                    return {
                        tanggal: $("#tanggal").val(),
                    };
                },
                "dataSrc": function(data) {
                    return data;
                }
            },
            "columns": [
                { 
                    "data": "keterangan",
                    "render": function(data, type, row) {
                        // Tambahkan class untuk baris summary
                        if (data.includes('Total') || data.includes('Laba') || data.includes('Bersih')) {
                            return '<span class="summary-row">' + data + '</span>';
                        }
                        return data;
                    }
                },
                { 
                    "data": "jumlah", 
                    "className": "text-right",
                    "render": function(data, type, row) {
                        // Format angka dengan pemisah ribuan
                        let formatted = new Intl.NumberFormat('id-ID').format(data);
                        
                        // Tambahkan tanda Rp
                        formatted = 'Rp ' + formatted;
                        
                        // Tambahkan class untuk nilai positif/negatif
                        if (data < 0) {
                            return '<span class="negative">' + formatted + '</span>';
                        } else if (row.keterangan.includes('Total') || row.keterangan.includes('Laba')) {
                            return '<span class="positive">' + formatted + '</span>';
                        }
                        
                        return formatted;
                    }
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                // Tambahkan class untuk baris summary
                if (data.keterangan.includes('Total') || data.keterangan.includes('Laba') || data.keterangan.includes('Bersih')) {
                    $(row).addClass('summary-row');
                }
            },
        });

// tombol lihat untuk reload berdasarkan tahun
$("#lihat").on("click", function(){
    table.ajax.reload();
});
</script>

