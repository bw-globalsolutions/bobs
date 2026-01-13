<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Visita anunciada</title>
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
	                                <span>Informações sobre a próxima visita RAV</span>
	                            </td></tr>
                            <tr><td><center><img src="<?=base_url()?>/Assets/images/logo.png?<?=rand(1, 15)?>" style="height:75px; width:85px;" alt="logo-church's"></center></td></tr>
                            <tr>
                                <td style='font-size:14px;padding:0px'>
                                    <div style='border:1px solid #eee;padding:10px'>
                                        <hr style='margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee'>
                                        <div>
	                                            <span><?=(in_array($data['country'], [1,6])?"Church's Texas Chicken":"Texas Chicken")?> decidiu fazer parceria com a Arguilea para realizar uma Avaliação Anunciada RAV. A visita RAV avalia as operações do restaurante e os padrões de segurança alimentar.</span>
	                                        </div>
	                                        <div>
	                                            <p>O que esperar:</p>
	                                            <ul>
	                                                <li>1. Um especialista da Arguilea chegará dentro do horário indicado. Ele/ela estará credenciado(a).</li>
	                                                <li>2. Por favor, receba-o(a); ele/ela se apresentará e explicará o que fará durante a visita.</li>
	                                                <li>3. O RGM deve estar presente nesta visita.</li>
	                                                <li>4. Você pode acompanhar o(a) especialista durante a visita.</li>
	                                                <li>5. O(a) especialista da Arguilea observará e registrará todas as constatações. Observação: mesmo que a constatação seja corrigida no local, ela ainda será registrada na avaliação.</li>
	                                                <li>6. A visita durará aproximadamente 2,5 horas.</li>
	                                                <li>7. O(a) especialista da Arguilea revisará os resultados da visita RAV com o RGM. Como esta é uma visita anunciada, o RGM receberá um plano de ação para revisar e tratar quaisquer constatações.</li>
	                                            </ul>
	                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <table align="center" class="table_store" style="width: 800px;">    
                                    <tr>
	                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Número da loja</td>
	                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Nome da loja</td>    
	                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Data</td>
	                                        <td style="background-color: rgba(211, 211, 211, 1); padding: 4px; font-weight: bold;">Horário</td>            
	                                    </tr>
                                    <tr>
                                        <td><?=$data['tienda_number']?></td>
                                        <td><?=$data['tienda_name']?></td>    
                                        <td><?=$data['fecha']?></td>
                                        <td><?=$data['hora']?></td>            
                                    </tr>
                                </table>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
