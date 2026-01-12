function saveTimes(audit_id){
    console.log(audit_id);
    let med_1M = document.getElementById('med_1M').value;
    let med_2M = document.getElementById('med_2M').value;
    let med_3M = document.getElementById('med_3M').value;
    let med_4M = document.getElementById('med_4M').value;
    let med_5M = document.getElementById('med_5M').value;
    let med_6M = document.getElementById('med_6M').value;
    let med_7M = document.getElementById('med_7M').value;
    let med_8M = document.getElementById('med_8M').value;
    let med_9M = document.getElementById('med_9M').value;
    let med_10M = document.getElementById('med_10M').value;
    let med_1S = document.getElementById('med_1S').value;
    let med_2S = document.getElementById('med_2S').value;
    let med_3S = document.getElementById('med_3S').value;
    let med_4S = document.getElementById('med_4S').value;
    let med_5S = document.getElementById('med_5S').value;
    let med_6S = document.getElementById('med_6S').value;
    let med_7S = document.getElementById('med_7S').value;
    let med_8S = document.getElementById('med_8S').value;
    let med_9S = document.getElementById('med_9S').value;
    let med_10S = document.getElementById('med_10S').value;
    let med_1 = med_1M + ':' + med_1S;
    let med_2 = med_2M + ':' + med_2S;
    let med_3 = med_3M + ':' + med_3S;
    let med_4 = med_4M + ':' + med_4S;
    let med_5 = med_5M + ':' + med_5S;
    let med_6 = med_6M + ':' + med_6S;
    let med_7 = med_7M + ':' + med_7S;
    let med_8 = med_8M + ':' + med_8S;
    let med_9 = med_9M + ':' + med_9S;
    let med_10 = med_10M + ':' + med_10S;
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/audits/saveTimes';
    var strData = "audit_id="+audit_id+"&med_1="+med_1+"&med_2="+med_2+"&med_3="+med_3+"&med_4="+med_4+"&med_5="+med_5+"&med_6="+med_6+"&med_7="+med_7+"&med_8="+med_8+"&med_9="+med_9+"&med_10="+med_10;
    request.open("POST",ajaxUrl,true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(strData);
    request.onreadystatechange = function(){

        if(request.readyState == 4 && request.status == 200){
            console.log(request.responseText);
            var objData = JSON.parse(request.responseText);

            if(objData.status)
            {
                    swal({
                        title: "",
                        text: fnT(objData.msg),
                        type: "success",
                        confirmButtonText: fnT("Aceitar"),
                        closeOnConfirm: true,
                    });
            }else{
                swal("Atención!", fnT(objData.msg), "error");
            }
        }

    }
}

function calcularPromedio(){
    let med_1M = document.getElementById('med_1M').value;
    let med_2M = document.getElementById('med_2M').value;
    let med_3M = document.getElementById('med_3M').value;
    let med_4M = document.getElementById('med_4M').value;
    let med_5M = document.getElementById('med_5M').value;
    let med_6M = document.getElementById('med_6M').value;
    let med_7M = document.getElementById('med_7M').value;
    let med_8M = document.getElementById('med_8M').value;
    let med_9M = document.getElementById('med_9M').value;
    let med_10M = document.getElementById('med_10M').value;
    let med_1S = document.getElementById('med_1S').value;
    let med_2S = document.getElementById('med_2S').value;
    let med_3S = document.getElementById('med_3S').value;
    let med_4S = document.getElementById('med_4S').value;
    let med_5S = document.getElementById('med_5S').value;
    let med_6S = document.getElementById('med_6S').value;
    let med_7S = document.getElementById('med_7S').value;
    let med_8S = document.getElementById('med_8S').value;
    let med_9S = document.getElementById('med_9S').value;
    let med_10S = document.getElementById('med_10S').value;
    let med_1 = med_1M + ':' + med_1S;
    let med_2 = med_2M + ':' + med_2S;
    let med_3 = med_3M + ':' + med_3S;
    let med_4 = med_4M + ':' + med_4S;
    let med_5 = med_5M + ':' + med_5S;
    let med_6 = med_6M + ':' + med_6S;
    let med_7 = med_7M + ':' + med_7S;
    let med_8 = med_8M + ':' + med_8S;
    let med_9 = med_9M + ':' + med_9S;
    let med_10 = med_10M + ':' + med_10S;

    let arrTimes = [med_1, med_2, med_3, med_4, med_5, med_6, med_7, med_8, med_9, med_10];
    let times = [];
    arrTimes.forEach(t=>{
        if(parseInt(t.split(':')[0])!=0 || parseInt(t.split(':')[1])!=0){
            times.push(t);
        }
    });
    let totalSegundos = 0;
    times.forEach(t=>{
        let min = parseInt(t.split(':')[0]);
        let seg = parseInt(t.split(':')[1]);
        totalSegundos += min * 60 + seg;
    });

    // Calcular promedio en segundos
    let promedioSegundos = totalSegundos / times.length;
    
    // Convertir a minutos:segundos
    let minutos = Math.floor(promedioSegundos / 60);
    let segundos = Math.round(promedioSegundos % 60);

    // Asegurar que los segundos tengan dos dígitos
    segundos = segundos.toString().padStart(2, '0');

    document.getElementById('averageM').innerHTML=minutos;
    document.getElementById('averageS').innerHTML=segundos;

}

calcularPromedio();