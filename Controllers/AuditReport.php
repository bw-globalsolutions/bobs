<?php
		use Dompdf\Dompdf;
		use Dompdf\Options;
class AuditReport extends Controllers{
	
	private $audit_id;

	public function __construct()
	{
		parent::__construct();
		session_start();
		$this->audit_id = decryptId($_GET['tk']??-1); 
		if(!is_numeric($this->audit_id)){
			die(http_response_code(401));
		}
	}

	public function dq_R1_24()
	{
		$data['audit'] = $this->model->getAuditListById($this->audit_id);

		$tmp = selectAuditFiles(['url'],"audit_id = $this->audit_id AND (name = 'Picture of the Front Door/Entrance of the Restaurant' OR name = 'Foto de entrada principal del restaurante')");$data['audit']['picture_front'] = $tmp[0]['url'];
		
		$data['scoring'] = setScore($this->audit_id, $data['audit']['scoring_id']);
		$data['mains'] = $this->model->getSectionsOpp($this->audit_id, $data['audit']['checklist_id']);
		$data['isAmerican'] = isAmerican2([$data['audit']['country_id']]);

		//die($_SESSION['userData']['default_language']);
		$data['lan'] = $_SESSION['userData']['default_language'];
		$data['questions'] = $this->model->getQuestionsOpp($this->audit_id, $data['audit']['checklist_id'], $_SESSION['userData']['default_language']);//$_GET['lan']?? 'eng'

		$this->views->getView($this, "dq_R1_24", $data);
	}

	public function downloadReport(){
		// Configurar opciones - DESHABILITAR imágenes remotas
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isRemoteEnabled', false); // ← FALSE aquí
		$options->set('isPhpEnabled', true);
		$options->set('fontDir', '/tmp/dompdf_fonts');
		$options->set('fontCache', '/tmp/dompdf_fonts');
		$options->set('defaultFont', 'Arial');

		$dompdf = new Dompdf($options);

		// Crear directorio de fuentes
		$fontDir = '/tmp/dompdf_fonts';
		if (!is_dir($fontDir)) {
			mkdir($fontDir, 0755, true);
		}

		// Obtener datos
		$data['audit'] = $this->model->getAuditListById($this->audit_id);
		$tmp = selectAuditFiles(['url'],"audit_id = $this->audit_id AND (name = 'Picture of the Front Door/Entrance of the Restaurant' OR name = 'Foto de entrada principal del restaurante')");
		$data['audit']['picture_front'] = $tmp[0]['url'];
		$data['scoring'] = setScore($this->audit_id, $data['audit']['scoring_id']);
		$data['mains'] = $this->model->getSectionsOpp($this->audit_id, $data['audit']['checklist_id']);
		$data['isAmerican'] = isAmerican2([$data['audit']['country_id']]);
		$data['questions'] = $this->model->getQuestionsOpp($this->audit_id, $data['audit']['checklist_id'], $_SESSION['userData']['default_language']);

		// Obtener HTML de la vista
		ob_start();
		$this->views->getView($this, "dq_R1_24", $data);
		$html = ob_get_clean();

		// Remover imágenes WebP del HTML
		$html = $this->removeWebpImages($html);

		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream("reporte_auditoria.pdf", ["Attachment" => true]);
	}

	private function removeWebpImages($html) {
		// Remover etiquetas img que apunten a WebP
		$html = preg_replace('/<img[^>]*\.webp[^>]*>/i', '<div style="border:1px dashed #ccc; padding:10px; text-align:center;">[Imagen WebP no disponible]</div>', $html);
		
		// Remover backgrounds WebP en CSS
		$html = preg_replace('/background-image:\s*url\([^)]*\.webp[^)]*\)/i', 'background-image: none', $html);
		
		return $html;
	}
}
?>