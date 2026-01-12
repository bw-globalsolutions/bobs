const panelGallery = document.getElementById('panel-gallery');

const reloadAll = async (element) => {
    payload = new FormData(element);
    
    const fetchGallery = fetch(base_url + "/statistics/getGallery", {
        method: 'POST',
		body: payload
    }).then(res => res.json());

    $('#divLoading').css('display', 'flex');
        panelGallery.innerHTML = '';
        dataGallery = await fetchGallery;

    $('#divLoading').css('display', 'none');
    
    if(dataGallery.length){
        showElements();
        showElementsQuestion();
    }else{
        panelGallery.innerHTML = `<h2>${fnT('Nenhuma imagem para mostrar')}</h2>`;
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

function showElements(){
    let n = 4;
    let index = dataGallery.length;


    while (n != 0 && dataGallery.length) {

        
       
        const item = dataGallery.shift();
        let carryAudit = '';
   
        
      

        Object.entries(item.files).forEach(([category, files]) => {
          
         
            let carryCat = '';
            files.forEach(f => {

               carryCat += `<div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 cr-pointer tooltip${index}" style="height: 200px" data-toggle="tooltip" data-placement="top" title="${f.name}">
                               <img class="h-100 w-100 of-cover" width="500" height="400" src="${f.url}" onclick="openImage(this, '${f.name}', '${category}', ${f.reference_id || 'null'})" loading="lazy">
                            </div>`;
            });

             carryAudit += `<ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                   <h4 class="h6 mb-3">${fnT(category)}: </h4>
                                   <div class='row'>${carryCat}</div>
                                </li>
                             </ul>`  
        });

        panelGallery.innerHTML  += `<div class='tile'>
                                        <h2 class='h5 mb-1'>
                                            ${item.brand_prefix} #${item.number} (${item.country_name}) ${fnT(item.type)}, 
                                            <span class='text-secondary'> 
                                                ${item.date_visit}  
                                            </span>
                                        </h2>
                                        <p class='mb-3'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;${fnT('ID da auditoria')}: ${item.id},&nbsp;&nbsp;${fnT('Auditor')}: ${item.auditor_name},&nbsp;&nbsp;${fnT('Status')}: ${fnT(item.status)}
                                        </p>
                                        ${carryAudit}
                                    </div>`;
        n--;
    }
    if(index){
        $(`#panel-gallery .tooltip${index}[data-toggle='tooltip']`).tooltip();
    }
}



const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting && typeof dataGallery !== 'undefined') {
            showElements();
        }
    });
}, { root: null, rootMargin: '0px', threshold: 0 });

const miElemento = document.getElementById('upload-img');
observer.observe(miElemento);