<?php
class Technology
{
	var $ID;
	var $Name;
	var $ShortDescription;
	var $LongDescription;

	function Technology($ID,$Name,$ShortDescription,$LongDescription='')
	{
		$this->ID=$ID;
		$this->Name=$Name;
		$this->ShortDescription =$ShortDescription;
		if (empty($LongDescription)==false)
		$this->LongDescription =$LongDescription;
	}
}
class CaracteristicaArmamento
{
	public $Casco;
	public $Escudo;
	public $Ataque;
	public $FuegoRapido;

	function CaracteristicaArmamento($Casco, $Escudo,$Ataque,$FuegoRapido)
	{
		$this->Casco=$Casco;
		$this->Escudo=$Escudo;
		$this->Ataque=$Ataque;
		$this->FuegoRapido=$FuegoRapido;
	}
}
class CaracteristicaNave extends CaracteristicaArmamento
{
	public $CapacidadCarga;
	public $Velocidad;
	public $ConsumoCombustible;

	function CaracteristicaNave($Casco, $Escudo,$Ataque,$CapacidadCarga,$Velocidad,$ConsumoCombustible,$FuegoRapido)
	{
		$this->Casco=$Casco;
		$this->Escudo=$Escudo;
		$this->Ataque=$Ataque;
		$this->CapacidadCarga=$CapacidadCarga;
		$this->Velocidad=$Velocidad;
		$this->ConsumoCombustible=$ConsumoCombustible;
		$this->FuegoRapido=$FuegoRapido;
	}
}

/**
 * Obtiene el coste de una tecnología en el nivel 1 o por unidad
 * @return array
 */
function CosteInicialTecnologia($ID)
{
	switch ($ID)
	{
		//Edificios
		case 1:
			return array(60,15,0);    //Mina de metal
		case 2:
			return array(48,24,0);    //Mina de cristal
		case 3:
			return array(225,75,0);    //Acelerador de partículas
		case 10:
			return array(75,30,0);    //Planta solar
		case 11:
			return array(900,360,180);    //Planta de antimateria
		case 12:
			return array(0,50000,3000);    //Planta de microondas
		case 20:
			return array(200,400,200);    //Laboratorio
		case 21:
			return array(400,120,200);    //Robots
		case 22:
			return array(1000000,500000,100000);    //Nanobots
		case 23:
			return array(400,200,100);    //Hangar
		case 24:
			return array(20000,40000,0);    //Estacion espacial
		case 25:
			return array(20000,20000,500);    //Silo
		case 90:
			return array(2000,0,0);    //Almacen de metal
		case 91:
			return array(2000,1000,0);    //Almacen de cristal
		case 92:
			return array(2000,2000,0);    //Almacen de antimateria

			//Tecnologias
		case 100:
			return array(200,1000,200);    //Espionaje
		case 101:
			return array(0,400,600);    //Computacion
		case 102:
			return array(800,200,0);    //Militar
		case 103:
			return array(200,600,0);    //Defensa
		case 104:
			return array(1000,0,0);    //Blindaje
		case 105:
			return array(0,800,400);    //Energía
		case 106:
			return array(400,1600,1000);    //Antimateria
		case 107:
			return array(0,4000,2000);    //Hiperespacio
		case 108:
			return array(400,0,600);    //Combustion
		case 109:
			return array(2000,4000,600);    //Impulso
		case 110:
			return array(10000,20000,6000);    //Propulsor
		case 111:
			return array(200,100,0);    //Laser
		case 112:
			return array(1000,300,100);    //Ionica
		case 113:
			return array(2000,4000,1000);    //Plasma
		case 114:
			return array(0,0,0,300000);    //Graviton
		case 115:
			return array(2500000,4000000,80000);    //Red

			//Naves
		case 299:
			return array(0,2000,500);    //Satelite solar
		case 300:
			return array(5000,5000,0);    //Nave pequeña de carga
		case 301:
			return array(20000,20000,0);    //Nave grande de carga
		case 302:
			return array(35000,20000,0);    //Reciclador
		case 303:
			return array(0,1000,0);    //Sondas
		case 304:
			return array(10000,20000,10000);    //Colonizador
		case 305:
			return array(1500,500,0);    //Cazador ligero
		case 306:
			return array(3000,2000,0);    //Cazador pesado
		case 307:
			return array(15000,7000,800);    //Crucero
		case 308:
			return array(40000,20000,0);    //Nave de batalla
		case 309:
			return array(50000,25000,15000);    //Bombardero
		case 310:
			return array(60000,50000,15000);    //Destructor
		case 311:
			return array(250000,100000,75000);    //Interceptor
		case 312:
			return array(5000000,4000000,1000000);    //Estrella de la muerte

			//Defensas
		case 400:
			return array(1000,0,0);    //Lanzamisiles
		case 401:
			return array(1500,500,0);    //Láser pequeño
		case 402:
			return array(6000,2000,0);    //Láser grande
		case 403:
			return array(2000,6000,0);    //Cañon ionico
		case 404:
			return array(20000,15000,2000);    //Cañon gauss
		case 405:
			return array(50000,50000,30000);    //Cañon de plasma
		case 406:
			return array(150000,75000,75000);    //Cañon antimateria
		case 502:
			return array(10000,10000,0);    //Escudo
		case 500:
			return array(8000,2000,0);    //Misil de intercepcion
		case 501:
			return array(12500,2500,10000);    //Misil interplanetario

	}
}


/**
 * Obtiene las caracteristicas de una unidad teniendo sin tener en cuenta las tecnologías del jugador
 * @return CaracteristicaNave
 */
function CaracteristicasBaseArmamento($ID)
{
	//Casco, escudo, ataque, velocidad de disparo, capacidad, velocidad, consumo
	switch ($ID)
	{
		case 299:
			return new CaracteristicaNave(200,20,0,0,0,0,array());    //Satélite solar
		case 300:
			return new CaracteristicaNave(500,10,5,20000,18450,2,array(303=>5,299=>5));    //Nave pequeña de carga
		case 301:
			return new CaracteristicaNave(1500,50,5,100000,20500,4,array(303=>5,299=>5));    //Nave grande de carga
		case 302:
			return new CaracteristicaNave(1700,30,1,90000,10895,12,array(303=>5,299=>5));    //Reciclador
		case 303:
			return new CaracteristicaNave(100,10,0,5,1750000,1,array());    //Sondas de espionaje
		case 304:
			return new CaracteristicaNave(3000,100,50,20000,12900,35,array(303=>5,299=>5));    //Colonizador
		case 305:
			return new CaracteristicaNave(400,10,50,100,26000,3,array(303=>5,299=>5,311=>5));    //Cazador ligero
		case 306:
			return new CaracteristicaNave(1000,25,150,300,25200,5,array(303=>5,299=>5,311=>10));    //Cazador pesado
		case 307:
			return new CaracteristicaNave(2700,50,400,1000,27000,11,array(303=>5,299=>5,305=>3,400=>10));    //Crucero
		case 308:
			return new CaracteristicaNave(6000,200,1000,2000,20000,20,array(303=>5,299=>5));    //Nave de batalla
		case 309:
			return new CaracteristicaNave(7500,500,1000,1000,16500,35,array(303=>5,299=>5,400=>20,401=>20,402=>10,403=>10));    //Bombardero
		case 310:
			return new CaracteristicaNave(11000,500,2000,4000,15750,40,array(303=>5,299=>5,401=>10));    //Destructor
		case 311:
			return new CaracteristicaNave(45000,15000,5000,25000,14850,60,array(303=>5,299=>5,310=>10,312=>25));    //Interceptor
		case 312:
			return new CaracteristicaNave(900000,50000,200000,50000000,3250,1,array(303=>1250,299=>1250,300=>250,301=>250,302=>250,304=>250,305=>200,306=>100,307=>35,308=>30,309=>25,311=>5,400=>200,401=>200,402=>100,403=>100,404=>50));    //Estrella de la muerte
		case 400:
			return new CaracteristicaArmamento(200,20,80,array());//Lanzamisiles
		case 401:
			return new CaracteristicaArmamento(300,25,100,array());//Laser pequeño
		case 402:
			return new CaracteristicaArmamento(800,100,250,array());//Laser grande
		case 403:
			return new CaracteristicaArmamento(800,500,150,array());//Cañon ionico
		case 404:
			return new CaracteristicaArmamento(3500,200,110,array());//Cañon Gauss
		case 405:
			return new CaracteristicaArmamento(10000,300,3000,array());//Cañon de plasma
		case 406:
			return new CaracteristicaArmamento(25000,600,15000,array(308=>20,310=>10));//Cañon de antimateria
		case 500:
			return new CaracteristicaArmamento(800,1,1,array());//Misil de intercepcion
		case 501:
			return new CaracteristicaArmamento(1500,1,12000,array());//Misil interplanetario
	}
}

/**
 * Obtiene las caracteristicas de una unidad teniendo en cuenta las tecnologias mejoradas
 * @return CaracteristicaNave
 */
function CaracteristicasActualesArmamento($ID,$tecnos=null)
{
	if(!isset($tecnos))
	$tecnos=$GLOBALS['jugador']->Tecnologias;

	$caracteristicas=CaracteristicasBaseArmamento($ID);

	$caracteristicas->Velocidad=VelocidadActualNave($ID,$caracteristicas->Velocidad);
	$caracteristicas->Ataque=(int)round($caracteristicas->Ataque*(1+$tecnos[102]*0.1));
	$caracteristicas->Escudo=(int)round($caracteristicas->Escudo*(1+$tecnos[103]*0.1));
	$caracteristicas->Casco=(int)round($caracteristicas->Casco*(1+$tecnos[104]*0.1));
	$caracteristicas->ConsumoCombustible=(int)round($caracteristicas->ConsumoCombustible*(1-$tecnos[105]/100));

	return $caracteristicas;
}

/**
 * Calcula la velocidad de vuelo de una nave teniendo en cuenta las tecnologías desarrolladas
 * @param integer $velocidad Velocidad base de la nave
 * @return integer
 */
function VelocidadActualNave($ID,$velocidadBase)
{
	global $jugador;

	$motorCombustion=$jugador->Tecnologias[108];
	$motorImpulso=$jugador->Tecnologias[109];
	$propulsor=$jugador->Tecnologias[110];

	if($ID==300 && $motorImpulso<7)//Nave pequeña de carga
	$velocidad= $velocidadBase*(1+$motorCombustion*0.05);

	if (($ID==309 && $propulsor<8)) //Bombardero
	$velocidad= $velocidadBase*(1+$motorImpulso*0.05);

	if($ID==312 && $propulsor>=12)//Estrella de la muerte
	$velocidad= $velocidadBase*(2+$propulsor*0.2);

	switch ($ID)
	{
		//Motor de combustión
		case 303:	//Sondas de espionaje
		case 305:  //Cazador ligero
		$velocidad=$velocidadBase*(1+$motorCombustion*0.05);
		break;

		//Motor de impulso
		case 301:   //Nave grande de carga
		case 302:	//Reciclador
		case 304: //Colonizador
		case 306:  //Cazador pesado
		case 307:  //Crucero
		case 300:   //Nave pequeña de carga
		case 312:  //Estrella de la muerte
		$velocidad=$velocidadBase*(1+$motorImpulso*0.1);
		break;

		//Propulsor
		case 308: //Nave de batalla
		case 310:  //Destructor
		case 311:  //Interceptor
		case 309://Bombardero
		$velocidad=$velocidadBase*(1+$propulsor*0.15);
		break;
	}

	return (int)round($velocidad*$GLOBALS['configuracion']['VelocidadVuelo']);
}

/**
 * Obtiene los requisitos necesarios para realizar una tecnologia, en formato de array asociantivo $IdTecnologiaNecesaria=>$NivelNecesario
 * @return array
 */
function RequisitosTecnologia($ID)
{
	switch ($ID)
	{
		case 11:
			return array(3=>5,105=>5,106=>3);//Planta de antimateria
		case 12:
			return array(10=>8,105=>3,23=>1);//Planta de microondas
		case 22:
			return array(21=>10,101=>10);//Nanos
		case 23:
			return array(21=>2);//Hangar
		case 81:
			return array(80=>1);//Sensor galactico
		case 82:
			return array(80=>1,107=>10);//Salto cuantico
		case 100:
			return array(20=>3);//Espionaje
		case 101:
			return array(20=>1);//Computacion
		case 102:
			return array(20=>4);//Militar
		case 103:
			return array(20=>6,105=>3);//Defensa
		case 104:
			return array(20=>2);//Blindaje
		case 105:
			return array(20=>1);//Energia
		case 106:
			return array(20=>8,105=>7);//Antimateria
		case 107:
			return array(20=>7,103=>5,105=>5);//Hiperespacio
		case 108:
			return array(20=>1,105=>1);//Combustion
		case 109:
			return array(20=>2,105=>1);//Impulso
		case 110:
			return array(20=>7,107=>5);//Prop. hiperespacial
		case 111:
			return array(20=>2,105=>2);//Laser
		case 112:
			return array(20=>4,111=>5,105=>4);//Ionica
		case 113:
			return array(20=>4,111=>8,105=>10,112=>5);//Plasma
		case 114:
			return array(20=>12);//Graviton
		case 115:
			return array(20=>10,101=>8,107=>8);//RedIntergalactica

			//Naves
		case 299:
			return array(12=>1);//Satélite Solar
		case 300:
			return array(23=>2,108=>2);//Nave pequeña de carga
		case 301:
			return array(23=>5,109=>4);//Nave grande de carga
		case 302:
			return array(23=>5,109=>4,104=>3);//Reciclador
		case 303:
			return array(23=>3,100=>2,108=>3);//Sondas
		case 304:
			return array(23=>5,109=>4);//Colonizador
		case 305:
			return array(23=>1,108=>1);//Cazador ligero
		case 306:
			return array(23=>3,104=>2,109=>2);//Cazador pesado
		case 307:
			return array(23=>5,109=>4,111=>5,112=>3);//Crucero
		case 308:
			return array(23=>7,111=>6,112=>4,110=>3);//Nave de batalla
		case 309:
			return array(23=>8,109=>6,113=>3,106=>5);//Bombardero
		case 310:
			return array(23=>9,113=>7,110=>6);//Destructor
		case 311:
			return array(23=>10,106=>7,110=>7);//Interceptor

		case 312:
			return array(23=>12,110=>8,114=>1);//Estrella de la muerte

			//Defensas
		case 400:
			return array(23=>1);//Lanzamisiles
		case 401:
			return array(23=>2,111=>3);//Laser pequeño
		case 402:
			return array(23=>4,111=>6,104=>3);//Laser grande
		case 403:
			return array(23=>5,105=>3,112=>5);//Cañon ionico
		case 404:
			return array(23=>6,103=>2);//Cañon Gauss
		case 405:
			return array(23=>8,113=>7);//Cañon de plasma
		case 406:
			return array(23=>11,106=>6,103=>6);//Cañon de antimateria
		case 502:
			return array(23=>5,103=>3,105=>5);//Cupula de proteccion
		case 500:
			return array(25=>2);//Misil de intercepcion
		case 501:
			return array(25=>4);//Misil interplanetario
	}
}

/**
 * Comprueba si se cumplen todos los requisitos para iniciar la construcción de una tecnología
 * @return bool
 */
function TecnologiaDisponible($ID)
{
	$requisitos=RequisitosTecnologia($ID);

	if(empty($requisitos))
	return true;

	if(TipoTecnologia($ID)==4)//Investigación
	{
		global $jugador;
		foreach ($requisitos as $tecno=>$nivel)
		{
			if($jugador->Tecnologias[$tecno]<$nivel)
			return false;
		}
	}
	else
	{
		global $planeta;
		foreach ($requisitos as $tecno=>$nivel)
		{
			if($planeta->Tecnologias[$tecno]<$nivel)
			return false;
		}
	}

	return true;
}

/**
 * Coste de una tecnologia al nivel especificado
 * @return array
 */
function CosteTecnologia($ID,$nivel=0)
{
	$CosteBase=CosteInicialTecnologia($ID);

	if (TipoTecnologia($ID)==2 || $nivel<=1)//Nave o nivel 1
	$costes=$CosteBase;
	else if ($ID== 114)//Graviton, no devuelve metal, devuelve energia)
	$costes= array($CosteBase[3]*3*$nivel);
	else
	{
		$costes=array();
		for($i=0;$i<3;$i++)
		{
			switch ($ID)
			{
				case 1:
					$costes[$i]=$CosteBase[$i]*pow(1.5,($nivel-1));break;  //Mina de metal
				case 2:
					$costes[$i]=$CosteBase[$i]*pow(1.6,($nivel-1));break;    //Mina de cristal
				case 3:
					$costes[$i]=$CosteBase[$i]*pow(1.5,($nivel-1));break;   //Acelerador de partículas
				case 10:
					$costes[$i]=$CosteBase[$i]*pow(1.5,($nivel-1));break;    //Planta solar
				case 11:
					$costes[$i]=$CosteBase[$i]*pow(1.8,($nivel-1));break;     //Planta de antimateria
				default:
					$costes[$i]=$CosteBase[$i]*pow(2,$nivel-1);break;//Resto de edificios
			}
		}
	}

	/*for($i=0;$i<3;$i++)
	{
	$costes[$i]=(int)round($costes[$i]/$GLOBALS['configuracion']['CosteTecnologia']);
	}*/
	$costes[0]=(int)round($costes[0]/$GLOBALS['configuracion']['CosteTecnologia']);
	$costes[1]=(int)round($costes[1]/$GLOBALS['configuracion']['CosteTecnologia']);
	$costes[2]=(int)round($costes[2]/$GLOBALS['configuracion']['CosteTecnologia']);

	return $costes;
}

/**
* 	Devuelve
*
*	1 - edificio
*	2 - tecnologia por cantidad (naves, defensas)
*	3 - tecnologia de un solo nivel (planta de microndas)
*   4 - investigación
* @return integer
*/
function TipoTecnologia($ID)
{
	if ($ID>=250 && $ID<250)
	return 4;
	else if ($ID>=250 && $ID!=502)
	return 2;
	else if($ID==12 || $ID==115)
	return 3;
	else
	return 1;
}

/**
 * Obtiene el tiempo de construccion o investigación en segundos de una tecnologia
 *
 */
function TiempoProduccionTecnologia($ID,$Nivel,$coste=0,$array=null)
{
	if($coste==0)
	$coste=CosteTecnologia($ID,$Nivel);

	if($array==null)
	$array=$GLOBALS['planeta']->Tecnologias;

	if ($ID<100)//Edificios
	{
		$nivelNanos=$array[22]==0?1:(2*$array[22]);
		$tiempo=(($coste[1]+$coste[0])/2500)*(1/($array[21]+1))/$nivelNanos;
	}
	else if ($ID>=100 && $ID<250)//Investigaciones
	{
		$nivelLaboratorio=$GLOBALS['jugador']->Tecnologias[115]>0?$GLOBALS['jugador']->Tecnologias[20]:$array[20];
		$tiempo=($coste[1]+$coste[0])/(1000*($nivelLaboratorio+1));
	}
	else//Naves o defensas
	{
		$tiempo=((($coste[1]+$coste[0])/5000)*(2/($array[23]+1)))/($array[22]==0?1:(2*$array[22]));
	}


	//echo '<br>Calcular tiempo para '.GetTechnology($ID)->Name.' al nivel '.$Nivel.': '.round($tiempo*3600).'s <br>';

	return (int)round(($tiempo*3600)/$GLOBALS['configuracion']['TiempoProduccion']);
	//return 10;
}
/**
* Convierte un numero de segundos a una fecha en formato x años, xd xh xm xs
* @return string
*/
function SegundosAFecha($tiempo)
{
	$segundos=floor($tiempo%60);
	$minutos=floor(($tiempo/60)%60);
	$horas=floor(($tiempo/3600)%24);

	return (floor($tiempo/31536000)>0?floor($tiempo/31536000).' '.GetString('años'):'').
	(floor(($tiempo/86400)%365)>0?' '.floor(($tiempo/86400)%365).GetString('d'):'').
	($horas>0?' '.$horas.GetString('h'):'').
	($minutos>0?' '.$minutos.GetString('m'):'').
	($segundos>0?' '.$segundos.GetString('s'):'');
}

/**
 * Tiempo aproximado para poder producir los recursos necesesarios para una construccion
 *
 */
function TiempoNecesarioInicioConstruccion($planeta,$idTecno,$nivel,$costes=0)
{
	$tiempoMaximo=0;
	if($costes==0)
	$costes=CosteTecnologia($idTecno,$nivel);
	if($planeta['Metal']<$costes[0])
	{
		if($planeta['ProduccionMetal']<=0)
		return -1;
		
		$recursosNecesarios=$costes[0]-$planeta['Metal'];
		$tiempo=round(($recursosNecesarios/$planeta['ProduccionMetal'])*3600);
		$tiempoMaximo=max($tiempoMaximo,$tiempo);
	}
	if($planeta['Cristal']<$costes[1])
	{
		if($planeta['ProduccionCristal']<=0)
		return -1;
		
		$recursosNecesarios=$costes[1]-$planeta['Cristal'];
		$tiempo=round(($recursosNecesarios/$planeta['ProduccionCristal'])*3600);
		$tiempoMaximo=max($tiempoMaximo,$tiempo);
	}
	if($planeta['Antimateria']<$costes[2])
	{
		if($planeta['ProduccionAntimateria']<=0)
		return -1;
		
		$recursosNecesarios=$costes[2]-$planeta['Antimateria'];
		if($costes[2]>0 && $planeta['ProduccionAntimateria']<=0)
		return -1;
		$tiempo=round(($recursosNecesarios/$planeta['ProduccionAntimateria'])*3600);
		$tiempoMaximo=max($tiempoMaximo,$tiempo);
	}
	return $tiempoMaximo;
}

function ProduccionMina($ID,$nivel,$planeta)
{
	global $jugador;

	$produccion=0;
	switch ($ID)
	{
		case 1:
			$produccion=35*$nivel*pow(1.09,$nivel)+($nivel*(500000/($planeta->Datos['CamposTotales']*80)));//Mina de metal
			break;
		case 2:
			$produccion=25*$nivel*pow(1.095,$nivel);//Mina de cristal
			break;
		case 3:
			$produccion=10*$nivel*pow(1.05,$nivel)*(-0.002*(1/($planeta->Datos['CamposTotales']*80))+1.28);//Acelerador de partículas
			break;
		case 10:
			$produccion=20*$nivel*pow(1.11,$nivel)*(1+$jugador->Tecnologias[105]/100);//Planta de fusión
			break;
		case 11:
			$produccion=60*$nivel*pow(1.11,$nivel)*(1+$jugador->Tecnologias[105]/100);//Planta de antimateria
			break;
		case 12:
			$produccion=$planeta->Tecnologias[12]*ProduccionSatelite($planeta);//Planta de microondas
	}

	return (int)round($produccion*$GLOBALS['configuracion']['NivelProduccion']);
}

function GastoEnergiaMina($ID,$nivel)
{
	switch ($ID)
	{
		case 1:
			return round(10*$nivel*pow(1.08,$nivel));//Mina de metal
		case 2:
			return round(10*$nivel*pow(1.12,$nivel));//Mina de metal
		case 3:
			return round(30*$nivel*pow(1.1,$nivel));//Acelerador de partículas
		case 11:
			return round(12*$nivel*pow(1.15,$nivel));//Planta de antimateria, gasto de antimateria
	}
}

function CapacidadAlmacen($ID,$nivel)
{
	switch ($ID)
	{
		case 90://Almacén de metal
		case 91://Almacén de cristal
		if($nivel==0)
		return 100000;
		return 100000+50000*floor(pow(1.6,$nivel));

		case 92://Trampa de antimateria
		if($nivel==0)
		return 15000;
		return 15000+10000*floor(pow(1.6,$nivel));
	}
}

function ProduccionSatelite($planeta)
{
	global $jugador;

	return ($planeta->Datos['Temperatura']/4)+25*(1+$jugador->Tecnologias[105]/100);
}

function AlcanceSensorEspacial($nivel)
{
	global $jugador;

	return ($nivel*$nivel)+$jugador->Tecnologias[100];
}

/**
 * Maxima cantidad de una tecnologia que se puede realizar en un planeta (naves o defensas)
 *
 */
function MaximaCantidadRealizable($coste,$planeta)
{
	if($coste[0]!=0)
	$precio[]=$planeta->Metal/$coste[0];

	if($coste[1]!=0)
	$precio[]=$planeta->Cristal/$coste[1];

	if($coste[2]!=0)
	$precio[]=$planeta->Antimateria/$coste[2];

	if(!empty($precio))
	return floor(min($precio));
	else return 0;
}

/**
 * Obtiene las tecnologías para un tipo de cola o evento
 * 
 *1 - construccion de edificio
 *2 - investigacion
 *3 - hangar
 *4 - defensas
 *
 * @param int $tipo
 * @param Planeta $planeta
 * @return array
 */
function ObtenerTecnologias($tipo,$planeta=null)
{
	switch ($tipo)
	{
		case 1:
			if(!isset($planeta))
			global $planeta;

			if($planeta->Datos['Luna']==1)
			return array(80,81,82);
			else
			return array(1,2,3,10,11,12,20,21,22,23,24,25,90,91,92);
			
		case 2:
			return array(100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115);
			
		case 3:
			return array(299,300,301,302,303,304,305,306,307,308,309,310,311,312);
			
		case 4:
			return array(400,401,402,403,404,405,406,502,500,501);
	}
}
?>
