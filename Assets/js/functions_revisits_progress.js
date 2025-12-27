const divLoading = document.getElementById('divLoading');
let tableRevisitsProgress;

document.addEventListener('DOMContentLoaded', function(){
	//Cargr datatable
	tableRevisitsProgress = $('#tableRevisitsProgress').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/statistics/getRevisitsProgress",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_type = $('#filter_type').val();
					d.f_country = $('#filter_country').val();
					d.f_period = $('#filter_period').val();
					d.f_number = $('#filter_number').val();
					d.list_franchise = $('#filter_franchise').val();       // ← agregado
    				d.list_area_manager = $('#list_area_manager').val();   // ← agregado
			 	},
			"dataSrc":"",
			"beforeSend": function(){
				$('#divLoading').show();
			},
			"error": function(){
				$('#divLoading').hide();
			},
			"complete": function(){
				$('#divLoading').hide();
			}
		},
		"columns":[
			{"data":"id"},
			{"data":"store","className": "text-center"},
			{"data":"visit","className": "text-center"},
			// {"data":"end_time","className": "text-center"},
			// {"data":"deadline","className": "text-center"},
			{"data":"historical","className": "text-center"}
		],
		"dom": "lfBrtip",
		"buttons": [
			{	"extend": "excelHtml5",
        		"text": "<i class='fa fa-file-excel-o'></i> Excel",
        		"titleAttr": "Export to Excel",
        		"className": "btn btn-success" }
        ],
		"responsive": true,
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 100,
		"order":[[0,"desc"]]
	});
	//Funciones para cargar selects inicialmente una vez cargado el DOM
});

function reloadData(){
	tableRevisitsProgress.api().ajax.reload();
}

