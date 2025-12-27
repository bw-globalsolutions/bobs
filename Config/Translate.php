<?php
function translate($lan, $json = false)
{
    if (file_exists("Config/Languages/$lan.json")) {
        $jLan = file_get_contents("Config/Languages/$lan.json");
        $language = json_decode($jLan, true);
        $func = function ($txt) use ($language) {
            return $language[$txt] ?? $txt;
        };
        if($json){
            echo "<script>const fnT = (txt, jLan = $jLan) => jLan[txt] || txt</script>";
        }
    } else {
        $func = function ($txt) {
            return $txt;
        };
        if($json){
            echo "<script>const fnT = txt => txt</script>";
        }
    }
    return $func;
};

function translateBack($lan, $txt)
{
    if (file_exists("Config/Languages/$lan.json")) {
        $jLan = file_get_contents("Config/Languages/$lan.json");
        $language = json_decode($jLan, true);
        $func = function ($txt) use ($language) {
            return $language[$txt] ?? $txt;
        };
        if($json){
            echo "<script>const fnT = (txt, jLan = $jLan) => jLan[txt] || txt</script>";
        }
    } else {
        $func = function ($txt) {
            return $txt;
        };
        if($json){
            echo "<script>const fnT = txt => txt</script>";
        }
    }
    return $func;
};

function translate_live($lang, $txt)
{
    $lang = strtolower($lang);
    $langs = ['eng', 'esp', 'por', 'ind', 'fr', 'ko'];

    if(in_array($lang, $langs)){
        //Obtener traducción directo desde la BD
        require_once "$_SERVER[DOCUMENT_ROOT]/Models/LanguageModel.php";
        $language = LanguageModel::getLanguage([$lang], "interface='" . addslashes($txt) . "'")[0];
        $request = $language[$lang] ?? $txt;
    } else{
        //Si no se ha declarado regresar el texto tal cual
        $request = $txt;
    }
    return $request;
};
