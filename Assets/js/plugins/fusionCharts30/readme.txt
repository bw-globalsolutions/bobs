//CODIGO PARA INCLUSION
<script src="http://src.arguilea.net/fusionCharts20/fusioncharts.js"></script>
<script src="http://src.arguilea.net/fusionCharts20/fusioncharts.charts.js"></script>

// MODO DE USO
<div id="G01" style="float:left; width:950px; height:295px; z-index:0">FusionCharts will render here</div>
<script type="text/javascript">
FusionCharts.ready(function () {
    var myChart = new FusionCharts({
      "type": "stackedcolumn2dline",
      "renderAt": "G01",
      "width": "950",
      "height": "295",
      "dataFormat": "xml"
    });
  myChart.setDataURL("Complaints/Graficos/01-PorCoMaker.php");
  myChart.render();
});
</script>


//EJEMP[LO DE USO
<?php include($_SERVER['DOCUMENT_ROOT']."/Scripts/inc.php"); connectDB();

function getValor($comaker, $fecha){
	
    //Acceso del Comaker
    if($_SESSION[login][acceso]=='CoMaker') $comaker = $_SESSION[login][nombre];
	
	if($comaker) $byCo = "comaker='$comaker' AND";
	else $byCo=NULL;
	
	$sql = "SELECT 1 FROM reclamos WHERE $byCo DATE_FORMAT(fecha, '%Y-%m')='$fecha' ";
	$rs = mysql_query($sql) or die (mysql_error()."$sql");
	return mysql_num_rows($rs);
};
?>
<chart useRoundEdges='1' showLegend='1' showYAxisValues='0' bgColor='FFFFFF' showBorder='0' palette='3' showvalues='0' showsum='1'>

   <categories>
      <? for($m=1 ; $m<=12 ; $m++){ $mm = zerofill($m, 2);?>
      <category label='<?=fechaMKTime(date("Y-$mm-d H:i:s"), 'F')?>' />
      <? }?>
   </categories>
   
   <?php
   //Acceso del Comaker
   if($_SESSION[login][acceso]=='CoMaker') $fil = "WHERE comaker='".$_SESSION[login][nombre]."' ";
   else $fil = NULL;
   
   $rsA = mysql_query("SELECT comaker FROM comakers $fil ") or die (mysql_error());
   while($comaker = mysql_fetch_array($rsA)){
   ?>
   <dataset seriesName='<?=$comaker[0]?>'>
      <? for($m=1 ; $m<=12 ; $m++){ $mm = zerofill($m, 2);
		  $v = getValor($comaker[0], date("Y-$mm"));?>
      <set value='<? if($v>0) echo $v?>' toolText='(<?=$v?>) <?=$comaker[0]?>' />
      <? }?>
   </dataset>
   <? }?>
   
   <dataset seriesName='<?=date("Y")-1?>' renderAs='Line' color='FF0000'>
      <? for($m=1 ; $m<=12 ; $m++){ $mm = zerofill($m, 2);
		  $v = getValor(NULL, (date("Y")-1)."-$mm");?>
      <set value='<?=$v?>' toolText='(<?=$v?>) <?=date("Y")-1?>' />
      <? }?>
   </dataset>

</chart>