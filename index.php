<?php
require_once 'conecta.php';
require_once 'funciones.php';

// Obtener array con los destinatarios
$clientes = obtenerClientes($con);
require_once 'desconecta.php';

// Declarar variables vacías
$destinatarios = array();
$mensaje = "";
$asunto = ""; // Dependerá de la temática del mensaje
$imagenSeleccionada = "";
$errores = array();

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {

    // Recoger los datos del formulario

    // Recorrer los destinatarios seleccionados y almacenarlos en el array $destinatarios

    if (empty($_POST['destinatario'])) {
        $errores[] = 'Tiene que escoger al menos un destinatario';
    } else {
        foreach ($_POST['destinatario'] as $destinatario) {
            // Agregar el destinatario al array
            $destinatarios[] = $destinatario;
        }
    }

    //Testeo de que muestra solo el email.
    //var_dump($destinatarios);

    if (empty($_POST['mensaje'])) {
        $errores[] = 'El mensaje no puede estar vacío';
    } else {
        $mensaje = $_POST['mensaje'];
    }

    // Dependiendo del tema se pone un asunto u otro
    if (isset($_POST['tema'])) {
        $tema = $_POST['tema'];

        switch ($tema) {
            case 'navidad':
                $asunto = '¡Feliz Navidad';
                break;

            case 'bodas':
                $asunto = '¡Enhorabuena!';
                break;

            case 'cumpleaños':
                $asunto = '¡Que cumplas muchos más';
                break;

            default:
                $asunto = 'Sin Asunto';
                break;
        }
    }

    if (isset($_POST['imagen'])) {
        $imagenSeleccionada = $_POST['imagen'];
    } else {
        $errores[] = 'Debe escoger una imagen';
    }

    // Si el array de errores no está vacío, repintar
    if (count($errores) > 0) {
        $temaSeleccionado = isset($_POST['tema']) ? $_POST['tema'] : '';
        $subcarpetas = array_filter(glob('temas/*'), 'is_dir');
        pintaConParametros($destinatarios, $mensaje, $imagenSeleccionada, $errores, $clientes, $temaSeleccionado, $subcarpetas);
    } else {


        //Si el array de errores está vacío, manda el email

        enviarEmail($destinatarios, $asunto, $mensaje, $imagenSeleccionada, 'cristina@domenico.es', 'admin1234');


    }
} else {
?>
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
    </style>
</head>
<body>
    <form action="index.php" method="post">
        <label for="destinatario">Destinatario:</label>
        <select id="destinatario" name="destinatario[]" multiple>
            <?php
            // Mostrar lista de clientes
            foreach ($clientes as $cliente) {
                echo "<option value=\"{$cliente['email']}\">{$cliente['nombre']} ({$cliente['email']})</option>";
            }
            ?>
        </select>
        <br>

        <label for="mensaje">Mensaje:</label>
        <textarea name="mensaje" rows="4"></textarea>
        <br>

        <label for="tema">Tema:</label>
        <select id="tema" name="tema" onchange="this.form.submit()">
            <?php
            $ruta_temas = 'temas';
            $subcarpetas = array_filter(glob($ruta_temas . '/*'), 'is_dir');

            foreach ($subcarpetas as $subcarpeta) {
                $nombre_subcarpeta = basename($subcarpeta);
                echo "<option value=\"$nombre_subcarpeta\"";
                if (isset($_POST['tema']) && $_POST['tema'] === $nombre_subcarpeta) {
                    echo " selected";
                }
                echo ">$nombre_subcarpeta</option>";
            }
            ?>
        </select>

        <div id="checkboxes-container">
            <?php
            $temaSeleccionado = isset($_POST['tema']) ? $_POST['tema'] : '';

            foreach ($subcarpetas as $subcarpeta) {
                $nombre_subcarpeta = basename($subcarpeta);
                $mostrar = $temaSeleccionado === $nombre_subcarpeta;
                $imagenes = glob($subcarpeta . '/' . $temaSeleccionado . '*.jpg');

                foreach ($imagenes as $imagen) {
                    $nombre_imagen = basename($imagen);
                    echo "<label>";
                    echo "<img src=\"$imagen\" alt=\"$nombre_imagen\" width=\"100\">";
                    echo "<input type=\"radio\" name=\"imagen\" value=\"$nombre_imagen\"";
                    if ($mostrar && isset($_POST['imagen']) && $_POST['imagen'] === $nombre_imagen) {
                        echo " checked";
                    }
                    echo ">";
                    echo "</label>";
                }
            }
            ?>
        </div>

        <input type="submit" name="enviar"value="Enviar">
    </form>
</body>
</html>


<?php
}
?>