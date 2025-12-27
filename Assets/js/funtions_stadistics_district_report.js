const divLoading = document.getElementById('divLoading');
let tabledistrictReportGlobal; 



document.getElementById('btnExportAll').addEventListener('click', function () {
	exportTablesToExcel();
});

function exportTablesToExcel() {

    const table1 = $('#tabledistrictReportGlobal').DataTable();
    const table2 = $('#tabledistrictReportStore').DataTable();

    // Obtener encabezados
    const data1 = [table1.columns().header().toArray().map(h => h.innerText)];

    // Obtener filas
    table1.rows().every(function () {
        const rowData = this.data();
        if (rowData) {
            data1.push(Object.values(rowData));
        }
    });

    // Calcular totales
    let totals = [];
    for (let col = 0; col < data1[0].length; col++) {
        let sum = 0;
        let isNumeric = true;
        for (let row = 1; row < data1.length; row++) {
            let value = data1[row][col];
            // Quitar HTML y comas
            if (typeof value === 'string') {
                value = value.replace(/<[^>]*>/g, '').replace(/,/g, '');
            }
            let num = parseFloat(value);
            if (!isNaN(num)) {
                sum += num;
            } else {
                isNumeric = false;
                break;
            }
        }
        totals.push(isNumeric ? sum : '');
    }

    // Poner "Total" en la primera celda de la fila de totales
    if (totals.length > 0) {
        totals[0] = 'Total';
    }

    // Agregar la fila de totales
    data1.push(totals);

    // Tabla 2 (sin totales)
 const headers2 = [
    fnT('Branch number'),
    fnT('Branch name'),
    fnT('Type'),
    fnT('Consult'),
    fnT('Distrital'),
	fnT('Audit') + ' ' + fnT('Q1'),
  	fnT('Re-Audit') + ' ' + fnT('Q1'),
    fnT('Re-Aud 2') + ' ' + fnT('Q1'),

	fnT('Audit') + ' ' + fnT('Q2'),
  	fnT('Re-Audit') + ' ' + fnT('Q2'),
    fnT('Re-Aud 2') + ' ' + fnT('Q2'),

	fnT('Audit') + ' ' + fnT('Q3'),
  	fnT('Re-Audit') + ' ' + fnT('Q3'),
    fnT('Re-Aud 2') + ' ' + fnT('Q3'),

	fnT('Audit') + ' ' + fnT('Q4'),
  	fnT('Re-Audit') + ' ' + fnT('Q4'),
    fnT('Re-Aud 2') + ' ' + fnT('Q4'),


];

const columns2 = [
    "location_number",
    "location_name",
  
    "consultor",
    "distrital",
    "Q1",
    "Q1R",
    "SegundaReauditQ1",

    "Q2",
    "Q2R",
    "SegundaReauditQ2",

    "Q3",
    "Q3R",
    "SegundaReauditQ3",
   
    "Q4",
    "Q4R",
    "SegundaReauditQ4",


];

// Crear data2
const data2 = [headers2];


    table2.rows().every(function () {
        const rowData = this.data();
        if (rowData) {
            const values = Object.values(rowData);
            values[3] = values[3] ? values[3].replace(/<[^>]+>/g, '') : '';
            data2.push(values);
        }
    });

    // Crear libro
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(data1), 'District Global');
    XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(data2), 'District Store');

    XLSX.writeFile(wb, 'ReporteDistrital.xlsx');
}






document.addEventListener('DOMContentLoaded', function(){
	//Cargr datatable
	tabledistrictReportGlobal = $('#tabledistrictReportGlobal').dataTable({
		"aProcessing":true,
		"aServerSide":true,
        "paging": false,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/statistics/districtReportGlobalTable",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_country = $('#filter_country').val();
                    d.f_years = $('#filter_years').val();
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
			{"data":"calification","className": "text-center","width": "5%"},
			{"data":"auditoria1","className": "text-center","width": "5%"},
			{"data":"re_auditoria1","className": "text-center","width": "5%"},
			{"data":"re_auditoria2_Q1","className": "text-center","width": "8%"},
		
			{"data":"auditoria2","className": "text-center","width": "5%"},
			{"data":"re_auditoria2","className": "text-center","width": "5%"},
			{"data":"re_auditoria2_Q2","className": "text-center","width": "5%"},
				
			
			
		
		],
		"dom": "Brt",
		"buttons": [
            // {
            //     extend: 'excelHtml5',
            //     text: 'Exportar a Excel',
            //     titleAttr: 'Exportar a Excel',
            //     className: 'btn btn-success'
            // }
        ],
		"responsive": true,
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]],
        "drawCallback": function () {
			columsColor(); 
            updateTotals();
		}
        
	});
	//Funciones para cargar selects inicialmente una vez cargado el DOM
});

let tabledistrictReportStore;

document.addEventListener('DOMContentLoaded', function(){
	//Cargr datatable
	tabledistrictReportStore = $('#tabledistrictReportStore').dataTable({
		"aProcessing":true,
		"aServerSide":true,
        "paging": false,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/statistics/districtReportStoreTable",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_country = $('#filter_country').val();
					d.f_years = $('#filter_years').val();
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
			{"data":"location_number"},
			{"data":"location_name"},

			{"data":"email_franchisee"},
			{"data":"email_area_manager"},
		
	
		
			
			{"data":"Q1","className": "text-center","width": "5%"},
			{"data":"Q1R","className": "text-center","width": "5%"},
			{"data":"SegundaReauditQ1","className": "text-center","width": "5%"},
			
            {"data":"Q2","className": "text-center","width": "5%"},
            {"data":"Q2R","className": "text-center","width": "5%"},
            {"data":"SegundaReauditQ2","className": "text-center","width": "5%"},
       
      
			
		],
		"dom": "Brt",
		"buttons": [
            // /*{
            //     extend: 'excelHtml5',
            //     text: 'Exportar a Excel',
            //     titleAttr: 'Exportar a Excel',
            //     className: 'btn btn-success'
            // }*/
        ],
		"responsive": true,
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]],
        "drawCallback": function () {
			columsColorStores(); 
		}
        
	});
	//Funciones para cargar selects inicialmente una vez cargado el DOM
});

function reloadData(){
	tabledistrictReportGlobal.api().ajax.reload();
	tabledistrictReportStore.api().ajax.reload(); 
}
 
//REPORTE GLOBAL
function columsColor() {
	$('#tabledistrictReportGlobal tbody tr').each(function () {
		var calification = $(this).find("td").eq(0).text().trim();

		if (calification == 'Pass' ) {
			$(this).find("td").eq(0).css({
				"background-color": "#28a745",
				"color": "white"
			});
		}  else if ( calification == 'Fail' ) {
			$(this).find("td").eq(0).css({
				"background-color": "#dc3545",
				"color": "white"
			});
		
		}
	});
}

function columsColorStores() {
    $('#tabledistrictReportStore tbody tr').each(function () {
        // Índices de columnas a evaluar (ajusta si cambia el orden)
        var columnsToCheck = [ 3,4,5, 6, 7, 8, 9, 10, 11, 12, 13,14,15,16,17,18,19,20];

        columnsToCheck.forEach(function (index) {
            var $cell = $(this).find("td").eq(index);
            var calificationText = $cell.text().trim().toLowerCase(); // convertimos a minúsculas para evitar errores

            if (calificationText === 'pass') {
                $cell.css({
                    "background-color": "#28a745",
                    "color": "white"
                });
            } else if (calificationText === 'fail') {
                $cell.css({
                    "background-color": "#dc3545",
                    "color": "white"
                });
            }
        }, this); // Mantener `this` del .each
    });
}


// Función para actualizar los totales en el pie de la tabla
function updateTotals() {
	let totalAuditoria1 = 0;
	let totalReAuditoria1 = 0;
	let re_auditoria2_Q1 = 0;
	let re_auditoria3_Q1 = 0;
	let re_auditoria4_Q1 = 0;
	let totalAuditoria2 = 0;
	let totalReAuditoria2 = 0;
	let re_auditoria2_Q2 = 0;
	let re_auditoria3_Q2 = 0;
	let re_auditoria4_Q2 = 0;
	let totalAuditoria3 = 0;
	let totalReAuditoria3 = 0;
	let re_auditoria2_Q3 = 0;
	let re_auditoria3_Q3 = 0;
	let re_auditoria4_Q3 = 0;
	let totalAuditoria4 = 0;
	let totalReAuditoria4 = 0;
	let re_auditoria2_Q4 = 0;
	let re_auditoria3_Q4 = 0;
	let re_auditoria4_Q4 = 0;
	let totalReAuditoria2nd = 0;
	let totalReAuditoria3rd = 0;

	// Recorremos cada fila de la tabla y sumamos los totales
	$('#tabledistrictReportGlobal tbody tr').each(function() {
		totalAuditoria1 += parseInt($(this).find("td").eq(1).text()) || 0;
		totalReAuditoria1 += parseInt($(this).find("td").eq(2).text()) || 0;
		re_auditoria2_Q1 += parseInt($(this).find("td").eq(3).text()) || 0;
		re_auditoria3_Q1 += parseInt($(this).find("td").eq(4).text()) || 0;
		re_auditoria4_Q1 += parseInt($(this).find("td").eq(5).text()) || 0;
		totalAuditoria2 += parseInt($(this).find("td").eq(6).text()) || 0;
		totalReAuditoria2 += parseInt($(this).find("td").eq(7).text()) || 0;
		re_auditoria2_Q2 += parseInt($(this).find("td").eq(8).text()) || 0;
		re_auditoria3_Q2 += parseInt($(this).find("td").eq(9).text()) || 0;
		re_auditoria4_Q2 += parseInt($(this).find("td").eq(10).text()) || 0;
		totalAuditoria3 += parseInt($(this).find("td").eq(11).text()) || 0;
		totalReAuditoria3 += parseInt($(this).find("td").eq(12).text()) || 0;
		re_auditoria2_Q3 += parseInt($(this).find("td").eq(13).text()) || 0;
		re_auditoria3_Q3 += parseInt($(this).find("td").eq(14).text()) || 0;
		re_auditoria4_Q3 += parseInt($(this).find("td").eq(15).text()) || 0;
		totalAuditoria4 += parseInt($(this).find("td").eq(16).text()) || 0;
		totalReAuditoria4 += parseInt($(this).find("td").eq(17).text()) || 0;
		re_auditoria2_Q4 += parseInt($(this).find("td").eq(18).text()) || 0;
		re_auditoria3_Q4 += parseInt($(this).find("td").eq(19).text()) || 0;
		re_auditoria4_Q4 += parseInt($(this).find("td").eq(20).text()) || 0;
		totalReAuditoria2nd += parseInt($(this).find("td").eq(21).text()) || 0;
		totalReAuditoria3rd += parseInt($(this).find("td").eq(22).text()) || 0;
	});

	// Actualizamos los totales en el pie de la tabla
	$('#auditoria1').text(totalAuditoria1);
	$('#re_auditoria1').text(totalReAuditoria1);
	$('#re_auditoria2_Q1').text(re_auditoria2_Q1);
	$('#re_auditoria3_Q1').text(re_auditoria3_Q1);
	$('#re_auditoria4_Q1').text(re_auditoria4_Q1);
	$('#auditoria2').text(totalAuditoria2);
	$('#re_auditoria2').text(totalReAuditoria2);
	$('#re_auditoria2_Q2').text(re_auditoria2_Q2);
	$('#re_auditoria3_Q2').text(re_auditoria3_Q2);
	$('#re_auditoria4_Q2').text(re_auditoria4_Q2);
	$('#auditoria3').text(totalAuditoria3);
	$('#re_auditoria3').text(totalReAuditoria3);
	$('#re_auditoria2_Q3').text(re_auditoria2_Q3);
	$('#re_auditoria3_Q3').text(re_auditoria3_Q3);
	$('#re_auditoria4_Q3').text(re_auditoria4_Q3);
	$('#auditoria4').text(totalAuditoria4);
	$('#re_auditoria4').text(totalReAuditoria4);
	$('#re_auditoria2_Q4').text(re_auditoria2_Q4);
	$('#re_auditoria3_Q4').text(re_auditoria3_Q4);
	$('#re_auditoria4_Q4').text(re_auditoria4_Q4);
	$('#re_auditoria2nd').text(totalReAuditoria2nd);
	$('#re_auditoria3rd').text(totalReAuditoria3rd);
}


