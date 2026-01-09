let color1 = document.getElementById('color1');
let color2 = document.getElementById('color2');
let color3 = document.getElementById('color3');
let color4 = document.getElementById('color4');
let color5 = document.getElementById('color5');
let color6 = document.getElementById('color6');
let color7 = document.getElementById('color7');
let color8 = document.getElementById('color8');
let color9 = document.getElementById('color9');
let radius1 = document.getElementById('radius1');
let radius2 = document.getElementById('radius2');
let img1 = "";
let img2 = "";
let img3 = "";

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
const radius1v = rootStyles.getPropertyValue('--radius').trim();
const radius2v = rootStyles.getPropertyValue('--radius2').trim();
color1.value=color1v;
color2.value=color2v;
color3.value=color3v;
color4.value=color4v;
color5.value=color5v;
color6.value=color6v;
color7.value=color7v;
color8.value=color8v;
color9.value=color9v;
radius1.value=radius1v.split('px')[0];
document.querySelector('.ejR1').style.borderRadius=radius1v;
radius2.value=radius2v.split('px')[0];
document.querySelector('.ejR2').style.borderRadius=radius2v;

color1.addEventListener('input', (e)=>{
    document.querySelector('.color1').style.backgroundColor=e.target.value;
});

color2.addEventListener('input', (e)=>{
    document.querySelector('.color2').style.backgroundColor=e.target.value;
});

color3.addEventListener('input', (e)=>{
    document.querySelector('.color3').style.backgroundColor=e.target.value;
});

color4.addEventListener('input', (e)=>{
    document.querySelector('.color4').style.backgroundColor=e.target.value;
});

color5.addEventListener('input', (e)=>{
    document.querySelector('.color5').style.backgroundColor=e.target.value;
});

color6.addEventListener('input', (e)=>{
    document.querySelector('.color6').style.backgroundColor=e.target.value;
});

color7.addEventListener('input', (e)=>{
    document.querySelector('.color7').style.backgroundColor=e.target.value;
});

color8.addEventListener('input', (e)=>{
    document.querySelector('.color8').style.backgroundColor=e.target.value;
});

color9.addEventListener('input', (e)=>{
    document.querySelector('.color9').style.backgroundColor=e.target.value;
});

radius1.addEventListener('input', (e)=>{
    document.querySelector('.ejR1').style.borderRadius=e.target.value+'px';
});

radius2.addEventListener('input', (e)=>{
    document.querySelector('.ejR2').style.borderRadius=e.target.value+'px';
});

document.getElementById('img1').addEventListener('input', (e)=>{
    let imgUrl = URL.createObjectURL(e.target.files[0]);
    document.querySelector('.img1').src=imgUrl;
    if(e.target.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            const image = new Image();
            image.onload = function(){
                img1 = convertToPNG(image).split(',')[1];
            }
            image.src=e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
    
    /*blobToBase64(e.target.files[0]).then(res =>{
        img1 = res.split(',')[1];
    });*/
});

document.getElementById('img2').addEventListener('input', (e)=>{
    let imgUrl = URL.createObjectURL(e.target.files[0]);
    document.querySelector('.img2').src=imgUrl;
    if(e.target.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            const image = new Image();
            image.onload = function(){
                img2 = convertToPNG(image).split(',')[1];
            }
            image.src=e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

document.getElementById('img3').addEventListener('input', (e)=>{
    let imgUrl = URL.createObjectURL(e.target.files[0]);
    document.querySelector('.img3').src=imgUrl;
    if(e.target.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            const image = new Image();
            image.onload = function(){
                img3 = convertToPNG(image).split(',')[1];
            }
            image.src=e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

//cargarTema();

function convertToWebp(img){
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = img.width;
    canvas.height = img.height;

    ctx.drawImage(img, 0, 0);

    const webpBase64 = canvas.toDataURL('image/webp');

    return webpBase64;
}

// Función para convertir a PNG
function convertToPNG(image) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Establecer el tamaño del canvas igual al de la imagen
    canvas.width = image.width;
    canvas.height = image.height;
    
    // Dibujar la imagen en el canvas
    ctx.drawImage(image, 0, 0);
    
    // Convertir a PNG (formato por defecto de toDataURL)
    return canvas.toDataURL('image/png');
}

function blobToBase64(blob){
    return new Promise((resolve, _) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.readAsDataURL(blob);
    });
}

function postTema(id, color1, color2, color3, color4, color5, color6, color7, color8, color9, radius='20px', radius2='50px', img1='default', img2='default', img3='default'){
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/personalization/guardarTema';
            var strData = "id="+id+"&color1="+color1+"&color2="+color2+"&color3="+color3+"&color4="+color4+"&color5="+color5+"&color6="+color6+"&color7="+color7+"&color8="+color8+"&color9="+color9+"&radius1="+radius1+"&radius2="+radius2+"&img1="+img1+"&img2="+img2+"&img3="+img3;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){

                if(request.readyState == 4 && request.status == 200){
                    console.log('respuesta:'+request.responseText);
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
                        console.log(request.responseText);
                        swal("Atención!", fnT(objData.msg), "error");
                    }
                }
            }
}

function temaDefault(){
    postTema(1, '#eab54c', '#d49924', '#000000', '#ffffff', '#f99148', '#76a1f7', '#f9e152', '#52f996', '#ff6969');
}

function guardarTema(){
    postTema(1, color1.value, color2.value, color3.value, color4.value, color5.value, color6.value, color7.value, color8.value, color9.value, radius1.value+'px', radius2.value+'px', img1, img2, img3);
}

/*function cargarTema(){

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

                    document.querySelector('.color1').style.backgroundColor=objData[0].color1;
                    color1.value=objData[0].color1;
                    document.querySelector('.color2').style.backgroundColor=objData[0].color2;
                    color2.value=objData[0].color2;
                    document.querySelector('.color3').style.backgroundColor=objData[0].color3;
                    color3.value=objData[0].color3;
                    document.querySelector('.color4').style.backgroundColor=objData[0].color4;
                    color4.value=objData[0].color4;
                    document.documentElement.style.setProperty("--color1", objData[0].color1);
                    document.documentElement.style.setProperty("--color2", objData[0].color2);
                    document.documentElement.style.setProperty("--color3", objData[0].color3);
                    document.documentElement.style.setProperty("--color4", objData[0].color4);
                    if(objData[0].img1!=''){
                        document.querySelector('.img1').src=objData[0].img1;
                    }
                    if(objData[0].img2!=''){
                        document.querySelector('.img2').src=objData[0].img2;
                        if(document.querySelector('.img-fluid')){
                            document.querySelector('.img-fluid').src=objData[0].img2;
                        }
                    }
                    if(objData[0].img3!=''){
                        document.querySelector('.img3').src=objData[0].img3;
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

