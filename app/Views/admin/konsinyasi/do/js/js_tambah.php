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
            { targets: [0,1,2,3,4,5,6], className: "text-center" }
        ],
        drawCallback: function(settings) {
            const api = this.api();
            const totalColIndex = 5; // kolom "Total" (0-based)

            // ambil data untuk halaman saat ini (pakai {page:'current'} kalau mau per-halaman)
            // atau hapus opsi page untuk semua data yang ada di client
            const columnData = api.column(totalColIndex, { page: 'current' }).data();

            // helper parse: ambil nilai dari input[name='total[]'] jika ada, atau bersihkan format ribuan
            function parseCellValue(cellValue){
                // coba parse sebagai HTML dan cari input[name='total[]']
                try {
                    const tmp = document.createElement('div');
                    tmp.innerHTML = cellValue;
                    const input = tmp.querySelector("input[name='total[]']");
                    if(input && input.value !== undefined){
                        return parseFloat(input.value) || 0;
                    }
                } catch(e){ /* ignore */ }

                // fallback: bersihkan string, support format "1.234" (ribuan) dan "1.234,56"
                let s = String(cellValue || '').trim();
                s = s.replace(/[^0-9\-\.,]/g, ''); // sisakan digit, minus, titik, koma
                if(s.indexOf(',') !== -1 && s.indexOf('.') !== -1){
                    // anggap format "1.234,56" -> "1234.56"
                    s = s.replace(/\./g, '').replace(',', '.');
                } else {
                    // hapus titik ribuan, ganti koma desimal
                    s = s.replace(/\./g, '').replace(/,/g, '.');
                }
                return parseFloat(s) || 0;
            }

            // hitung sum dengan reduce
            const sum = columnData.reduce(function(a, b){
                return parseFloat(a) + parseFloat(parseCellValue(b));
            }, 0);

            // tampilkan di footer dan/atau input subtotal
            const formatted = (Math.round((sum + Number.EPSILON) * 100) / 100).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            // update tfoot (kolom total)
            $(api.column(totalColIndex).footer()).html(formatted);

            // jika kamu punya input #subtotal, update juga
            $("#subtotal").val(formatted);
        }
    });


    // current product size-stock map (updated on product change)
    let currentSizeStock = {}; // { "S":10, "M":5, ... }

    // format angka dengan ribuan (Indonesia pakai ".")
    function formatNumber(num) {
        if (isNaN(num) || num === null) return "0";
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // parse data-sizestok (terima format "S:10,M:5" atau JSON)
    function parseSizeStock(s){
        let map = {};
        if(!s) return map;
        s = s.toString().trim();
        try {
            if(s.startsWith("{") || s.startsWith("[")){
                let obj = JSON.parse(s);
                // if array of pairs or object
                if(Array.isArray(obj)){
                    obj.forEach(it => { if(it.size) map[it.size] = parseInt(it.stok||0); });
                } else {
                    Object.keys(obj).forEach(k => map[k] = parseInt(obj[k]||0));
                }
            } else {
                // expected "S:10,M:5,L:0"
                s.split(",").forEach(p => {
                    let [sz,qty] = p.split(":").map(x => x && x.trim());
                    if(sz) map[sz] = parseInt(qty || 0);
                });
            }
        } catch(e){
            // fallback: try comma-only sizes (no stok info)
            s.split(",").forEach(sz => { sz = sz.trim(); if(sz) map[sz] = 0; });
        }
        return map;
    }

    // cek apakah produk tersedia (kalau tidak, sembunyikan tombol +Tambah)
    function checkProdukAvailable(){
        let anyAvailable = Object.values(currentSizeStock).some(v => (parseInt(v) || 0) > 0);
        if(!anyAvailable){
            $("#btnAdd").hide();
        } else {
            // jika user belum pilih size tapi ada availability, tunjukkan tombol
            // (but tombol tetap dicegah saat size dipilih tapi stok=0)
            $("#btnAdd").show();
        }
    }

    // ketika produk dipilih -> populate size + set max jumlah default (jika ada satu size)
    $("#produk").change(function(){
        let selected = $(this).find(":selected");
        let harga = selected.data("harga");
        let sizeStr = selected.data("size");      // fallback sizes like "S,M,L"
        let sizeStokStr = selected.data("sizestok"); // e.g. "S:10,M:5" or JSON
        currentSizeStock = parseSizeStock(sizeStokStr);

        // clear & populate size select
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');

        // prefer sizes from currentSizeStock keys; else fall back to data-size
        let sizes = Object.keys(currentSizeStock);
        if(sizes.length === 0 && sizeStr){
            sizes = sizeStr.toString().split(",").map(s => s.trim()).filter(s => s);
            // set default stock 0 for these
            sizes.forEach(sz => { if(!(sz in currentSizeStock)) currentSizeStock[sz] = 0; });
        }

        sizes.forEach(sz => {
            let stok = parseInt(currentSizeStock[sz] || 0);
            $("#size").append(`<option value="${sz}" data-stok="${stok}">${sz} ${stok>0?`(stok ${stok})`: `(kosong)`}</option>`);
        });

        // set harga field (if available)
        if(harga !== undefined && harga !== null && harga !== ''){
            $("#harga").val(harga);
        } else {
            $("#harga").val("");
        }

        // reset jumlah and remove max (will be set after size chosen)
        $("#jumlah").val(1).removeAttr("max");

        checkProdukAvailable();
    });

    // ketika size dipilih -> set max jumlah berdasarkan stok per size
    $("#size").change(function(){
        let sel = $(this).find(":selected");
        let stok = parseInt(sel.data("stok") || 0);
        if(isNaN(stok)) stok = 0;
        $("#jumlah").attr("max", stok);
        // set default jumlah ke stok (jika stok>0) atau 1
        let cur = parseInt($("#jumlah").val()) || 1;
        if(stok === 0){
            $("#jumlah").val(1);
            // jangan sembunyikan tombol, tapi mencegah add di saat klik
        } else if(cur > stok){
            $("#jumlah").val(stok);
        } else {
            // jika user belum ubah jumlah, set ke stok agar memudahkan
            $("#jumlah").val(1);
        }
        checkProdukAvailable();
    });

    // Validasi jumlah ketika user ubah manual
    $("#jumlah").on("input", function(){
        let max = parseInt($(this).attr("max")) || 0;
        let val = parseInt($(this).val()) || 0;
        let namaProduk = $("#produk option:selected").text();

        if(max > 0 && val > max){
            alert("Stok " + namaProduk.trim() + " tersisa " + max);
            $(this).val(max); // kembalikan ke max
        } else if(val < 1){
            $(this).val(1); // biar ga bisa nol/negatif
        }
    });

    // Tambah ke grid
    $("#btnAdd").click(function(){
        let barcode = $("#produk").val();
        let nama    = $("#produk option:selected").text();
        let size    = $("#size").val();
        let jumlah  = parseInt($("#jumlah").val()) || 0;
        let harga   = parseInt($("#harga").val()) || 0;
        let maxJumlah = parseInt( (currentSizeStock[size] || 0) );

        if(!barcode || !size || !jumlah || !harga){
            alert("Produk, size, jumlah & harga wajib diisi!");
            return;
        }

        if(maxJumlah <= 0){
            alert("Size " + size + " tidak tersedia (stok 0).");
            return;
        }

        // cek apakah produk+size sudah ada di DataTable
        let rowFound = null;
        table.rows().nodes().each(function(){
            let node = this; // tr node
            let existingBarcode = $(node).find("input[name='barcode[]']").val();
            let existingSize    = $(node).find("input[name='size[]']").val();
            if(existingBarcode === barcode && existingSize === size){
                rowFound = table.row(node);
            }
        });

        if(rowFound){
            // update jumlah lama
            let node = rowFound.node();
            let oldJumlah = parseInt($(node).find("input[name='jumlah[]']").val()) || 0;
            let newJumlah = oldJumlah + jumlah;

            if(newJumlah > maxJumlah){
                alert("Jumlah melebihi stok, dibatasi " + maxJumlah);
                newJumlah = maxJumlah;
            }

            let total = newJumlah * harga;

            // update row DOM
            let cells = $(node).find("td");
            cells.eq(0).html(`<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`);
            cells.eq(1).html(nama);
            cells.eq(2).html(`<input type="hidden" name="size[]" value="${size}">${size}`);
            cells.eq(3).html(`<input type="hidden" name="jumlah[]" value="${newJumlah}">${newJumlah}`);
            cells.eq(4).html(`<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`);
            cells.eq(5).html(`<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`);
        }else{
            // insert row baru (include size column)
            let total = jumlah * harga;
            table.row.add([
                `<input type="hidden" name="barcode[]" value="${barcode}">${barcode}`,
                nama,
                `<input type="hidden" name="size[]" value="${size}">${size}`,
                `<input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}`,
                `<input type="hidden" name="harga[]" value="${harga}">${formatNumber(harga)}`,
                `<input type="hidden" name="total[]" value="${total}">${formatNumber(total)}`,
                `<button type="button" class="btn btn-danger btn-sm btnDelete">x</button>`
            ]).draw(false);
        }

        // Reset input
        $("#produk").val("").trigger("change");
        $("#size").empty().append('<option value="" disabled selected>-- Pilih Size --</option>');
        $("#jumlah").val(1).removeAttr("max");
        $("#harga").val("");

        checkProdukAvailable(); // cek lagi setelah reset
    });

    // Hapus baris
    $("#table_data tbody").on("click", ".btnDelete", function(){
        let row    = $(this).closest("tr");
        table.row(row).remove().draw(false);
    });


    // Submit form (perbaikan id form)
    $("#form_order").submit(function(e){
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
                    // buka cetak
                    window.open("<?=base_url('admin/konsinyasi/cetaknotado')?>/" + res.nonota, "_blank");
                    // kembali ke index
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
