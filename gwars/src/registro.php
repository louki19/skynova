<?php

/* Registro.php

Registro y login de nuevos jugadores y planetas
*/

//Obtiene la ID del jugador especificado
function ObtenerJugador($Nombre, $Pass)
{
	global $DB;
	$datos=$DB->first_assoc('select ID,Estado,MetaDatos from `jugadores` where `Nombre`="'.$DB->escape_string($Nombre).'" LIMIT 1');

	$metaDatos=unserialize($datos['MetaDatos']);
	if ($metaDatos['P']== bin2hex(md5($Pass,true)))
	{
		if($datos['Estado']==1)
		return -2;
		return $datos['ID'];
	}
	else return -1;
}

/**
 * Crea un nuevo jugador y devuelve su ID. No valida si el nombre de usuario es correcto o repetido
 *
 */
function RegistrarNuevoJugador($nombre,$pass,$email)
{
	global $DB;

	$metadatos=array(
	'P'=>bin2hex( md5($pass, true) ),
	'Email'=>$DB->escape_string($email),
	'SG'=>'../EnergyStyle/modernGalaxy.css'
	);

	$sql="INSERT INTO `jugadores` (
`ID` ,
`Nombre` ,
`PlanetaPrincipal` ,
`UrlSkin` ,
`Investigando` ,
`IP` ,
`UltimoAcceso` ,
`MensajesSinLeer` ,
`Alianza` ,
`RangoAlianza` ,
`Ranking` ,
`Puntos` ,
`Estado` ,
`Tecnologias` ,
`MetaDatos`
)
VALUES (
NULL , '".$DB->escape_string($nombre)."', 0, '../EnergyStyle/' , '', NULL , NULL , '', '', '', '', '', '', '', '".serialize($metadatos)."'
);";

	$DB->query($sql);

	return $DB->lastInsertId();
}

/**
 * Comprueba si un nombre de usuario es válido.
 * 
 * Devuelve un numero, cuyo valor significa
 * 
 * 	0 - Nombre correcto
 *  1 - Nombre repetido
 *  2 - Nombre con formato inválido
 *
 */
function ComprobarNombreUsuario($nombre)
{
	global $DB;

	$valida=strip_tags(stripslashes($nombre));

	if(strcmp($nombre,$valida)!=0)
	return 2;

	$consulta=$DB->query('SELECT ID FROM `jugadores` WHERE `Nombre`="'.$DB->escape_string($nombre).'" LIMIT 1');

	if($consulta->num_rows()>0)
	return 1;

	return 0;
}

/**
 * Crea un nuevo planeta y devuelve su id
 *
 */
function RegistrarNuevoPlaneta($jugador=null,$nombre=null,$galaxia=null,$sistema=null,$posicion=null,$campos=null,$temperatura=null,$metal=0,$cristal=0,$antimateria=0,$esLuna=false)
{
	global $DB;

	if(!isset($nombre))
	$nombre=GetString('Planeta principal');
	else
	$nombre=$DB->escape_string(stripslashes(strip_tags($nombre)));

	if(!isset($galaxia))//Buscar coordenadas vacías
	{
		mt_srand(date('G',time()));
		$galaxia=Aleatorio(1,6,1,8);
		$sistema=mt_rand(1,499);
		$posicion=mt_rand(3,8);
		mt_srand(rand());//Reiniciar los aleatorios "puros"

		do {
			$posicion+=mt_rand(1,4);
			if($posicion>8)//Nuevo sistema
			{
				$posicion=mt_rand(3,5);
				$sistema+=mt_rand(1,4);

				if($sistema>499)//Nueva galaxia
				{
					$sistema=mt_rand(1,499);
					$galaxia+=mt_rand(1,4);
					if($galaxia>8)
					{
						$galaxia=Aleatorio(1,6,1,8);
					}
				}
				else//Comprobar si el sistema esta vacio
				{
					$cantidad=$DB->first_row("select count(*) from planetas where galaxia=$galaxia && sistema=$sistema");
					if($cantidad[0]>4)//Sistema lleno, buscar uno nuevo
					{
						$posicion=9;//Reiniciar la posicion
						continue;
					}
				}
			}

			$cantidad=$DB->first_row("select count(*) from planetas where galaxia=$galaxia && sistema=$sistema && posicion=$posicion");
		} while($cantidad[0]>0);
	}

	//Calcular campos y temperatura
	if($posicion==1 || $posicion==2)
	{
		if(!isset($temperatura))$temperatura=mt_rand(265,365);
		if(!isset($campos))$campos=Aleatorio(50,90,40,350);
	}
	if($posicion==3 || $posicion==4)
	{
		if(!isset($temperatura))$temperatura=mt_rand(-77,23);
		if(!isset($campos))$campos=Aleatorio(120,175,40,350);
	}
	if($posicion==5 || $posicion==6)
	{
		if(!isset($temperatura))$temperatura=mt_rand(-176,-76);
		if(!isset($campos))$campos=Aleatorio(200,300,40,350);
	}
	if($posicion==7 || $posicion==8)
	{
		if(!isset($temperatura))$temperatura=mt_rand(-275,-163);
		if(!isset($campos))$campos=Aleatorio(40,150,245,350);
	}
	if($posicion==9 || $posicion==10)
	{
		if(!isset($temperatura))$temperatura=mt_rand(-273,-179);
		if(!isset($campos))$campos=Aleatorio(40,70,40,350);
	}

	$sql="INSERT HIGH_PRIORITY INTO `planetas` (
`ID` ,
`Jugador` ,
`Nombre` ,
`Galaxia` ,
`Sistema` ,
`Posicion` ,
`CamposOcupados` ,
`CamposTotales` ,
`Temperatura` ,
`Metal` ,
`Cristal` ,
`Antimateria` ,
`EnergiaLibre` ,
`Luna` ,
`Imagen` ,
`Fondo` ,
`Estado` ,
`EscombrosMetal` ,
`EscombrosCristal` ,
`Orden` ,
`Tecnologias` ,
`Rendimientos` ,
`UltimoAcceso` ,
`UltimaActualizacion` ,
`ProduccionMetal` ,
`ProduccionCristal` ,
`ProduccionAntimateria` ,
`NivelProduccion` ,
`Construcciones`
)
VALUES (
NULL , '$jugador', '$nombre' , $galaxia, $sistema, $posicion, 0 , $campos , $temperatura , $metal , $cristal,$antimateria,
 0, ".($esLuna==true?1:0).", ".mt_rand(1,5).", ".mt_rand(1,10).", ".(isset($jugador)?0:3).", 0, 0, '', '".serialize(array())."', '', '', '', 0, 0, 0, '', ''
);";
	$DB->query($sql);
	$id= $DB->lastInsertId();

	//Calcular producciones
	$planeta=new Planeta($id);
	$planeta->RecalcularProducciones();
	$planeta->GuardarCambios();

	return $id;
}

/**
* Obtiene un valor aleatorio, dando mas relevancia los valores entre $minimo y $maximo
*
*/
function Aleatorio($minimo,$maximo,$minimoAbsoluto,$maximoAbsoluto)
{
	if(mt_rand(0,100)>mt_rand(40,55))
	return mt_rand($minimoAbsoluto,$maximoAbsoluto);
	else
	return mt_rand($minimo,$maximo);
}


/**
 * Generador de nombres y contraseñas accesibles
 * http://www.tufuncion.com/generar-passwords-php
 *
 */
function GenerarTexto($silabas= 3, $use_prefix = false)
{

	// Definimos la función a menos de que esta exista
	if (!function_exists('ae_arr'))
	{
		// Esta función devuleve un elemento aleatorio
		function ae_arr(&$arr)
		{
			return $arr[rand(0, sizeof($arr)-1)];
		}
	}

	// Prefijos
	$prefix = array('aero', 'anti', 'auto', 'bi', 'bio',
	'cine', 'deca', 'demo', 'contra', 'eco',
	'ergo', 'geo', 'hipo', 'cent', 'kilo',
	'mega', 'tera', 'mini', 'nano', 'duo');

	// Sufijos
	$suffix = array('on', 'ion', 'ancia', 'sion', 'ia',
	'dor', 'tor', 'sor', 'cion', 'acia');

	// Sonidos
	$vowels = array('a', 'o', 'e', 'i', 'u', 'ia', 'eo');

	// Consonantes
	$consonants = array('r', 't', 'p', 's', 'd', 'f', 'g', 'h', 'j',
	'k', 'l', 'z', 'c', 'v', 'b', 'n', 'm', 'qu');

	$password = $use_prefix?ae_arr($prefix):'';
	$password_suffix = ae_arr($suffix);

	for($i=0; $i<$silabas; $i++)
	{
		// Selecciona una consonante al azar
		$doubles = array('c', 'l', 'r');
		$c = ae_arr($consonants);
		if (in_array($c, $doubles)&&($i!=0)) {
			if (rand(0, 4) == 1) // 20% de probabiidad
			$c .= $c;
		}
		$password .= $c;
		//

		// Seleccionamos un sonido al azar
		$password .= ae_arr($vowels);

		if ($i == $silabas - 1) // Si el sufijo empieza con vocal
		if (in_array($password_suffix[0], $vowels)) // Añadimos una consonante
		$password .= ae_arr($consonants);

	}

	// Seleccionamos un sufijo aleatorio
	$password .= $password_suffix;

	return $password;
}

?>