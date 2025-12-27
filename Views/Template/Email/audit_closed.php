<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Visita Cerrada</title>
</head>
<body>
    <table border='0' align='left' cellpadding='3' cellspacing='2'>
        <tbody>
            <tr>
                <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                    <table width='90%' border='0' cellspacing='10' cellpadding='10' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                        <tbody>
                            <tr><td style='padding:10px;background:#009688;color:#ffffff;font-size:22px'>
                                <span>Visita Cerrada</span>
                            </td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <h3><?=$data['content_title']?></h3>
                                        <div>
                                            <span><?=$data['content_message']?></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Creado:</b> <?=date('M d - h:i', time())?> &nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>