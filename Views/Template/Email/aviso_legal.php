<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Notificação legal</title>
</head>
<body>
    <table border='0' align='left' cellpadding='3' cellspacing='2'>
        <tbody>
            <tr>
                <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                        <tbody>
                            <tr><td width='717'></td></tr>
                            <tr>
                                <td style='text-align: center; padding:10px;background:#eab54c; color:#ffffff; font-size:11px'>
	                                <span>Melhoria de desempenho operacional necessária</span></td>
                            </tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>

                            <? if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false): ?>
                                <tr>
                                    <td style='text-align: center; padding:10px;background:#7FDFD4; color:black; font-size:11px'>
	                                    <span><b>PARA STAGE: <?=$data['email']?></b></span></td>
                                </tr>
                            <? endif ?>

                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <img class="img-fluid" style="width: 200px;" src="<?=media()?>/images/logo_<?=NOMBRE_EMPESA?>.jpg" alt="Logo">
                                </td>
                            </tr>

                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <span><?= date("F j, Y") ?></span><br><br>
                                        <div>
	                                            <span>Smalls Sliders Restaurants Nº <?=$data['location_number']?></span><br>
                                            <span><?=$data['location_address']?></span><br><br>
                                        </div>

                                        <div>
	                                            <span>Ref.: Aviso de necessidade de melhoria de desempenho operacional</span><br><br>
	                                            <span>Prezado(a) <?=$data['dirigido']?>,</span><br><br>
                                        </div>

                                        <div>
                                            <span>
	                                                Em <?= date("F j, Y", strtotime( $data['date_visit'] )) ?>, foi realizada uma inspeção QSC no restaurante #<?=$data['location_number']?>, que resultou em uma classificação de “<?=$data['score']?>, <?=getScoreDefinition($data['score'])[1]?>”. Essa classificação é resultado direto das condições apontadas no relatório da inspeção QSC, as quais não estão em conformidade com os procedimentos operacionais exigidos no Manual de Operações do Smalls Sliders.
	                                            </span><br><br>
	                                        </div>

                                        <div>
                                            <span>
	                                                Em resposta a esta inspeção QSC, a Arguilea realizará outra inspeção QSC em aproximadamente 30–45 dias para confirmar que as condições apontadas na inspeção QSC foram resolvidas. À medida que você se prepara para esta inspeção QSC, envie um “Plano de Ação Corretivo” no portal da Arguilea e considere utilizar a ferramenta de autoavaliação.
	                                            </span><br><br>
	                                            <span>Se você tiver alguma dúvida, entre em contato com seu Diretor Regional de Negócios.</span><br><br><br>
	                                        </div>

                                        <div>
	                                            <span>Obrigado.</span><br><br>
	                                            <span>Clique no link abaixo para acessar o relatório completo da inspeção QSC:</span><br><br>
	                                            <b><a href="<?=$data['url_report']?>"><?=$data['url_report']?></a></b><br><br>
	                                        </div>

                                        <div>
	                                            <span>Atenciosamente,</span><br>
	                                            <b>Smalls Sliders Restaurants Inc.</b>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
	                                <td><b>Criado:</b> <?=date('M d - h:i', time())?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
