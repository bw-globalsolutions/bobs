/*** ---------------------------
 *  FILTRAR COUNTRIES POR REGION
 * --------------------------- ***/

// Guardar estado inicial del select completo
let originalOptgroups = null;

document.addEventListener("DOMContentLoaded", () => {

    // Clonamos el select original antes de que Bootstrap Select lo modifique
    const selectCountry = document.querySelector("#list_country");
    originalOptgroups = selectCountry.cloneNode(true);

    // Evento Change para regiones
    $("#list_region").on("changed.bs.select", function () {
        filterCountriesByRegion();
    });

    // Trigger inicial
    filterCountriesByRegion();
});


function filterCountriesByRegion() {

    const selectedRegions = $("#list_region").val() || [];

    console.log("▶ Regiones seleccionadas:", selectedRegions);

    const selectCountry = document.querySelector("#list_country");

    // Reset al estado original SIN Bootstrap Select
    selectCountry.innerHTML = "";
    originalOptgroups.childNodes.forEach(n => {
        selectCountry.appendChild(n.cloneNode(true));
    });

    // Si NO hay regiones seleccionadas → mostrar todo
    if (selectedRegions.length === 0) {
        $("#list_country").selectpicker("refresh");
        return;
    }

    // Recorrer optgroups y ocultar los que no coinciden
    const groups = selectCountry.querySelectorAll("optgroup");

    groups.forEach(group => {
        const region = group.getAttribute("data-region");

        if (!selectedRegions.includes(region)) {
            group.style.display = "none";
            // quitar selección
            group.querySelectorAll("option").forEach(opt => opt.selected = false);
        } else {
            group.style.display = "block";
        }
    });

    console.log("▶ Países filtrados aplicados.");

    $("#list_country").selectpicker("refresh");
}





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

const genTable = data => {

    if(!data.length){
        $('#cotainer-table-pp').html(`<h4>${fnT('No records were found with these parameters')} :(</h4>`);
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
                <th>${fnT('Auditor email')}</th>
                ${Object.keys(data[0].periods).reduce((acc, cur) => `${acc}<th>${cur}</th>`, '')}
                <th>${fnT('Total')}</th>
            </tr>
        </thead>
    </table>`);
    
    $('#table-pp').DataTable({
        dom: 'Bfrtip',
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
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