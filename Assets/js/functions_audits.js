function applyFilter(){
    console.log('Filtros apply');
    let count = 0;
    $('.audit-item').filter(function(){
        const include = $('#filter_rname').val().includes($(this).data('rname').toString()) && 
        $('#filter_location').val().includes($(this).data('lnumber').toString()) && 
        $('#filter_status').val().includes($(this).data('status')) &&
        $('#filter_aemail').val().includes($(this).data('aemail'))
        $(this).toggle(include);
        count = include? count + 1 : count;
    });
    $('#count').html(count);
    $('#filter_search').val('');
    $('#search-addon').removeClass('text-primary');
    window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    console.log(0);
    // saveFilter();
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

    document.querySelectorAll('.etlbls').forEach(e=>{
        switch(e.innerHTML){
            case 'Pending':
                e.style.backgroundColor=color6v;
                break;
            case 'In Process':
                e.style.backgroundColor=color7v;
                break;
            case 'Completed':
                e.style.backgroundColor=color8v;
                break;
            case 'Deleted!':
                e.style.backgroundColor=color9v;
                break;
        }
    });

/*window.onload = function() {
    setTimeout(()=>{
        document.getElementById('selectSE').nextElementSibling.setAttribute('class','addSelf');
    }, 500)
    
}*/

function abrirAdd(event){
    event.preventDefault();
    event.stopPropagation();
    document.querySelector('.addSelf').click();
}

function searchString(val){
    if(val != ''){
        let count = 0;
        $('.audit-item').filter(function(){
            const include = $(this).data('lnumber').toString().indexOf(val) > -1 || 
            $(this).data('lname').toLowerCase().indexOf(val.toLowerCase()) > -1 || 
            $(this).data('aname').toLowerCase().indexOf(val.toLowerCase()) > -1 || 
            $(this).data('aemail').indexOf(val) > -1 || 
            $(this).data('id').toString().indexOf(val) > -1;
            $(this).toggle(include);
            count = include? count + 1 : count;
        });
        $('#count').html(count);
        $('#search-addon').addClass('text-primary');
    } else{
        $('#search-addon').removeClass('text-primary');
        applyFilter();
    }
}

function limitCountry(){
    let setLocations = [];
    $('#filter_location').selectpicker('deselectAll');
    $('#filter_location').find(':not(:selected)').hide();

    $('#filter_country').val().forEach(element => setLocations.push(...setCountries[element]));
    $('#filter_location').selectpicker('val', setLocations);

    $('#filter_location').find(':selected').show();
    $('#filter_location').selectpicker('refresh');
}

function generarAutoEval(idTienda){
    const pais = $(`#selectSE option[value='${idTienda}']`).data('country');
    $('#selectSE').selectpicker('val', '');

    swal({
        title: fnT("Self-Evaluation"),
        text: fnT("Do you really want to continue?"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: fnT("Yes, accept"),
        cancelButtonText: fnT("No, cancel"),
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if(isConfirm){
            //location.reload();

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/audits/addAutoEval';
            var strData = "location_id="+idTienda+"&country_id="+pais;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){

                if(request.readyState == 4 && request.status == 200){
                    //console.log(request.responseText);
                    var objData = JSON.parse(request.responseText);

                    if(objData.status)
                    {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: fnT("Accept"),
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                if(isConfirm){
                                    location.reload();
                                }
                            });
                    }else{
                        swal("Atención!", fnT(objData.msg), "error");
                    }
                }
                //divLoading.style.display = "none";
                //return false;
            }
            //alert('ok');
        }
    });
}

// function saveFilter(){
//     sessionStorage.setItem('audit_type', audit_type);
//     document.querySelectorAll('#form-filter select[id]').forEach(item => {
//         const id = item.getAttribute('id');
//         sessionStorage.setItem(id, JSON.stringify($('#' + id).val()));
//     });
// }

// window.addEventListener('DOMContentLoaded', () => {
//     if(sessionStorage.getItem("audit_type") == audit_type){
//         document.querySelectorAll('#form-filter select[id]').forEach(item => {
//             const id = item.getAttribute('id');
//             let data = JSON.parse(sessionStorage.getItem(id));
//             $('#' + id).selectpicker('val', data);
//         });
//     }
//     applyFilter();
// });

window.addEventListener('DOMContentLoaded', () => {
    applyFilter();
});