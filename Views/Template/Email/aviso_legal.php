<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Legal Notification</title>
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
                                <td style='text-align: center; padding:10px;background:red; color:#ffffff; font-size:11px'>
                                <span>Operational Performance Improvement Required</span></td>
                            </tr>

                            <? if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false): ?>
                                <tr>
                                    <td style='text-align: center; padding:10px;background:#7FDFD4; color:black; font-size:11px'>
                                    <span><b>TO STAGE: <?=$data['email']?></b></span></td>
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
                                            <span>Smalls Sliders Restaurants No. <?=$data['location_number']?></span><br>
                                            <span><?=$data['location_address']?></span><br><br>
                                        </div>

                                        <div>
                                            <span>Re: Notice of Operational Performance Improvement Required Letter</span><br><br>
                                            <span>Dear <?=$data['dirigido']?>,</span><br><br>
                                        </div>

                                        <div>
                                            <span>
                                                On <?= date("F j, Y", strtotime( $data['date_visit'] )) ?>, a QSC Inspection was conducted at restaurant #<?=$data['location_number']?> which resulted in a Restaurant Rating of “<?=$data['score']?>, <?=getScoreDefinition($data['score'])[1]?>”. This Restaurant Rating is a direct result of conditions noted in the QSC Inspection report which are non-compliant with the required operating procedures in the Smalls Sliders Operations Manual.  
                                            </span><br><br>
                                        </div>

                                        <div>
                                            <span>
                                                In response to this QSC Inspection, Arguilea will conduct another QSC Inspection in approximately 30 – 45 days to confirm that the conditions noted in the QSC Inspection have been resolved.  As you prepare for this QSC Inspection, please submit a "Corrective Action Plan" within the Arguilea portal and consider leveraging the self-assessment tool.
                                            </span><br><br>
                                            <span>If you have any questions, please reach out to your Regional Business Director.</span><br><br><br>
                                        </div>

                                        <div>
                                            <span>Thank you.</span><br><br>
                                            <span>Click the link below for the completed QSC Inspection report: </span><br><br>
                                            <b><a href="<?=$data['url_report']?>"><?=$data['url_report']?></a></b><br><br>
                                        </div>

                                        <div>
                                            <span>Best Regards,</span><br>
                                            <b>Smalls Sliders Restaurants Inc.</b>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><b>Created:</b> <?=date('M d - h:i', time())?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>