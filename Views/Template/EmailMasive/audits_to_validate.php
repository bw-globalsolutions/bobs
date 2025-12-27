<?php
    function audits_to_validate($data){
        $rows = "";
        foreach($data['audits'] as $a){
            $rows .= '<tr>
                <td>#'.$a['location_number'].' - '.$a['location_name'].'</td>
                <td>'.$a['country_name'].'</td>
                <td>'.$a['score'].'</td>
                <td>'.$a['type'].'</td>
            </tr>';
        }
        $mensaje = "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
            <title>Auditorías para validar</title>
        </head>
        <body>
            <table border='0' align='left' cellpadding='3' cellspacing='2'>
                <tbody>
                    <tr>
                        <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                            <table width='100%'' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                                <tbody>
                                    <tr><td width='717'></td></tr>
                                    <tr>
                                        <td style='text-align: center; padding:10px;background:#cf0a2c; color:#ffffff; font-size:11px'>
                                        <span>Auditorías para validar</span></td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px;padding:0px'>
                                            <div style='border:1px solid #eee;padding:10px'>
                                                <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                                <div>
                                                    <span>La calificación de esta visita es 100, por favor validala</span>
                                                </div>
                                                <div style='justify-content: center;'>
                                                    <table width='500' border='1' style='white-space: nowrap; font-size: 12px;'>
                                                        <tr bgcolor='orange'>
                                                            <th>Tienda</th>
                                                            <th>País</th>
                                                            <th>Puntuación</th>
                                                            <th>Tipo</th>
                                                        </tr>
                                                        $rows
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Creado:</b>". date('M d - h:i', time()) . "</td>
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