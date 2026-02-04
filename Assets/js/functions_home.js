const $periodos = $('#filter_period');
const $tipos = $('#filter_tipo');
const $clasificacion = $('#filter_clasificacion');
const $regional = $('#filter_regional');
const $estado = $('#filter_estado');
const $gerente = $('#filter_gerente');
const $consultor = $('#filter_consultor');
const $loja = $('#filter_loja');
const $seccion = $('#filter_seccion');
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
let chart;
let chart2;
let chart3;
let chart4;
let chart5;
let chart6;
let chart7;
let chart8;
let chart9;
let datosOrdenados;
const data = {
            
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    '#82be4dff', // Zona de exceléncia
                    '#ddd832ff', // Zona de cualidade
                    '#e16361ff', // Zona de critica
                    '#d66933ff'  // Zona de atenção
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
        chart = new Chart(ctx, config);

const value = 0;
const data2 = {
            
            datasets: [{
                    data: [value, 100 - value],
                    backgroundColor: [
                        getColorForValue(value), // Color para el valor
                        '#e0e0e0' // Color de fondo
                    ],
                    borderWidth: 0,
                    circumference: 270, // Ángulo del gráfico (270° para semicírculo)
                    rotation: 225, // Rotación inicial (225° para apuntar a la izquierda)
                }]
        };

        // Configuración
        const config2 = {
            type: 'doughnut',
            data: data2,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '80%', // Grosor del anillo
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        };

        // Crear la gráfica
        const ctx2 = document.getElementById('tacometro1').getContext('2d');
        chart2 = new Chart(ctx2, config2);
const data3 = {
            
            datasets: [{
                    data: [value, 100 - value],
                    backgroundColor: [
                        getColorForValue(value), // Color para el valor
                        '#e0e0e0' // Color de fondo
                    ],
                    borderWidth: 0,
                    circumference: 270, // Ángulo del gráfico (270° para semicírculo)
                    rotation: 225, // Rotación inicial (225° para apuntar a la izquierda)
                }]
        };

        // Configuración
        const config3 = {
            type: 'doughnut',
            data: data3,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '80%', // Grosor del anillo
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        };

        // Crear la gráfica
        const ctx3 = document.getElementById('tacometro2').getContext('2d');
        chart3 = new Chart(ctx3, config3);


        

        
actualizarEstadisticas();

$periodos.on('changed.bs.select', actualizarEstadisticas);
$tipos.on('changed.bs.select', actualizarEstadisticas);
$clasificacion.on('changed.bs.select', actualizarEstadisticas);
$regional.on('changed.bs.select', actualizarEstadisticas);
$estado.on('changed.bs.select', actualizarEstadisticas);
$gerente.on('changed.bs.select', actualizarEstadisticas);
$consultor.on('changed.bs.select', actualizarEstadisticas);
$loja.on('changed.bs.select', actualizarEstadisticas);
$seccion.on('changed.bs.select', actualizarEstadisticas);

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
// Función para determinar el color según el valor
function getColorForValue(val) {
    if (val < 80) return '#e16361'; // Verde
    if (val < 90) return '#ddd832'; // Amarillo
    return '#82be4d'; // Rojo
}

function actualizarEstadisticas(){
    const periodosSeleccionados = $periodos.val() || [];
    const tiposS = $tipos.val() || [];
    const clasificacionS = $clasificacion.val() || [];
    const regionalS = $regional.val() || [];
    const estadoS = $estado.val() || [];
    const gerenteS = $gerente.val() || [];
    const consultorS = $consultor.val() || [];
    const lojaS = $loja.val() || [];
    const secciones = $seccion.val() || [];
    $.ajax({
        cache: false,
            data: {"periodos":periodosSeleccionados, "tipos":tiposS, "clasificaciones":clasificacionS, "regiones":regionalS, "estados":estadoS, "gerentes":gerenteS, "consultores":consultorS, "tiendas":lojaS, "secciones":secciones},
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
                if(document.querySelector('.p1')){
                    document.querySelector('.p1').innerHTML=response['promedio'];
                    document.querySelector('.p2').innerHTML=response['visitas'];
                    document.querySelector('.p3').innerHTML=response['tiendas'];
                    document.querySelector('.p4').innerHTML=response['noEntrada'];
                    document.getElementById('fs').innerHTML=response['promediofs'];
                    document.getElementById('fsC').innerHTML=response['fsgg'];
                    document.getElementById('fsP').innerHTML=response['tiendasggfs']+'%';
                    document.getElementById('pm').innerHTML=response['promediopm'];
                    document.getElementById('pmC').innerHTML=response['pmgg'];
                    document.getElementById('pmP').innerHTML=response['tiendasggpm']+'%';
                    let i = 1;
                    response['top5FS'].forEach(e=>{
                        document.querySelector('.preg'+i).innerHTML=e['pregunta'];
                        document.querySelector('.porcent'+i).innerHTML=e['frecuencia']+'%';
                        i++;
                    });
                    i = 1;
                    response['top5PM'].forEach(e=>{
                        document.querySelector('.preg'+i+'PM').innerHTML=e['pregunta'];
                        document.querySelector('.porcent'+i+'PM').innerHTML=e['frecuencia']+'%';
                        i++;
                    });
                    chart.data.datasets[0].data = [response['excelencia'], response['cuidado'], response['atencion'], response['critica']];
                    chart.update();
                    let newValue = response['promediofs'];
                    newValue = Math.max(0, Math.min(100, newValue));
                    currentValue = newValue;
                    
                    // Actualizar datos
                    chart2.data.datasets[0].data = [newValue, 100 - newValue];
                    chart2.data.datasets[0].backgroundColor[0] = getColorForValue(newValue);
                    
                    // Actualizar gráfico con animación
                    chart2.update();
                    newValue = response['promediopm'];
                    newValue = Math.max(0, Math.min(100, newValue));
                    currentValue = newValue;
                    
                    // Actualizar datos
                    chart3.data.datasets[0].data = [newValue, 100 - newValue];
                    chart3.data.datasets[0].backgroundColor[0] = getColorForValue(newValue);
                    
                    // Actualizar gráfico con animación
                    chart3.update();
                }
                if(document.getElementById('pConsultor')){
                    let gerentes = [];
                    let califG = [];
                    let coloresG = [];
                    Object.keys(response['gerentes']).forEach(e => {
                        gerentes.push(response['gerentes'][e]['name']);
                        califG.push(response['gerentes'][e]['promedio']);
                        coloresG.push(getColorForValue(response['gerentes'][e]['promedio']));
                    });
                    // Ordenar los datos
                    datosOrdenados = ordenarPorPromedio(response['gerentes']);
                    chart4.data.labels = datosOrdenados.map(g => g.name);
                    chart4.data.datasets[0].data = datosOrdenados.map(g => g.promedio);
                    chart4.data.datasets[0].backgroundColor = datosOrdenados.map(g => getColorForValue(g.promedio));
                    chart4.update();

                    let estados = [];
                    let califE = [];
                    let coloresE = [];
                    Object.keys(response['estados']).forEach(e => {
                        estados.push(response['estados'][e]['name']);
                        califE.push(response['estados'][e]['promedio']);
                        coloresE.push(getColorForValue(response['estados'][e]['promedio']));
                    });
                    // Ordenar los datos
                    datosOrdenados = ordenarPorPromedio(response['estados']);

                    chart5.data.labels = datosOrdenados.map(g => g.name);
                    chart5.data.datasets[0].data = datosOrdenados.map(g => g.promedio);
                    chart5.data.datasets[0].backgroundColor = datosOrdenados.map(g => getColorForValue(g.promedio));
                    chart5.update();

                    let consultores = [];
                    let califC = [];
                    let coloresC = [];
                    Object.keys(response['consultores']).forEach(e => {
                        consultores.push(response['consultores'][e]['name']);
                        califC.push(response['consultores'][e]['promedio']);
                        coloresC.push(getColorForValue(response['consultores'][e]['promedio']));
                    });
                    // Ordenar los datos
                    datosOrdenados = ordenarPorPromedio(response['consultores']);

                    chart6.data.labels = datosOrdenados.map(g => g.name);
                    chart6.data.datasets[0].data = datosOrdenados.map(g => g.promedio);
                    chart6.data.datasets[0].backgroundColor = datosOrdenados.map(g => getColorForValue(g.promedio));
                    chart6.update();

                    let regiones = [];
                    let califR = [];
                    let coloresR = [];
                    console.log(response['regiones']);
                    Object.keys(response['regiones']).forEach(e => {
                        regiones.push(response['regiones'][e]['name']);
                        califR.push(response['regiones'][e]['promedio']);
                        coloresR.push(getColorForValue(response['regiones'][e]['promedio']));
                    });
                    // Ordenar los datos
                    datosOrdenados = ordenarPorPromedio(response['regiones']);
                    
                    // Actualizar el gráfico
                    chart7.data.labels = datosOrdenados.map(g => g.name);
                    chart7.data.datasets[0].data = datosOrdenados.map(g => g.promedio);
                    chart7.data.datasets[0].backgroundColor = datosOrdenados.map(g => getColorForValue(g.promedio));
                    chart7.update();
                }
                if(document.querySelector('.tabla')){
                    document.querySelector('.tabla tbody').innerHTML='';
                    let i = 1;
                    response['allOpp'].forEach(e=>{
                        document.querySelector('.tabla tbody').innerHTML+=`
                            <tr>
                                <td>${e['question_prefix']}</td>
                                <td>${e['pregunta']}</td>
                                <td>${e['total']}</td>
                            </tr>
                        `;
                        i++;
                    });
                    document.querySelector(".totalAudits").innerHTML=response['visitas'];
                    // Convertir objeto a array y ordenar por número de sección
                    const seccionesArray = Object.values(response['pSecc']);
                    const seccionesOrdenadas = seccionesArray.sort((a, b) => 
                        parseInt(b.score) - parseInt(a.score)
                    );
                    console.log(seccionesOrdenadas);

                    chart8.data.labels = seccionesOrdenadas.map(g => g.section_name);
                    chart8.data.datasets[0].data = seccionesOrdenadas.map(g => g.score);
                    chart8.data.datasets[0].backgroundColor = seccionesOrdenadas.map(g => getColorForValue(g.score));
                    chart8.update();

                    newValue = response['scoreG'];
                    newValue = Math.max(0, Math.min(100, newValue));
                    currentValue = newValue;

                    document.getElementById('secAll').innerHTML=newValue;
                    
                    // Actualizar datos
                    chart9.data.datasets[0].data = [newValue, 100 - newValue];
                    chart9.data.datasets[0].backgroundColor[0] = getColorForValue(newValue);
                    
                    // Actualizar gráfico con animación
                    chart9.update();
                }
            },
        contentType: "application/x-www-form-urlencoded;charset=iso-8859-1"
    });
}

function ordenarPorPromedio(datosObjeto) {
    return Object.values(datosObjeto)
        .sort((a, b) => b.promedio - a.promedio); // b - a = descendente
}

function ordenarPorPromedio2(datosObjeto) {
    return Object.values(datosObjeto)
        .sort((a, b) => b.score - a.score); // b - a = descendente
}

document.querySelectorAll('input[name="opG"]').forEach(e=>{
    e.addEventListener('click', ()=>{
        let secc = document.querySelector('input[name="opG"]:checked').value;
        document.querySelector('.contenidoD').innerHTML='';
        switch(secc){
            case "1":
                console.log('here');
                document.querySelector('.contenidoD').innerHTML=`
                <div class="contCuentas">
                    <div class="mitad">
                    <div style="display:flex; gap:10px;">
                        <div class="contPG">
                        <p>Pontuação geral</p>
                        <b class="p1">0</b>
                        </div>
                        <div class="contPG" style="background-color:#ffffff00; gap:10px; box-sizing:border-box">
                        <div class="contPS">
                            <p>Avaliações</p>
                            <b class="p2">0</b>
                        </div>
                        <div class="contPS">
                            <p>Lojas avaliadas</p>
                            <b class="p3">0</b>
                        </div>
                        </div>
                        <div class="contPG">
                        <p>Tentativas de auditoria</p>
                        <b class="p4">0</b>
                        </div>
                    </div>
                    </div>
                    <div class="mitad">
                    <div style="display:flex; gap:10px; flex-direction:column;">
                        <p>Classificação geral</p>
                        <div style="display:flex;">
                        <div class="chart-container">
                            <canvas id="myPieChart"></canvas>
                        </div>
                        <div class="contClasificacion">
                            <b>Classificação</b>
                            <div class="contColorC">
                            <div class="circulo cverde"></div>
                            <p>Zona de exceléncia</p>
                            </div>
                            <div class="contColorC">
                            <div class="circulo camarillo"></div>
                            <p>Zona de qualidade</p>
                            </div>
                            <div class="contColorC">
                            <div class="circulo crojo"></div>
                            <p>Zona de critica</p>
                            </div>
                            <div class="contColorC">
                            <div class="circulo cnaranja"></div>
                            <p>Zona de atenção</p>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="contCuentas">
                    <div class="mitad contClasificacion">
                        <b style="text-align:center;">Segurança dos Alimentos</b>
                        <div style="display:flex;">
                        <div class="mitad">
                            <div class="chart-container">
                            <canvas id="tacometro1"></canvas>
                            <b id="fs" class="califSecT">0</b>
                            </div>
                        </div>
                        <div class="mitad" style="display:flex; flex-direction:column; align-items:center;">
                            <p>Lojas acima de 80%</p>
                            <b id="fsC" style="font-size:20px;">0</b>
                            <b id="fsP" style="font-size:20px;">0%</b>
                        </div>
                        </div>
                        <div class="contTopDesvios">
                        <div class="lineaDes"></div>
                        <b>Maiores desvios de segurança dos alimentos</b>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg1"></span><span style="padding: 0 0 0 10px;" class="porcent1"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg2"></span><span style="padding: 0 0 0 10px;" class="porcent2"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg3"></span><span style="padding: 0 0 0 10px;" class="porcent3"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg4"></span><span style="padding: 0 0 0 10px;" class="porcent4"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg5"></span><span style="padding: 0 0 0 10px;" class="porcent5"></span></div>
                        </div>
                    </div>
                    <div class="mitad contClasificacion">
                        <b style="text-align:center;">Padrões da Marca</b>
                        <div style="display:flex;">
                        <div class="mitad">
                            <div class="chart-container">
                            <canvas id="tacometro2"></canvas>
                            <b id="pm" class="califSecT">0</b>
                            </div>
                        </div>
                        <div class="mitad" style="display:flex; flex-direction:column; align-items:center;">
                            <p>Lojas acima de 80%</p>
                            <b id="pmC" style="font-size:20px;">0</b>
                            <b id="pmP" style="font-size:20px;">0%</b>
                        </div>
                        </div>
                        <div class="contTopDesvios">
                        <div class="lineaDes"></div>
                        <b>Maiores desvios de segurança dos alimentos</b>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg1PM"></span><span style="padding: 0 0 0 10px;" class="porcent1PM"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg2PM"></span><span style="padding: 0 0 0 10px;" class="porcent2PM"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg3PM"></span><span style="padding: 0 0 0 10px;" class="porcent3PM"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg4PM"></span><span style="padding: 0 0 0 10px;" class="porcent4PM"></span></div>
                        <div style="display:flex; width:100%; justify-content:space-between;"><span class="preg5PM"></span><span style="padding: 0 0 0 10px;" class="porcent5PM"></span></div>
                        </div>
                    </div>
                </div>`;
                const data = {
            
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            '#82be4dff', // Zona de exceléncia
                            '#ddd832ff', // Zona de cualidade
                            '#e16361ff', // Zona de critica
                            '#d66933ff'  // Zona de atenção
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
                chart = new Chart(ctx, config);

        const value = 0;
        const data2 = {
                    
                    datasets: [{
                            data: [value, 100 - value],
                            backgroundColor: [
                                getColorForValue(value), // Color para el valor
                                '#e0e0e0' // Color de fondo
                            ],
                            borderWidth: 0,
                            circumference: 270, // Ángulo del gráfico (270° para semicírculo)
                            rotation: 225, // Rotación inicial (225° para apuntar a la izquierda)
                        }]
                };

                // Configuración
                const config2 = {
                    type: 'doughnut',
                    data: data2,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '80%', // Grosor del anillo
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                };

                // Crear la gráfica
                const ctx2 = document.getElementById('tacometro1').getContext('2d');
                chart2 = new Chart(ctx2, config2);
        const data3 = {
                    
                    datasets: [{
                            data: [value, 100 - value],
                            backgroundColor: [
                                getColorForValue(value), // Color para el valor
                                '#e0e0e0' // Color de fondo
                            ],
                            borderWidth: 0,
                            circumference: 270, // Ángulo del gráfico (270° para semicírculo)
                            rotation: 225, // Rotación inicial (225° para apuntar a la izquierda)
                        }]
                };

                // Configuración
                const config3 = {
                    type: 'doughnut',
                    data: data3,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '80%', // Grosor del anillo
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                };

                // Crear la gráfica
                const ctx3 = document.getElementById('tacometro2').getContext('2d');
                chart3 = new Chart(ctx3, config3);
                break;
            case "2":
                document.querySelector('.contenidoD').innerHTML=`
                <div class="contCuentas">
                    <div class="mitad">
                    <b>Pontuação por gerente</b>
                    <div class="chart-container">
                        <canvas id="pGerente"></canvas>
                    </div>
                    </div>
                    <div class="mitad">
                    <b>Pontuação por estado</b>
                    <div class="chart-container">
                        <canvas id="pEstado"></canvas>
                    </div>
                    </div>
                </div>
                <div class="contCuentas">
                    <div class="mitad">
                    <b>Pontuação por consultor</b>
                    <div class="chart-container">
                        <canvas id="pConsultor"></canvas>
                    </div>
                    </div>
                    <div class="mitad">
                    <b>Pontuação por regional</b>
                    <div class="chart-container">
                        <canvas id="pRegion"></canvas>
                    </div>
                    </div>
                </div>
                `;
                // Configuración
                const config4 = {
                    type: 'bar',
                    data: {
                            labels: [],    
                            datasets: [{
                                label: 'Pontuação',
                                data: [],   
                                backgroundColor: [],  
                                borderColor: '#333',
                                borderWidth: 1,
                                borderRadius: 5,
                                barPercentage: 0.7
                            }]
                        },
                    options: {
                        scales: {
                        y: {
                            beginAtZero: true
                        }
                        }
                    },
                    plugins: [{
                        id: 'porcentajesVerticales',
                        afterDatasetsDraw(chart) {
                            const { ctx, data, chartArea: { top, bottom, left, right } } = chart;
                            
                            // Solo si hay datos
                            if (!data.datasets[0].data.length) return;
                            
                            ctx.save();
                            ctx.font = 'bold 12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#FFFFFF';
                            
                            data.datasets[0].data.forEach((valor, index) => {
                                if (valor == null) return;
                                
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                
                                if (!bar) return;
                                
                                const texto = `${valor.toFixed(1)}%`;
                                
                                // POSICIÓN DENTRO DE LA BARRA (vertical)
                                const x = bar.x;
                                const mitadBarra = bar.y + (bar.height / 2);
                                
                                // Rotar texto verticalmente
                                ctx.save();
                                ctx.translate(x, mitadBarra);
                                ctx.rotate(-Math.PI / 2); // Rotar 90 grados (-90°)
                                
                                // Dibujar texto rotado
                                ctx.fillText(texto, 0, 0);
                                
                                ctx.restore();
                            });
                            
                            ctx.restore();
                        }
                    }]
                };
                // Crear la gráfica
                const ctx4 = document.getElementById('pGerente').getContext('2d');
                chart4 = new Chart(ctx4, config4);

                // Configuración
                const config5 = {
                    type: 'bar',
                    data: {
                            labels: [],    // Nombres de gerentes
                            datasets: [{
                                label: 'Pontuação',
                                data: [],   // Valores de calificación
                                backgroundColor: [],  // Colores dinámicos
                                borderColor: '#333',
                                borderWidth: 1,
                                borderRadius: 5,
                                barPercentage: 0.7
                            }]
                        },
                    options: {
                        scales: {
                        y: {
                            beginAtZero: true
                        }
                        }
                    },
                    plugins: [{
                        id: 'porcentajesVerticales',
                        afterDatasetsDraw(chart) {
                            const { ctx, data, chartArea: { top, bottom, left, right } } = chart;
                            
                            // Solo si hay datos
                            if (!data.datasets[0].data.length) return;
                            
                            ctx.save();
                            ctx.font = 'bold 12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#FFFFFF';
                            
                            data.datasets[0].data.forEach((valor, index) => {
                                if (valor == null) return;
                                
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                
                                if (!bar) return;
                                
                                const texto = `${valor.toFixed(1)}%`;
                                
                                // POSICIÓN DENTRO DE LA BARRA (vertical)
                                const x = bar.x;
                                const mitadBarra = bar.y + (bar.height / 2);
                                
                                // Rotar texto verticalmente
                                ctx.save();
                                ctx.translate(x, mitadBarra);
                                ctx.rotate(-Math.PI / 2); // Rotar 90 grados (-90°)
                                
                                // Dibujar texto rotado
                                ctx.fillText(texto, 0, 0);
                                
                                ctx.restore();
                            });
                            
                            ctx.restore();
                        }
                    }]
                };

                // Crear la gráfica
                const ctx5 = document.getElementById('pEstado').getContext('2d');
                chart5 = new Chart(ctx5, config5);

                // Configuración
                const config6 = {
                    type: 'bar',
                    data: {
                            labels: [],    // Nombres de gerentes
                            datasets: [{
                                label: 'Pontuação',
                                data: [],   // Valores de calificación
                                backgroundColor: [],  // Colores dinámicos
                                borderColor: '#333',
                                borderWidth: 1,
                                borderRadius: 5,
                                barPercentage: 0.7
                            }]
                        },
                    options: {
                        scales: {
                        y: {
                            beginAtZero: true
                        }
                        }
                    },
                    plugins: [{
                        id: 'porcentajesVerticales',
                        afterDatasetsDraw(chart) {
                            const { ctx, data, chartArea: { top, bottom, left, right } } = chart;
                            
                            // Solo si hay datos
                            if (!data.datasets[0].data.length) return;
                            
                            ctx.save();
                            ctx.font = 'bold 12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#FFFFFF';
                            
                            data.datasets[0].data.forEach((valor, index) => {
                                if (valor == null) return;
                                
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                
                                if (!bar) return;
                                
                                const texto = `${valor.toFixed(1)}%`;
                                
                                // POSICIÓN DENTRO DE LA BARRA (vertical)
                                const x = bar.x;
                                const mitadBarra = bar.y + (bar.height / 2);
                                
                                // Rotar texto verticalmente
                                ctx.save();
                                ctx.translate(x, mitadBarra);
                                ctx.rotate(-Math.PI / 2); // Rotar 90 grados (-90°)
                                
                                // Dibujar texto rotado
                                ctx.fillText(texto, 0, 0);
                                
                                ctx.restore();
                            });
                            
                            ctx.restore();
                        }
                    }]
                };

                // Crear la gráfica
                const ctx6 = document.getElementById('pConsultor').getContext('2d');
                chart6 = new Chart(ctx6, config6);

                // Configuración
                const config7 = {
                    type: 'bar',
                    data: {
                            labels: [],    // Nombres de gerentes
                            datasets: [{
                                label: 'Pontuação',
                                data: [],   // Valores de calificación
                                backgroundColor: [],  // Colores dinámicos
                                borderColor: '#333',
                                borderWidth: 1,
                                borderRadius: 5,
                                barPercentage: 0.7
                            }]
                        },
                    options: {
                        scales: {
                        y: {
                            beginAtZero: true
                        }
                        }
                    },
                    plugins: [{
                        id: 'porcentajesVerticales',
                        afterDatasetsDraw(chart) {
                            const { ctx, data, chartArea: { top, bottom, left, right } } = chart;
                            
                            // Solo si hay datos
                            if (!data.datasets[0].data.length) return;
                            
                            ctx.save();
                            ctx.font = 'bold 12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = '#FFFFFF';
                            
                            data.datasets[0].data.forEach((valor, index) => {
                                if (valor == null) return;
                                
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                
                                if (!bar) return;
                                
                                const texto = `${valor.toFixed(1)}%`;
                                
                                // POSICIÓN DENTRO DE LA BARRA (vertical)
                                const x = bar.x;
                                const mitadBarra = bar.y + (bar.height / 2);
                                
                                // Rotar texto verticalmente
                                ctx.save();
                                ctx.translate(x, mitadBarra);
                                ctx.rotate(-Math.PI / 2); // Rotar 90 grados (-90°)
                                
                                // Dibujar texto rotado
                                ctx.fillText(texto, 0, 0);
                                
                                ctx.restore();
                            });
                            
                            ctx.restore();
                        }
                    }]
                };

                // Crear la gráfica
                const ctx7 = document.getElementById('pRegion').getContext('2d');
                chart7 = new Chart(ctx7, config7);
                break;
            case "3":
                document.querySelector('.contenidoD').innerHTML=`
                    <div class="contCuentas">
                    <div class="mitad" style="width:65%">
                            <table class="tabla">
                            <thead>
                                <tr>
                                <th style="width: 10%">#</th>
                                <th style="width: 80%">Pergunta</th>
                                <th style="width: 10%"># de NCs</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>
                    </div>
                    <div class="mitad" style="width:35%; display:flex; flex-direction:column;">
                        <div style="display:flex;">
                            <div class="mitad" style="display:flex; flex-direction:column; align-items: center;">
                            <b class="totalAudits" style="font-size:40px;"></b>
                            <span>Auditorias</span>
                            </div>
                            <div class="mitad">
                            <span>Média por seções</span>
                            <div class="chart-container">
                            <canvas id="tacometro3"></canvas>
                            <b id="secAll" class="califSecT">0</b>
                            </div>
                            </div>
                        </div>
                        <div style="display:flex;">
                        <div class="chart-container">
                                <canvas id="gSecciones" style="min-height:1000px; max-width:300px;"></canvas>
                        </div>
                        </div>
                    </div>
                    </div>
                `;

                // Configuracion

                const config8 = {
                    type: 'bar',
                    data: {
                            labels: [],    // Nombres de gerentes
                            datasets: [{
                                label: 'Pontuação',
                                data: [],   // Valores de calificación
                                backgroundColor: [],  // Colores dinámicos
                                borderColor: '#333',
                                borderWidth: 1,
                                borderRadius: 5,
                                barPercentage: 0.7,
                                barThickness: 20, // Grosor fijo de barras
                            }]
                        },
                    options: {
                        indexAxis: 'y', // Esto hace las barras horizontales
                        responsive: true,
                        maintainAspectRatio: false, // IMPORTANTE: desactivar proporción
                    },
                    plugins: [{
                        id: 'textoEnBarrasHorizontales',
                        afterDatasetsDraw(chart) {
                            const { ctx, data } = chart;
                            
                            // Solo si hay datos
                            if (!data.datasets[0].data.length) return;
                            
                            ctx.save();
                            ctx.font = 'bold 13px Arial';
                            ctx.textBaseline = 'middle';
                            
                            data.datasets[0].data.forEach((valor, index) => {
                                if (valor == null) return;
                                
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                
                                if (!bar) return;
                                
                                const texto = `${valor.toFixed(1)}%`;
                                const textoWidth = ctx.measureText(texto).width;
                                
                                // Para barras horizontales: bar.x es el final, bar.base es el inicio
                                const largoBarra = bar.x - bar.base;
                                const y = bar.y; // Posición Y es constante para cada barra
                                
                                // Calcular si el texto cabe dentro de la barra
                                if (textoWidth + 15 < largoBarra) {
                                    // Cabe dentro: poner al final de la barra (derecha)
                                    const x = bar.x - 10; // 10px antes del final
                                    ctx.textAlign = 'right';
                                    
                                    // Determinar color de texto según color de fondo
                                    const colorFondo = data.datasets[0].backgroundColor[index];
                                    const esColorClaro = esColorClaroFunc(colorFondo);
                                    ctx.fillStyle = esColorClaro ? '#333333' : '#FFFFFF';
                                    
                                    ctx.fillText(texto, x, y);
                                } else {
                                    // No cabe dentro: poner fuera (a la derecha)
                                    const x = bar.x + 10; // 10px después del final
                                    ctx.textAlign = 'left';
                                    ctx.fillStyle = '#333333';
                                    ctx.fillText(texto, x, y);
                                }
                            });
                            
                            ctx.restore();
                        }
                    }],
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { 
                                callback: v => v + '%',
                                font: { size: 12 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 12,
                                    family: 'Arial, sans-serif'
                                },
                                autoSkip: false, // IMPORTANTE: mostrar todos los labels
                                maxRotation: 0,
                                minRotation: 0,
                                padding: 10 // Espacio entre labels
                            },
                            grid: {
                                display: false
                            },
                            afterFit: function(scale) {
                                // Asegurar suficiente espacio para labels largos
                                scale.width = Math.max(300, 
                                    Math.max(...secciones.map(s => s.section_name.length * 8))
                                );
                            }
                        }
                    },
                };

                // Crear la gráfica
                const ctx8 = document.getElementById('gSecciones').getContext('2d');
                chart8 = new Chart(ctx8, config8);

                const data9 = {
                            
                            datasets: [{
                                    data: [0, 100 - 0],
                                    backgroundColor: [
                                        getColorForValue(0), // Color para el valor
                                        '#e0e0e0' // Color de fondo
                                    ],
                                    borderWidth: 0,
                                    circumference: 270, // Ángulo del gráfico (270° para semicírculo)
                                    rotation: 225, // Rotación inicial (225° para apuntar a la izquierda)
                                }]
                        };

                // Configuración
                const config9 = {
                    type: 'doughnut',
                    data: data9,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '80%', // Grosor del anillo
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                };

                // Crear la gráfica
                const ctx9 = document.getElementById('tacometro3').getContext('2d');
                chart9 = new Chart(ctx9, config9);
                break;
        }
        actualizarEstadisticas();
    });
});

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

// Función para determinar si color es claro (necesita texto oscuro)
function esColorClaroFunc(color) {
    if (!color) return false;
    
    try {
        let r, g, b;
        
        if (color.startsWith('#')) {
            r = parseInt(color.slice(1, 3), 16);
            g = parseInt(color.slice(3, 5), 16);
            b = parseInt(color.slice(5, 7), 16);
        } else if (color.startsWith('rgb')) {
            const match = color.match(/\d+/g);
            r = parseInt(match[0]);
            g = parseInt(match[1]);
            b = parseInt(match[2]);
        } else {
            return false;
        }
        
        // Calcular luminosidad (fórmula estándar)
        const luminosidad = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminosidad > 0.6; // Si > 0.6, es color claro
    } catch {
        return false;
    }
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
                "xAxisName": fnT('Perguntas'),
                "yAxisName": fnT('Incidência'),
                "theme": 'ocean',
                "palettecolors": "5d62b5,29c3be,f2726f,A88CCC,EED482,FFAE91,FE93B5,D98ACF,7BCDE8,94A8E9",
                "showBorder": "1",
                numberSuffix: "%"
            },
            "data": currTopOpp[mainSection].map(item => ({label: item.question_prefix, value: `${Math.round(item.frecuency * 100 / item.count)}%`, tooltext: `${fnT('Auditorias')}: ${item.frecuency}<br><br> ${item.text}`}))
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
                caption: fnT('Status do plano de ação'),
                theme: 'ocean',
                showBorder: '1',
                "yAxisName": fnT('Auditorias'),
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
                            seriesname: fnT('Pendente'),
                            data: data.map(item => ({value: item.pending}))
                        },
                        {
                            seriesname: fnT('Em processo'),
                            data: data.map(item => ({value: item.in_process}))
                        },
                        {
                            seriesname: fnT('Finalizado'),
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
                caption: fnT('Média trimestral total'),
                theme: 'ocean',
                showBorder: '1',
                valueFontSize: "12",
                yAxisName: fnT('Auditorias'),
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
                    seriesname: fnT('Platina'),
                    data: data.map(item => ({value: Math.round(item.sum_platino * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Verde'),
                    data: data.map(item => ({value: Math.round(item.sum_verde * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Amarelo'),
                    data: data.map(item => ({value: Math.round(item.sum_amarillo * 100 / item.sum_total)}))
                },
                {
                    seriesname: fnT('Vermelho'),
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
            title: fnT('Alerta'),
            text: fnT('Limite de arquivos atingido'),
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
            title: fnT('Erro'),
            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
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
        <strong>${file.name}</strong> / ${fnT('Tamanho')}": ${file.size}b
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
            title: fnT('Alerta'),
            text: fnT('Adicione pelo menos um arquivo'),
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
            title: fnT('Erro'),
            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
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
                    <small>${fnT('Criado')}: ${cur.created} &#124; ${fnT('Por')}: ${cur.name}</small>
                </div>
                <ul class="list-group list-group-flush">
                    ${Object.values(cur.jfiles).reduce((_acc, _cur) => _acc + `<li class="list-group-item">
                        <a href="${_cur.url}" target="_blank" download>${_cur.name} &#124; ${fnT('Tamanho')}: ${_cur.size}b</a>
                    </li>`, '')}
                </ul>
                ${permissionDoc.u == 1 || permissionDoc.d == 1? `<div class="card-footer bg-white text-right">
                    <button type="button" class="btn btn-warning btn-sm mr-2" onclick="prepareUpdFile(${cur.id})" ${permissionDoc.u != 1? 'disabled' : ''}>${fnT('Editar')}&#160;&#160;<i class="fa fa-pencil"></i></button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFile(${cur.id})" ${permissionDoc.d != 1? 'disabled' : ''}>${fnT('Remover')}&#160;&#160;<i class="fa fa-trash"></i></button>
                </div>` : ''}
            </div>
        </div>`;
        first = false;
        return acc;
    }, "<h5 class='mt-2'>" + fnT('Nenhum arquivo para mostrar') + "</h5>");
}

const removeFile = id => {
    swal({
        title: fnT('Alerta'),
        text: fnT('Deseja remover estes arquivos?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
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
                    title: fnT('Erro'),
                    text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
                    type: 'error'
                });
            }
        }
    });
}

const prepareNewFile = () => {
    document.getElementById('form-panel-files').innerHTML = '';
    document.getElementById('form-files').reset();
    $('#btn-send-af').html(fnT('Inserir registro'));
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
            <strong>${value.name}</strong> / ${fnT('Tamanho')}": ${value.size}b
            <button type="button" class="close" onclick="dropFile('${key}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
    });
    document.getElementById('form-panel-files').innerHTML = tmp;
    
    $('#btn-send-af').html(fnT('Atualizar registro'));
    $('#collapseFormFile').collapse('show');
}

document.addEventListener('DOMContentLoaded', reloadAll);