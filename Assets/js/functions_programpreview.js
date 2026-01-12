let bgColors = [
    '007bff',
    '6c757d',
    '28a745',
    'dc3545',
    '17a2b8',
    '343a40'
];

const reloadAll = async (element = false) => {
    if(element != false){
        payload = new FormData(element);
    }
    
    const fetchProgramPreview = fetch(base_url + "/statistics/getProgramPreview", {
        method: 'POST',
		body: payload
    }).then(res => res.json());

    $('#divLoading').css('display', 'flex');
    const dataProgramPreview = await fetchProgramPreview;
    $('#divLoading').css('display', 'none');

    genTable(dataProgramPreview);
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

const genTable = data => {

    if(!data.length){
        $('#cotainer-table-pp').html(`<h4>${fnT('Nenhum registro foi encontrado com estes parâmetros')} :(</h4>`);
        return;
    }

    let colorIndex = 0;
    let auxBg = [];

    $('#cotainer-table-pp').html(`<table id="table-pp" class="table table-hover table-bordered">
        <thead>
            <tr>
                <th></th>
                ${`<th colspan=6 style="background-color: #${bgColors[colorIndex++]}; color:white">${$('#filter_period').val()}</th>`}
                <th colspan=2></th>
            </tr>
            <tr>
                <th>${fnT('E-mail do auditor')}</th>
                ${Object.keys(data[0].periods).reduce((acc, cur) => `${acc}<th>${cur}</th>`, '')}
                <th>${fnT('Total')}</th>
            </tr>
        </thead>
    </table>`);
    
    $('#table-pp').DataTable({
        dom: 'Bfrtip',
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
        },
        "data": data.map(item => {
            let tmp = [];

            tmp.push(item.auditor_email == 'total'?  `<b>TOTAL</b>`:item.auditor_email);

            Object.entries(item.periods).forEach(([key, value]) => { 
                if (value != null){
                    const params = value.split('/');
                    const percent = Math.round((params[0] / params[1]) * 100);
                    tmp.push(`<div class="progress cr-pointer bg-dark" onclick="getExportable('exportPPDetails/${params[2]}', '${item.auditor_email} ${key}')">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${percent}%" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">%${percent}</div>
                    </div> <small>${params[0]} / ${params[1]}</small>`);
                } else{
                    tmp.push('NA');
                }
            });

            const params = item.avg.split('/');
            const percent = Math.round((params[0] / params[1]) * 100);
            tmp.push(`<div class="progress bg-dark cr-pointer" onclick="getExportable('exportPPDetails/${params[2]}', '${item.auditor_email}')">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: ${percent}%" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">%${percent}</div>
                </div> <small>${params[0]} / ${params[1]}</small>`);

            return tmp;
        }),
        "buttons": [],
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"}
        ],
        "ordering": false
    });
}

const getExportable = async (target, auditor) => {
    $('#divLoading').css('display', 'flex');
    const fetchExportable = await fetch(base_url + "/statistics/" + target, {
        method: 'POST'
    });
    const file = await fetchExportable.blob();
    const a = document.createElement("a");
    a.href = window.URL.createObjectURL(file);
    a.download = `General Program Preview (${auditor}).xls`;
    a.click();
    $('#divLoading').css('display', 'none');
}

document.addEventListener('DOMContentLoaded', () => {
    $('#filter_form').submit();
});