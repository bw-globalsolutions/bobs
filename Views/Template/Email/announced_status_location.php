<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?=$data['asunto']?></title>
</head>
<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f9f9f9;">
  <!-- Encabezado -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0d8bf2;">
    <tr>
      <td align="center" style="padding:20px;">
        
        <span style="color:#fff; font-size:36px; font-weight:bold;">Dairy queen</span>
      </td>
    </tr>
  </table>

  <!-- Barra de título -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0d8bf2; color:#fff;">
    <tr>
      <td align="center" style="padding:10px; font-size:18px; font-weight:bold;">
        La tienda con el número <?=$data['numero'] ?> actualizó su estatus a <?=$data['actual'] ?>
      </td>
    </tr>
  </table>

  <!-- Contenido -->
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px; background-color:#fff;">
    <tr>
      <td align="center" style="color:#555; font-size:14px; max-width:600px;">
        La tienda <strong><?=$data['numero'] ?></strong>, <strong><?=$data['name'] ?></strong>, 
        ubicado en <strong><?=$data['direccion'] ?><strong> actualizó su estatus a <?=$data['actual'] ?></strong>, con fecha de <strong><?=date('M d - h:i', time())?></strong>.  
        <br><br>
      
      </td>
    </tr>
    <tr>
      <td align="center" style="padding:30px;">
        
      </td>
    </tr>
  </table>

  <!-- Pie -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0d8bf2; color:#fff;">
    <tr>
    
    </tr>
  </table>
</body>
</html>


