<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Reporte final</title>
</head>
<body>
    <table border='0' align='left' cellpadding='3' cellspacing='2'>
        <tbody>
            <tr>
                <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                        <tbody>
                            <tr><td width='717'></td></tr>
                            <tr><td style='padding:10px;background: #eab54c;color:#ffffff;font-size:11px'>
                                <span>Le informamos que la auditoría en cuestión ha sido liberada en el sistema.</span>
                            </td></tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <span>Visita el portal para ver todos los detalles y dar seguimiento al Plan de Acción.</span>
										<ul>
                                            <li><b>Tienda: <?=$data['location_number']?>, <?=$data['location_address']?></b></li>
											<li><b>Puntaje: <?=$data['score']?></li>
											<!--<li><b>Resultado: <?=$data['result']?></li>-->
											<li><b>Auditoria: #<?=$data['audit_id']?>, <?=$data['type']?></b></li>
										</ul>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span>También puedes ver el informe en el siguiente enlace:</span> <b><a href="<?=$data['url_report']?>"><?=$data['url_report']?></a></b>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Creado:</b> <?=date('M d - h:i', time())?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>