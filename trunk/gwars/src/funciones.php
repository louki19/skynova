<?php

function ComprimirTexto($texto,$escapar=true)
{
	if(empty($texto))
	return;

	if($escapar)
	return '0x'.(string)bin2hex(gzcompress($texto,6));
	else
	return gzcompress($texto,6);
}

function DescomprimirTexto($texto)
{
	if(empty($texto))
	return;

	return gzuncompress($texto);
}

function ObtenerValorCookie($valor,$porDefecto=1)
{
	if(isset($_POST[$valor]))
	{
		$resultado=$_POST[$valor];
	}
	else if(isset($_GET[$valor]))
	{
		$resultado=$_GET[$valor];
	}
	else
	{
		if(isset($_COOKIE[$valor]))
		$resultado=$_COOKIE[$valor];
		else
		$resultado=$porDefecto;
	}

	if($_COOKIE[$valor]!=$resultado)
	setcookie($valor,$resultado, time() + 31536000);//Establecer cookie para un año

	return $resultado;
}

function CabeceraTab()
{
	echo '<div style="width:100%" id="tab"><ul class="tab">';
}

function CierreTab()
{
	echo '</ul><br style="line-height:26px"/><div id="tabContent" align="center">';
}

function Tab($texto,$id='',$link='',$seleccionada=false,$fade=null)
{
	$res= '<li';
	if(isset($_GET[$id]) || $seleccionada)
	$res.= ' class="tabSelected"';

	if(!empty($id) && !empty($link))
	$res.= ' id="'.$id.'"';

	if(empty($link))
	$link="Mostrar('?$id')";

	if(!isset($fade))
	$fade=ObtenerValorCookie('efectosOpacidad',1)==1?true:false;

	if($fade)
	{
		$res.="><a onclick=\"$('#tabContent').fadeOut(250,function(){
	$link;
	$('#tabContent').fadeIn(250);
});\">$texto</a></li>";
	}
	else
	{
		$res.="><a onclick=\"$link\">$texto</a></li>";
	}
	echo $res;
}

function SeparadorMiles($num)
{
	return number_format($num,null,null,'.');
}

function MostrarError($texto,$botonVolverAtras=false)
{
	echo $texto.'<br><br>';

	if($botonVolverAtras==false)
	echo '<a onclick="Mostrar(\'visionGeneral.php\')">'.GetString('Volver').'</a>';
	else
	echo '<a onclick="history.back(1)">'.GetString('Volver').'</a>';

	exit;
}

/**
 * Muestra la localización de un planeta y el link a su posicion
 *
 * @param array $planeta Datos del planeta
 */
function MostrarLocalizacionPlaneta($planeta,$link=false,$prefijoPlaneta=true)
{
	if(!isset($planeta['Galaxia']))
	return;

	if(isset($planeta['Nombre']) && !empty($planeta['Nombre']))
	{
		if(isset($planeta['Luna']) && $planeta['Luna']==1)
		$texto=GetString('Luna').' '.$planeta['Nombre'].' ';
		else
		$texto=($prefijoPlaneta?GetString('Planeta'):'').' '.$planeta['Nombre'].' ';
	}

	if($link==true)
	return $texto.'<a onclick="Mostrar(\'galaxia.php?galaxia='.$planeta['Galaxia'].'&sistema='.$planeta['Sistema'].'&posicion='.$planeta['Posicion'].'\')">['.$planeta['Galaxia'].':'.$planeta['Sistema'].':'.$planeta['Posicion'].']</a>';
	else
	return $texto.'['.$planeta['Galaxia'].':'.$planeta['Sistema'].':'.$planeta['Posicion'].']';
}

function IconoEnviarMensajeJugador($jugadorDestino,$tamaño=25)
{
	global $jugador;
	return '<a onclick="Mostrar(\'enviarMensaje.php?jugador='.$jugadorDestino['ID'].'\')"><img class="sendMessageImage" src="'.$jugador->UrlSkin.'images/message.png" title="'.GetString('Enviar mensaje').'" width="'.$tamaño.'"></a>';
}

function IconoEnviarMensajeAlianza($alianzaDestino,$tamaño=25)
{
	global $jugador;
	return '<a onclick="Mostrar(\'enviarMensaje.php?alianza='.$alianzaDestino['ID'].'\')"><img class="sendMessageImage" src="'.$jugador->UrlSkin.'images/message.png" title="'.GetString('Enviar mensaje').'" width="'.$tamaño.'"></a>';
}

/**
 * Muestra la cantidad disponible de un recurso
 *
 * @param Planeta $planeta
 * @return string codigo html generado
 */
function MostrarCantidadRecusos($tipoRecurso,$planeta,$mostrarEnergiaTotal=false,$id=null)
{
	//tipoRecurso: 1 - metal 2 -cristal 3 - antimateria 4 - energia
	$cantidad=0;
	if($tipoRecurso!=4)
	{
		switch ($tipoRecurso)
		{
			case 1:
				$cantidad=$planeta->Metal;
				$idSpan="Metal";
				$limiteAlmacenes=CapacidadAlmacen(90,$planeta->Tecnologias[90]);
				break;
			case 2:
				$cantidad=$planeta->Cristal;
				$idSpan="Cristal";
				$limiteAlmacenes=CapacidadAlmacen(91,$planeta->Tecnologias[91]);
				break;
			case 3:
				$cantidad=$planeta->Antimateria;
				$idSpan="Antimateria";
				$limiteAlmacenes=CapacidadAlmacen(92,$planeta->Tecnologias[92]);
				break;
		}
		if(isset($id))
		$idSpan=$id;
		$resultado='<span id="'.$idSpan.'" class="resourcesCountText"';
		if ($cantidad>$limiteAlmacenes)      //Almacences llenos
		$resultado.=' style="color:red;">'.number_format($cantidad,0,'.','.');
		else
		$resultado.='>'.number_format($cantidad,0,'.','.');
		$resultado.='</span>';
	}
	else
	{
		//Mostrar enegia
		$resultado='<span id="Energia" class="resourcesCountText"';
		if($planeta->Energia<0)
		$resultado.=' style="color:red;">'.number_format($planeta->Energia,0,'.','.');
		else
		$resultado.='>'.number_format($planeta->Energia,0,'.','.');
		if($mostrarEnergiaTotal)
		$resultado.='</span> / '.$planeta->EnergiaProducida();
		else
		$resultado.='</span>';
	}
	return $resultado;
}

/**
 * Limita las etiquetas html que se muestran en un texto
 *
 */
function LimitarEtiquetas(&$texto)
{
	return strip_tags($texto,'<p><font><br><strong><b><em><i><strike><u><ol><ul><li><alt><sub><sup><blockquote><table><tr><td><th><tbody><thead><tfoot><caption><span><pre><address><h1><h2><h3><h4><h5><h6><hr><dd><dl><dt><cite><abbr><acronym><del><ins><div><img><a>');
}

/**
 * Obtiene el codigo html que representa la imagen de un planeta
 *
 */
function ImagenPlaneta($planeta, $AdicionarToolTip=true, $LinkIrPlaneta=false,$class='thumbnailPlanet',$alto=90, $ancho=90)
{
	global $jugador;
	global $configuracion;

	$tooltip='';
	if ($AdicionarToolTip)
	$tooltip='tip="tooltip.php?tipo=planetaJugador&id='.$planeta['ID'].'&skin='.$jugador->UrlSkin.'"';
	if ($LinkIrPlaneta)
	{
		$link=' onclick="CambiarPlaneta('.$planeta['ID'].')"';
		$estiloLink='style="cursor:pointer"';
	}

	if($planeta['Luna']==1)
	$imagenPlaneta='moon';
	else
	$imagenPlaneta=TipoPlaneta($planeta['Posicion']).$planeta['Imagen'];

	$datos=array(
	$jugador->UrlSkin,
	$imagenPlaneta,
	$planeta['Fondo'],
	$alto,
	$ancho
	);

	$arg=base64_encode(serialize($datos));

	return "<img class=\"$class\" $estiloLink src=\"{$configuracion['ServImg']}?i=$arg\" $tooltip $link/>";

	//return '<img src="'.$ServidorImagenes.'?theme='.$jugador->UrlSkin.'&planeta='.$imagenPlaneta.'&fondo='.$planeta['Fondo']."&alto=$alto&ancho=$ancho\" class=\"$class\"  width=\"$ancho\" height=\"$alto\" $tooltip $link/>";
}

/**
 * Obtiene una cadena que representa el tipo de planeta según su posición en la galaxia
 *
 */
function TipoPlaneta($posicion)
{
	if($posicion==1 || $posicion==2)
	return 'fireplanet';
	if($posicion==3 || $posicion==4)
	return 'normaltemp';
	if($posicion==5 || $posicion==6)
	return 'gigantgas';
	if($posicion==7 || $posicion==8)
	return 'gigantice';
	if($posicion==9 || $posicion==10)
	return 'iceplanet';
}

/**
 * Redondea el numero pasado como argumento en formato 100k ó 100M
 *
 * @return string
 */
function RedondearNumero($numero)
{
	if ($numero>10000000 || abs($numero)>10000000)
	{
		return round($numero/1000000,1)."M";
	}
	else if ($numero>10000 || abs($numero)>10000)
	{
		return round($numero/1000,1)."k";
	}
	return $numero;
}

function ObtenerNombreAlianza($id)
{
	if($id==0)
	return null;

	global $nombresAlianza;

	if(isset($nombresAlianza[$id]))
	return $nombresAlianza[$id];
	else
	{
		if(!isset($nombresAlianza))$nombresAlianza=array();

		$nombresAlianza[$id]=$GLOBALS['DB']->getRowProperty('alianzas',$id,'Nombre');
		return $nombresAlianza[$id];
	}
}

/**
 * Muestra el nombre de un jugador al lado de un link a las coordenadas de su planeta principal
 *
 */
function LinkJugador($id)
{
	$consulta=$GLOBALS['DB']->first_assoc('SELECT Jugadores.Nombre,Planetas.Galaxia,Planetas.Sistema,Planetas.Posicion FROM `jugadores` JOIN Planetas ON Jugadores.PlanetaPrincipal=Planetas .ID WHERE Jugadores.ID='.$id);

	return $consulta['Nombre'].' '.MostrarLocalizacionPlaneta($consulta,true).IconoEnviarMensajeJugador($id);
}

/**
 * Formatea el numero con separador de miles y colores
 *
 * @param int $numero
 * @param bool $color indica si se colorean para valores menores o mayores que cero
 */
function FormatearNumero($numero,$color=false)
{
	if($color)
	{
		if($numero>0)
		return '<span style="color:lime">'.number_format($numero,null,null,'.').'</span>';
		if($numero<0)
		return '<span style="color:red">'.number_format($numero,null,null,'.').'</span>';
		if($numero==0)
		return '<span style="color:coral">'.number_format($numero,null,null,'.').'</span>';
	}
	else
	return number_format($numero,null,null,'.');
}

/**
 * Actualiza los recursos y datos mostrados en la cabecera
 *
 */
function ActualizarDatosCabecera($limpiarArray=true)
{
	global $planeta;

	if(isset($planeta) && isset($planeta->Datos['ProduccionMetal']))
	{
		echo '<script type="text/javascript">
var fechaActualizacionRecursos=new Date().getTime()/1000;
'.($limpiarArray?'datosRecursos=new Array();':'').'datosRecursos[0]=new Array("Metal",'.$planeta->Metal.','.$planeta->Datos['ProduccionMetal'].'/3600,'.CapacidadAlmacen(90,$planeta->Tecnologias[90]).');
datosRecursos[1]=new Array("Cristal",'.$planeta->Cristal.','.$planeta->Datos['ProduccionCristal'].'/3600,'.CapacidadAlmacen(91,$planeta->Tecnologias[91]).');
datosRecursos[2]=new Array("Antimateria",'.$planeta->Antimateria.','.$planeta->Datos['ProduccionAntimateria'].'/3600,'.CapacidadAlmacen(92,$planeta->Tecnologias[92]).');
document.getElementById("Energia").innerHTML='.$planeta->Energia.';';

		echo 'ActualizarDatosCabecera();</script>';
	}
	
	if(isset($GLOBALS['jugador']->Datos['MensajesSinLeer']))
	{
		echo '<script type="text/javascript">clearTimeout(comprobarMensajesId);
comprobarMensajesId=setTimeout("ComprobarMensajes()",30000);
ActualizarMensajes('.$GLOBALS['jugador']->Datos['MensajesSinLeer'].');</script>';
	}

	if($GLOBALS['DEBUG']==true)
	include('footerDebug.php');
}

/**
	 * Guarda todos los cambios realizados en una clase
	 *
	 */
function GuardarCambios($clase,$nombreTabla)
{
	global $DB;

	$sql='';
	foreach ($clase->Datos as $clave=>$valorActual)
	{
		$valorOriginal=$clase->DatosOriginales[$clave];

		if($valorOriginal!=$valorActual)
		{
			if(is_numeric($valorActual))
			$sql.="`$clave`=".$valorActual.',';
			else if(is_array($valorActual))
			{
				//Convertir todos los numeros a int
				foreach ($valorActual as $claveArray=>$valorArray)
				{
					if(is_numeric($valorArray))
					{
						if($valorArray==0)
						unset($valorActual[$claveArray]);
						else
						$valorActual[$claveArray]=(int)$valorArray;
					}
				}
				$sql.="`$clave`='".$DB->escape_string(serialize($valorActual)).'\',';
			}
			else
			$sql.="`$clave`='".$DB->escape_string($valorActual).'\',';

			$clase->DatosOriginales[$clave]=$valorActual;
		}
	}
	if(!empty($sql))
	{
		$DB->query("UPDATE `$nombreTabla` SET ".substr($sql,0,strlen($sql)-1)." where `ID`=$clase->ID LIMIT 1");
	}
}

function Ventana($contenido,$id,$linkId)
{
	echo '<div id="'.$id.'" class="window">
	<div class="windowTop">
		<div id="windowTopContent">Window example</div>
		<img src="images/window_min.jpg" class="windowMin" />
		<img src="images/window_max.jpg" class="windowMax" />
		<img src="images/window_close.jpg" class="windowClose" />
	</div>
	<div class="windowBottom"><div class="windowBottomContent">&nbsp;</div></div>

	<div class="windowContent"><p>'.$contenido.'</p>
	</div>
	<img class="windowResize" src="images/window_resize.gif"  />
</div>
<script type="text/javascript">
$(document).ready(function(){
PrepararVentana("'.$linkId.'","'.$id.'");
});
</script>
';	
}

/**
 * Muestra las paginas de una búsqueda
 *
 * @param int $inicio Indice de inicio del resultado actual
 * @param int $porPagina Resultados mostrados por pagina
 * @param int $totales Resultados totales
 * @param string $get Datos Get para enviar en la url de los link. Ejemplo &usuario=1
 * @param bool $texto Indica si se muestra un resumen de los resultados obtenidos
 * @param int $links Numero de links de páginas a mostrar
 */
function Paginador($inicio,$porPagina,$totales,$get='',$texto=true,$links=11)
{
	$resultado='<div class="paginador">';

	if($texto)
	$resultado.=sprintf(GetString('Mostrando resultados %s - %s de %s'),$inicio,min($inicio+$porPagina,$totales),$totales).'<br>';

	$paginasTotales=ceil($totales/$porPagina);
	$paginaActual=ceil($inicio/$porPagina);

	$url=substr($_SERVER['SCRIPT_URL'],1);
	if($paginaActual<$paginasTotales && $paginasTotales>1)//Mostrar paginacion
	{
		$contadorPagina=max(0,$paginaActual-floor($links/2));

		if($paginaActual>0)
		$resultado.= '<a style="font-size:30px" title="'.GetString('Página anterior').'" onclick="Mostrar(\''.$url.'?inicio='.(($paginaActual-1)*$porPagina).$get.'\')">&laquo;</a>&nbsp;&nbsp;';

		for($mostrados=0;$mostrados<$links && $contadorPagina<$paginasTotales;$contadorPagina++)
		{
			$distancia=min(abs($contadorPagina-$paginaActual),4);

			if($distancia==0)
			$estilo='style="font-size:30px;text-decoration:underline;" ';
			else
			$estilo='style="font-size:'.round(30-$distancia*4).'px;" ';

			$resultado.= '<a '.$estilo.' onclick="Mostrar(\''.$url.'?inicio='.(($paginaActual+1)*$porPagina).$get.'\')">'.$contadorPagina.'</a>';

			$mostrados++;

			if($mostrados<$links && $contadorPagina+1<$paginasTotales)
			$resultado.= '&nbsp;-&nbsp;';
		}

		if($paginaActual+1<$paginasTotales)
		$resultado.= '&nbsp;&nbsp;<a title="'.GetString('Página siguiente').'" style="font-size:30px" onclick="Mostrar(\''.$url.'?inicio='.(($paginaActual+1)*$porPagina).$get.'\')">&raquo;</a>';


	}
	$resultado.='</div>';
	return $resultado;
}
?>