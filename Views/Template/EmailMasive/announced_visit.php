<?php
    function announced_visit($data){
        $mensaje = "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
                        <title>Visitas anunciadas</title>
                    </head>
                    <body>
                        <table border='0' align='left' cellpadding='3' cellspacing='2'>
                            <tbody>
                                <tr>
                                    <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                                        <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                                            <tbody>
                                                <tr><td width='717'></td></tr>
                                                <tr><td style='padding:10px;background:#eab54c;color:#ffffff;font-size:11px'>
                                                    <span>Information about next RAV visit</span>
                                                </td></tr>
                                                <tr><td><center><img src='".base_url()."/Assets/images/logo.png?".rand(1, 15)."' style='height:75px; width:85px;' alt='logo-church's'></center></td></tr>
                                                <tr>
                                                    <td style='font-size:14px;padding:0px'>
                                                        <div style='border:1px solid #eee;padding:10px'>
                                                            <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                                            <div>
                                                                <span>Church's Texas Chicken/Texas Chicken has chosen to partner with Arguilea to conduct a RAV Announced Assessment. The RAV visit assess restaurant operations and food safety standards.</span>
                                                            </div>
                                                            <div>
                                                                <p>What to expect:</p>
                                                                <ul>
                                                                    <li>1. An Arguilea Specialist will arrive within the time frame shown.  They will have credentials</li>
                                                                    <li>2. Please welcome them and they will introduce themselves and explain what they will doing during the visit</li>
                                                                    <li>3. The RGM should be present for this visit.</li>
                                                                    <li>4. You can shadow the specialist during the visit.</li>
                                                                    <li>5. The Arguilea Specialist will observe and document all observations. Note:  If the observation is corrected on site, it will still be an observation in the assessment.</li>
                                                                    <li>6. The visit will last approximately 2.5 hours</li>
                                                                    <li>7. The Arguilea Specialist will review the results of the RAV visit with the RGM.  Since this is an announced visit, the RGM will get an action plan to view and address any observations.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <table align='center' class='table_store' style='width: 800px;'>    
                                                        <tr>
                                                            <td style='background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;'>Número de tienda</td>
                                                            <td style='background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;'>Nombre de la tienda</td>    
                                                            <td style='background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;'>Fecha de visita</td>
                                                            <td style='background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;'>Horario</td>            
                                                        </tr>";
                                                        foreach($data['tiendas'] as $t){
                                                            $mensaje.="
                                                        <tr>
                                                            <td>".$t['number']."</td>
                                                            <td>".$t['name']."</td>    
                                                            <td>".$t['fecha']."</td>
                                                            <td>".$t['hora']."</td>            
                                                        </tr>";
                                                        }
                                                        $mensaje.="
                                                    </table>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                    </html>";
        return $mensaje;
    }
?>