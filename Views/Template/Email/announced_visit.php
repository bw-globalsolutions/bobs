<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Visita anunciada</title>
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
                                <span>Información sobre la próxima Auditoría RAV</span>
                            </td></tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span><?=(in_array($data['country'], [1,6])?"Church's Texas Chicken":"Texas Chicken")?> ha decidido asociarse con Arguilea para realizar una Evaluación Anunciada de la auditoría  RAV. La visita de la RAV evalúa la operación del restaurante y los estándares de seguridad alimentaria.</span>
                                        </div>
                                        <div>
                                            <p>Qué esperar:</p>
                                            <ul>
                                                <li>1. Un especialista de Arguilea llegará dentro del plazo indicado. Tendrá credenciales.</li>
                                                <li>2. Por favor, darle la bienvenida, el especialista se presentará y explicará lo que hará durante la visita.</li>
                                                <li>3. El Gerente General de Registro (RGM) debe estar presente en esta visita.</li>
                                                <li>4. Pueden observar al especialista durante la visita.</li>
                                                <li>5. El especialista de Arguilea observará y documentará todas las observaciones. Nota: Si la observación se corrige en el momento, seguirá siendo una observación en la evaluación.</li>
                                                <li>6. La visita durará aproximadamente 2.5 horas.</li>
                                                <li>7. El especialista de Arguilea revisará los resultados de la visita de la RAV con el Gerente General de Registro (RGM). Al ser una visita anunciada, el RGM recibirá un plan de acción para revisar y abordar cualquier observación.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <table align="center" class="table_store" style="width: 800px;">    
                                    <tr>
                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Número de tienda</td>
                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Nombre de la tienda</td>    
                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Fecha de visita</td>
                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Horario</td>            
                                    </tr>
                                    <tr>
                                        <td><?=$data['tienda_number']?></td>
                                        <td><?=$data['tienda_name']?></td>    
                                        <td><?=$data['fecha']?></td>
                                        <td><?=$data['hora']?></td>            
                                    </tr>
                                </table>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>