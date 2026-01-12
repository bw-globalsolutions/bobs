const reloadAll = async (element) => {
    payload = new FormData(element);
    
    const fetchTopOpp = fetch(base_url + "/statistics/getTopOpp", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchLeadership = fetch(base_url + "/statistics/getLeadership", {
        method: 'POST',
		body: payload
    }).then(res => res.json());

    const fetchActionPlanStatus = fetch(base_url + "/statistics/getActionPlanStatus", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchDaypart = fetch(base_url + "/statistics/getDaypart", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchWeekday = fetch(base_url + "/statistics/getWeekday", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchDuration = fetch(base_url + "/statistics/getDuration", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchProgressStatus = fetch(base_url + "/statistics/getProgressStatus", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si

    const fetchCategoryTrend = fetch(base_url + "/statistics/getCategoryTrend", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchQuestionTrend = fetch(base_url + "/statistics/getQuestionTrend", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si
    
    const fetchActionCompletion = fetch(base_url + "/statistics/getActionCompletion", {
        method: 'POST',
		body: payload
    }).then(res => res.json()); //si

    const fetchScoreTopBottom = fetch(base_url + "/statistics/getScoreTopBottom", {
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    $('#divLoading').css('display', 'flex');
    [dataTopOpp, dataLeadership, dataActionPlanStatus, dataDaypart, dataWeekday, dataDuration, dataProgressStatus, dataCategoryTrend, dataQuestionTrend, dataActionCompletion, dataScoreTopBottom] = await Promise.all([fetchTopOpp, fetchLeadership, fetchActionPlanStatus, fetchDaypart, fetchWeekday, fetchDuration, fetchProgressStatus, fetchCategoryTrend, fetchQuestionTrend, fetchActionCompletion, fetchScoreTopBottom]);
    
    genTopOpp(dataTopOpp);
    genActionPlanStatus(dataActionPlanStatus);
    genLeadership(dataLeadership);
    genDaypart(dataDaypart);
    genWeekday(dataWeekday);
    genDuration(dataDuration);
    genProgressStatus(dataProgressStatus);
    genCategoryTrend(dataCategoryTrend);
    genQuestionTrend(dataQuestionTrend);
    genActionCompletion(dataActionCompletion);
    genScoreTopBottom(dataScoreTopBottom);

    $('#divLoading').css('display', 'none');
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

const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

const getExportable = async (target, name) => {
    $('#divLoading').css('display', 'flex');
    const fetchExportable = await fetch(base_url + "/statistics/" + target, {
        method: 'POST',
		body: payload
    });
    const file = await fetchExportable.blob();
    const a = document.createElement("a");
    a.href = window.URL.createObjectURL(file);
    a.download = name + ".xls";
    a.click();
    $('#divLoading').css('display', 'none');
}

const genLeadership = data => { 
    console.log('data: '+data[0]['Overall score']); 
    if($.fn.DataTable.isDataTable('#tableLeadership')) {
        $('#tableLeadership').DataTable().destroy();
      }
    
      $('#tableLeadership').DataTable({
        data: data,
        columns: [
          {"data": "name"},
          {"data": "visits"},
          {"data": "af"},
          {"data": "Food safety"},
          {"data": "Operations excellence"},
          {"data": "Overall score"}
        ],
        processing: true,
        language: {
          url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" // Ajusta el idioma
        },
        dom: "<'row'<'col-sm-12'tr>>", // Estructura simple
        responsive: true,
        destroy: true,
        pageLength: 10,
        order: [[0, "asc"]],
        paging: false,
        columnDefs: [
          {"className": "dt-center", "targets": "_all"}
        ],
        footerCallback: function(row, data, start, end, display) {
          const api = this.api();
          const totalRows = api.rows().count();
          
          // Actualizar footer para visitas (suma)
          const visitTotal = api
            .column(1, {page: 'current'})
            .data()
            .reduce((a, b) => a + (parseFloat(b) || 0), 0);
          $(api.column(1).footer()).html(visitTotal);
    
          // Actualizar otros (promedios)
          for (let i = 2; i <= 4; i++) {
            const colTotal = api
              .column(i, {page: 'current'})
              .data()
              .reduce((a, b) => a + (parseFloat(b) || 0), 0);
            $(api.column(i).footer()).html((colTotal / totalRows).toFixed(2));
          }
        }
      });
}

const genTopOpp = data => {
    console.log(data);
    currTopOpp = data;
    setTopOpp($('#select-top-opp').val());
}

const setTopOpp = mainSection => {
    var fcTopOppBs = new FusionCharts({
        type: 'bar2d',
        renderAt: 'chart-top-opp',
        width: '100%',
        height: '400',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": fnT(mainSection),
                "subCaption": "Top 10",
                "xAxisName": fnT('Perguntas'),
                "yAxisName": fnT('Incidência'),
                "theme": 'ocean',
                "palettecolors": "5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9",
                "showBorder": "1",
                "numberSuffix": "%"
            },
            "data": currTopOpp[mainSection].map(item => ({label: item.question_prefix, displayValue: `${Math.round(item.frecuency * 100 / item.count)}%`, value: item.frecuency * 100 / item.count, tooltext: `${fnT('Auditorias')}: ${item.frecuency}<br><br> ${item.text}`}))
        },
        events: {
            "dataPlotClick": (eventObj, dataObj) => showTopOppDetails(eventObj, dataObj)
        }
    });
    fcTopOppBs.render();
}

const showTopOppDetails = (eventObj, dataObj) => {
    swal({
        title: dataObj.categoryLabel,
        text: dataObj.toolText.split('<br><br> ')[1],
        type: "info",
        showCancelButton: true,
        confirmButtonColor: '#0B9C26',
        confirmButtonText: fnT('Baixar detalhes da picklist'),
        cancelButtonText: fnT('Fechar')
    },
    function(isConfirm){
        if (isConfirm){
            getExportable('exportTopOppDetails/' + dataObj.categoryLabel, `Opp ${dataObj.categoryLabel} details`);
        }
    });
}

window.genActionPlanStatus = (data, renderAt = 'chart-action-plan') => {
    const fcActionPlanStatus = new FusionCharts({
        type: 'doughnut2d',
        renderAt: renderAt,
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Plano de ação'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Auditorias') + ": $value",
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9',
                valueFontSize: "12",
                showBorder: '1'
            },
            data: data.map(item => ({label: fnT(item.action_plan_status), value: item.count}))
        }
    });
    fcActionPlanStatus.render();
}

window.genDaypart = (data, renderAt = 'chart-daypart') => {
    const fcActionDaypart = new FusionCharts({
        type: 'doughnut2d',
        renderAt: renderAt,
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Período do dia'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Auditorias') + ": $value",
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9',
                valueFontSize: "12",
                showBorder: '1'
            },
            data: data.map(item => ({label: fnT(item.daypart), value: item.count}))
        }
    });
    fcActionDaypart.render();
};

window.genWeekday = (data, renderAt = 'chart-weekday') => {
    const fcActionWeekday = new FusionCharts({
        type: 'doughnut2d',
        renderAt: renderAt,
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Dia da semana'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Auditorias') + ": $value",
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9',
                valueFontSize: "12",
                showBorder: '1'
            },
            data: data.map(item => ({label: fnT(daysOfWeek[item.weekday - 1]), value: item.count}))
        }
    });
    fcActionWeekday.render();
}

window.genDuration = (data, renderAt = 'chart-duration') => {
    const fcActionDuration = new FusionCharts({
        type: 'doughnut2d',
        renderAt: renderAt,
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Duração'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Auditorias') + ": $value",
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9',
                valueFontSize: "12",
                showBorder: '1'
            },
            data: data.map(item => ({label: fnT(item.duration), value: item.count}))
        }
    });
    fcActionDuration.render();
}

const genCategoryTrend = data => {
    const total = data.reduce((acc, cur) => acc + parseInt(cur.opp), 0);
    const fcCategoryTrend = new FusionCharts({
        type: 'pie2d',
        renderAt: 'chart-category-trend',
        width: '100%',
        height: '540',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Tendência da seção'),
                plottooltext: '$label',
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5, 29c3be, f2726f, A88CCC, EED482, FFAE91, FE93B5, D98ACF, 7BCDE8, 94A8E9, B5A3D1, 82C9E3, 6FB2A9, CCB0A8, 82D4E2',
                valueFontSize: "12",
                showBorder: '1',
                showlegend: "1"
            },
            data: data.map(item => ({
                label: `${item.main_section.split(' ').map(palabra => palabra.charAt(0)).join('')} ${item.section_number}: ${fnT(item.section_name)}`,
                value: item.opp,
                displayValue: `${item.main_section.split(' ').map(palabra => palabra.charAt(0)).join('')} ${item.section_number}: ${(item.opp*100 / total).toFixed(2)}% (${item.opp})`,
                tooltext: `${fnT(item.section_name)}`
            })),
        },
        events: {
            dataPlotClick: (eventObj, dataObj) => setQuestionTrend(dataObj.displayValue.split(' ')[1].replace(':', ''))
        }
    });
    fcCategoryTrend.render();
};

const genQuestionTrend = data => {
    curQuestionTrend = data;
    const value = Math.min(...new Set(data.map(item =>  parseInt(item.section_number))));

    setQuestionTrend(value);
};

const setQuestionTrend = section_number => {
    const data = curQuestionTrend.filter(item => item.section_number == section_number);
    const total = data.reduce((acc, cur) => acc + parseInt(cur.opp), 0);
    const fcCategoryTrend = new FusionCharts({
        type: 'pie2d',
        renderAt: 'chart-question-trend',
        width: '100%',
        height: '540',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Tendência de perguntas por seção') + ': ' + section_number,
                subCaption: fnT('Selecione a seção no gráfico de tendência da seção'),
                plottooltext: '$label: <b>$value</b>',
                theme: 'ocean',
                showPercentageValues: '1',
                palettecolors: '5d62b5, 29c3be, f2726f, A88CCC, EED482, FFAE91, FE93B5, D98ACF, 7BCDE8, 94A8E9, B5A3D1, 82C9E3, 6FB2A9, CCB0A8, 82D4E2',
                valueFontSize: "12",
                showBorder: '1',
                showlegend: "1"
            },
            data: data.map(item => ({
                label: `${item.question_prefix}: ${item.txt}`,
                value: item.opp,
                displayValue: `${item.question_prefix}: ${(item.opp*100 / total).toFixed(2)}% (${item.opp})`,
                tooltext: `${item.txt}`
            }))
        },
        events: {
            dataPlotClick: (eventObj, dataObj) => {
                const text = dataObj.toolText;
                const token = dataQuestionTrend.filter(item => item.txt == text)[0].tk;
                getExportable(`exportPtsDetails/${token}`, `Points ${text} details`);
            } 
        }
    });
    fcCategoryTrend.render();
}

const genProgressStatus = data => {
    currProgressStatus = data;
    setProgressStatus($('#select-groupby').val());
}

const setProgressStatus = groupBy => {
    stackData = currProgressStatus[groupBy];
        
    const fcActionDaypart = new FusionCharts({
        type: 'msstackedcolumn2d',
        renderAt: 'chart-progress-status',
        width: '100%',
        height: '400',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Prévia do programa'),
                theme: 'ocean',
                showBorder: '1',
                plottooltext: '<b>$label</b>, $percentValue, $value',
                yAxisName: fnT('Auditorias')
            },
            categories: [
                {
                    category: stackData.map(item => ({label: fnT(item.label)}))
                }
            ],
            dataset: [
                {
                    dataset: [
                        {
                            seriesname: fnT('Pendente'),
                            data: stackData.map(item => ({value: item.pending}))
                        },
                        {
                            seriesname: fnT('Em processo'),
                            data: stackData.map(item => ({value: item.in_process}))
                        },
                        {
                            seriesname: fnT('Concluído'),
                            data: stackData.map(item => ({value: item.completed}))
                        }
                    ]
                }
            ]
        }
    });
    fcActionDaypart.render();
}

const genActionCompletion = data => {   
    const fcActionDaypart = new FusionCharts({
        type: 'msstackedcolumn2d',
        renderAt: 'chart-action-completion',
        width: '100%',
        height: '442',
        dataFormat: 'json',
        theme: 'ocean',
        dataSource: {
            chart: {
                caption: fnT('Status do plano de ação'),
                theme: 'ocean',
                showBorder: '1',
                plottooltext: '$seriesname, $percentValue, $value',
                yAxisName: fnT('Auditorias'),
                palettecolors: '5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9'
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
                            seriesname: fnT('Em processo'),
                            data: data.map(item => ({value: item['In Process'] || 0}))
                        },
                        {
                            seriesname: fnT('Pendente'),
                            data: data.map(item => ({value: item.Pending}))
                        },
                        {
                            seriesname: fnT('Concluído'),
                            data: data.map(item => ({value: item.Finished}))
                        }
                    ]
                }
            ]
        }
    });
    fcActionDaypart.render();
}

$('#select-score-topbutton').on('changed.bs.select', function (e) {
    setScoreTopBottom(e.target.value);
});  

document.getElementById('filter_countrys').addEventListener('change', (e)=>{
    console.log($('#filter_countrys').val());
    actualizarTiendasFiltro();
});

document.getElementById('filter_ml').addEventListener('change', (e)=>{
    console.log($('#filter_ml').val());
    actualizarTiendasFiltro();
});

document.getElementById('filter_subF').addEventListener('change', (e)=>{
    console.log($('#filter_subF').val());
    actualizarTiendasFiltro();
});

function actualizarTiendasFiltro(){
    let paises = $('#filter_countrys').val().join(',');
    let ml = $('#filter_ml').val().join(',');
    let subF = arrayToSafeQuotedString($('#filter_subF').val());
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/statistics/actualizarTiendasFiltro';
    var strData = "countrys="+paises+"&ml="+encodeURIComponent(ml)+"&subF="+encodeURIComponent(subF);
    request.open("POST",ajaxUrl,true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(strData);
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            //console.log(request.responseText);
            var objData = JSON.parse(request.responseText);
            if(objData.status)
            {
                document.getElementById('filter_franchise').innerHTML='';
                objData.stores.forEach(e=>{
                    document.getElementById('filter_franchise').innerHTML+='<option value="'+e['name']+'" selected>'+e['name']+'</option>';
                });
                $('#filter_franchise').selectpicker('refresh');
                console.log($('#filter_franchise').val())
            }else{
                swal("Atención!", fnT(objData.msg), "error");
            }
        }
    }
}

function arrayToSafeQuotedString(arr) {
    if (!Array.isArray(arr)) return "''";
    if (arr.length === 0) return "''";
    
    return arr
        .map(item => `'${String(item).replace(/'/g, "\\'")}'`)
        .join(', ');
}

const genScoreTopBottom = data =>{
    currScoreTopBottom = data;
    setScoreTopBottom($('#select-score-topbutton').val());
}

const setScoreTopBottom = select => {
    let data = currScoreTopBottom[select];
    $('#table-score-topbutton').html(data.reduce((acc, cur) => 
        `${acc}
        <tr class="text-center">
            <td>${cur.location_number}</td>
            <td>${cur.location_name}</td>
            <td>${cur.score}</td>
        </tr>`, ''));
}

const genCompare = async (period, reference) => {
    console.log('period:'+period);
    console.log('reference:'+reference);
    const comparePayload = payload;
    comparePayload.delete('list_period[]');
    comparePayload.append('list_period[]', period);

    const fetchCompare = fetch(base_url + `/statistics/get${reference}`, {
        method: 'POST',
		body: comparePayload
    }).then(res => res.json());

    $('#divLoading').css('display', 'flex');
    const dataCompare = await fetchCompare;
    //console.log(dataCompare['sql']);
    $('#divLoading').css('display', 'none');

    window[`gen${reference}`](window[`data${reference}`], 'chart-current-period');
    window[`gen${reference}`](dataCompare, 'chart-compare-period');

    $('#modalStatisticsCompare').modal('show');
}

const limitRegion = () => {
    let setCountries = [];
    $('#filter_country').selectpicker('deselectAll');
    $('#filter_country').find(':not(:selected)').hide();

    $('#filter_region').val().forEach(element => setCountries.push(...element.split(',')));
    $('#filter_country').selectpicker('val', setCountries);

    $('#filter_country').find(':selected').show();
    $('#filter_country').selectpicker('refresh');
}

document.addEventListener('DOMContentLoaded', () => $('#filter_form').submit());