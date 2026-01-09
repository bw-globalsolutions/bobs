function toggleLanguage(language_id, status){
    const itemTranslate = document.querySelectorAll('.item-translate');
    itemTranslate.forEach(item => {
        const lan = item.getAttribute('data-language');
        if(lan == language_id){
            if(status)
                item.classList.remove('d-none');
            else
                item.classList.add('d-none');
        }
    });
    searchString($('#filter_search').val());
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

function searchString(val){
    const wordRow = document.querySelectorAll('.word-row');
    wordRow.forEach(item => {
        if(val != ''){
            let haveData = false;
            const itemTranslate = item.querySelectorAll('.item-translate:not(.d-none)');
            itemTranslate.forEach(_item => {
                const translation = _item.getAttribute('data-translation');
                if(translation.toLowerCase().indexOf(val.toLowerCase()) > -1){
                    haveData = true;
                }
            })
            if(!haveData){
                item.classList.add('d-none');
            }else{
                item.classList.remove('d-none');
            }
        } else{
            item.classList.remove('d-none');
        }
    } )    
}

function openModalAdd(dictionary_id, language_id, language, word){
    $('#to-language').html(language);
    $('#word-translate').html(word);
    
    const translation = $(`#cell-dictionary${dictionary_id} [data-language='${language_id}']`).data('translation');
    if(translation != undefined){
        $('#input-word-translate').val(translation);
        $('#btn-remove').removeClass('d-none');
        curItemTable = upItemTable;
    }else{
        $('#input-word-translate').val('');
        $('#btn-remove').addClass('d-none');
        curItemTable = addItemTable;
    }
    $('#input-dictionary_id').val(dictionary_id);
    $('#input-language_id').val(language_id);

    $('#modal-add-translation').modal('show');
}

function sendTranslate(element){
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to update this translation?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, async function(isConfirm){
        if(isConfirm){
            const payload = new FormData(element);
            const fetchSetTranslate = fetch(base_url + "/systemLanguages/setTranslate", {
                method: 'POST',
                body: payload
            }).then(res => res.json());

            $('#divLoading').css('display', 'flex');
            const data = await fetchSetTranslate;
            $('#divLoading').css('display', 'none');

            if(data.status == 1){
                $('#modal-add-translation').modal('hide');
                curItemTable(element['dictionary_id'].value, element['language_id'].value, element['word-translate'].value);  
            }else{
                swal({
                    title: fnT('Error'),
                    text: fnT('Format not supported'),
                    type: 'An error occurred in the process, if the problem persists please contact support'
                });
            }
        }
    });
}

function addItemTable(dictionary_id, language_id, word){
    const bgColor = $('#bg-color' + language_id).val();
    const counter = $('#counter' + language_id ).data('count') + 1;
    
    $('#counter' + language_id ).html(counter);
    $('#counter' + language_id ).data('count', counter);

    $('#cell-dictionary' + dictionary_id).append(`<span class="badge badge-pill item-translate mb-1 ml-1" style="background-color: ${bgColor}; color: white;" data-language="${language_id}" data-translation="${word}">${word}</span>`);
}

function upItemTable(dictionary_id, language_id, word){
    $(`#cell-dictionary${dictionary_id} [data-language='${language_id}']`).data('translation');
    $(`#cell-dictionary${dictionary_id} [data-language='${language_id}']`).html(word);
}

function sendDelTranslate(){
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to remove this translation?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, async function(isConfirm){
        if(isConfirm){
            const dictionary_id = $("#input-dictionary_id").val();
            const language_id = $("#input-language_id").val();

            const payload = new FormData();
            payload.append('dictionary_id', dictionary_id);
            payload.append('language_id', language_id);

            const fetchDelTranslate = fetch(base_url + "/systemLanguages/delTranslate", {
                method: 'POST',
                body: payload
            }).then(res => res.json());

            $('#divLoading').css('display', 'flex');
            const data = await fetchDelTranslate;
            $('#divLoading').css('display', 'none');

            if(data.status == 1){
                $(`#cell-dictionary${dictionary_id} [data-language='${language_id}']`).remove();
                
                const counter = $('#counter' + language_id ).data('count') - 1;
                $('#counter' + language_id ).html(counter);
                $('#counter' + language_id ).data('count', counter);
                
                $('#modal-add-translation').modal('hide');

            }else{
                swal({
                    title: fnT('Error'),
                    text: fnT('An error occurred in the process, if the problem persists please contact support'),
                    type: 'error'
                });
            }
        }
    });
}

function sendSynchronize(language_id){
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to synchronize the language file?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, async function(isConfirm){
        if(isConfirm){
            const payload = new FormData();
            payload.append('language_id', language_id);

            const fetchGenJson = fetch(base_url + "/systemLanguages/genJson", {
                method: 'POST',
                body: payload
            }).then(res => res.json());

            $('#divLoading').css('display', 'flex');
            const data = await fetchGenJson;
            $('#divLoading').css('display', 'none');

            if(data.status == 1){
                swal({
                    title: fnT('success'),
                    text: fnT('The file has been successfully updated'),
                    type: 'success'
                });
            }else{
                swal({
                    title: fnT('Error'),
                    text: fnT('An error occurred in the process, if the problem persists please contact support'),
                    type: 'error'
                });
            }
        }
    });
}