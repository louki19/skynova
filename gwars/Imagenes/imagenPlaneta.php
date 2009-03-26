<?php
$ruta=bin2hex(md5($_GET['i'],true)).'.jpg';
if(file_exists($ruta))
{
	header("Location: $ruta");
	exit;
}

header('Expires: Mon, 26 Jul 2050 05:00:00 GMT');
header('Content-type: image/jpeg');

$datos=unserialize(base64_decode($_GET['i']));

$alto=$datos[3];
$ancho=$datos[4];

if (empty($alto))
$alto=200;

if (empty($ancho))
$ancho=200;

$UrlSkin=$datos[0];

 $rutaImagenFondo=$UrlSkin.'planets/backgrounds/background'.$datos[2].'.jpg';
 $rutaImagenPlaneta=$UrlSkin.'planets/'.$datos[1].'.png';

$imagen = imagecreatetruecolor ($alto,$ancho);

//Pintar fondo
$imagenFondo=CargarJpeg($rutaImagenFondo);
if($imagenFondo)
{
	list($anchoFondoOriginal, $altoFondoOriginal) = getimagesize($rutaImagenFondo);
	imagecopyresampled ($imagen, $imagenFondo, 0, 0, 0, 0, $alto, $ancho,$anchoFondoOriginal, $altoFondoOriginal);
}



//Pintar imagen
$imagenPlaneta=CargarPng($rutaImagenPlaneta);
if($imagenPlaneta)
{
	list($anchoPlanetaOriginal, $altoPlanetaOriginal) = getimagesize($rutaImagenPlaneta);
	imagecopyresampled ($imagen, $imagenPlaneta, 0, 0, 0, 0, $alto, $ancho, $anchoPlanetaOriginal, $altoPlanetaOriginal);
}

ImageJPEG($imagen,$ruta);
ImageJPEG($imagen);
exit;

function CargarPng($nombreimg) {
	$im = @imagecreatefrompng ($nombreimg); /* Intento de apertura */

	if (!$im)
	{ /* Comprobar si ha fallado */
		ErrorAlCargar($nombreimg);
	}

	return $im;
}

function CargarJpeg ($nombreimg) {
	$im = @imagecreatefromjpeg ($nombreimg); /* Intento de apertura */
	
	return $im;
}

function ErrorAlCargar($urlImagen)
{
	$im  = imagecreate (600,200); /* Crear una imagen en blanco */
	$bgc = imagecolorallocate ($im, 200, 200, 200);
	$tc  = imagecolorallocate ($im, 0, 0, 0);
	imagefilledrectangle ($im, 0, 0, 150, 200, $bgc);
	/* Mostrar un mensaje de error */
	imagestring ($im, 1, 5, 5, "Error cargando", $tc);
	imagestring($im,1,5,20,$urlImagen, $tc);

	ImageJPEG($im);
	exit;
}
?>