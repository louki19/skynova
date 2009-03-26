<?php
//Iniciar sesion
session_start();
// indicar el charset del documento actual
header("Content-Type: text/html; charset=ISO-8859-1");
//Comprimir contenido con gzip
ob_start('ob_gzhandler');


//DEBUG
$DEBUG=false;
/* Establecer jugador ID 1 por defecto
if(!isset($_SESSION['id']))
$_SESSION['id']=1;
if(!isset($_SESSION['planeta']))
$_SESSION['planeta']=1;
*/
$textoFooter='';
$queries='';
$inicioCreacion=microtime(true);
//FIN

include('src/configuracion.php');
include('src/tecnologia.php');
include_once('src/funciones.php');
include('src/eventos.php');
include('src/jugador.php');
include('src/planeta.php');

/*
Tipos de inicio
0 - normal
1 - no cargar datos del planeta y unos pocos del jugador
2 - cargar planeta y algunos datos del jugador, pero no los eventos

Datos de Sesion

$_SESSION['id']=Id del jugador actual
$_SESSION['planeta']=Id del planeta actual
*/
if(!isset($tipoInicio))
$tipoInicio=0;

// Variables importantes del juego
if(!isset($_SESSION['id']))//Salir
{
	header('Location: ./login/index.php');
	exit;
}
if(isset($_POST['planeta']) && is_numeric($_POST['planeta']))
{
	$_SESSION['planeta']=$_POST['planeta'];
}

if($tipoInicio!=2)
{
	$eventos=new Eventos();
	$eventos->Procesar($_SESSION['id'],time());
}

if($tipoInicio==0)
$consulta=$_SESSION['id'];
else if($tipoInicio==1 || $tipoInicio==2)
$consulta=$DB->first_assoc('select ID,Nombre,Alianza,UrlSkin,RangoAlianza,MensajesSinLeer from `jugadores` where `ID`='.$_SESSION['id'].' LIMIT 1');

$jugador=new Jugador($consulta);

if(!isset($_SESSION['planeta']))
$_SESSION['planeta']=$jugador->Datos['PlanetaPrincipal'];

//Comprobar cambio de IP y salir si la variable ID no existe
if ($_SERVER['REMOTE_ADDR']!=$jugador->Datos['IP'])
{
	//echo '<script language=JavaScript>top.location.href = "'.$UrlBase.'login";</script>';
}

if($tipoInicio!=1)//Cargar datos del planeta
{
	$consulta=$DB->first_assoc('select * from `planetas` where `ID`='.$_SESSION['planeta'].' LIMIT 1;');
	$planeta=new Planeta($consulta);

	//Comprobar que el planeta que se intenta cargar pertenece al usuario
	if(isset($jugador) && $planeta->Datos['Jugador']!=$jugador->ID)
	{
		$_SESSION['planeta']=$jugador->Datos['PlanetaPrincipal'];
		$planeta=new Planeta($jugador->Datos['PlanetaPrincipal']);
	}
}
?>