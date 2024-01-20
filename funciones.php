<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            resize: none; 
        }

        #checkboxes-container {
            margin-bottom: 15px;
            display: flex; 
            flex-wrap: wrap; 
            justify-content: center; 
            align-items: center; 
        }

        #checkboxes-container label {
            margin-right: 10px;
            flex: 0 0 calc(33.333% - 10px); 
            text-align: center; 
        }

        img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-bottom: 5px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .alert-danger{
            color: red;
        }
    </style>
</head>
<body>

<?php

//Función para obtener los clientes de la base de datos
function obtenerClientes($con) {

    $sql = 'SELECT nombre, email FROM clientes';

    $stmt = $con->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

// Función repintado
function pintaConParametros($destinatarios, $mensaje, $imagenSeleccionada, $errores, $clientes, $temaSeleccionado, $subcarpetas) {
    echo '<form action="index.php" method="post">';

    if (!empty($errores)) {
        echo '<div class="alert alert-danger" role="alert">
            <ul>';
        foreach ($errores as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul></div>';
    }

    // Resto del formulario
    echo '
        <label for="destinatario">Destinatario:</label>
        <select id="destinatario" name="destinatario[]" multiple>';

    // Mostrar lista de clientes
    foreach ($clientes as $cliente) {
        echo "<option value=\"{$cliente['email']}\"";
        if (in_array($cliente['email'], $destinatarios)) {
            echo " selected";
        }
        echo ">{$cliente['nombre']} ({$cliente['email']})</option>";
    }

    echo '</select>
        <br>
        <label for="mensaje">Mensaje:</label>
        <textarea name="mensaje" rows="4">' . $mensaje . '</textarea>
        <br>
        <label for="tema">Tema:</label>
        <select id="tema" name="tema" onchange="this.form.submit()">';

    // Mostrar opciones de tema
    foreach ($subcarpetas as $subcarpeta) {
        $nombre_subcarpeta = basename($subcarpeta);
        echo "<option value=\"$nombre_subcarpeta\"";
        if ($temaSeleccionado === $nombre_subcarpeta) {
            echo " selected";
        }
        echo ">$nombre_subcarpeta</option>";
    }

    echo '</select>
        <div id="checkboxes-container">';

    // Mostrar checkboxes para las imágenes
    foreach ($subcarpetas as $subcarpeta) {
        $nombre_subcarpeta = basename($subcarpeta);
        $mostrar = $temaSeleccionado === $nombre_subcarpeta;
        $imagenes = glob($subcarpeta . '/' . $temaSeleccionado . '*.jpg');

        foreach ($imagenes as $imagen) {
            $nombre_imagen = basename($imagen);
            echo "<label>";
            echo "<img src=\"$imagen\" alt=\"$nombre_imagen\" width=\"100\">";
            echo "<input type=\"radio\" name=\"imagen\" value=\"$nombre_imagen\"";
            if ($mostrar && $imagen === $imagenSeleccionada) {
                echo " checked";
            }
            echo ">";
           
            echo "</label>";
        }
    }

    echo '</div>
        <input type="submit" name="enviar" value="Enviar">
    </form>';
}

//función para mandar la postal

function enviarEmail($destinatarios, $asunto, $mensaje, $imagenSeleccionada, $usuario, $pass){
    
    include_once('PHPMailer-master/src/PHPMailer.php');
    include_once('PHPMailer-master/src/SMTP.php');

   $mail = new PHPMailer();
   //$mail->PluginDir = "PracticaPostales/PHPMailer-master/PHPMailer-master/";  // Not needed if PHPMailer files are in the same directory
   $mail->isSMTP();
   $mail->Mailer = "SMTP";
   $mail->SMTPAuth = true;
   $mail->isHtml(true);
   $mail->SMTPAutoTLS = false;
   $mail->Port = 25;
   $mail->CharSet = 'UTF-8';
   $mail->Host = "localhost";
   $mail->Username = $usuario;
   $mail->Password = $pass;
   $mail->setFrom("cristina@domenico.es");
   $mail->SMTPDebug = 2;  // Enable verbose debug output

   if(is_array($destinatarios)){

       foreach ($destinatarios as $destinatario) {
           $mail->addAddress($destinatario);
    }
  
  $mail->Subject = $asunto;
  $mail->Body = $mensaje;

  // Cuerpo del mensaje con la imagen seleccionada
  $body .= "<img src='$imagenSeleccionada' alt='Imagen'>";
  
  if(!$mail->send()){
       echo $mail->ErrorInfo;
  } else {
    //El email ha sido enviado.
       header('Location: confirmacionEnvio.php');
  }

    }
}

?>
</body>
</html>