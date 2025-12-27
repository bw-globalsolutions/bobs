<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background: #000;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
    <script>
        // Bloquear menú contextual
        document.addEventListener('contextmenu', e => e.preventDefault());

        // Bloquear Ctrl+S, Ctrl+P, Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && ['s','p','u'].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }
        });
    </script>
</head>
<body>
    <?php 
        // En tu MVC, $data contiene el array enviado desde el controlador
        $pdfUrl = isset($data['pdfUrl']) ? $data['pdfUrl'] : '';
    ?>
   

    
    <iframe src="<?= $pdfUrl ?>#toolbar=0&navpanes=0&scrollbar=0"></iframe>
</body>
</html>
<script>
    // Bloquear clic derecho

    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // Bloquear selección de texto
    document.addEventListener('selectstart', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // Bloquear copiar
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // Bloquear arrastrar contenido
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // Bloquear teclas para descargar, imprimir e inspeccionar
    document.addEventListener('keydown', function(e) {
        // Ctrl+S, Ctrl+P, Ctrl+U
        if (e.ctrlKey && ['s', 'p', 'u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            return false;
        }
        // F12
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+I
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'i') {
            e.preventDefault();
            return false;
        }
    }, false);
</script>