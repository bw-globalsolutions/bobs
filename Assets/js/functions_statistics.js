const reloadAll = async (element) => {
    payload = new FormData(element);
    
    const fetchTopOpp = fetch(base_url + "/statistics/getTopOpp", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchLeadership = fetch(base_url + "/statistics/getLeadership", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());

    const fetchActionPlanStatus = fetch(base_url + "/statistics/getActionPlanStatus", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchDaypart = fetch(base_url + "/statistics/getDaypart", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchWeekday = fetch(base_url + "/statistics/getWeekday", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchDuration = fetch(base_url + "/statistics/getDuration", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchProgressStatus = fetch(base_url + "/statistics/getProgressStatus", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());

    const fetchCategoryTrend = fetch(base_url + "/statistics/getCategoryTrend", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchQuestionTrend = fetch(base_url + "/statistics/getQuestionTrend", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    const fetchActionCompletion = fetch(base_url + "/statistics/getActionCompletion", {//OK
        method: 'POST',
		body: payload
    }).then(res => res.json());

    const fetchScoreTopBottom = fetch(base_url + "/statistics/getScoreTopBottom", {
        method: 'POST',
		body: payload
    }).then(res => res.json());
    
    $('#divLoading').css('display', 'flex');
    [dataTopOpp, 
     dataLeadership, 
     dataActionPlanStatus, 
     dataDaypart, 
     dataWeekday, 
     dataDuration, 
     dataProgressStatus, 
     dataCategoryTrend, 
     dataQuestionTrend, 
     dataActionCompletion, 
     dataScoreTopBottom] = await Promise.all([fetchTopOpp, 
                                              fetchLeadership, 
                                              fetchActionPlanStatus, 
                                              fetchDaypart, 
                                              fetchWeekday, 
                                              fetchDuration, 
                                              fetchProgressStatus, 
                                              fetchCategoryTrend, 
                                              fetchQuestionTrend, 
                                              fetchActionCompletion, 
                                              fetchScoreTopBottom]);
    
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

const genLeadership = data => $('#tableLeadership').dataTable({
    footerCallback: function(){

        const api = this.api();

        const total_visits = api
            .column(1)
            .data()
            .reduce((a, b) => a + parseFloat(b), 0);
        $(api.column(1).footer()).html(total_visits);

        const total_rows = api.column(0).data().length
        for (var i = 2; i < 7; i++) {
            const total_items = api
                .column(i)
                .data()
                .reduce((a, b) => a + parseFloat(b), 0);
            $(api.column(i).footer()).html((total_items / total_rows).toFixed(2));
        }
    },
    "aProcessing":true,
    "aServerSide":true,
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
    },
    "data": data,
    "columns": [
            {"data":"franchissees_name"},
            {"data":"visits"},
            {"data":"criticos"},
            {"data":"no_criticos"},
            {"data":"amarillo"},
            {"data":"rojo"},
            {"data":"mantenimiento"},
    ],
    "dom": "lrtip",
    "buttons": [],
    "resonsieve":"true",
    "bDestroy": true,
    "iDisplayLength": 10,
    "order":[[0,"asc"]],
    "paging": false,
    "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
    ]
});

const genTopOpp = data => {
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
                "caption": '',
                "subCaption": "Top 10",
                "xAxisName": fnT('Questions'),
                "yAxisName": fnT('Incidence'),
                "theme": 'ocean',
                "palettecolors": "5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9",
                "showBorder": "1",
                "numberSuffix": "%"
            },
            "data": currTopOpp[mainSection].map(item => ({label: item.question_prefix, displayValue: `${Math.round(item.frecuency * 100 / item.count)}%`, value: item.frecuency * 100 / item.count, tooltext: `${fnT('Audits')}: ${item.frecuency}<br><br> ${item.text}`}))
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
        confirmButtonText: fnT('Download'),
        cancelButtonText: fnT('Close')
    },
    function(isConfirm){
        if (isConfirm){
            getExportable('exportTopOppDetails/' + dataObj.categoryLabel, `Opp ${dataObj.categoryLabel} details`);
        }
    });
}

window.genActionPlanStatus = (data, renderAt = 'chart-action-plan') => {
    // Definimos colores fijos por status
    const statusColors = {
        'Pending': '29c3be',   // rojo
        'In Process': 'f2726f', // verde agua
        'Finished': '5d62b5'  // morado
    };

    const fcActionPlanStatus = new FusionCharts({
        type: 'doughnut2d',
        renderAt: renderAt,
        width: '100%',
        height: '400',
        dataFormat: 'json',
        dataSource: {
            chart: {
                caption: fnT('Action plan'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Audits') + ": $value",
                theme: 'ocean',
                showPercentageValues: '1',
                valueFontSize: "12",
                showBorder: '1'
            },
            data: data.map(item => ({
                label: fnT(item.action_plan_status),
                value: item.count,
                color: statusColors[item.action_plan_status] || 'cccccc' // gris si no está en el mapa
            }))
        }
    });
    fcActionPlanStatus.render();
};


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
                caption: fnT('Daypart'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Audits') + ": $value",
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
                caption: fnT('Weekday'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Audits') + ": $value",
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
                caption: fnT('Duration'),
                plottooltext: '$label: <b>$value</b>',
                centerlabel: fnT('Audits') + ": $value",
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
                caption: fnT('Section trend'),
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
                caption: fnT('Question trend by section') + ': ' + section_number,
                subCaption: fnT('Select section in chart section trend'),
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
                caption: fnT('Program preview'),
                theme: 'ocean',
                showBorder: '1',
                plottooltext: '<b>$label</b>, $percentValue, $value',
                yAxisName: fnT('Audits')
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
                            seriesname: fnT('Pending'),
                            data: stackData.map(item => ({value: item.pending}))
                        },
                        {
                            seriesname: fnT('In Process'),
                            data: stackData.map(item => ({value: item.in_process}))
                        },
                        {
                            seriesname: fnT('Completed'),
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
                caption: fnT('Action plan status'),
                theme: 'ocean',
                showBorder: '1',
                plottooltext: '$seriesname, $percentValue, $value',
                yAxisName: fnT('Audits'),
                palettecolors: 'f2726f,29c3be,5d62b5'
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
                            seriesname: fnT('In Process'),
                            data: data.map(item => ({value: item['In Process'] || 0}))
                        },
                        {
                            seriesname: fnT('Pending'),
                            data: data.map(item => ({value: item.Pending}))
                        },
                        {
                            seriesname: fnT('Finished'),
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
    const comparePayload = payload;
    comparePayload.delete('list_period[]');
    comparePayload.append('list_period[]', period);

    const fetchCompare = fetch(base_url + `/statistics/get${reference}`, {
        method: 'POST',
		body: comparePayload
    }).then(res => res.json());

    $('#divLoading').css('display', 'flex');
    const dataCompare = await fetchCompare;
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



