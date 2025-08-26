<!-- Select2 -->
<script src="<?=base_url()?>assets/bootstrap/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.25/api/sum().js"></script>

<script>
$(document).ready(function(){
    // init select2
    $(".select2").select2();

    const $doSelect = $("#do_konsinyasi");
    const $produkSelect = $("#produk");
    const $tableBody = $("#table_data tbody");

    // cache produk per DO (data dari server: { barcode, nama, sisa })
    const productsCache = {};

    function optionLabel(barcode, nama, sisa){
        return `${barcode} - ${nama} (Sisa: ${sisa})`;
    }

    function refreshProdukSelectHtml(html, keepSelect2 = true){
        // refresh produk select safely (destroy & re-init select2 to reflect options reliably)
        try {
            $produkSelect.select2('destroy');
        } catch(e){}
        $produkSelect.html(html);
        // re-init select2 only for produk (others remain)
        $produkSelect.select2();
    }

    function getTempUsageMap(do_id){
        // hitung total jumlah yang sudah ada di temporary table untuk do_id, per barcode
        const map = {};
        $tableBody.find(`tr[data-do="${do_id}"]`).each(function(){
            const bc = $(this).data('barcode');
            const j = parseInt($(this).find("input[name='jumlah[]']").val()) || 0;
            map[bc] = (map[bc] || 0) + j;
        });
        return map;
    }

    function checkBtnVisibility(){
        if ($produkSelect.find("option").length <= 1) $("#btnAdd").hide();
        else $("#btnAdd").show();
    }

    function setJumlahMaxFromSelected(){
        const $opt = $produkSelect.find("option:selected");
        const max = parseInt($opt.data("max")) || 0;
        if(max > 0){
            $("#jumlah").attr("max", max);
            const cur = parseInt($("#jumlah").val()) || 1;
            if(cur > max) $("#jumlah").val(max);
        } else {
            $("#jumlah").removeAttr("max");
        }
    }

    // render produk dropdown dari serverData (mengurangi pemakaian di temp table)
    function renderProdukDropdown(serverData, do_id){
        const used = getTempUsageMap(do_id);
        let html = '<option value="" disabled selected>--Pilih Produk--</option>';
        serverData.forEach(function(p){
            const sisaServer = parseInt(p.sisa) || 0;
            const sisaClient = sisaServer - (used[p.barcode] || 0);
            if(sisaClient > 0){
                html += `<option value="${p.barcode}" data-max="${sisaClient}" data-nama="${p.nama}" data-do="${do_id}">${optionLabel(p.barcode, p.nama, sisaClient)}</option>`;
            }
        });

        refreshProdukSelectHtml(html);
        setJumlahMaxFromSelected();
        checkBtnVisibility();

        // jika tidak ada produk tersisa untuk DO ini -> hapus DO dari dropdown
        if ($produkSelect.find("option").length <= 1) {
            // remove DO option if present
            $doSelect.find(`option[value="${do_id}"]`).remove();
            // reset DO select if it was selected
            if ($doSelect.val() == do_id) {
                $doSelect.val("").trigger("change");
            }
        }
    }

    // helper: ambil products dari server (atau dari cache) lalu callback(data)
    function ensureProductsForDo(do_id, callback){
        if (!do_id) return callback([]);
        if (productsCache[do_id]) return callback(productsCache[do_id]);

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/listprodukbydo')?>",
            type: "POST",
            data: { do_id: do_id },
            dataType: "json",
            success: function(res){
                productsCache[do_id] = res;
                callback(res);
            },
            error: function(xhr){
                alert("Gagal load produk dari DO!\n" + xhr.responseText);
                callback([]);
            }
        });
    }

    // === Load awal DO yang punya sisa (server-side) ===
    $.ajax({
        url: "<?=base_url('admin/konsinyasi/listdo')?>",
        type: "GET",
        dataType: "json",
        success: function(res){
            let html = '<option value="" disabled selected>--Pilih DO Konsinyasi--</option>';
            res.forEach(function(item){
                html += `<option value="${item.do_id}">${item.do_id}</option>`;
            });
            $doSelect.html(html).trigger("change");
        },
        error: function(xhr){
            alert("Gagal load daftar DO!\n" + xhr.responseText);
        }
    });

    // when DO changed -> ensure cache then render produk (serverData minus temp usage)
    $doSelect.on("change", function(){
        const do_id = $(this).val();
        if(!do_id){
            refreshProdukSelectHtml('<option value="" disabled selected>--Pilih Produk--</option>');
            checkBtnVisibility();
            return;
        }
        ensureProductsForDo(do_id, function(data){
            renderProdukDropdown(data, do_id);
        });
    });

    // update jumlah max when product selected
    $produkSelect.on("change", setJumlahMaxFromSelected);

    // === Add button handler ===
    $("#btnAdd").on("click", function(){
        const $opt = $produkSelect.find("option:selected");
        const barcode = $opt.val();
        const nama = $opt.data("nama");
        const max = parseInt($opt.data("max")) || 0;
        const do_id = $opt.data("do");
        const jumlah = parseInt($("#jumlah").val()) || 0;

        if(!barcode){
            alert("Pilih produk dulu!");
            return;
        }
        if(jumlah <= 0){
            alert("Jumlah harus lebih dari 0!");
            return;
        }
        if(jumlah > max){
            alert("Jumlah tidak boleh melebihi " + max);
            return;
        }

        // cek existing row (same do + barcode)
        let $existingRow = $tableBody.find(`tr[data-do="${do_id}"][data-barcode="${barcode}"]`);
        if($existingRow.length){
            // merge jumlah
            let oldJumlah = parseInt($existingRow.find("input[name='jumlah[]']").val()) || 0;
            let newJumlah = oldJumlah + jumlah;
            // serverSisa = (find in cache) OR fallback to max+oldJumlah
            let serverSisa = max + oldJumlah; // maximal allowed total for this barcode at moment computed by previous render
            if(newJumlah > serverSisa){
                alert("Total jumlah tidak boleh melebihi " + serverSisa);
                return;
            }
            $existingRow.find("input[name='jumlah[]']").val(newJumlah);
            $existingRow.find("td:eq(3)").text(newJumlah);
        } else {
            // add new row
            const row = `
                <tr data-barcode="${barcode}" data-do="${do_id}">
                    <td><input type="hidden" name="do_id[]" value="${do_id}">${do_id}</td>
                    <td><input type="hidden" name="barcode[]" value="${barcode}">${barcode}</td>
                    <td>${nama}</td>
                    <td><input type="hidden" name="jumlah[]" value="${jumlah}">${jumlah}</td>
                    <td><button type="button" class="btn btn-danger btn-sm btnDelete">x</button></td>
                </tr>`;
            $tableBody.append(row);
        }

        // after mutate temp-table, re-render produk dropdown for this DO from cache (serverData - tempUsage)
        if (productsCache[do_id]) {
            renderProdukDropdown(productsCache[do_id], do_id);
        } else {
            // fallback: fetch then render
            ensureProductsForDo(do_id, function(data){
                renderProdukDropdown(data, do_id);
            });
        }

        // clear selection + jumlah
        $produkSelect.val("").trigger("change");
        $("#jumlah").val(1);
    });

    // === Delete row handler (restore sisa to dropdown) ===
    $(document).on("click", ".btnDelete", function(){
        const $tr = $(this).closest("tr");
        const barcode = $tr.data("barcode");
        const do_id = $tr.data("do");
        const nama = $tr.find("td:eq(2)").text();
        const jumlah = parseInt($tr.find("td:eq(3)").text()) || 0;

        // ensure DO option exists
        if ($doSelect.find(`option[value="${do_id}"]`).length === 0) {
            $doSelect.append(`<option value="${do_id}">${do_id}</option>`);
        }

        // remove the row first (so getTempUsageMap becomes accurate)
        $tr.remove();

        // ensure we have server data cached, then re-render dropdown for this DO,
        // and select the restored barcode so user can continue
        ensureProductsForDo(do_id, function(data){
            // render will compute sisa = serverSisa - tempUsageAfterDeletion
            renderProdukDropdown(data, do_id);

            // if the product still available now, select it
            if ($produkSelect.find(`option[value="${barcode}"]`).length) {
                $produkSelect.val(barcode).trigger("change");
            } else {
                // else no option for that barcode (odd) — nothing to select
                $produkSelect.val("").trigger("change");
            }
        });

        checkBtnVisibility();
    });

    // === Submit Nota Konsinyasi ===
    $("#form_nota").on("submit", function(e){
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: "<?=base_url('admin/konsinyasi/add-data-nota')?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res){
                if(res.status){
                    alert("Nota Konsinyasi berhasil disimpan!");
                    window.location.href = "<?=base_url('admin/konsinyasi/nota')?>";
                } else {
                    alert(res.message || "Gagal simpan data");
                }
            },
            error: function(xhr){
                alert("Terjadi kesalahan server!\n" + xhr.responseText);
            }
        });
    });

}); // end document ready
</script>
