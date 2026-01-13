<?php
    function reaudit($data){
        $fecha = date('F j, Y');
        $mensaje = 
        "<!DOCTYPE html>
	<html lang='pt'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
	    <title>Carta de melhoria operacional</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .letter-heading {
            text-align: center;
        }
        .letter-heading h1 {
            margin: 0;
        }
        .letter-content {
            margin-top: 20px;
        }
        .letter-content p {
            margin: 10px 0;
        }
        .letter-signature {
            margin-top: 40px;
            font-weight: bold;
        }
        .letter-footer {
            font-size: 0.9em;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

	    <div class='letter-heading'>
	        <h1>Carta de melhoria operacional</h1>
	        <p>ID: {$data['id_visit']}</p>
	    </div>

    <div class='letter-content'>
        <p><strong>{$fecha}</strong></p>

        <p><strong>{$data['address_1']} </strong></p>
        <p><strong> #{$data['numero_tienda']} </strong></p>

	        <p>Ref.: Aviso ao franqueado sobre melhorias necessárias nº 55777</p>

	        <p>Prezado(a) franqueado(a):</p>

	        <p>A ARGUILEA concluiu recentemente uma visita PRIDE para a American Corporation (“<b>{$data['franchissees_name']}</b>”) no restaurante nº <b>{$data['numero_tienda']}</b>, localizado em <b>{$data['address_1']}</b>, em <b>{$data['date_visit']}</b>.</p>

	        <p>Os franqueados devem operar seus restaurantes de acordo com os padrões do franqueador, conforme descrito nos Padrões do Sistema e no Manual de Operações, bem como nos Padrões de Desempenho PRIDE (“Padrões do Sistema”).</p>
        
	        <p>Durante a visita PRIDE, a ARGUILEA identificou áreas de desempenho operacional em que é necessário melhorar para atender aos referidos Padrões do Sistema. Detalhes específicos das deficiências de desempenho operacional identificadas na visita PRIDE foram enviados a você por e-mail e também pelo portal da ARGUILEA, acessível pelo The Feed.</p>
        
	        <p>O franqueador deseja oferecer a você a oportunidade de corrigir as deficiências de desempenho operacional observadas pela ARGUILEA durante a última visita. A ARGUILEA realizará outra visita dentro dos próximos quinze (15) a quarenta e cinco (45) dias para verificar se as deficiências operacionais do seu restaurante foram corrigidas.</p>

	        <p>Se as deficiências não forem corrigidas até a visita de acompanhamento da ARGUILEA, seu caso poderá ser encaminhado à equipe de Operações do franqueador e ao Departamento Jurídico.</p>

	        <p>Se você tiver alguma dúvida sobre este aviso, os Padrões do Sistema ou o relatório da visita PRIDE, entre em contato com seu representante de território – Gerente de Área ou Diretor de Operações.</p>

	        <p>CC:</p>

	        <div class='letter-signature'>
	            <p><strong>Proprietário da franquia: {$data['email_franchisee']}</strong></p>
	        </div>
    </div>



</body>
</html>
";
        return $mensaje;
    }
?>
