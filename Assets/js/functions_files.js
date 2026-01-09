

const addFile = async element => {
    if(Object.keys(stackFiles).length > 4){
        swal({
            title: fnT('Alert'),
            text: fnT('File limit reached'),
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
    console.log(response);
    if(response.Message != 'SUCCESS'){
        swal({
            title: fnT('Error'),
            text: fnT('An error occurred in the process, if the problem persists please contact support'),
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
        <strong>${file.name}</strong> / ${fnT('Size')}": ${file.size}b
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
            title: fnT('Alert'),
            text: fnT('Add at least one file'),
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
    console.log(response);
    if(response.status == 0){
        swal({
            title: fnT('Error'),
            text: fnT('An error occurred in the process, if the problem persists please contact support'),
            type: 'error'
        });
        return;
    }
    
    fetchFiles();
}

const fetchFiles = async () => {
    const pet = fetch(base_url + '/files/getFiles').then(res => res.json());
    const response = await pet;
    let first = true;
    dataFilles = response;

    document.getElementById('panel-files').innerHTML = response.reduce((acc, cur) => {
        console.log(validarPais((cur.countrys==null?'':cur.countrys)));
        if((validarPais((cur.countrys==null?'':cur.countrys)) && validarRol((cur.roles==null?'':cur.roles))) || ['1','2'].includes(rolUsuario)){
            if(first){
                acc = '';
            }
            acc += `
            <div id="accordionFile">
                <div class="card">
                    <div class="card-header" style="display:flex;justify-content:space-between;" id="heading${cur.id}">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" style="color: var(--color3);" data-target="#collapse${cur.id}" aria-expanded="${first? 'true' : 'false'}" aria-controls="collapse${cur.id}">
                                ${cur.title}
                            </button>
                        </h2>
                        ${permissionDoc.u == 1 || permissionDoc.d == 1? `<div class="contOpsF">
                            <button type="button" style="border-radius:var(--radius); background-color:var(--color1); padding: 5px 10px;" class="btn-s3" onclick="prepareUpdFile(${cur.id}); document.querySelector('#heading${cur.id} h2 button').click(); window.scrollTo({ top: 0, behavior: 'smooth' });" ${permissionDoc.u != 1? 'disabled' : ''}>${fnT('Edit')}&#160;&#160;<i class="fa fa-pencil"></i></button>
                            <button type="button" style="border-radius:var(--radius); padding: 5px 10px;" class="btn-s3" onclick="removeFile(${cur.id})" ${permissionDoc.d != 1? 'disabled' : ''}>${fnT('Remove')}&#160;&#160;<i class="fa fa-trash"></i></button>
                        </div>` : ''}
                    </div>
                    <div id="collapse${cur.id}" class="collapse ${first? 'show' : ''}" aria-labelledby="heading${cur.id}" data-parent="#accordionFile">
                        <div class="card-body">
                            <p>${cur.description}</p>
                            <small>${fnT('Created')}: ${cur.created} &#124; ${fnT('By')}: ${cur.name}</small>
                        </div>
                        <ul class="list-group list-group-flush">
                            ${Object.values(cur.jfiles).reduce((_acc, _cur) => _acc + `<li class="list-group-item">
                                <a href="${_cur.url}" target="_blank" download>${_cur.name} &#124; ${fnT('Size')}: ${_cur.size}b</a>
                            </li>`, '')}
                        </ul>
                    </div>
                </div>
            </div>`;
            first = false;
        }
        return acc;
    }, "<h5 class='mt-2'>" + fnT('No files to show') + "</h5>");
}

const removeFile = id => {
    swal({
        title: fnT('Alert'),
        text: fnT('Do you want to remove this files?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
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
                    title: fnT('Error'),
                    text: fnT('An error occurred in the process, if the problem persists please contact support'),
                    type: 'error'
                });
            }
        }
    });
}

const prepareNewFile = () => {
    document.getElementById('form-panel-files').innerHTML = '';
    document.getElementById('form-files').reset();
    stackFiles = {};
}

const prepareUpdFile = id => {
    const currFile = dataFilles.filter(item => item.id == id)[0];    
    const formFiles = document.getElementById('form-files');
    formFiles.reset();

    formFiles['id'].value = currFile.id;
    formFiles['title'].value = currFile.title;
    formFiles['description'].value = currFile.description;
    formFiles['countrys'].value = currFile.countrys;
    formFiles['roles'].value = currFile.roles;
    formFiles['expirationDate'].value = currFile.expirationDate;
    cargarChecksPais(currFile.countrys);
    cargarChecksRol(currFile.roles);
    cargarEstatus(currFile.active);
    stackFiles = currFile.jfiles;

    let tmp = '';
    Object.entries(currFile.jfiles).forEach(([key, value]) => {
        tmp += `<div class="alert alert-warning alert-dismissible fade show mb-1" role="alert" id="file${key}">
            <strong>${value.name}</strong> / ${fnT('Size')}": ${value.size}b
            <button type="button" class="close" onclick="dropFile('${key}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`;
    });
    document.getElementById('form-panel-files').innerHTML = tmp;

}

function validarPais(paisesPermitidos){
    let arr = [];
    let res = false;
    if(paisesPermitidos.includes(',')){
        arr = paisesPermitidos.split(',');
    }else if(paisesPermitidos!=""){
        arr.push(paisesPermitidos);
    }
    if(arr.length>0){
        paisesUsuario.forEach(p=>{
            if(arr.includes(p.id)){
                res = true;
            }
        });
        return res;
    }else{
        return false;
    }
}

function validarRol(rolesPermitidos){
    let arr = [];
    let res = false;
    if(rolesPermitidos.includes(',')){
        arr = rolesPermitidos.split(',');
    }else if(rolesPermitidos!=""){
        arr.push(rolesPermitidos);
    }
    if(arr.length>0){
            if(arr.includes(rolUsuario)){
                res = true;
            }
        return res;
    }else{
        return false;
    }
}

function actualizarPais(){
    let valores = [];
    let checks = 0;
    document.querySelectorAll('.country').forEach(p=>{
        if(p.checked){
            valores.push(p.value);
            checks++;
        }
    });
    if(checks==0){ //si no se selecciona ninguno se ponen todos por default
        document.querySelectorAll('.country').forEach(p=>{
            valores.push(p.value);
        });
    }
    document.getElementById('countrys').value = valores;
}

function actualizarRoles(){
    let valores = [];
    let checks = 0;
    document.querySelectorAll('.rol').forEach(p=>{
        if(p.checked){
            valores.push(p.value);
            checks++;
        }
    });
    if(checks==0){ //si no se selecciona ninguno se ponen todos por default
        document.querySelectorAll('.rol').forEach(p=>{
            valores.push(p.value);
        });
    }
    document.getElementById('roles').value = valores;
}

function cargarChecksPais(paises){
    let arr = [];
    if(paises==null)paises='';
    if(paises.includes(',')){
        arr = paises.split(',');
    }else if(paises!=""){
        arr.push(paises);
    }
    console.log(arr);
    if(arr.length>0){
        arr.forEach(a=>{
            document.querySelectorAll('.country').forEach(p=>{
                if(a==p.getAttribute('pid')){
                    p.checked=true;
                }
            });
        });
    }
}

function cargarChecksRol(roles){
    let arr = [];
    if(roles==null)roles='';
    if(roles.includes(',')){
        arr = roles.split(',');
    }else if(roles!=""){
        arr.push(roles);
    }
    console.log(arr);
    if(arr.length>0){
        arr.forEach(a=>{
            document.querySelectorAll('.rol').forEach(p=>{
                if(a==p.getAttribute('rId')){
                    p.checked=true;
                }
            });
        });
    }
}

function cargarEstatus(estatus){
    if(estatus==1){
        document.getElementById('activo').checked = true;
    }else{
        document.getElementById('inactivo').checked = true;
    }
}

if(permissionDoc.u == 1){
    prepareNewFile();
    actualizarPais();
    actualizarRoles();
}else{
    $('#collapseFormFile').css('display', 'none');
}
fetchFiles();