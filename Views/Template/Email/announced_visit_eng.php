<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Announced visit</title>
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
                                <span>Information about the visits of the following week.</span>
                            </td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
                                            <span><?=NOMBRE_EMPESA?> restaurants have hired Arguilea, to carry out a QSC (Quality, Service and Cleanliness) visit. The goal of the visit is to help you improve operations, drive traffic and increase franchise profitability.</span>
                                        </div>
                                        <div>
                                            <p>About the visit and what to expect:</p>
                                            <ul>
                                                <li>You can follow the specialist and ask questions</li>
                                                <li>At the end of the visit, a complete report will be made with the opportunities identified</li>
                                                <li>The full report will be sent to the lead team within 48 hours after the evaluation</li>
                                                <li>The objective of the visit is for the <?=NOMBRE_EMPESA?> team to understand the QSC audit standards</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <p>Visits will be made to the following stores:</p>
                                            <ul>
                                                <?= $data['tiendas']?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Visits will take place from: </b><?= $data['inicio']?> to <?= $data['fin']?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>