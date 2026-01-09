const $periodos = $('#filter_period');
const rootStyles = getComputedStyle(document.documentElement);
const color1v = rootStyles.getPropertyValue('--color1').trim();
const color2v = rootStyles.getPropertyValue('--color2').trim();
const color3v = rootStyles.getPropertyValue('--color3').trim();
const color4v = rootStyles.getPropertyValue('--color4').trim();
const color5v = rootStyles.getPropertyValue('--color5').trim();
const color6v = rootStyles.getPropertyValue('--color6').trim();
const color7v = rootStyles.getPropertyValue('--color7').trim();
const color8v = rootStyles.getPropertyValue('--color8').trim();
const color9v = rootStyles.getPropertyValue('--color9').trim();
const data = {
            labels: [fnT('Pending'), fnT('In Process'), fnT('Completed'), fnT('Failed')],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    color6v, // Pendiente
                    color7v, // En proceso
                    color8v, // Completadas
                    color9v  // Reprobadas
                ],
                borderColor: '#fff',
                borderWidth: 0,
                hoverOffset: 15
            }]
        };

        // Configuración
        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };

        // Crear la gráfica
        const ctx = document.getElementById('myPieChart').getContext('2d');
        const chart = new Chart(ctx, config);
actualizarEstadisticas();

$periodos.on('changed.bs.select', actualizarEstadisticas);

document.querySelectorAll('input[name="auditType"]').forEach(e=>{
    e.addEventListener('change', ()=>{
        actualizarEstadisticas();
    });
});
document.querySelectorAll('input[name="country"]').forEach(e=>{
    e.addEventListener('change', ()=>{
        actualizarEstadisticas();
    });
});

function actualizarEstadisticas(){
    const periodosSeleccionados = $periodos.val() || [];
    const tipo = document.querySelector('input[name="auditType"]:checked').value;
    const pais = (document.querySelector('input[name="country"]:checked')?document.querySelector('input[name="country"]:checked').value:"")
    $.ajax({
        cache: false,
            data: {"periodos":periodosSeleccionados, "tipos":tipo, "paises":pais},
            type: "POST",
            url: '../Home/actualizarDatos',
        beforeSend: function(){
            
        },
            error: function(response){
                console.log(response);
            },
            success: function(response){
                console.log(response);
                response = JSON.parse(response);
                document.getElementById('lblComp').innerHTML=response['completadas'];
                document.getElementById('lblInP').innerHTML=response['inProcess'];
                document.getElementById('lblPen').innerHTML=response['pendientes'];
                document.getElementById('lblZero').innerHTML=response['zero'];
                console.log('completadas: '+response['completadas']);
                console.log('En proceso: '+response['inProcess']);
                console.log('Pendientes: '+response['pendientes']);
                console.log('Zero tolerance: '+response['zero']);
                chart.data.datasets[0].data = [response['pendientes'], response['inProcess'], response['completadas'], response['zero']];
                chart.update();
            },
        contentType: "application/x-www-form-urlencoded;charset=iso-8859-1"
    });
}

const reloadAll = () => {
    fetch(base_url + "/Home/getTopOpp").then(res => res.json()).then(data => genTopOpp(data));
    fetch(base_url + "/Home/getAVGScore").then(res => res.json()).then(data => genAVGScore(data));
    fetch(base_url + "/Home/getProgressActionPlan").then(res => res.json()).then(data => genProgressActionPlan(data));
    if(permissionDoc.r){
        fetchFiles();
    }
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

const genTopOpp = data => {
    currTopOpp = data;
    setTopOpp($('#select-top-opp').val());

}

const setTopOpp = mainSection => {
    var fcTopOppBs = new FusionCharts({
        type: 'bar2d',
        renderAt: 'chart-top-opp',
        width: '100%',
        height: '355',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": fnT(mainSection),
                "xAxisName": fnT('Questions'),
                "yAxisName": fnT('Incidence'),
                "theme": 'ocean',
                "palettecolors": "5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9",
                "showBorder": "1",
                numberSuffix: "%"
            },
            "data": currTopOpp[mainSection].map(item => ({label: item.question_prefix, value: `${Math.round(item.frecuency * 100 / item.count)}%`, tooltext: `${fnT('Audits')}: ${item.frecuency}<br><br> ${item.text}`}))
        },
        events: {
            "dataPlotClick": (eventObj, dataObj) => swal('', dataObj.toolText.split('<br><br> ')[1], "info")
        }
    });
    fcTopOppBs.render();
}

const genProgressActionPlan = data => {        
    const fcActionDaypart = new FusionCharts({
        type: 'msstackedcolumn2d',
        renderAt: 'chart-progress-action-plan',
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Action plan status'),
                theme: 'ocean',
                showBorder: '1',
                "yAxisName": fnT('Audits'),
                plottooltext: '<b>$label</b>, $percentValue, $value',
            },
            categories: [
                {
                    category: data.map(item => ({label: fnT(item.label)}))
                }
            ],
            dataset: [
                {
                    dataset: [
                        {
                            seriesname: fnT('Pending'),
                            data: data.map(item => ({value: item.pending}))
                        },
                        {
                            seriesname: fnT('In Process'),
                            data: data.map(item => ({value: item.in_process}))
                        },
                        {
                            seriesname: fnT('Finished'),
                            data: data.map(item => ({value: item.finished}))
                        }
                    ]
                }
            ]
        }
    });
    fcActionDaypart.render();
}

const genAVGScore = data => {        
    const fcActionDaypart = new FusionCharts({
        type: 'dragcolumn2d',
        renderAt: 'chart-avg-score',
        width: '100%',
        height: '395',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Quarterly average total'),
                theme: 'ocean',
                showBorder: '1',
                valueFontSize: "12",
                yAxisName: fnT('Audits'),
                numberSuffix: "%",
                palettecolors: '778899,008000,F1C40F,FF0000'
            },
            categories: [
                {
                    category: data.map(item => ({label: `${item.quarter}`}))
                }
            ],
            dataset: [
                {
                    seriesname: fnT('Platino'),
                    data: data.map(item => ({value: Math.round(item.sum_platino * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Verde'),
                    data: data.map(item => ({value: Math.round(item.sum_verde * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Amarillo'),
                    data: data.map(item => ({value: Math.round(item.sum_amarillo * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Rojo'),
                    data: data.map(item => ({value: Math.round(item.sum_rojo * 100 / item.sum_total)}))
                }
            ]
        }
    });
    fcActionDaypart.render();
}

const addFile = async element => {
    if(Object.keys(stackFiles).length > 4){
        swal({
            title: fnT('Alert'),
            text: fnT('File limit reached'),
            type: 'warning'
        });
        return;
    }

    const file = element.files[0];
    const pet = fetch('https://ws.bw-globalsolutions.com/WSAAA/receiveFile.php?token=x', {
        method: 'POST',
        body: file
    }).then(res => res.json());
    
    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');
    
    if(response.Message != 'SUCCESS'){
        swal({
            title: fnT('Error'),
            text: fnT('An error occurred in the process, if the problem persists please contact support'),
            type: 'error'
        });
        return;
    }

    const idImg = Date.now();
    stackFiles[idImg] = {
        url: response.Info.location,
        name: file.name,
        size: file.size
    };

    document.getElementById('form-panel-files').innerHTML += `<div class="alert alert-warning alert-dismissible fade show mb-1" role="alert" id="file${idImg}">
        <strong>${file.name}</strong> / ${fnT('Size')}": ${file.size}b
        <button type="button" class="close" onclick="dropFile('${idImg}')">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>`;
    element.value = '';

}

const dropFile = (idImg) => {
    delete stackFiles[idImg];
    $('#file' + idImg).remove();
}

const sendFormAddFile = async element => {
    if(Object.keys(stackFiles).length < 1){
        swal({
            title: fnT('Alert'),
            text: fnT('Add at least one file'),
            type: 'warning'
        });
        return;
    }

    const payload = new FormData(element);
    payload.append('jfiles', JSON.stringify(stackFiles));
    const pet = fetch( base_url +  '/files/addFile', {
        method: 'POST',
        body: payload
    }).then(res => res.json());
    
    $('#divLoading').css('display', 'flex');
    const response = await pet;
    $('#divLoading').css('display', 'none');

    if(response.status == 0){
        swal({
            title: fnT('Error'),
            text: fnT('An error occurred in the process, if the problem persists please contact support'),
            type: 'error'
        });
        return;
    }
    
    $('#collapseFormFile').collapse('hide');
    fetchFiles();
}

const fetchFiles = async () => {
    const pet = fetch(base_url + '/files/getFiles').then(res => res.json());
    const response = await pet;
    let first = true;
    dataFilles = response;

    document.getElementById('panel-files').innerHTML = response.reduce((acc, cur) => {
        if(first){
            acc = '';
        }
        acc += `<div class="card">
            <div class="card-header" id="heading${cur.id}">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse${cur.id}" aria-expanded="${first? 'true' : 'false'}" aria-controls="collapse${cur.id}">
                        ${cur.title}
                    </button>
                </h2>
            </div>
            <div id="collapse${cur.id}" class="collapse ${first? 'show' : ''}" aria-labelledby="headingTwo" data-parent="#accordionFile">
                <div class="card-body">
                    <p>${cur.description}</p>
                    <small>${fnT('Created')}: ${cur.created} &#124; ${fnT('By')}: ${cur.name}</small>
                </div>
                <ul class="list-group list-group-flush">
                    ${Object.values(cur.jfiles).reduce((_acc, _cur) => _acc + `<li class="list-group-item">
                        <a href="${_cur.url}" target="_blank" download>${_cur.name} &#124; ${fnT('Size')}: ${_cur.size}b</a>
                    </li>`, '')}
                </ul>
                ${permissionDoc.u == 1 || permissionDoc.d == 1? `<div class="card-footer bg-white text-right">
                    <button type="button" class="btn btn-warning btn-sm mr-2" onclick="prepareUpdFile(${cur.id})" ${permissionDoc.u != 1? 'disabled' : ''}>${fnT('Edit')}&#160;&#160;<i class="fa fa-pencil"></i></button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFile(${cur.id})" ${permissionDoc.d != 1? 'disabled' : ''}>${fnT('Remove')}&#160;&#160;<i class="fa fa-trash"></i></button>
                </div>` : ''}
            </div>
        </div>`;
        first = false;
        return acc;
    }, "<h5 class='mt-2'>" + fnT('No files to show') + "</h5>");
}

const removeFile = id => {
    swal({
        title: fnT('Alert'),
        text: fnT('Do you want to remove this files?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    },async function(isConfirm){
        if(isConfirm){
            const pet = fetch(base_url +  '/files/removeFile/' + id).then(res => res.json());
            $('#divLoading').css('display', 'flex');
            const response = await pet;
            $('#divLoading').css('display', 'none');

            if(response.status == 1){
                fetchFiles();
            } else{
                swal({
                    title: fnT('Error'),
                    text: fnT('An error occurred in the process, if the problem persists please contact support'),
                    type: 'error'
                });
            }
        }
    });
}

const prepareNewFile = () => {
    document.getElementById('form-panel-files').innerHTML = '';
    document.getElementById('form-files').reset();
    $('#btn-send-af').html(fnT('Insert record'));
    stackFiles = {};
    $('#collapseFormFile').collapse('show');
}

const prepareUpdFile = id => {
    const currFile = dataFilles.filter(item => item.id == id)[0];    
    const formFiles = document.getElementById('form-files');
    formFiles.reset();

    formFiles['id'].value = currFile.id;
    formFiles['title'].value = currFile.title;
    formFiles['description'].value = currFile.description;
    stackFiles = currFile.jfiles;

    let tmp = '';
    Object.entries(currFile.jfiles).forEach(([key, value]) => {
        tmp += `<div class="alert alert-warning alert-dismissible fade show mb-1" role="alert" id="file${key}">
            <strong>${value.name}</strong> / ${fnT('Size')}": ${value.size}b
            <button type="button" class="close" onclick="dropFile('${key}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
    });
    document.getElementById('form-panel-files').innerHTML = tmp;
    
    $('#btn-send-af').html(fnT('Update record'));
    $('#collapseFormFile').collapse('show');
}

document.addEventListener('DOMContentLoaded', reloadAll);