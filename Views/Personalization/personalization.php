<?php 
    headerTemplate($data);
    global $fnT;
?>
<style>
    .contColores{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }
    .color{
        border-radius:50%;
        border: 3px solid #fff;
        cursor:pointer;
        width: 40px;
        height:40px;
        box-shadow: 1px 1px 8px 0 #b4a9a9;
    }
    .hovEdit{
        width: 100%;
        height:100%;
        display:flex;
        position: absolute;
        justify-content:center;
        align-items:center;
        background-color:#00000059;
        cursor:pointer;
        opacity:0;
        transition:.5s;
        top:0;
    }
    .hovEdit:hover{
        opacity: 1;
    }
</style>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
<main class="app-content" style="position:relative; display:flex; flex-direction:column;">
    <input type="hidden" id="tipoR" value="<?=$_SESSION['userData']['role']['name']?>">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-sliders" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$fnT($data['page_title'])?></a></li>
        </ul>
    </div>
    
    <div class="contColores" style="width:100%">
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px; margin:0;"><?=$fnT('Color 1 (This color should be the one that best represents the brand):')?></p>
            <div class="color color1" onclick="document.getElementById('color1').click()" style="background-color:var(--color1);"></div>
            <input type="color" id="color1" value="#eab54c" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 2 (This color appears on some button hovers; it should preferably be a color similar to color1.):')?></p>
            <div class="color color2" onclick="document.getElementById('color2').click()" style="background-color:var(--color2);"></div>
            <input type="color" id="color2" value="#d79c26" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 3 (This color is used to highlight certain texts and labels; preferably, it should be a color with a dark tendency.):')?></p>
            <div class="color color3" onclick="document.getElementById('color3').click()" style="background-color:var(--color3);"></div>
            <input type="color" id="color3" value="#000000" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 4 (This color is used to highlight elements in dark areas; it\'s preferable to use a color with a light tendency.):')?></p>
            <div class="color color4" onclick="document.getElementById('color4').click()" style="background-color:var(--color4);"></div>
            <input type="color" id="color4" value="#ffffff" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 5 (You\'ll see this color often as a secondary color; you can use any color, but preferably one that matches the brand\'s style.):')?></p>
            <div class="color color5" onclick="document.getElementById('color5').click()" style="background-color:var(--color5);"></div>
            <input type="color" id="color5" value="#f99148" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 6 (This color is used to represent pending audits):')?></p>
            <div class="color color6" onclick="document.getElementById('color6').click()" style="background-color:var(--color6);"></div>
            <input type="color" id="color6" value="#76a1f7" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 7 (This color is used to represent audits in progress):')?></p>
            <div class="color color7" onclick="document.getElementById('color7').click()" style="background-color:var(--color7);"></div>
            <input type="color" id="color7" value="#f9e152" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 8 (This color is used to represent completed audits and also as a success color.):')?></p>
            <div class="color color8" onclick="document.getElementById('color8').click()" style="background-color:var(--color8);"></div>
            <input type="color" id="color8" value="#52f996" hidden>
        </div>
        <div class="contColores" style="flex-direction: column;max-width: 20dvw;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Color 9 (This color is used to represent audits with a failing grade and also as a fail color):')?></p>
            <div class="color color9" onclick="document.getElementById('color9').click()" style="background-color:var(--color9);"></div>
            <input type="color" id="color9" value="#ff6969" hidden>
        </div>

    </div>
    <div style="display:flex; width:100%; margin-top: 20px;">
        <div class="contColores" style="width: 50%; display:flex; justify-content:space-around; padding:10px;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Radius 1 (the curvature that elements such as buttons or cards will have):')?></p>
            <div style="display:flex; align-items:center; width:100%; padding:0 20px; gap:20px;">
                <input type="range" id="radius1" min="0" max="40">
                <div class="ejR ejR1"></div>
            </div>
        </div>
        <div class="contColores" style="width: 50%; display:flex; justify-content:space-around; padding:10px;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Radius 2 (the curvature that elements such as buttons or larger cards will have):')?></p>
            <div style="display:flex; align-items:center; width:100%; padding:0 20px; gap:20px;">
                <input type="range" id="radius2" min="0" max="60">
                <div class="ejR ejR2"></div>
            </div>
        </div>
    </div>
    <div class="contColores" style="width: 100%; margin-top:50px; flex-wrap:wrap; gap:75px;">
        <div style="display:flex; flex-direction:column; gap:10px; align-items:center;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Banner background (background that appears at login):')?></p>
            <div class="contColores" style="width: 300px; height:200px; position:relative;">
                <img style="width:100%; height:100%;" class="img1" src="<?=base_url()?>/Assets/images/fondo.png?<?=rand(1, 15)?>" alt="">
                <input type="file" id="img1" hidden accept="image/*">
                <label class="hovEdit" for="img1"><i class="fa fa-pencil" style="color:#fff; font-size: 3rem;" aria-hidden="true"></i></label>
            </div>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px; align-items:center;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('General logo (appears in the navigation menu):')?></p>
            <div class="contColores" style="width: 180px; height:150px; position:relative;">
                <img style="width:100%; height:100%;" class="img2" src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" alt="">
                <input type="file" id="img2" hidden accept="image/*">
                <label class="hovEdit" for="img2"><i class="fa fa-pencil" style="color:#fff; font-size: 3rem;" aria-hidden="true"></i></label>
            </div>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px; align-items:center;">
            <p style="background-color:var(--color4); border-radius:var(--radius); padding:10px;"><?=$fnT('Icon (appears as favicon):')?></p>
            <div class="contColores" style="width: 150px; height:150px; position:relative;">
                <img style="width:100%; height:100%;" class="img3" src="<?=base_url()?>/Assets/images/icono.png?<?=rand(1, 15)?>" alt="">
                <input type="file" id="img3" hidden accept="image/*">
                <label class="hovEdit" for="img3"><i class="fa fa-pencil" style="color:#fff; font-size: 3rem;" aria-hidden="true"></i></label>
            </div>
        </div>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>
    <div class="contColores" style="width:100%; justify-content: space-between; padding:20px;">
        <button class="btn-s2 btn btn-primary" onclick="temaDefault()"><?=$fnT('Return to the default theme')?></button>
        <button class="btn-s1 btn btn-primary" onclick="guardarTema()"><?=$fnT('Save theme')?></button>
    </div>
    
</main>
<?php footerTemplate($data);?>