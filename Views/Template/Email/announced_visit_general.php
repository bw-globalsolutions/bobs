<!DOCTYPE html>
<html lang="pt">
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
	                            <tr><td style='padding:10px;background:#eab54c;color:#ffffff;font-size:11px'>
	                                <span>Informações sobre as visitas da próxima semana</span>
	                            </td></tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
	                                            <span>Os restaurantes bobs contrataram a Arguilea para realizar uma visita QSC (Qualidade, Serviço e Limpeza). O objetivo da visita é ajudá-lo a melhorar as operações, impulsionando o tráfego e aumentando a rentabilidade das franquias.</span>
	                                        </div>
	                                        <div>
	                                            <p>Sobre a visita e o que esperar:</p>
	                                            <ul>
	                                                <li>Você pode acompanhar o(a) especialista e fazer perguntas</li>
	                                                <li>Ao final da visita, será elaborado um relatório completo com as oportunidades identificadas</li>
	                                                <li>O relatório completo será enviado à equipe de liderança dentro de 48 horas após a avaliação</li>
	                                                <li>O objetivo da visita é que a equipe do bobs entenda os padrões da auditoria QSC</li>
	                                            </ul>
	                                        </div>

                                        <div>
	                                            <p>As visitas serão realizadas nas seguintes lojas:</p>
	                                            <ul>
	                                                <?= $data['tiendas']?>
	                                            </ul>
	                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
	                                <td><b>As visitas serão realizadas de: </b><?= $data['inicio']?> a <?= $data['fin']?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
