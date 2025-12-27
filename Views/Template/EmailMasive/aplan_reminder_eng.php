<?php
    function aplan_reminder_eng($data){
        $mensaje = "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
            <title>Action Plan Reminder</title>
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
                                        <td style='text-align: center; padding:10px;background:#cf0a2c; color:#ffffff; font-size:11px'>
                                        <span>Action Plan Reminder</span></td>
                                    </tr> 
                                    <tr>
                                        <td style='font-size:14px;padding:0px'>
                                            <div style='border:1px solid #eee;padding:10px'>
                                                <ul>
                                                    <li><b>Location: {$data['brand_prefix']} #{$data['location_number']}, {$data['location_name']}</b></li>
                                                    <li><b>Round: {$data['type']}, {$data['round_name']}</b></li>
                                                    <li><b>Date of visit: ". date('F j, Y, h:m', strtotime( $data['date_visit'] )) ."</b></li>
                                                    <li><b>Release of the Final Report: ". date('F j, Y, h:m', strtotime( $data['date_release'] )) ."</b></li>
                                                    <li><b>Score: {$data['score']}</b></li>
                                                    <li><b>Pending opportunities to be completed: {$data['total_opps']}</b></li>
                                                </ul>
                                                <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                                <div>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Create:</b> ". date('M d - h:i', time()) ."</td>
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