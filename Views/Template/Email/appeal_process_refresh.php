<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?=$fnT('Final Appeal Decision - APPEAL APPROVED')?></title>
</head>
<body>
    <table border='0' align='left' cellpadding='3' cellspacing='2'>
        <tbody>
            <tr>
                <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                        <tbody>
                            <tr><td width='1200'></td></tr>
                            <tr>
                                <td style='text-align: center; padding:10px;background:#eab54c; color:#ffffff; font-size:15px'>
                                <span><?=$fnT('Final Appeal Decision - APPEAL APPROVED')?></span></td>
                            </tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>

                            <? if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false): ?>
                                <tr>
                                    <td style='text-align: center; padding:10px;background:#7FDFD4; color:black; font-size:11px'>
                                    <span><b>PARA STAGE: <?=$data['email']?></b></span></td>
                                </tr>
                            <? endif ?>

                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:11px'>
                                        <ul>
                                            <li><b><?=$fnT('Location')?>: <?=$data['brand_prefix']?> #<?=$data['location_number']?>, <?=$data['location_name']?></b></li>
                                            <li><b><?=$fnT('ROUND')?>: <?=$data['type']?>, <?=$data['round_name']?></b></li>
                                            <li><b><?=$fnT('Date visit')?>: <?= date("F j, Y, h:m", strtotime( $data['date_visit'] )) ?></b></li>
                                            <li><b><?=$fnT('Release of the Final Report')?>: <?= date("F j, Y, h:m", strtotime( $data['date_release'] )) ?></b></li>
                                            <li><b><?=$fnT('Total opportunities')?>: <?=$data['total_opps']?></b></li>
                                            <li><b><?=$fnT('Score')?>: <?=$data['score']?></b></li>
										</ul>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span><?=$fnT('You can also view the report at the following link:')?></span> <b><a href="<?=$data['url_report']?>"><?=$data['url_report']?></a></b>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;padding:0px">
                                    <div style="border:1px solid #eee;padding:10px">
                                        <div>
                                            <?= $data['appeals']?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b><?=$fnT('Created:')?></b> <?=date('M d - h:i', time())?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>