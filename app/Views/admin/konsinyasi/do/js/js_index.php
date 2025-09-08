<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
    var table;
    $(function(){
        console.log("Inisialisasi DataTable untuk DO Konsinyasi...");

        table = $('#table_data').DataTable({
            "order": [[ 0, "asc" ]],
            "scrollX": true,
            "ajax": {
                "url": "<?=base_url()?>admin/konsinyasi/dolistdata",
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
                { "data": "nonota" },
                { "data": "partner" },
                { "data": "tanggal" },
                { "data": "total",
                    "render": function(data){
                        return new Intl.NumberFormat('id-ID').format(data); 
                    }
                },
                { 
                    "data": "nonota",
                    "render": function (data, type, full, meta){
                        let button = '';
                        button += '<button type="button" class="btn btn-simple btn-info btn-icon btnDetail" title="Detail Nota" data-nonota="' + data + '"><i class="material-icons">info_outline</i></button>';
                        button += '<button type="button" class="btn btn-simple btn-info btn-icon btnPrint" title="Print Ulang" data-nonota="' + data + '"><i class="material-icons">print</i></button>';
                        button += '<button type="button" class="btn btn-simple btn-danger btn-icon btnDelete" title="Hapus" data-nonota="' + data + '"><i class="material-icons">close</i></button>';
                        return button;
                    }
                }
            ]
        });

        table.on('error.dt', function(e, settings, techNote, message) {
            console.error("DataTables error:", message);
        });

        // === Handle Button Detail Nota ===
        $('#table_data').on("click", ".btnDetail", function () {
            const nonota = $(this).data("nonota");
            if (!nonota) return;

            // reset isi modal
            $("#detail_nonota, #detail_tanggal, #detail_partner, #detail_user").text("");
            $("#detail_tableBody").empty();
            $("#detail_totalJumlah, #detail_totalHarga").text("");

            $.ajax({
                url: "<?=base_url()?>admin/konsinyasi/notadodetail/" + nonota,
                type: "GET",
                dataType: "json",
                success: function(res){
                    if(res && res.header){
                        $("#detail_nonota").text(res.header.nonota);
                        $("#detail_tanggal").text(res.header.tanggal);
                        $("#detail_partner").text(res.header.nama_partner);
                        $("#detail_user").text(res.header.nama_user);

                        let totalJumlah = 0;
                        let totalHarga  = 0;

                        if(res.detail && res.detail.length > 0){
                            res.detail.forEach(function(item){
                                const harga = Number(item.harga) || 0;
                                const jumlah = Number(item.jumlah) || 0;
                                totalJumlah += jumlah;
                                totalHarga  += (jumlah * harga);

                                $("#detail_tableBody").append(`
                                    <tr>
                                        <td>${item.barcode}</td>
                                        <td>${item.namaproduk}</td>
                                        <td>${item.sku}</td>
                                        <td>${item.size ?? '-'}</td>
                                        <td>${item.warna ?? '-'}</td>
                                        <td class="text-center">${jumlah}</td>
                                        <td class="text-right">${new Intl.NumberFormat('id-ID').format(harga)}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            $("#detail_tableBody").append('<tr><td colspan="7" class="text-center">Tidak ada detail</td></tr>');
                        }

                        $("#detail_totalJumlah").text(totalJumlah);
                        $("#detail_totalHarga").text(new Intl.NumberFormat('id-ID').format(totalHarga));

                        $("#modal_detailDo").modal("show");
                    }
                },
                error: function(xhr, status, err){
                    console.error("Error ambil detail nota:", err);
                    alert("Gagal memuat detail nota.");
                }
            });
        });

        // === Handle Print Ulang ===
        $('#table_data').on("click", ".btnPrint", function () {
            const nonota = $(this).data("nonota");
            if (nonota) {
                window.open("<?=base_url('admin/konsinyasi/cetaknotado')?>/" + nonota, "_blank");
            }
        });

        // === Handle Hapus ===
        $('#table_data').on("click", ".btnDelete", function () {
            const nonota = $(this).data("nonota");
            const encoded = btoa(nonota);
            $("#nonotaDoToDelete").text(nonota);
            $("#nonotaDoHidden").val(encoded);
            $("#modal_deleteDo").modal("show");
        });

        $("#confirmDeleteBtn").on("click", function () {
            const encodedNota = $("#nonotaDoHidden").val();
            if (encodedNota) {
                window.location.href = "<?=base_url()?>admin/konsinyasi/dohapus/" + encodedNota;
            }
        });
    });
</script>

