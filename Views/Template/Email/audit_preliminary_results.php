<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Resultados Preliminares</title>
</head>
<body>
    <table border='0' align='left' cellpadding='3' cellspacing='2'>
        <tbody>
            <tr>
                <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                    <table width='90%' border='0' cellspacing='10' cellpadding='10' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                        <tbody>
                            <tr><td style='padding:10px;background:#cf0a2c;color:#ffffff;font-size:22px'>
                                <span>Resultados Preliminares</span>
                            </td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <h3><?=$data['content_title']?></h3>
                                        <div>
                                            <span><?=$data['content_message']?></span>
                                            <p><b><?=$data['content_url']?></b></p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Creado:</b> <?=date('M d - h:i', time())?>  </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>