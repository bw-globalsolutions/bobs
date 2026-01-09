<?php global $fnT; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>New access generated</title>
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
                                <span><?=$fnT('We inform you that an access to the auditing platform has been generated')?></span>
                            </td></tr>
                            <tr><td style="display:flex; justify-content: center;"><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <h3><?=$fnT('Access by URL')?></h3>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span><?=$fnT('Generate your password here:')?></span> <b><a href='<?=base_url() . "/login/resetPassword?token=" . $data['token']?>'><?=base_url() . "/login/resetPassword?token=" . $data['token']?></a></b>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b><?=$fnT('Created:')?></b> <?=date('M d - h:i', time())?> &nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>

