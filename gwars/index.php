<?php
require('basePage.php');
$jugador->CargarMetadatos();
//Elementos del menu
$menu=array(
GetString('Visión general')=>array('visionGeneral.php','generalView.png'),
GetString('Recursos')=>array('recursos.php','resources.png'),
GetString('Imperio')=>array('imperio.php','empire.png'),
GetString('Edificios')=>array('edificios.php','buildings.png'),
GetString('Investigación')=>array('investigaciones.php','investigations.png'),
GetString('Hangar')=>array('hangar.php','hangar.png'),
GetString('Flota')=>array('flota.php','fleet.png'),
GetString('Tecnología')=>array('tecnologia.php','technology.png'),
GetString('Galaxia')=>array('galaxia.php','galaxy.png'),
GetString('Defensa')=>array('defensa.php','defense.png'),
null,
GetString('Alianza')=>array('alianza.php','alliance.png'),
GetString('Simulador')=>array('analisisBatalla.php','battle.png'),
GetString('Estadísticas')=>array('estadisticas.php','statistics.png'),
GetString('Buscar')=>array('buscar.php','search.png'),
null,
GetString('Mensajes')=>array('mensajes.php','messages.png'),
GetString('Notas')=>array('notas.php','notes.png'),
GetString('Opciones')=>array('opcionesjugador.php','options.png'),
GetString('Salir')=>array('salir.php','exit.png'),
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="favicon.ico" />
<link href="<?php echo $jugador->Datos['MetaDatos']['SG'] ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo $jugador->UrlSkin ?>style.css" rel="stylesheet" type="text/css" />
<title>Galactic Wars</title>
<script type="text/javascript" src="combine.php?type=javascript&files=jquery.js,interface.js,general.js,js.js,flotas.js,ajaxToolTip.js,filtros.js"></script>
<?php 
if(!empty($jugador->Datos['MetaDatos']['Menu']))
echo '<link href="'.$jugador->UrlSkin.'dock/style.css" rel="stylesheet" type="text/css" />';
?>
<!--[if lt IE 7]>
 <style type="text/css">
 .dock img { behavior: url(JavaScripts/iepngfix.htc) }
 </style>
<![endif]-->
<script type="text/javascript" src="../Firebug/firebug.js"></script>
<script language="Javascript" type="text/javascript">
var loaderImage='<?php echo $jugador->UrlSkin?>images/loader.gif';
var sinMensajes='<?php EchoString('Sin mensajes nuevos') ?>';
var unMensaje='<?php EchoString('1 mensaje nuevo') ?>';
var mensajesNuevos='<?php EchoString('mensajes nuevos') ?>';
var rutaImagenes= '<?php echo $jugador->UrlSkin.'images/' ?>';
var textoImagenPost='<?php EchoString('Guardando datos...'); ?>';
var sonidoNuevoMensaje='<?php echo $jugador->UrlSkin?>newMessage.mp3';
var usarEfectosOpacidad=<?php if(ObtenerValorCookie('efectosOpacidad',1)==1) echo 'true'; else echo 'false'; ?>;
var formatoFecha='<?php EchoString('d/MM H:mm:ss'); ?>';
var formatoTiempo='<?php EchoString('H:mm:ss'); ?>';
var textoHecho='<?php EchoString('Hecho'); ?>';
var unidadesTiempo='<?php EchoString('d h m s'); ?>';
ajaxWindow.imageLocation='<?php echo $jugador->UrlSkin?>windows/';

function MostrarImagenCarga(id)
{
	gid(id).innerHTML='<div><img src="'+loaderImage+'" alt="<?php EchoString('Cargando...');?>" title="<?php EchoString('Cargando la página...');?>"/></div>';
	gid(id).style.opacity=1;
}

function ErrorAjax(id,ajaxObject,description)
{
	if(ajaxObject.httpStatus==0)
	gid(id).innerHTML='<?php EchoString('El servidor no responde a la petición, prueba de nuevo en unos instantes')?>';
	else
	gid(id).innerHTML='<?php EchoString('El servidor ha enviado un error:')?> '+description;
	gid(id).style.opacity=1;
}
</script>
</head>

<body onKeyDown="TeclaPulsada(event);">
<div id="header">
  <div id="logo"><a title="<?php EchoString('Mostrar información sobre el servidor y la versión del juego'); ?>" onclick="Mostrar('cambios.php')"><img style="width:100%;" src="<?php echo $jugador->UrlSkin?>images/headerLogo.png"/></a></div>
  <div id="controlesGalaxia" style="float:left;display:none;"><?php MostrarControlesGalaxia(); ?></div>
  <div id="cabecera"><?php include('cabecera.php'); ?></div>
  <div id="cleared"></div>
</div>
<?php empty($jugador->Datos['MetaDatos']['Menu'])?MostrarMenu():'';?>
<div id="right"></div>
<div id="mainTop">  <?php $jugador->Datos['MetaDatos']['Menu']==2?MostrarDockSuperior():''; ?></div>
<div id="main"><?php include('visionGeneral.php'); ?></div>
<?php $jugador->Datos['MetaDatos']['Menu']==1?MostrarDockInferior():'';?>
<div id="sonidos" style="visibility: hidden; height: 1px; width: 1px;"></div>
</body>
</html>

<?php
function MostrarMenu()
{
	global $menu;
	$rutaSkin=$GLOBALS['jugador']->UrlSkin;

	echo '<div id="left"><ul class="leftMenu">
<li class="leftMenuHeader"><a onclick="Mostrar(\'skin.php?skin='.$rutaSkin.'\')"><img src="'.$rutaSkin.'images/skinLogo.png"  /></a></li>';

	foreach ($menu as $texto=>$link)
	{
		if($link==null)//Separador
		echo '<li class="leftMenuSeparator"></li>';
		else
		echo '<li><a onclick="Mostrar(\''.$link[0].'\')">'.$texto.'</a></li>';
	}
	echo '</ul></div>';
}

function MostrarDockInferior()
{
	global $menu;

	$rutaImagenes=$GLOBALS['jugador']->UrlSkin.'dock';
	echo '<div class="dock" id="dock2"><div class="dock-container2">';

	foreach ($menu as $texto=>$link)
	{
		if($link==null)//Separador
		continue;
		else
		echo '<a class="dock-item2" onclick="Mostrar(\''.$link[0].'\')"><span>'.$texto.'</span><img src="'.$rutaImagenes.'/'.$link[1].'" alt="'.$texto.'" /></a>';
	}

	echo '</div></div>
<script type="text/javascript">
$(document).ready(
	function()
	{
		$("#dock2").Fisheye(
			{
				maxWidth: 60,
				items: "a",
				itemsText: "span",
				container: ".dock-container2",
				itemWidth: 40,
				proximity: 80,
				alignment : "left",
				valign: "bottom",
				halign : "center"
			}
		)
	}
);
</script>';
}

function MostrarDockSuperior()
{
	global $menu;

	$rutaImagenes=$GLOBALS['jugador']->UrlSkin.'dock';
	echo '<div class="dock" id="dock"><div class="dock-container">';

	foreach ($menu as $texto=>$link)
	{
		if($link==null)//Separador
		continue;
		else
		echo '<a class="dock-item" onclick="Mostrar(\''.$link[0].'\')"><img src="'.$rutaImagenes.'/'.$link[1].'" alt="'.$texto.'" /><span>'.$texto.'</span></a>';
	}

	echo '</div></div>
<script type="text/javascript">
$(document).ready(
	function()
	{
		$("#dock").Fisheye(
			{
				maxWidth: 50,
				items: "a",
				itemsText: "span",
				container: ".dock-container",
				itemWidth: 40,
				proximity: 90,
				halign : "center"
			}
		)
	}
);
</script>';
}

function MostrarControlesGalaxia()
{
	?>
<div style="float:left">
  <input type="hidden" id="tipoVista" value="1">
  <input type="button" id="tipoGalaxia" onclick="Mostrar('galaxia.php?galaxia='+gid('galaxia').value+'&sistema='+gid('sistema').value+'&vista='+gid('tipoVista').value)">
</div>
<table class="GalaxyControlsTable">
  <tr>
    <th colspan="3"><? EchoString('Galaxia'); ?></th>
    <td>&nbsp;</td>
    <th colspan="3"><? EchoString('Sistema solar'); ?></th>
  </tr>
  <tr>
    <td><input type="button" id="GS" value="<-" onClick="if(gid('galaxia').value>1){gid('galaxia').value--;gid('mostrarGalaxia').click();}" /></td>
    <td><input type="text" id="galaxia" size="5" maxlength="1"/></td>
    <td><input type="button" id="GA" value="->" onClick="if(gid('galaxia').value<499){gid('galaxia').value++;gid('mostrarGalaxia').click();}" /></td>
    <td>&nbsp;</td>
    <td><input type="button" id="SS" value="<-" onClick="if(gid('sistema').value>1){gid('sistema').value--;gid('mostrarGalaxia').click();}" /></td>
    <td><input type="text" id="sistema" size="5" maxlength="3"  /></td>
    <td><input type="button" id="SA" value="->" onClick="if(gid('sistema').value<499){gid('sistema').value++;gid('mostrarGalaxia').click();}" /></td>
  </tr>
  <tr>
    <td colspan="6"><input type="button" id="mostrarGalaxia" value="<?php EchoString('Mostrar'); ?>" onclick="$.ajax({   type: 'GET',   url: 'galaxia.php',   data: 'galaxia='+gid('galaxia').value+'&sistema='+gid('sistema').value,   success: function(msg){     $('#main').html(msg);   } });"/></td>
  </tr>
</table>
	<?php
}
?>
