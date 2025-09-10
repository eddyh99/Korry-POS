<!-- Select2 -->
<link rel="stylesheet" href="<?=base_url()?>assets/bootstrap/plugins/select2/css/select2.min.css">
<style>
#table_retur tbody tr{
  cursor: pointer;
}
.hanaka-row {
  width:100%;
  height: 100%;
}

.hanaka-button{
	height:100px;
	width:100px;
	border-radius: 10px;
}

.hanaka-col{
	height:120px;
	width:120px;
	float: left;
}

.hanaka-space{
	padding-left: 5px;
}
</style>

<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<link href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
    var barangretur = [];
    var tableretur = $('#table_retur').DataTable({
        "scrollX": true,
        "order": [[1, 'asc']],
        "ajax": {
            "url": "<?=base_url()?>staff/retur/listretur",
            "type": "POST",
            "data": { key: $("#key").val() },
            "dataSrc": function (data) {
                return data;
            }
        },
        "columnDefs": [
            {
                "orderable": false,
                "targets": 0,
                "render": function () {
                    return "<input type='checkbox'>";
                }
            },
            { "data": "barcode",   "targets": 1 },
            { "data": "namaproduk","targets": 2 },
            { "data": "namabrand", "targets": 3 },
            { "data": "size",      "targets": 4 },
            { "data": "jumlah",    "targets": 5 },
            { 
                "data": "harga",   "targets": 6,
                "render": $.fn.dataTable.render.number(',', '.', 0, '')
            },
            { 
                "data": "total",   "targets": 7,
                "render": $.fn.dataTable.render.number(',', '.', 0, '')
            },
        ]
    });

    // event checkbox
    $('#table_retur tbody').on('click', 'input[type="checkbox"]', function(e) {
        var $row = $(this).closest('tr');
        var data = tableretur.row($row).data();

        if (this.checked) {
            barangretur.push([data.barcode, data.size, data.jumlah]);
            $row.addClass('selected');
        } else {
            for (i = 0; i < barangretur.length; i++) {
                if (barangretur[i][0] == data.barcode) {
                    barangretur.splice(i, 1);
                }
            }
            $row.removeClass('selected');
        }

        localStorage.setItem('returbrg', JSON.stringify(barangretur));
        e.stopPropagation();
    });

    // klik baris = toggle checkbox
    $('#table_retur').on('click', 'tbody td, thead th:first-child', function(e) {
        $(this).parent().find('input[type="checkbox"]').trigger('click');
    });

    // tombol simpan retur
    $("#btnpayment").on("click", function() {
        var Objectretur = JSON.parse(localStorage.getItem('returbrg'));
        if (!Objectretur || Objectretur.length === 0) {
            alert("Pilih barang yang akan diretur terlebih dahulu!");
            return;
        }

        var barangreturJSON = JSON.stringify(Objectretur);

        $.ajax({
            url: "<?=base_url()?>staff/retur/addretur",
            type: "post",
            data: {
                id: $("#key").val(),
                memberid: $("#memberid").val(),
                method: "cash",   // default, bisa diganti dropdown kalau mau
                fee: 0,           // default, kalau ga ada fee
                barang: "[]",     // kosong, karena retur ini tidak ada transaksi jual baru
                brgretur: barangreturJSON
            },
            success: function(data) {
                alert("Retur berhasil disimpan!");
                window.location.href = "<?=base_url()?>staff/retur";
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                alert("Terjadi kesalahan saat menyimpan retur.");
            }
        });
    });
</script>
