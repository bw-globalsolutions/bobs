let storeData = [];

const UpdStatusLocation = (id, status) => {
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to change the status?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData();
            payload.append('location_id', id);
            payload.append('status', status);
            fetch(base_url + '/location/updStatusLocation', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                $('#divLoading').css('display', 'none');
                if(dat.status = 1){
                    tableLocations.api().ajax.reload();
                } else{
                    swal({
						title: 'Error',
						text: fnT('An error occurred in the process, if the problem persists please contact support'),
						type: 'error'
					})
                }
            });
        }
    });
}

const delLocation = (id) => {
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to delete location?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData();
            payload.append('location_id', id);
            fetch(base_url + '/location/delLocation', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                $('#divLoading').css('display', 'none');
                if(dat.status == 1){
                    tableLocations.api().ajax.reload();
                } else if(dat.status == -1){
					swal({
						title: 'Error',
						text: fnT('It is not possible to delete a location with assigned audits'),
						type: 'warning'
					});
				}else{
                    swal({
						title: 'Error',
						text: fnT('An error occurred in the process, if the problem persists please contact support'),
						type: 'error'
					});
                }
            });
        }
    });
}

const readStoreFile = element => {
    const file = element.files[0];

    var fileReader = new FileReader();
    fileReader.onload = function(e) {
        var arrayBuffer = e.target.result;
        var data = new Uint8Array(arrayBuffer);
        var workbook = XLSX.read(data, {
            type: 'array'
        });

        var worksheet = workbook.Sheets[workbook.SheetNames[0]];
        var jsonData = XLSX.utils.sheet_to_json(worksheet, {
            header: 1
        });

        if(jsonData.length){
            storeData['columns'] = JSON.stringify(jsonData.shift().map(item => item.trim()));
            storeData['jdata'] = JSON.stringify(jsonData);
        } else{
            swal({
                title: 'Error',
                text: fnT('The file is empty'),
                type: 'error'
            });
            storeData = [];
            element.value = '';
        }
    };

    fileReader.readAsArrayBuffer(file);
}

const sendStoreFile = async () => {
    const payload = new FormData();
    payload.append('columns', storeData['columns']);
    payload.append('data', storeData['jdata']);

    const pet = fetch(base_url + '/location/massInsertion', {
        method: 'post',
        body: payload
    }).then(res => res.json());

    $('#divLoading').css('display', 'flex');
    const response = await pet;
    const locations = response.locations || [];
    const errors = response.errors || [];
    const users = response.users || [];
    $('#divLoading').css('display', 'none');

    document.getElementById('list-locations').innerHTML = !locations.length?
        `<li class="list-group-item list-group-item-secondary">${fnT('No items')}</li>` :
        locations.reduce((acc, cur) => {
            return `${acc} <li data-location="${cur[0] || ''}" class="item-locations list-group-item"><b>${cur[0]}</b> &#187; ${fnT('Action')}: ${fnT(cur[1])}</li>`
        }, `<li class="list-group-item list-group-item-secondary d-flex justify-content-between">
            ${fnT('Renew Locations')}
            <div class="input-group rounded" style="width: 200px;">
                <input class="form-control rounded" id="filter_search" placeholder="${fnT('Search number')}" onkeyup="searchInTable(this.value, 'locations')">
                <span class="input-group-text border-0 bg-transparent" id="search-addon">
                    <i class="fa fa-search"></i>
                </span>
            </div>
        </li>`);
    document.getElementById('counter-locations').innerHTML = locations.length;

    document.getElementById('list-errors').innerHTML = !errors.length?
        `<li class="list-group-item list-group-item-secondary">${fnT('Without errors')}</li>` :
        errors.reduce((acc, cur) => {
            return `${acc} <li data-location="${cur[0] || ''}" class="item-locations-error list-group-item"><b>${cur[0]}</b> &#187; ${fnT(cur[1])}, ${fnT('In row')}: ${cur[2]}</li>`
        },  `<li class="list-group-item list-group-item-secondary d-flex justify-content-between">
            ${fnT('Errors')}
            <div class="input-group rounded" style="width: 200px;">
                <input class="form-control rounded" id="filter_search" placeholder="${fnT('Search number')}" onkeyup="searchInTable(this.value, 'locations-error')">
                <span class="input-group-text border-0 bg-transparent" id="search-addon">
                    <i class="fa fa-search"></i>
                </span>
            </div>
        </li>`);
    document.getElementById('counter-errors').innerHTML = errors.length;
    
    document.getElementById('list-users').innerHTML = !users.length?
        `<li class="list-group-item list-group-item-secondary">${fnT('Without users')}</li>` :
        users.reduce((acc, cur) => {
            return `${acc} <li data-location="${cur[2] || ''}" class="item-users list-group-item"><b>${cur[2]}</b> &#187; ${fnT('Action')}: ${fnT(cur[1])}, ${fnT('with role')}: ${cur[3]}, ${fnT('in locations')}: <span class="text-secondary">${cur[0].join(' ')}</span></li>`
        },  `<li class="list-group-item list-group-item-secondary d-flex justify-content-between">
            ${fnT('Users')}
            <div class="input-group rounded" style="width: 200px;">
                <input class="form-control rounded" id="filter_search" placeholder="${fnT('Search email')}" onkeyup="searchInTable(this.value, 'users')">
                <span class="input-group-text border-0 bg-transparent" id="search-addon">
                    <i class="fa fa-search"></i>
                </span>
            </div>
        </li>`);
    document.getElementById('counter-users').innerHTML = users.length;
    
    tableLocations.api().ajax.reload();
    document.getElementById('store-file').value = '';
    storeData = [];

    console.log(response);
    $('#modalMassInsertion').modal('show');
}

addEventListener("DOMContentLoaded", (event) => {
    tableLocations = $('#tableLocations').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/location/getLocations",
			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"number"},
			{"data":"name"},
			{"data":"country"},
			{"data":"city"},
			{"data":"address_1"},
			{"data":"email"},
			{"data":"shop_type"},
			{"data":"status"},
			{"data":"actions"}
		],
		"dom": "lfBrtip",
        "buttons": [],
		"resonsieve":"true",
		"bDestroy": true,
        "buttons": [
            {
              className: 'btn btn-primary',
              text: fnT('Download Database'),
              action: () => getExportable('exportLocations', 'Locations')
            }
        ],
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});
});

async function getExportable(target, name){
    $('#divLoading').css('display', 'flex');
    const fetchExportable = await fetch(base_url + "/statistics/" + target, {
        method: 'POST'
    });
    const file = await fetchExportable.blob();
    const a = document.createElement("a");
    a.href = window.URL.createObjectURL(file);
    a.download = name + ".xls";
    a.click();
    $('#divLoading').css('display', 'none');
}

function searchInTable(inp, table){
    $('.item-' + table).filter(function(){
        $(this).toggle($(this).data('location').toString().indexOf(inp) > -1 || inp == '');
    });
}

async function UpdLocation(idLocation){
    const pet = fetch(base_url + '/location/getFullDataLocation/'+idLocation).then(res => res.json());
    
    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');

    document.querySelectorAll('#form-location [name]').forEach(item => {
        const name = item.getAttribute('name');
        item.value = response[name];
    });
    
    $('#modalEditLocation').modal('show');
}

async function sendLocation(element){
    const payload = new FormData(element);
    const pet = fetch(base_url + "/location/insLocation", {
        method: 'POST',
        body: payload
    }).then(res => res.json());;

    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');

    if(response.status == 1){
        tableLocations.api().ajax.reload();
        $('#modalEditLocation').modal('hide');
    } else{
        swal({
            title: 'Error',
            text: fnT('An error occurred in the process, if the problem persists please contact support'),
            type: 'error'
        });
    }
}