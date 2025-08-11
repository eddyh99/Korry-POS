<!-- Select2 -->
<link href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>

<script>
	var barangpinjam=[];
	var tablepinjam = $('#table_pinjam').DataTable({
            "scrollX": true,
            "pageLength": 50,
            "order": [[ 1, 'asc' ]],
			"ajax": {
					"url": "<?=base_url()?>admin/pinjam/listpinjam",
					"type": "POST",
					"data" : {
					    key: $("#key").val()
					},
					"dataSrc":function (data){
							return data;							
						  }
			},
            "columnDefs": [ 
                    {
                        "orderable" : false,
                        "targets"   : 0,
                        "render"    : function (){
                            return "<input type='checkbox'>"
                        }
                    },
                    {
                        "data"      : "barcode",
                        "targets"   : 1
                    },
                    { 
                        "data"      : "namaproduk",
                        "targets"   : 2
                    },                    
				    { 
				        "data"      : "namabrand",
				        "targets"   : 3
				    },
				    { 
				        "data"      : "size",
				        "targets"   : 4
				    },
				    {   
				        "data"      : "jumlah",
				        "targets"   : 5
				    }
            ],
	});
    
    var hasil=0;
    $('#table_pinjam tbody').on('click', 'input[type="checkbox"]', function(e){
        var $row = $(this).closest('tr');
        var data = tablepinjam.row($row).data();
        if(this.checked){
            hasil=hasil+Number(data.total)
            barangpinjam.push([data.barcode]);
            $row.addClass('selected');
        } else {
            hasil=hasil-Number(data.total)
            for (i=0;i<barangpinjam.length;i++){
                if (barangpinjam[i][0]==data.barcode){
                    barangpinjam.splice(i,1);
                }
            }
            $row.removeClass('selected');
        }
		localStorage.setItem('returpinjam', JSON.stringify(barangpinjam));
        e.stopPropagation();
    });
    
    $('#table_pinjam').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
    });


	$("#simpan").on("click",function(){
		var Object = JSON.parse(localStorage.getItem('returpinjam'));
		var barang = JSON.stringify(Object);
		$.ajax({
			url: "<?=base_url()?>admin/pinjam/simpandata",
			type: "post",
			data: "barang="+barang+"&id="+$("#key").val(),
			success: function (data) {
				window.location.href="<?=base_url()?>admin/pinjam";
			},
			error: function(jqXHR, textStatus, errorThrown) {
			   console.log(textStatus, errorThrown);
			}
		});
	});


</script>