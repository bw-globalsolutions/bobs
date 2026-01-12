let storeData = [];

const UpdStatusLocation = (id, status) => {
    swal({
        title: fnT('Alerta'),
        text: fnT('Tem certeza de que deseja alterar o status?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
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
						text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
						type: 'error'
					})
                }
            });
        }
    });
}

/*cargarTema();

function cargarTema(){

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/personalization/cargarTema';
            var strData = "id=1";
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){

                if(request.readyState == 4 && request.status == 200){
                    //console.log(request.responseText);
                    var objData = JSON.parse(request.responseText);

                    document.documentElement.style.setProperty("--color1", objData[0].color1);
                    document.documentElement.style.setProperty("--color2", objData[0].color2);
                    document.documentElement.style.setProperty("--color3", objData[0].color3);
                    document.documentElement.style.setProperty("--color4", objData[0].color4);

                    if(objData[0].img2!=''){
                        if(document.querySelector('.img-fluid')){
                            document.querySelector('.img-fluid').src=objData[0].img2;
                        }
                    }

                    if(objData[0].img3!=''){
                        // Obtener el elemento del favicon (si existe)
                        const favicon = document.querySelector('link[rel="icon"]') || 
                        document.createElement('link');

                        // Configurar sus atributos
                        favicon.rel = 'icon';
                        favicon.href = objData[0].img3; // Ruta del nuevo favicon
                        favicon.type = 'image/x-icon';

                        // Añadirlo al <head> si no existía
                        document.head.appendChild(favicon);
                    }

                }
            }
}*/

const delLocation = (id) => {
    swal({
        title: fnT('Alerta'),
        text: fnT('Tem certeza de que deseja excluir a localização?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
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
						text: fnT('Não é possível excluir uma localização com auditorias atribuídas'),
						type: 'warning'
					});
				}else{
                    swal({
						title: 'Error',
						text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
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
                text: fnT('O arquivo está vazio'),
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

    try {
        // Primero obtenemos la respuesta como texto
        const response = await fetch(base_url + '/location/massInsertion', {
            method: 'post',
            body: payload
        });
        
        // Mostramos la respuesta textual en la consola
        const textResponse = await response.text();
        console.log("Respuesta textual del servidor:", textResponse);
        
        // Ahora intentamos parsearla a JSON
        let jsonResponse;
        try {
            jsonResponse = JSON.parse(textResponse);
        } catch (e) {
            console.error("La respuesta no es JSON válido:", e);
            throw new Error("La respuesta del servidor no es JSON válido");
        }
        
        const locations = jsonResponse.locations || [];
        const errors = jsonResponse.errors || [];
        const users = jsonResponse.users || [];
        
        // Resto de tu código para manejar la respuesta...
        $('#divLoading').css('display', 'none');

        document.getElementById('list-locations').innerHTML = !locations.length?
            `<li class="list-group-item list-group-item-secondary">${fnT('Nenhum item')}</li>` :
            locations.reduce((acc, cur) => {
                return `${acc} <li data-location="${cur[0] || ''}" class="item-locations list-group-item"><b>${cur[0]}</b> &#187; ${fnT('Ação')}: ${fnT(cur[1])}</li>`
            }, `<li class="list-group-item list-group-item-secondary d-flex justify-content-between">
                ${fnT('Renovar localizações')}
                <div class="input-group rounded" style="width: 200px;">
                    <input class="form-control rounded" id="filter_search" placeholder="${fnT('Pesquisar número')}" onkeyup="searchInTable(this.value, 'locations')">
                    <span class="input-group-text border-0 bg-transparent" id="search-addon">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </li>`);
        document.getElementById('counter-locations').innerHTML = locations.length;

        // ... resto del código para manejar errors y users ...
        
        tableLocations.api().ajax.reload();
        document.getElementById('store-file').value = '';
        storeData = [];

        console.log("Respuesta JSON:", jsonResponse);
        $('#modalMassInsertion').modal('show');
        
    } catch (error) {
        $('#divLoading').css('display', 'none');
        console.error("Error en la petición:", error);
        // Aquí podrías mostrar un mensaje de error al usuario
    }
}

addEventListener("DOMContentLoaded", (event) => {
    tableLocations = $('#tableLocations').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
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
              text: fnT('Baixar base de dados'),
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
    let response = await pet;
    $('#divLoading').css('display', 'none');

    document.querySelectorAll('#form-location [name]').forEach(item => {
        const name = item.getAttribute('name');
        if(name=='status')response[name] = (response[name]==1?'Open':'Closed');
        item.value = response[name];
    });
    
    $('#modalEditLocation').modal('show');
}

async function sendLocation(element){
    let formulario = document.querySelectorAll('#form-location')[1];
    const payload = new FormData(formulario);
    const pet = fetch(base_url + "/location/insLocation", {
        method: 'POST',
        body: payload
    }).then(res => res.json());;

    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');

    if(response.status == 1){
        console.log('res: '+response);
        tableLocations.api().ajax.reload();
        $('#modalEditLocation').modal('hide');
    } else{
        console.log('res:'+response);
        swal({
            title: 'Error',
            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
            type: 'error'
        });
    }
}

async function openModal() {
    document.getElementById('input-number').removeAttribute('readonly');
    console.log("Abriendo modal...");

    // try {
        // Llamar a la ruta del backend para obtener datos del país
        // const pet = fetch(base_url + '/location/getCountry/?key=Mexico').then(res => res.json());
        // const response = await pet;

        // console.log("Respuesta del servidor:", response);

        // Verifica si la respuesta contiene datos
        // if (response.status === 1 && response.data) {
        //     const select = document.getElementById("input-country");

        //     // Limpia el contenido actual del select
        //     select.innerHTML = "";

        //     // Rellena el select con los datos del país
        //     response.data.forEach(country => {
        //         const option = document.createElement("option");
        //         option.value = country.id; // ID como valor
        //         option.textContent = country.name; // Nombre como texto
        //         select.appendChild(option);
        //     });
        // } else {
        //     console.warn("No se encontraron países en la respuesta.");
        // }

        // Limpiar los campos del formulario
        document.querySelectorAll('#modalFormUser [name]').forEach(item => {
             item.value = ""; 
        });
        if(document.querySelectorAll('#form-location')[0].querySelector('#inpNew')){
            console.log('existe');
            document.querySelectorAll('#inpNew').forEach(e=>{e.value='1'});
        }else{
            console.log('no existe');
            let formulario = document.querySelectorAll('#form-location')[0];
            let inpNew = document.createElement('INPUT');
            inpNew.type = 'hidden';
            inpNew.setAttribute('id', 'inpNew');
            inpNew.name = 'inpNew';
            formulario.appendChild(inpNew);
            document.querySelectorAll('#inpNew').forEach(e=>{e.value='1'});
        }

        // Actualizar el título del modal
        document.getElementById('titleModal').innerHTML = fnT("Nova localização");

        // Mostrar el modal
        $('#modalFormUser').modal('show');
    // } catch (error) {
    //     console.error("Error al obtener datos del país:", error);
    // }
}

async function addLocation(element){
    let formulario = document.querySelectorAll('#form-location')[0];
    const payload = new FormData(formulario);
    const pet = fetch(base_url + "/location/addlocation", {
        method: 'POST',
        body: payload
    }).then(res => res.json());;

    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');

    if(response.status == 1){
        console.log(response);
        tableLocations.api().ajax.reload();
        $('#modalFormUser').modal('hide');
    }else if(response.status == 2){
        swal({
            title: 'Error',
            text: fnT('Location number already exists '+response.sql),
            type: 'error'
        });
    }else{
        console.log(response);
        swal({
            title: 'Error',
            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
            type: 'error'
        });
    }
}