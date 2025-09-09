<style>
    tr { height: 50px; }
    #table_data tbody tr {
        cursor:pointer;
    }
</style>
<script>
    var table;
    $(function(){

        // =============== HALAMAN LIST CICILAN ==================
        if ($("body").find("#table_data").length && $("form#form_cicilan").length === 0) {
            console.log("Inisialisasi DataTable untuk Cicilan Wholesale...");

            table = $('#table_data').DataTable({
                "order": [[ 0, "asc" ]],
                "scrollX": true,
                "ajax": {
                    "url": "<?=base_url()?>admin/wholesale/cicilanlistdata",
                    "type": "POST",
                    "dataSrc": function (data){
                        console.log("Data diterima dari server:", data);
                        return data;                            
                    },
                    "error": function (xhr, error, code) {
                        console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
                    }
                },
                "columns": [
                    { "data": "notaorder" },
                    { "data": "nama_partner" },
                    { "data": "tanggal" },
                    { "data": "subtotal", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); } },
                    { "data": "total_cicilan", "render": function(data){ return new Intl.NumberFormat('id-ID').format(data); }},        
                    { "data": null, "render": function(data, type, full, meta){
                            let sisa = (full.subtotal ?? 0) - (full.total_cicilan ?? 0);
                            return new Intl.NumberFormat('id-ID').format(sisa); 
                        }
                    },      
                    { "data": "notaorder", "render": function (data, type, full, meta){
                            let button = '';
                            let sisa = (Number(full.subtotal) ?? 0) - (Number(full.total_cicilan) ?? 0);
                            if (sisa >0) {
                                button += '<button type="button" class="btn btn-simple btn-info btn-icon btnBP" title="Cetak BP" data-notaorder="' + data + '"><i class="material-icons">account_balance</i></button>';
                                button += '<a href="<?=base_url()?>admin/wholesale/detailcicilan/'+data+'" class="btn btn-primary btn-sm btnDetail" ' +
                                          'data-notaorder="'+full.notaorder+'" ' +
                                          'data-partner="'+full.nama_partner+'" ' +
                                          'data-tanggal="'+full.tanggal+'" ' +
                                          'data-subtotal="'+full.subtotal+'" ' +
                                          'data-totalcicilan="'+full.total_cicilan+'" ' +
                                          'data-sisa="'+sisa+'">' +
                                          '<i class="fas fa-info"></i> Detail</a>';
                            }
                            return button;
                        }
                    }
                ]
            });

            table.on('error.dt', function(e, settings, techNote, message) {
                console.error("DataTables error:", message);
            });

            // === Handle Print Balance ===
            $('#table_data').on("click", ".btnBP", function () {
                const notaorder = $(this).data("notaorder");
                $.ajax({
                    url: "<?=base_url()?>/admin/wholesale/complete/"+notaorder,
                    type: "GET",
                    success: function (response) {
                        $('#table_data').DataTable().ajax.reload(); 
                        if (notaorder) {
                            window.open("<?=base_url('admin/wholesale/cetakbalancepayment')?>/" + notaorder, "_blank");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        alert("Gagal update status!");
                    }
                });
            });

            // === Handle Detail Button ===
            $('#table_data').on("click", ".btnDetail", function(){
                const detailData = {
                    notaorder: $(this).data("notaorder"),
                    partner: $(this).data("partner"),
                    tanggal: $(this).data("tanggal"),
                    subtotal: $(this).data("subtotal"),
                    total_cicilan: $(this).data("totalcicilan"),
                    sisa: $(this).data("sisa")
                };
                localStorage.setItem("detailCicilan", JSON.stringify(detailData));
            });
        }

        // =============== HALAMAN DETAIL CICILAN ==================
        if ($("form#form_cicilan").length) {
            console.log("Inisialisasi halaman Detail Cicilan...");

            const detailData = JSON.parse(localStorage.getItem("detailCicilan") || "{}");
            if (detailData && detailData.notaorder){
                $("#notaorder").val(detailData.notaorder);
                $("#customer").val(detailData.partner);
                $("#tanggal").val(detailData.tanggal);
                $("#total").val(new Intl.NumberFormat('id-ID').format(detailData.subtotal));
                $("#sisa").val(new Intl.NumberFormat('id-ID').format(detailData.sisa));
            }

            const tableDetail = $("#table_data").DataTable({
                searching: false,
                lengthChange: false,
                ordering: false,
                paging: false,
                info: false,
                ajax: {
                    url: "<?=base_url()?>admin/wholesale/detailcicilan",
                    type: "POST",
                    data: function(d){
                        d.nonota = $("#notaorder").val();
                    },
                    dataSrc: function (data){
                        return data;
                    }
                },
                drawCallback: function () {
                    const api = this.api();
                    let total = api.column(1).data().reduce(function(a,b){return a+Number(b)},0);
                    $(api.column(1).footer()).html("<b>" + new Intl.NumberFormat('id-ID').format(total) + "</b>");
                },
                columns: [
                    { data: "tanggal" },
                    { data: "bayar", render: function(data){ return new Intl.NumberFormat('id-ID').format(data); }}
                ]
            });
        }

    });
</script>
