<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Segundo lembrete do Plano de Ação</title>
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
	                                <span>Lembrete do Plano de Ação</span></td>
                            </tr>  
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>                          
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <ul>
	                                            <li><b>Localização: <?=$data['brand_prefix']?> #<?=$data['location_number']?>, <?=$data['location_name']?></b></li>
	                                            <li><b>Rodada: <?=$data['type']?>, <?=$data['round_name']?></b></li>
	                                            <li><b>Data da visita: <?= date("F j, Y, h:m", strtotime( $data['date_visit'] )) ?></b></li>
	                                            <li><b>Liberação do Relatório Final: <?= date("F j, Y, h:m", strtotime( $data['date_release'] )) ?></b></li>
												<li><b>Pontuação: <?=$data['score']?></b></li>
												<li><b>Oportunidades pendentes a concluir: <?=$data['total_opps']?></b></li>
											</ul>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
	                                            <span>Você tem <?= $data['limit_days'] ?> dias para concluir seu plano de ação.</span><br>
	                                            <span>Prazo final: <?= date("F j, Y", strtotime( $data['date_limit'] )) ?></span>
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
