<?php
session_start();
include('src/configuracion.php');
$consulta=$DB->first_row('SELECT `MensajesSinLeer` FROM `jugadores` WHERE ID= '.$_SESSION['id'].' LIMIT 1');
echo $consulta[0];
?>