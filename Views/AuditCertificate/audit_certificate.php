<?php
  $lan = $_GET['lan'] ?? 'eng';
  //$fnT = translate($lan);

    $content = '
    <page>
      <STYLE type="text/css">    
        #divid{margin-top: 0px;margin-left: 0px;
            background-image: url(Assets/images/certificates/fCertificateB_'.$lan.'.png);
            background-repeat: no-repeat;
         }
        .etiqueta{font-size: 24px; color: #0099FF;}
        .etiqueta2{font-size: 35px; color: #0099FF;}
        .etiqueta3{font-size: 35px; color: #000000;}
        .etiqueta4{font-size: 18px; color: #000000;}
        .etiqueta5{font-size: 15px; color: #000000;}
        img {
        object-fit:cover;
            }
    </style>
        <div id="divid">
        <table border="0" cellspacing="0" cellspacing="0">
        <tr>
            <td width="" height="745" style="padding-left: 185px;" align="center">

            <table border="0" cellspacing="0" cellspacing="0">
            <tr>
                <td height="280">&nbsp;</td>
            </tr>
            <tr>
                <td width="710" height="85" style="padding-left: 0px; padding-right: 0px; vertical-align: middle;">

                </td>
            </tr>

            <tr>
                <td height="65">
                <label class="etiqueta3">'.$data['audit']['brand_name'].' #'.$data['audit']['location_number'].'</label><br>
                <label class="etiqueta4" style="margin-top: 5px;">'.limitString($data['audit']['location_address'], 68, '...').'</label><br>
                </td>
            </tr>
            <tr>
                <td height="210">&nbsp;</td>
            </tr>
            </table>
            <table border="0" cellspacing="0" cellspacing="0">
            <tr>
                <td width="820" height="40" align="right">
                <br>
                <label class="etiqueta5">'.$data['audit']['round_name'].'</label><br>
                </td>
            </tr>
            </table>
            </td>
        </tr>
        </table>

        </div>
    </page>';

    require_once('Assets/js/plugins/html2pdf/html2pdf.class.php');
    $html2pdf = new HTML2PDF('L','A4','en', true, 'UTF-8', array(5, 5, 5, 5));
    $html2pdf->WriteHTML($content);
    $file='certificate_'.date('YmdHis');
    //if($_REQUEST['p']) $file = str_replace(' ','_',$_REQUEST['p']);
    $html2pdf->Output($file.'.pdf');

?>