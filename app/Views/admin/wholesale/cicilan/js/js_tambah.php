<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/2.3.3/api/sum().js"></script>
<script>
    $(document).ready(function(){

        // Init Select2
        $("#notaorder").select2();

        // Init DataTable
        const table = $("#table_data").DataTable({
            searching: false, 
            lengthChange: false,
            ordering: false,
            paging: false,
            info: false,
            ajax: {
                url: "<?=base_url()?>admin/wholesale/detailcicilan",
                type: "POST",
                data: function(d){
                    d.nonota = $("#notaorder").val(); // perbaikan selector
                },
                dataSrc: function (data){
                    console.log("Data diterima dari server:", data);
                    return data;                            
                },
                error: function (xhr, error, code) {
                    console.error("AJAX error:", error, "Code:", code, "Response:", xhr.responseText);
                }
            },
            drawCallback: function () {
                var api = this.api();

                // Hitung jumlah seluruh data (overall, semua page)
                var total = api.column(1).data().sum();

                // Ambil subtotal dari input, lalu bersihkan titik/koma jadi angka murni
                let subtotal = $("#total").val() || "0";
                subtotal = parseFloat(subtotal.replace(/\./g, '').replace(/,/g, '.')) || 0;

                // Hitung sisa
                let sisa = subtotal - total;

                // Set hasil ke input #sisa (format ribuan Indonesia)
                $("#sisa").val(new Intl.NumberFormat('id-ID').format(sisa));

                // Format angka ke ribuan lokal Indonesia
                var formatted = new Intl.NumberFormat('id-ID').format(total);

                // Tulis hanya ke kolom ke-2 (index 1) di footer
                $(api.column(1).footer()).html(formatted);
            },
            columns: [
                { data: "tanggal" },
                { 
                    data: "bayar",
                    render: function(data, type, row){
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row){
                        return `
                            <button type="button" class="btn btn-danger btn-sm btnHapus" data-id="${row.id}">
                                Hapus
                            </button>
                        `;
                    }
                }
            ]
        });

        // reload tabel setiap ganti nota order
        $("#notaorder").on("change", function(){
            // ambil data-total dari option yang dipilih
            const total = Number($(this).find(":selected").data("total")) || 0;

            // format ke ribuan lokal Indonesia
            $("#total").val(total.toLocaleString("id-ID"));

            table.ajax.reload();
        });


        // contoh handler klik hapus
        $("#table_data").on("click", ".btnHapus", function(){
            const id = $(this).data("id");
            if(confirm("Yakin hapus cicilan ini?")){
                $.post("<?=base_url()?>admin/wholesale/cicilanhapus", { id:id }, function(res){
                    console.log("Hapus response:", res);
                    table.ajax.reload();
                });
            }
        });

    });



</script>
