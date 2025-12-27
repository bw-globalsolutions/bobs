<?php
    function announced_visit($data){
        $mensaje = "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
                        <title>Announced visits</title>
                    </head>
                    <body>
                        <table border='0' align='left' cellpadding='3' cellspacing='2'>
                            <tbody>
                                <tr>
                                    <td style='padding:5px;border:solid 1px #eeeeee;font-size:12px'>
                                        <table width='100%' border='0' cellspacing='0' cellpadding='5' style='font-size:11px;font-family:Arial,Helvetica,sans-serif'>
                                            <tbody>
                                                <tr><td width='1000'></td></tr>
                                                <tr>
                                                    <td style='padding:10px;background:#009688;color:#ffffff;font-size:11px'>
                                                        <span>Information about next week's visits</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align: center; vertical-align: middle;'>
                                                        <img class='img-fluid' style='width: 200px;' src='https://dqpridereports.bw-globalsolutions.com/Assets/images/logo.png' alt='Logo'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style='font-size:14px;padding:0px'>
                                                        <div style='border:1px solid #eee;padding:10px'>
                                                            <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                                            <div>
                                                                <span>Dairy Queen stores have hired Arguilea to conduct a visit PRIDE ( FSPC, CPC, FPC ).</span>
                                                            </div>
                                                            <div>
                                                                <span>The goal of the visit is to help you improve operations, boost sales flow, and thus increase franchise profitability.</span>
                                                            </div>
                                                            <div>
                                                                <p>About the visit and what to expect:</p>
                                                                <ul>
                                                                    <li>You can follow the specialist and ask questions</li>
                                                                    <li>At the end of the visit, a complete report will be made with the identified opportunities.</li>
                                                                    <li>The full report will be sent to the leadership team within 48 hours after the evaluation.</li>
                                                                    <li>The goal of the visit is for the Dairy Queen team to understand the PRIDE audit standards.</li>
                                                                    <li>In the week of ". $data['inicio']." to ". $data['fin']." the following visits will be made:</li>
                                                                </ul>
                                                            </div>
                    
                                                            <div>
                                                                ".$data['tiendas']."
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>I wish you great success!</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                    </html>";
        return $mensaje;
    }
?>