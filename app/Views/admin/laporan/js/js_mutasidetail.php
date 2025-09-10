<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>

<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
var table;
	table = $('#table_data').DataTable({
			"order": [[ 0, "asc" ]],
            "pageLength": 50,
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "scrollX": true,
			"ajax": {
				"url": "<?=base_url()?>admin/laporan/listmutasidetail",
				"type": "POST",
				"data": {
				    bulan   : function(){return $("#bulan").val()},
				    tahun   : function(){return $("#tahun").val()},
				    brand : function(){return $("#brand").val()},
				    kategori : function(){return $("#kategori").val()},
				    storeid : function(){return $("#store").val()}
				},
				"dataSrc":function (data){
						return data;
					  }
			},
			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api();

				// fungsi helper parse angka
				var numVal = function (val) {
					return typeof val === 'string' ? parseFloat(val.replace(/[^0-9.-]/g, '')) || 0 : (typeof val === 'number' ? val : 0);
				};

				// hitung per kolom
				var total_awal = api.column(2, { filter:'applied'}).data()
					.reduce(function(a, b){ return numVal(a) + numVal(b); }, 0);

				var total_masuk = data.reduce(function(a, row){
					return a + (Number(row.produksi_in) + Number(row.pindah_in));
				}, 0);

				var total_keluar = data.reduce(function(a, row){
					return a + (Number(row.pindah_out) + Number(row.pinjam_out));
				}, 0);

				var total_jual = data.reduce(function(a, row){
					return a + (
						Number(row.wholesale_out) +
						Number(row.consignment_sold) +
						Number(row.consignment_sold_non) +
						Number(row.penjualan)
					);
				}, 0);

				var total_retur = data.reduce(function(a, row){
					return a + (Number(row.retur) + Number(row.retur_konsinyasi_in));
				}, 0);

				var total_konsinyasi = data.reduce(function(a, row){
					return a + (
						Number(row.do_konsinyasi_out) -
						Number(row.consignment_sold)
					);
				}, 0);

				var total_sesuai = api.column(8, { filter:'applied'}).data()
					.reduce(function(a, b){ return numVal(a) + numVal(b); }, 0);

				var total_sisa = api.column(9, { filter:'applied'}).data()
					.reduce(function(a, b){ return numVal(a) + numVal(b); }, 0);

				// isi footer
				$(api.column(2).footer()).html(total_awal.toLocaleString("en"));
				$(api.column(3).footer()).html(total_masuk.toLocaleString("en"));
				$(api.column(4).footer()).html(total_keluar.toLocaleString("en"));
				$(api.column(5).footer()).html(total_jual.toLocaleString("en"));
				$(api.column(6).footer()).html(total_retur.toLocaleString("en"));
				$(api.column(7).footer()).html(total_konsinyasi.toLocaleString("en"));
				$(api.column(8).footer()).html(total_sesuai.toLocaleString("en"));
				$(api.column(9).footer()).html(total_sisa.toLocaleString("en"));
			},

			"columns": [
				{ "data": "namaproduk" },
				{ "data": "size" },
				{ "data": "stok_awal" },
				{ "data": null, "render": function (d,t,row){ return Number(row.produksi_in) + Number(row.pindah_in); }},
				{ "data": null, "render": function (d,t,row){ return Number(row.pindah_out) + Number(row.pinjam_out); }},
				{ "data": null, "render": function (d,t,row){ 
					return Number(row.wholesale_out) +
							Number(row.consignment_sold) +
							Number(row.consignment_sold_non) +
							Number(row.penjualan);
				}},
				{ "data": null, "render": function (d,t,row){ return Number(row.retur) + Number(row.retur_konsinyasi_in); }},
				{ "data": null, "render": function (d,t,row){ return Number(row.do_konsinyasi_out) - Number(row.consignment_sold); }},
				{ "data": "penyesuaian" },
				{ "data": "sisa" }
			]
	});
	
	$("#lihat").on("click",function(){
	    table.ajax.reload();
	})
</script>