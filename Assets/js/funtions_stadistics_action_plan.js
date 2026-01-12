const divLoading = document.getElementById('divLoading');
let tableActionPlan;

document.addEventListener('DOMContentLoaded', function(){
	//Cargr datatable
	tableActionPlan = $('#tableActionPlan').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/statistics/actionPlanTable",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_type = $('#filter_type').val();
					d.f_country = $('#filter_country').val();
					d.f_period = $('#filter_period').val();
					d.f_status = $('#filter_status').val();
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
			{"data":"visit","className": "text-center"}
		],
		"dom": "lfBrtip",
		"buttons": [],
		"responsive": true,
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"desc"]]
	});
	//Funciones para cargar selects inicialmente una vez cargado el DOM
});

function reloadData(){
	tableActionPlan.api().ajax.reload();
}

