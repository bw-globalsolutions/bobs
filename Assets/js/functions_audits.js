function applyFilter(){
    console.log('Filtros apply');
  
    let count = 0;
    $('.audit-item').filter(function(){


      


        
const rnameFilter        = $('#filter_rname').val();
const locationFilter     = $('#filter_location').val();
const statusFilter       = $('#filter_status').val();
const aemailFilter       = $('#filter_aemail').val();
const countryFilter      = $('#filter_country').val();
const areaFilter         = $('#filter_area').val();
const conceptFilter      = $('#filter_concept').val();
const franchisseesFilter = $('#filter_franchissees_name').val();
const areaManagerFilter  = $('#filter_email_area_manager').val();
const shopTypFilter      = $('#filter_shop_type').val();
const escalation1Filter  = $('#filter_email_ops_leader').val();
const escalation2Filter  = $('#filter_email_ops_director').val();

console.log('Filtro:', areaManagerFilter, 'Dato:', $(this).data('fareamanager'));


const include = 
    (!rnameFilter || rnameFilter.includes(($(this).data('rname') ?? '').toString())) &&
    (!locationFilter || locationFilter.includes(($(this).data('lnumber') ?? '').toString())) &&
    (!statusFilter || statusFilter.includes(($(this).data('status') ?? '').toString())) &&
    (!aemailFilter || aemailFilter.includes(($(this).data('aemail') ?? '').toString()))&&
    (!countryFilter || countryFilter.includes(($(this).data('acountry') ?? '').toString()))&&
    (!areaFilter || areaFilter.includes(($(this).data('farea') ?? '').toString()))&&
    (!conceptFilter || conceptFilter.includes(($(this).data('fconcept') ?? '').toString()))&&
    (!areaManagerFilter || areaManagerFilter.includes(($(this).data('fareamanager') ?? '').toString()))&&
    (!franchisseesFilter || franchisseesFilter.includes(($(this).data('ffranchissees') ?? '').toString()))&&
    (!escalation1Filter || escalation1Filter.includes(($(this).data('emailopsleader') ?? '').toString()))&&
    (!escalation2Filter || escalation2Filter.includes(($(this).data('emailopsdirector') ?? '').toString()))&&
    (!shopTypFilter || shopTypFilter.includes(($(this).data('shoptype') ?? '').toString()))
    ;

$(this).toggle(include);
        count = include? count + 1 : count;
    });
    $('#count').html(count);
    $('#filter_search').val('');
    $('#search-addon').removeClass('text-primary');
    // saveFilter();
}




function searchString(val){
    if(val != ''){
        let count = 0;
        $('.audit-item').filter(function(){
            const include = $(this).data('lnumber').toString().indexOf(val) > -1 || 
            $(this).data('lname').toLowerCase().indexOf(val.toLowerCase()) > -1 || 
            $(this).data('aname').toLowerCase().indexOf(val.toLowerCase()) > -1 || 
            $(this).data('aemail').indexOf(val) > -1 || 
            $(this).data('acountry').toLowerCase().indexOf(val) > -1 ||
            $(this).data('farea').toLowerCase().indexOf(val) > -1 ||
            $(this).data('fconcept').toLowerCase().indexOf(val) > -1 ||
            $(this).data('ffranchissees').toLowerCase().indexOf(val) > -1 ||
            $(this).data('fareamanager').toLowerCase().indexOf(val) > -1 ||
            $(this).data('shoptype').toLowerCase().indexOf(val) > -1 ||
            $(this).data('emailopsdirector').toLowerCase().indexOf(val) > -1 ||
            $(this).data('emailopsleader').toLowerCase().indexOf(val) > -1 ||
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
                                text: fnT(objData.msg),
                                type: "success",
                                confirmButtonText: fnT("Yes, accept"),
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                if(isConfirm){
                                     location.href = base_url+'/audits/auditInfo?id='+objData.id_audit;
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



function generarIDQ(idTienda){
    const pais = $(`#selectSE option[value='${idTienda}']`).data('country');
    $('#selectSE').selectpicker('val', '');

    swal({
        title: fnT("IDQ Internal Audit"),
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
            var ajaxUrl = base_url+'/audits/addIDQ';
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
                                text: fnT(objData.msg),
                                type: "success",
                                confirmButtonText: fnT("Yes, accept"),
                                closeOnConfirm: false,
                            }, function(isConfirm){
                                if(isConfirm){
                                     location.href = base_url+'/audits/audit?id='+objData.id_audit;
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


window.addEventListener('DOMContentLoaded', () => {
    applyFilter();
});