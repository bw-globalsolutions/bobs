let currReference = 0;

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
$('#divLoading').css('display', 'flex');
window.onload = function() {
  darStyle();
  $('#divLoading').css('display', 'none');
  window.scrollBy({
        top: 500,
        behavior: 'smooth'
    });
};

function darStyle(){
    col=1;
    row=1;
    rowR=1;
    i=1;
    conjunto=1;
    totalDeConjuntos = document.querySelectorAll('.imgGal').length/5;
    console.log(totalDeConjuntos);
    document.querySelectorAll('.imgGal').forEach(e=>{
        if(col%3==0 && conjunto<totalDeConjuntos){
            if(i%2==0){
                e.style.gridColumn='3 / span 2';
                e.style.gridRow=rowR+' / span 2';
                e.style.height='360px';
            }else{
                e.style.gridColumn='1 / span 2';
                e.style.gridRow=rowR+' / span 2';
                e.style.height='360px';
            }
            rowR+=2;
            i++;
            conjunto++;
        }
        if(col==4){
            col=1;
            row++;
        }else{
            col++;
        }
    });
}

async function openImage(element, title, type, reference_id){

    if(type == 'Opportunity'){
        $('#divLoading').css('display', 'flex');
        const response = await fetch(`${base_url}/audit_Opp/getOpp/${reference_id}`);
        const data = await response.json();

        $('#question_prefix').html(data.question_prefix);
        $('#question_text').html(data.text);
        $('#question_answers').html(data.answers.join(', '));
        $('#divLoading').css('display', 'none');
        $('#photo-action-panel').show();
    }else{
        $('#photo-action-panel').hide();
    }

    const showImagePanel = document.getElementById('show-image-panel');
    let clone = element.cloneNode();
    clone.classList.replace('of-cover', 'of-contain');
    showImagePanel.innerHTML = '';
    showImagePanel.append(clone);
    $('#show-image-title').html(title);
    $('#show-image-modal').modal('show');
}