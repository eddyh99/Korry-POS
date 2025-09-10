<style>
    tr { height: 45px; }
    #table_data tbody tr { cursor: pointer; }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
// Format angka dengan pemisah ribuan
        function formatRupiah(angka) {
            if (!angka) return '0';
            return new Intl.NumberFormat('id-ID').format(angka);
        }
        
        // Inisialisasi DataTable
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
                    title: 'Laporan Neraca Keuangan',
                    className: 'btn-export'
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    title: 'Laporan Neraca Keuangan',
                    className: 'btn-export'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    title: 'Laporan Neraca Keuangan',
                    className: 'btn-export'
                }
            ],
            "ajax": {
                "url": "<?= base_url('admin/laporan/listneraca') ?>",
                "type": "POST",
                "data": function(d) {
                    d.tanggal=$("#tanggal").val();
                },
                "dataSrc": function(json) {
                    // Format data untuk DataTables
                    let data = [];
                    
                    // Aktiva
                    data.push({ 
                        "akun": "AKTIVA", 
                        "saldo": "", 
                        "className": "section-header" 
                    });
                    
                    // Kas dengan detail
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) + "Kas / Saldo", 
                        "saldo": formatRupiah(json.kas.total), 
                        "className": "" 
                    });
                    
                    // Detail kas
                    for (const [key, value] of Object.entries(json.kas.breakdown)) {
                        data.push({ 
                            "akun": '\u00A0'.repeat(6) + '→ ' + key,
                            "saldo": formatRupiah(value), 
                            "className": "detail-row" 
                        });
                    }
                    
                    // Piutang
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Piutang Usaha", 
                        "saldo": formatRupiah(json.piutang), 
                        "className": "" 
                    });
                    
                    // Persediaan
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Persediaan", 
                        "saldo": formatRupiah(json.persediaan), 
                        "className": "" 
                    });
                    
                    // Total Aktiva
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Total Aktiva", 
                        "saldo": formatRupiah(json.total_aktiva), 
                        "className": "total-row" 
                    });
                    
                    // Pasiva
                    data.push({ 
                        "akun": "PASIVA", 
                        "saldo": "", 
                        "className": "section-header" 
                    });
                    
                    // Hutang
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Hutang Usaha", 
                        "saldo": formatRupiah(json.hutang), 
                        "className": "" 
                    });
                    
                    // Modal
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Modal", 
                        "saldo": formatRupiah(json.modal), 
                        "className": "" 
                    });
                    
                    // Total Pasiva
                    data.push({ 
                        "akun": '\u00A0'.repeat(2) +"Total Pasiva (Hutang + Modal)", 
                        "saldo": formatRupiah(json.total_pasiva), 
                        "className": "total-row" 
                    });
                    
                    return data;
                }
            },
            "columns": [
                { 
                    "data": "akun",
                    "render": function(data, type, row) {
                        return data;
                    }
                },
                { 
                    "data": "saldo",
                    "className": "text-right",
                    "render": function(data, type, row) {
                        if (data === "") return "";
                        return 'Rp ' + data;
                    }
                }
            ],
            "createdRow": function(row, data, dataIndex) {
                // Tambahkan class khusus untuk styling
                if (data.className) {
                    $(row).addClass(data.className);
                }
            }
        });
        
        // Event handler untuk filter
        $('#lihat').click(function() {
            table.ajax.reload();
            
            // Update tanggal laporan
            const bulan = $('#bulan option:selected').text();
            const tahun = $('#tahun').val();
            const dateText = bulan === 'Semua Bulan' ? 
                `Tahun ${tahun}` : 
                `Per ${bulan} ${tahun}`;
                
            $('#reportDate').text(dateText);
        });
</script>

