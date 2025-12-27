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
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js">                </script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js">            </script>
<script src="https://cdn.datatables.net/searchpanes/2.1.0/js/dataTables.searchPanes.min.js"></script>
<script src="https://cdn.datatables.net/searchpanes/2.1.0/js/searchPanes.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/select/1.5.0/js/dataTables.select.min.js">          </script>

<!--BOTONES PDF EXCEL-->
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">      </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js">   </script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js">     </script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js">     </script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js">    </script>

<!--RESPONSIVE-->
<script src="https://cdn.datatables.net/rowreorder/1.3.1/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <script type="text/javascript" src="<?php echo media()?>/js/functions_generals.js"></script>

    <?php if($data['page-functions_js'] != ''){?>
    <script src="<?=media()?>/js/<?=$data['page-functions_js']?>"></script>
    <?php }?>
  </body>
</html>