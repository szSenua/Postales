<?php

//Para que muestre los errores
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");

$dsn = 'mysql:dbname=postales;host=127.0.0.1';
$usuario = 'postales';
$pwd = 'postales';

try{
    $con = new PDO ($dsn, $usuario, $pwd);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }catch (PDOException $e) {
    echo 'Error: ' .$e->getMessage();
   }

?>