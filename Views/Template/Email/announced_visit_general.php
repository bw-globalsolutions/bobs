<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
                            <tr><td style='padding:10px;background:#009688;color:#ffffff;font-size:11px'>
                                <span>Información sobre las visitas de la siguiente semana</span>
                            </td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span>Los restaurantes de Smalls Sliders han contratado a Arguilea para llevar a cabo una vista QSC (Calidad, Servicio y Limpieza). La meta de la visita es ayudarle a mejorar las operaciones, impulsando el tráfico y aumentando la rentabilidad de las franquicias.</span>
                                        </div>
                                        <div>
                                            <p>Acerca de la visita y que se puede esperar:</p>
                                            <ul>
                                                <li>Usted puede seguir al especialista y hacer preguntas</li>
                                                <li>Al finalizar la visita, se realizará un reporte completo con las oportunidades identificadas</li>
                                                <li>El reporte completo será enviado al equipo líder dentro de las siguientes 48 horas después de la evaluación</li>
                                                <li>El objetivo de la visita es que el equipo Smalls Sliders entienda los estándares de la auditoría QSC</li>
                                            </ul>
                                        </div>

                                        <div>
                                            <p>Las visitas se realizarán a las siguientes tiendas:</p>
                                            <ul>
                                                <?= $data['tiendas']?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Las visitas se realizarán del: </b><?= $data['inicio']?> al <?= $data['fin']?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>