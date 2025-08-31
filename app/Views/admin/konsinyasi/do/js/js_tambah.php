<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
    $(document).ready(function(){
        $(".select2").select2();

        // Init DataTable
        let table = $("#table_data").DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            columnDefs: [
                { targets: [0,2,3,4], className: "text-center" }
            ]
        });

        // === fungsi format angka dengan ribuan (Indonesia pakai ".")
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // === fungsi cek apakah produk tersedia (kalau tidak, sembunyikan tombol +Tambah)
        function checkProdukAvailable() {
            let selected = $("#produk").find(":selected");
            let harga = selected.data("harga");
            let maxJumlah = selected.data("jumlah");

            if (!harga || !maxJumlah || maxJumlah <= 0) {
                $("#btnAdd").hide();   // sembunyikan tombol
            } else {
                $("#btnAdd").show();   // tampilkan tombol
            }
        }

        // === Isi harga & jumlah otomatis saat pilih produk
        $("#produk").change(function(){
            let selected = $(this).find(":selected");
            let harga = selected.data("harga");
            let maxJumlah = selected.data("jumlah");

            // kalau bukan produk valid, jangan apa-apa
            if (!harga || !maxJumlah) {
                $("#jumlah").val(1).removeAttr("max");
                $("#harga").val("");
                checkProdukAvailable();
                return;
            }

            $("#jumlah").val(maxJumlah);
            $("#harga").val(harga);

            $("#jumlah").attr("max", maxJumlah); // set max attribute

            checkProdukAvailable(); // cek tombol +Tambah
        });

        // === Validasi jumlah ketika user ubah manual
        $("#jumlah").on("input", function(){
            let max = parseInt($(this).attr("max")) || 0;
            let val = parseInt($(this).val()) || 0;
            let namaProduk = $("#produk option:selected").text();

            if(val > max){
                alert("Hanya boleh input jumlah " + namaProduk + " sebanyak " + max);
                $(this).val(max); // kembalikan ke max
            } else if(val < 1){
                $(this).val(1); // biar ga bisa nol/negatif
            }
        });

        // === Tambah ke grid
        $("#btnAdd").click(function(){
            let barcode = $("#produk").val();
            let nama    = $("#produk option:selected").text();
            let jumlah  = parseInt($("#jumlah").val());
            let harga   = parseInt($("#harga").val());
            let maxJumlah = parseInt($("#produk option:selected").data("jumlah"));

            if(!barcode || !jumlah || !harga){
                alert("Produk, jumlah & harga wajib diisi!");
                return;
            }

            // cek apakah produk sudah ada di DataTable
            let rowFound = null;
            table.rows().every(function(){
                let row = this.data();
                let existingBarcode = $(row[0]).filter("input").val();
                if(existingBarcode === barcode){
                    rowFound = this;
                }
            });

            if(rowFound){
                // update jumlah lama
                let oldJumlah = parseInt($(rowFound.data()[2]).filter("input").val());
                let newJumlah = oldJumlah + jumlah;

                if(newJumlah > maxJumlah){
                    alert("Jumlah melebihi stok, dibatasi " + maxJumlah);
                    newJumlah = maxJumlah;
                }

                let total = newJumlah * harga;

                // update row di DataTable
                rowFound.data([
                    `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                    nama,
                    `<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`,
                    `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                    `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                    `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
                ]).draw(false);

            }else{
                // insert row baru
                let total = jumlah * harga;
                table.row.add([
                    `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                    nama,
                    `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                    `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                    `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                    `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
                ]).draw(false);
            }

            // Reset input
            $("#produk").val("").trigger("change");
            $("#jumlah").val(1);
            $("#harga").val("");

            updateSubtotal();
            checkProdukAvailable(); // cek lagi setelah reset
        });

        // === Hapus baris
        $("#table_data tbody").on("click", ".btnDelete", function(){
            let row    = $(this).closest("tr");
            table.row(row).remove().draw(false);
            updateSubtotal();
        });

        // === Hitung subtotal
        function updateSubtotal(){
            let subtotal = 0;
            // ambil semua hidden input total[]
            $("input[name='total[]']").each(function(){
                subtotal += parseFloat($(this).val());
            });
            $("#subtotal").text(formatNumber(subtotal));
        }

        // === Submit form
        $("#form_do").submit(function(e){
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "<?=base_url('admin/konsinyasi/add-data-do')?>",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(res){
                    if(res.status){
                        alert("DO Konsinyasi berhasil disimpan!");
                        window.location.href = "<?=base_url('admin/konsinyasi/do')?>";
                    }else{
                        alert(res.message);
                    }
                },
                error: function(xhr){
                    alert("Terjadi kesalahan server!\n" + xhr.responseText);
                }
            });
        });

        // jalankan sekali di awal
        checkProdukAvailable();
    });
</script>
