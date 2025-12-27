    <script>
        const base_url = "<?=base_url()?>";
        const lastUpdPassword = new Date('<?=$_SESSION['userData']['last_upd_password']?>');
    </script>
    <!-- Essential javascripts for application to work-->
    <script src="<?php echo media()?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo media()?>/js/popper.min.js"></script>
    <script src="<?php echo media()?>/js/bootstrap.min.js"></script>
    <script src="<?php echo media()?>/js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?php echo media()?>/js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/sweetalert.min.js"></script>
    <!-- Data table plugin-->
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/fusionchartsv2/fusioncharts.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/fusionchartsv2/themes/fusioncharts.theme.ocean.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/fusionchartsv2/themes/fusioncharts.theme.zune.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/fusionchartsv2/fusioncharts.charts.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/fusionchartsv2/fusioncharts.powercharts.js"></script>
    <script type="text/javascript" src="<?php echo media()?>/js/plugins/googleCharts.js"></script>

    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

    <script type="text/javascript" src="<?php echo media()?>/js/functions_generals.js"></script>

    <?php if($data['page-functions_js'] != ''){?>
    <script src="<?=media()?>/js/<?=$data['page-functions_js']?>"></script>
    <?php }?>
  </body>
</html>