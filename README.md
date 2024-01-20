# Aplicación para Envío Automatizado de Correos con PHPMailer

## Descripción del Problema

Desarrollar una aplicación web en PHP que permita enviar correos automatizados a los clientes de una empresa, con información sobre las últimas novedades, felicitaciones de cumpleaños o Navidad. La aplicación deberá tener acceso a una base de datos con la información de los clientes, incluyendo nombre, apellidos, dirección, código postal, email y fecha de nacimiento.

## Páginas de la Aplicación

### Página Inicial (`index.php`)

- Elección del tema:
  - Lista de temas disponibles.
  - Muestra de imágenes relacionadas con el tema elegido.

- Elementos del Correo:
  - Lista desplegable de clientes (obtenida de la base de datos).
  - Lista desplegable o botones de selección para el tema del correo.
  - Lista de nombres de fotos mostradas.
  - Control de texto para el cuerpo del mensaje.

- Botón de envío que redirige a la página de confirmación (`confirmation.php`).

### Página de Confirmación (`confirmation.php`)

- Muestra una vista previa del correo con los elementos seleccionados.
- Botón de confirmación para enviar el correo (llama a un script de PHP para procesar el envío utilizando PHPMailer).
- Enlace a la página inicial.