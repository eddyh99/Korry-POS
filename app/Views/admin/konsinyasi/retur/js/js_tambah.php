<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
	$(document).ready(function(){
		$(".select2").select2();

		let productsCache = {};
		let availableProducts = {}; // state sisa produk
		let $doSelect = $("#do_konsinyasi");
		let $produkSelect = $("#produk");
		let $jumlahInput = $("#jumlah");
		let $alasanInput = $("#alasan");
		let $btnAdd = $("#btnAdd");
		let $tableBody = $("#table_data tbody");

		// Ambil list DO di awal
		$.ajax({
			url: "<?=base_url('admin/konsinyasi/listdo')?>",
			type: "GET",
			dataType: "json",
			success: function(res){
				let html = '<option value="" disabled selected>--Pilih No. Nota--</option>';
				res.forEach(item => {
					html += `<option value="${item.do_id}">${item.do_id}</option>`;
				});
				$doSelect.html(html);
			},
			error: function(xhr){
				alert("Gagal load daftar DO!\n" + xhr.responseText);
			}
		});

		// Load produk by DO
		$doSelect.on("change", function(){
			let do_id = $(this).val();
			if(!do_id) return;
			$.ajax({
				url: "<?=base_url('admin/konsinyasi/listprodukbydo')?>",
				type: "POST",
				data: { do_id: do_id },
				dataType: "json",
				success: function(res){
					availableProducts = {};
					res.forEach(p => {
						availableProducts[p.barcode] = {
							nama: p.nama,
							sisa: parseInt(p.sisa)
						};
					});
					renderProdukOptions();
					$tableBody.empty();
				},
				error: function(xhr){
					alert("Gagal load produk dari DO!\n" + xhr.responseText);
				}
			});
		});

		// Render dropdown produk
		function renderProdukOptions(){
			let html = '<option value="" disabled selected>--Pilih Produk--</option>';
			Object.keys(availableProducts).forEach(barcode => {
				let p = availableProducts[barcode];
				if(p.sisa > 0){
					html += `<option value="${barcode}">${p.nama} - ${barcode} (Max: ${p.sisa})</option>`;
				}
			});
			$produkSelect.html(html).trigger("change");

			// kontrol tombol +Tambah
			if(Object.keys(availableProducts).filter(b => availableProducts[b].sisa > 0).length === 0){
				$btnAdd.hide();
			} else {
				$btnAdd.show();
			}
		}

		// Tambah produk ke table
		$btnAdd.on("click", function(){
			let barcode = $produkSelect.val();
			let jumlah = parseInt($jumlahInput.val());
			let alasan = $alasanInput.val().trim();

			if(!barcode){
				alert("Silakan pilih produk!");
				return;
			}
			if(!jumlah || jumlah < 1){
				alert("Jumlah harus minimal 1!");
				return;
			}
			if(jumlah > availableProducts[barcode].sisa){
				alert("Jumlah retur tidak boleh lebih dari " + availableProducts[barcode].sisa);
				return;
			}

			// Tambahkan row
			let nama = availableProducts[barcode].nama;
			$tableBody.append(`
				<tr data-barcode="${barcode}" data-jumlah="${jumlah}">
					<td>${barcode}</td>
					<td>${nama}</td>
					<td>${jumlah}</td>
					<td>${alasan}</td>
					<td><button type="button" class="btn btn-danger btn-sm btnDelete">x</button></td>
					<input type="hidden" name="details[${barcode}][barcode]" value="${barcode}">
					<input type="hidden" name="details[${barcode}][jumlah]" value="${jumlah}">
					<input type="hidden" name="details[${barcode}][alasan]" value="${alasan}">
				</tr>
			`);

			// Kurangi stok sisa
			availableProducts[barcode].sisa -= jumlah;

			// reset input
			$produkSelect.val("").trigger("change");
			$jumlahInput.val(1);
			$alasanInput.val("");

			renderProdukOptions();
		});

		// Hapus row
		$tableBody.on("click", ".btnDelete", function(){
			let $row = $(this).closest("tr");
			let barcode = $row.data("barcode");
			let jumlah = parseInt($row.data("jumlah"));

			// kembalikan jumlah ke stok sisa
			availableProducts[barcode].sisa += jumlah;

			$row.remove();
			renderProdukOptions();
		});

		// Submit form
		$("#form_retur").on("submit", function(e){
			e.preventDefault();
			$.ajax({
				url: "<?=base_url('admin/konsinyasi/add-data-retur')?>",
				type: "POST",
				data: $(this).serialize(),
				dataType: "json",
				success: function(res){
					if(res.status){
						alert("Nota Konsinyasi berhasil disimpan!");
						window.location.href = "<?=base_url('admin/konsinyasi/retur')?>";
					} else {
						alert(res.message || "Gagal simpan data");
					}
				},
				error: function(xhr){
					alert("Terjadi kesalahan server!\n" + xhr.responseText);
				}
			});
		});
	});
</script>
