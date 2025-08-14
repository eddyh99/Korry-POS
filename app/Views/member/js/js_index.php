<style>
    tr { height: 50px; }
    #table_data tbody tr{
      cursor:pointer;
    }
</style>
<script>
var table;
$(function(){
	table = $('#table_data').DataTable({
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "pageLength": 50,

			"order": [[ 1, "asc" ]],
            "scrollX": true,
			"ajax": {
					"url": "<?=base_url()?>member/listdata",
					"type": "POST",
					"dataSrc":function (data){
							return data["member"];							
						  }
			},
		    "aoColumnDefs": [{	
				"aTargets": [4],
				"mData": "",
				"mRender": function (data, type, full, meta){
				        button='<a href="<?=base_url()?>member/ubah/'+encodeURI(btoa(full.member_id))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">update</i></a>';
				        button=button+'<a href="<?=base_url()?>member/hapus/'+encodeURI(btoa(full.member_id))+'" class="btn btn-simple btn-danger btn-icon remove"><i class="material-icons">close</i></a>';
    			        return button;
				}
			}],
            "columns": [
				  { "data": "member_id"},
                  { "data": "nama" },
                  { "data": "nope" },
                  { "data": "email" },
			]
	});
})
</script>