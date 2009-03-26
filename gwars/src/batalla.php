<?php

class ResultadoBatalla
{
	/**
	 * Ganador de la batalla: 0 - Empate 1 - Gana el atacante 2 - Gana el defensor
     * @var Ejercito
     */
	public $Ganador;
	/**
     * @var Ejercito
     */
	public $UnidadesAtacante;
	/**
     * @var Ejercito
     */
	public $UnidadesDefensor;
	/**
	 * Informe de la batalla
     * @var string
     */
	public $Informe;
	/**
	 * Recursos robados totales de todos los atacantes
     * @var array
     */
	public $RecursosRobados;
	/**
	 * Recursos que no cabian
     * @var int
     */
	public $RecursosRestantes;
	/**
	 * Escombros generados. Array
     * @var array
     */
	public $Escombros;
	public $ProbabilidadLuna;
	public $Rondas;
}

class ResultadoRonda
{
	public $Disparos;
	public $FuerzaTotal;
	public $DanosCasco;
	public $AbsorvidoEscudos;
	public $AbsorvidoCupula;
	public $Esquivados;
	public $UnidadesDestruidas;
	public $FuegoRapido;
}

class Ejercito
{
	/**
	 * Array con las flotas de los distintos jugadores del ejercito
     * @var array
     */
	public $Flotas;
	public $NavesTotales;
	/**
	 * Poder de ataque total que forman todas las naves de este ejercito
     * @var int
     */
	public $PoderAtaqueTotal;
	/**
	 * Pérdidas de recursos que ha sufrido este ejercito en la batalla
     * @var array
     */
	public $Perdidas;
	/**
	 * Pérdidas de recursos en naves que ha sufrido esta flota en la batalla
     * @var array
     */
	public $PerdidasNaves;

	//Array formando por elementos cuya clave es el ID del jugador y el valor la flota
	function Ejercito($array)
	{
		$this->Flotas=array();
		$this->NavesTotales=0;
		foreach ($array as $idJugador=>$flota)
		{
			$claseFlota=new FlotaJugador($idJugador);
			$contadorNaves=0;
			foreach ($flota as $idUnidad=>$cantidad)
			{
				$caracteristicas=CaracteristicasActualesArmamento($idUnidad,$flota);
				$claseNave=new Unidad();
				$claseNave->Cantidad=$cantidad;
				$claseNave->CantidadInicial=$cantidad;
				$claseNave->CantidadInicioBatalla=$cantidad;
				$claseNave->Escudo=$caracteristicas->Escudo*$cantidad;
				$claseNave->Casco=$caracteristicas->Casco*$cantidad;
				$claseNave->Ataque=$caracteristicas->Ataque;
				$claseNave->ProbabilidadEsquivar=(int)round(-5.92*log($caracteristicas->Casco)+56.03);
				$claseNave->ID=$idUnidad;
				$claseNave->Info=$caracteristicas;
				$claseNave->Nombre=GetTechnology($idUnidad)->Name;

				$contadorNaves+=$cantidad;
				$claseFlota->Unidades[]=$claseNave;
			}
			$claseFlota->NumeroUnidades=$contadorNaves;
			$this->NavesTotales+=$contadorNaves;
			$this->Flotas[]=$claseFlota;
		}
	}
}

class FlotaJugador
{
	public $IdJugador;
	/**
	 * Array con las distintas unidades de la flota actual
     * @var array
     */
	public $Unidades;
	public $NumeroUnidades;
	/**
	 * Pérdidas de recursos que ha sufrido esta flota en la batalla
     * @var array
     */
	public $Perdidas;
	/**
	 * Pérdidas de recursos en naves que ha sufrido esta flota en la batalla
     * @var array
     */
	public $PerdidasNaves;
	/**
	 * Recursos robados si el atacante gana
     * @var array
     */
	public $RecursosRobados;

	function FlotaJugador($jugador)
	{
		$this->IdJugador=$jugador;
	}
}

class Unidad
{
	/**
	 * Cantidad al inicio del turno
     */
	public $CantidadInicial;
	/**
	 * Cantidad al final del turno
     */
	public $Cantidad;
	/**
	 * Numero de naves destruidas a lo largo de la batalla
     */
	public $Destruidas;
	/**
	 * Numero de naves disponibles al principio de la batalla
     */
	public $CantidadInicioBatalla;
	public $Nombre;
	public $ID;
	/**
	 * Informacion sobre las caracteristicas de la nave
     * @var CaracteristicaNave 
     */
	public $Info;
	
	public $Casco;
	public $Escudo;
	public $Ataque;
	public $ProbabilidadEsquivar;
}

/**
 * Simula una batalla
 * @param array $atacantes Array que contiene información sobre los atacantes en formato $IdUnidad=>$Cantidad
 * @param array $defensores Array que contiene información sobre los atacantes en formato $IdUnidad=>$Cantidad
 * @param array $recursosPlaneta Array que contiene información sobre los recursos del planeta atacado
 * @return ResultadoBatalla
 */
function Batalla($atacantes, $defensores,$recursosPlaneta)
{
	$tiempoInicial=microtime(true);
	$informe='<h2>Se produjo una batalla entre las siguientes flotas:</h2><br/>';

	//Inicializar los ejercitos
	$ejercitoAtacante=new Ejercito($atacantes);
	$ejercitoDefensor=new Ejercito($defensores);

	//Inicializar la clase de resultados
	$resultado=new ResultadoBatalla();

	//Mostrar el estado inicial de las naves
	$informe.=ResumenNaves($ejercitoAtacante,$ejercitoDefensor);

	if($ejercitoAtacante->NavesTotales<=0 && $ejercitoDefensor->NavesTotales<=0)
	$resultado->Ganador=0;
	else if($ejercitoAtacante->NavesTotales<=0 && $ejercitoDefensor->NavesTotales>0)
	$resultado->Ganador=2;
	else if($ejercitoAtacante->NavesTotales>0 && $ejercitoDefensor->NavesTotales<=0)
	$resultado->Ganador=1;
	else
	{
		for($ronda=1;;$ronda++)
		{
			//Atacar
			$infoAtaque=Ronda($ejercitoAtacante,$ejercitoDefensor);
			$infoDefensa=Ronda($ejercitoDefensor,$ejercitoAtacante);

			//Ajustar y limpiar el ataque y escudo de los ejercitos, y eliminar las unidades destruidas
			FinalRonda($ejercitoAtacante);
			FinalRonda($ejercitoDefensor);

			$informe.='<h2>'.sprintf(GetString('Ronda %d'),$ronda).'</h2><br/>'
			.ResumenRonda($infoAtaque,GetString('atacantes'),GetString('defensores')).'<br/><br/>'
			.ResumenRonda($infoDefensa,GetString('defensores'),GetString('atacantes')).'<br/>';

			//Mostrar la tabla con las naves restantes
			$informe.=ResumenNaves($ejercitoAtacante,$ejercitoDefensor);

			//Comprobar las condiciones de victoria
			if($ejercitoDefensor->NavesTotales<=0 && $ejercitoAtacante->NavesTotales<=0)//Empate
			{
				$resultado->Ganador=0;
				break;
			}
			if($ejercitoDefensor->NavesTotales<=0)//Gana el atacante
			{
				$resultado->Ganador=1;
				break;
			}
			if($ejercitoAtacante->NavesTotales<=0)//Gana el defensor
			{
				$resultado->Ganador=2;
				break;
			}
			if($ronda>=5)//Comprobar si hay empate segun la diferencia de poder de ataque
			{
				$porcentage=100/max($ejercitoAtacante->PoderAtaqueTotal,$ejercitoDefensor->PoderAtaqueTotal)*min($ejercitoAtacante->PoderAtaqueTotal,$ejercitoDefensor->PoderAtaqueTotal);
				if($porcentage>75)//Empate
				{
					$resultado->Ganador=0;
					break;
				}
			}
			if($ronda>99)
			{
				$resultado->Ganador=0;
				break;
			}
		}
	}
	if($resultado->Ganador==0)
	$informe.='<br/><h2>'.GetString('La batalla terminó en empate').'</h2>';
	else if($resultado->Ganador==1)
	$informe.='<br/><h2>'.GetString('El atacante ganó la batalla').'</h2>';
	else if($resultado->Ganador==2)
	$informe.='<br/><h2>'.GetString('El defensor ganó la batalla').'</h2>';

	if(isset($ronda))
	$resultado->Rondas=$ronda;
	else
	$resultado->Rondas=0;

	$informe='Generado en '.(microtime(true)-$tiempoInicial).' s<br/>'.$informe;

	//Calcular las perdidas totales
	CalcularPerdidas($ejercitoAtacante);
	CalcularPerdidas($ejercitoDefensor);

	$resultado->Informe=$informe;
	$resultado->UnidadesAtacante=$ejercitoAtacante;
	$resultado->UnidadesDefensor=$ejercitoDefensor;

	//Calcular los escombros generados
	global $configuracion;
	$cantidadAescombros=(mt_rand($configuracion['PorcentageEscombros']-5,$configuracion['PorcentageEscombros']+5)/100);
	$cantidadDefensasEscombros=$configuracion['PorcentageDefensaEscombros']/100;
	//Metal
	$resultado->Escombros[0]=$cantidadAescombros*(($resultado->UnidadesAtacante->PerdidasNaves[0]+$resultado->UnidadesDefensor->PerdidasNaves[0]))+//Escombros naves
	($cantidadDefensasEscombros*($resultado->UnidadesAtacante->Perdidas[0]-$resultado->UnidadesAtacante->PerdidasNaves[0]+$resultado->UnidadesDefensor->Perdidas[0]-$resultado->UnidadesDefensor->PerdidasNaves[0]));
	//Cristal
	$resultado->Escombros[1]=$cantidadAescombros*(($resultado->UnidadesAtacante->PerdidasNaves[1]+$resultado->UnidadesDefensor->PerdidasNaves[1]))+//Escombros naves
	($cantidadDefensasEscombros*($resultado->UnidadesAtacante->Perdidas[1]-$resultado->UnidadesAtacante->PerdidasNaves[1]+$resultado->UnidadesDefensor->Perdidas[1]-$resultado->UnidadesDefensor->PerdidasNaves[1]));

	//Calcular recursos robados por el atacante
	$resultado->RecursosRobados=array();
	if($resultado->Ganador==1)
	{
		$resultado->RecursosRestantes=CalcularRecursosRobados($ejercitoAtacante,$recursosPlaneta);
		//Sumar los recursos robados a la clase resultado
		foreach ($ejercitoAtacante->Flotas as $flota)
		{
			for($contador=0;$contador<count($flota->RecursosRobados);$contador++)
			$resultado->RecursosRobados[$contador]+=$flota->RecursosRobados[$contador];
		}
	}

	//Calcular porcentage de luna
	$resultado->ProbabilidadLuna=min(35,round(array_sum($resultado->Escombros)/100000));

	return $resultado;
}

/**
 * Realiza los ataques de un ejercito sobre otro en su turno
 * @return ResultadoRonda
 */
function Ronda(&$ejercitoAtacante,&$ejercitoReceptor)
{
	$infoAtaque=new ResultadoRonda();
	foreach ($ejercitoAtacante->Flotas as $flotaAtacante)
	{
		foreach ($flotaAtacante->Unidades as $unidad)
		{
			if($unidad->Info->Ataque==0)
			continue;

			for($contador=0;$contador<$unidad->CantidadInicial;$contador++)
			{
				//Seleccionar la nave
				if($ejercitoReceptor->NavesTotales==0)
				break;

				//Seleccionar una nave al azar
				$nave=mt_rand(0,10000);
				$porcentaje=0;
				foreach($ejercitoReceptor->Flotas as $flota)
				{
					if($flota->NumeroUnidades==0)
					continue;

					$porcentajeActual=(int)((10000/$ejercitoReceptor->NavesTotales)*$flota->NumeroUnidades);
					if($porcentaje<=$nave && $porcentaje+$porcentajeActual>=$nave)
					{
						$nave=mt_rand(0,10000);
						$porcentaje=0;
						foreach($flota->Unidades as $unidadAtacada)
						{
							$porcentajeActual=(int)((10000/$flota->NumeroUnidades)*$unidadAtacada->Cantidad);
							if($porcentaje<=$nave && $porcentaje+$porcentajeActual>=$nave)
							{
								//Si esta destruida, escoger otra
								if($unidadAtacada->Casco<=0)
								{
									$contador--;//Volver a elegir nave
									continue;
								}
								else
								break;
							}
							else
							$porcentaje+=$porcentajeActual;
						}
						if(isset($unidadAtacada))
						break;
					}
					else
					$porcentaje+=$porcentajeActual;
				}
				if(isset($unidadAtacada)==false)
				{
					$contador--;//Volver a elegir nave
					continue;
				}
				$infoAtaque->Disparos++;

				//Si es nave, comprobar si el disparo se esquiva o no
				if($unidadAtacada->ID>=250 && $unidadAtacada->ID<400 &&
				$unidadAtacada->ProbabilidadEsquivar>0 && mt_rand(0,100)<$unidadAtacada->ProbabilidadEsquivar)
				{
					$infoAtaque->Esquivados++;
				}
				else //Dañar la nave
				{
					$poderAtaque=$unidad->Ataque;
					$infoAtaque->FuerzaTotal+=$poderAtaque;
					if($unidadAtacada->ID>=400 && $unidadAtacada->ID<500)//Es una defensa, el escudo del planeta absorve parte del ataque
					{
						$poderAtaque-=0.01*$poderAtaque;//Nivel de la cupula   ##########################################
						$infoAtaque->AbsorvidoCupula+=0.01*$poderAtaque;
					}
					if($poderAtaque>$unidadAtacada->Info->Escudo || $unidadAtacada->Escudo<=0)//Poder de ataque mayor que capacidad del escudo, o escudo destruido
					{
						if($unidadAtacada->Escudo>0)//Escudo aún no destruido, puede absorver el ataque
						{
							if($unidadAtacada->Escudo<$unidadAtacada->Info->Escudo)//Escudo mas debil que el de una sola unidad
							$capacidadEscudo=$unidadAtacada->Escudo;
							else
							$capacidadEscudo=$unidadAtacada->Info->Escudo;

							$poderAtaque-=$capacidadEscudo;//Calcular el ataque restante que afecta al casco
							$unidadAtacada->Escudo-=$capacidadEscudo;//Quitar al escudo de la nave
							$infoAtaque->AbsorvidoEscudos+=$capacidadEscudo;//Añadir el ataque absorvido por el escudo de la nave a las estadisticas

						}

						if($poderAtaque>$unidadAtacada->Info->Casco)//Comprobar si el ataque no es mas fuerte que el casco de la nave
						$poderAtaque=$unidadAtacada->Info->Casco;

						if($poderAtaque>($unidadAtacada->Casco-1)%$unidadAtacada->Info->Casco)//Se destruye la nave
						{
							$ejercitoReceptor->NavesTotales--;
							$flota->NumeroUnidades--;
							$unidadAtacada->Cantidad--;
							$unidadAtacada->Destruidas++;
							$infoAtaque->UnidadesDestruidas++;
						}
						$unidadAtacada->Casco-=$poderAtaque;//Quitar el daño restante al casco de la nave
						$infoAtaque->DanosCasco+=$poderAtaque;//Añadir el daño a las estadísticas

						if($unidadAtacada->Casco<0)//Comprobar que no hay valores negativos
						$unidadAtacada->Casco=0;
					}
					else if($poderAtaque>$unidadAtacada->Info->Escudo*0.01)//El escudo absorve el daño  sólo si es mayor de su 1%
					{
						$unidadAtacada->Escudo-=$poderAtaque;
						$infoAtaque->AbsorvidoEscudos+=$poderAtaque;
					}
				}
				//Comprobar el fuego rapido de la nave atacante sobre la atacada
				if(isset($unidad->Info->FuegoRapido[$unidadAtacada->ID]))
				{
					$probabilidad=100-(int)(100/$unidad->Info->FuegoRapido[$unidadAtacada->ID]);
					if(mt_rand(0,100)<$probabilidad)
					{
						$infoAtaque->FuegoRapido++;
						$contador--;//Volver a atacar
						continue;
					}
				}
			}
		}
	}
	return $infoAtaque;
}

/**
 * Procesa los datos de un ejercito al final del turno
  * @param Ejercito $ejercito ejercito que se va a procesar
 */
function FinalRonda(&$ejercito)
{
	$ejercito->PoderAtaqueTotal=0;
	foreach ($ejercito->Flotas as $flota)
	{
		foreach ($flota->Unidades as $tipoUnidad)
		{
			$tipoUnidad->CantidadInicial=$tipoUnidad->Cantidad;

			if($tipoUnidad->Cantidad<=0)//No hay naves
			continue;

			//Porcentage de daño que han recibido las unidades
			$porcentageDaño=$tipoUnidad->Casco/($tipoUnidad->Info->Casco*$tipoUnidad->Cantidad);
			$porcentageDaño=min($porcentageDaño,1);
			

			//Ajustar el ataque y el escudo según el estado del casco
			if($porcentageDaño<1)
			{
				$tipoUnidad->Ataque=(int)($tipoUnidad->Info->Ataque*$porcentageDaño*mt_rand(8,12)/10);

				if($tipoUnidad->Ataque<=0)//El ataque no puede ser 0
				$tipoUnidad->Ataque=$tipoUnidad->Info->Ataque*(0.2*$porcentageDaño);

				if($tipoUnidad->Ataque>$tipoUnidad->Info->Ataque)//Comprobar que el ataque no es mayor que el inicial
				$tipoUnidad->Ataque=$tipoUnidad->Info->Ataque;
			}
			else
			$tipoUnidad->Ataque=$tipoUnidad->Info->Ataque;

			//Escudo
			if($tipoUnidad->Escudo<=0)//Escudo destruido, partir de un escudo inicial
			{
				$tipoUnidad->Escudo=($tipoUnidad->Info->Escudo*$tipoUnidad->Cantidad)*(0.2*$porcentageDaño);
			}
			$escudoTotal=$tipoUnidad->Info->Escudo*$tipoUnidad->Cantidad;//Escudo maximo para la flota
						
			$tipoUnidad->Escudo=min($escudoTotal,($tipoUnidad->Escudo*(1+((mt_rand(0,5)*$porcentageDaño)/10))));//Regenerar parte del escudo, pero no mas del maximo

			$ejercito->PoderAtaqueTotal+=$tipoUnidad->Ataque*$tipoUnidad->Cantidad;
		}
	}
	if($ejercito->NavesTotales<0)
	$ejercito->NavesTotales=0;
}

/**
 * Muestra un resumen de las naves disponibles
 */
function ResumenNaves(&$ejercitoAtacante,&$ejercitoDefensor)
{
	return '<table class="battleReport"><tr><th colspan="99">'.GetString('Atacantes').'</th></tr>
	<tr>'.TablaNaves($ejercitoAtacante).'</tr>
	<tr><th colspan="99">'.GetString('Defensores').'</th></tr>
	<tr>'.TablaNaves($ejercitoDefensor).'</tr></table>';
}

function TablaNaves(&$ejercito)
{
	$informe='';

	if($ejercito->NavesTotales==0)
	return '<td colspan="0">Destruido</td>';

	foreach ($ejercito->Flotas as $flota)
	{
		if($flota->NumeroUnidades<=0)
		continue;
		$informe.='<td><table><tr><th colspan="0">'.$flota->IdJugador.'</th></tr>
		<tr><td>'.GetString('Tipo').'</td>';
		foreach ($flota->Unidades as $nave)
		{
			if($nave->Cantidad<=0)continue;
			$informe.='<td>'.$nave->Nombre.'</td>';
		}
		$informe.='</tr><tr><td>'.GetString('Cantidad').'</td>';
		foreach ($flota->Unidades as $nave)
		{
			if($nave->Cantidad<=0)continue;
			$informe.='<td>'.number_format($nave->Cantidad,0,null,'.').'</td>';
		}
		$informe.='</tr><tr><td>'.GetString('Ataque').'</td>';
		foreach ($flota->Unidades as $nave)
		{
			if($nave->Cantidad<=0)continue;
			$informe.='<td>'.number_format($nave->Ataque,0,null,'.').'</td>';
		}
		$informe.='</tr><tr><td>'.GetString('Escudos').'</td>';
		foreach ($flota->Unidades as $nave)
		{
			if($nave->Cantidad<=0)continue;
			$informe.='<td>'.number_format($nave->Escudo,0,null,'.').'</td>';
		}
		$informe.='</tr><tr><td>'.GetString('Casco').'</td>';
		foreach ($flota->Unidades as $nave)
		{
			if($nave->Cantidad<=0)continue;
			$informe.='<td>'.number_format($nave->Casco,0,null,'.').'</td>';
		}
		$informe.='</tr></table></td>';
	}

	return $informe;
}


/**
 * Calcula la perdida de recursos tras una batalla
 * @return string
 * @param Ejercito $ejercito
 */
function CalcularPerdidas(&$ejercito)
{
	$ejercito->Perdidas=array();
	foreach ($ejercito->Flotas as $flota)
	{
		$flota->Perdidas=array();
		if(!empty($flota->Unidades))
		{
			foreach ($flota->Unidades as $tipoUnidad)
			{
				$coste=CosteInicialTecnologia($tipoUnidad->ID);
				$contador=0;
				foreach($coste as $precio)
				{
					$flota->Perdidas[$contador]+=$precio*$tipoUnidad->Destruidas;
					if($tipoUnidad->ID>=250 && $tipoUnidad->ID<400)//Nave
					$flota->PerdidasNaves[$contador]+=$precio*$tipoUnidad->Destruidas;

					$contador++;
				}
			}

			for($contador=0;$contador<count($flota->Perdidas);$contador++)
			{
				$ejercito->Perdidas[$contador]+=$flota->Perdidas[$contador];
				$ejercito->PerdidasNaves[$contador]+=$flota->PerdidasNaves[$contador];
			}
		}
	}
}

/**
 * Calcula los recursos robados por el atacante
 * @param Ejercito $ejercitoAtacante
 * @param Ejercito $ejercitoDefensor
 */
function CalcularRecursosRobados(&$ejercitoAtacante,$recursosPlaneta)
{
	//Calcular primero los recursos a repartir
	$recursosRobados=array();
	$porcentageRobado=$GLOBALS['configuracion']['PorcentageRecursosRobados']/100;
	for($contador=0;$contador<count($recursosPlaneta);$contador++)
	$recursosRobados[$contador]=round($recursosPlaneta[$contador]*$porcentageRobado);

	$cantidadTotalRecursos=array_sum($recursosRobados);

	//Calcular la capacidad de carga de las naves
	foreach ($ejercitoAtacante->Flotas as $flota)
	{
		$flota->CapacidadCarga=0;
		foreach ($flota->Unidades as $tipoUnidad)
		{
			$flota->CapacidadCarga+=$tipoUnidad->Info->CapacidadCarga*$tipoUnidad->Cantidad;
			$ejercitoAtacante->NavesAportadas+=$tipoUnidad->CantidadInicioBatalla;
			$flota->NavesAportadas+=$tipoUnidad->CantidadInicioBatalla;
		}
	}

	//Repartir los recursos entre los atacantes
	$recursosRestantes=$cantidadTotalRecursos;
	$recursosRobadosDisponibles=$recursosRobados;
	$primeraIteracion=true;
	while($recursosRestantes>0)
	{
		$todosLlenos=true;//Indica si la capacidad de carga de todas las naves esta llena
		$recursosRobados=$recursosRobadosDisponibles;//Recursos que se van a repartir en esta iteracion
		foreach ($ejercitoAtacante->Flotas as $flota)
		{
			if($flota->CapacidadCarga<=0)
			continue;
			else
			$todosLlenos=false;

			if($primeraIteracion==true)//Asignar los recursos robados por esta flota según las perdidas sufridas
			{
				if(array_sum($ejercitoAtacante->Perdidas)<=0 || array_sum($flota->Perdidas)<=0)//Sin perdidas
				continue;
				$porcentageAsignado=array_sum($flota->Perdidas)/array_sum($ejercitoAtacante->Perdidas);
			}
			else //Asignar recursos por cantidad de flota
			$porcentageAsignado=(1/$ejercitoAtacante->NavesAportadas)*$flota->NavesAportadas;

			if($porcentageAsignado>1)
			$porcentageAsignado=1;

			if($porcentageAsignado==0)continue;

			$recursosAsignados=array();
			for($contador=0;$contador<count($recursosRobados);$contador++)
			$recursosAsignados[$contador]=round($recursosRobados[$contador]*$porcentageAsignado);

			$recursosCaben=array();

			if(array_sum($recursosAsignados)>$flota->CapacidadCarga)//No caben los recursos asignados
			{
				//Repartir los recursos que caben
				$porcentageCabe=(1/array_sum($recursosAsignados))*$flota->CapacidadCarga;
				for($contador=0;$contador<count($recursosAsignados);$contador++)
				$recursosCaben[$contador]=round($recursosAsignados[$contador]*$porcentageCabe);
			}
			else
			{
				$recursosCaben=$recursosAsignados;
			}

			for($contador=0;$contador<count($recursosCaben);$contador++)
			{
				$flota->RecursosRobados[$contador]+=$recursosCaben[$contador];
				$recursosRobadosDisponibles[$contador]-=$recursosCaben[$contador];
			}
			$flota->CapacidadCarga-=array_sum($recursosCaben);
			$recursosRestantes-=array_sum($recursosCaben);
		}
		if($todosLlenos==true)
		break;
		$primeraIteracion=false;
	}
	return $recursosRestantes;
}

/**
 * Muestra el resumen de disparos de las unidades tras una ronda
 * @param ResultadoRonda $infoRonda
 */
function ResumenRonda(&$infoRonda,$textoAtacantes,$textoDefensores)
{
	return sprintf(
	GetString('Las unidades %s han disparado %s veces%s, %s con un poder de %s sobre los %s, %s.<br/>Los escudos de las unidades han absorvido %s puntos, %s%s.'),
	$textoAtacantes,
	$infoRonda->Disparos,
	$infoRonda->FuegoRapido>0?' '.sprintf(GetString('(%s de fuego rápido)'),number_format($infoRonda->FuegoRapido,null,null,'.')):'',
	$infoRonda->Esquivados>0?sprintf(GetString('de las cuales %s han sido esquivados, y el resto han impactado'),number_format($infoRonda->Esquivados,null,null,'.')):GetString('impactando'),
	number_format($infoRonda->FuerzaTotal,null,null,'.'),
	$textoDefensores,
	$infoRonda->UnidadesDestruidas>0?sprintf(GetString('destruyendo %s unidades'),number_format($infoRonda->UnidadesDestruidas,null,null,'.')):GetString('sin lograr destruir ninguna unidad'),
	number_format($infoRonda->AbsorvidoEscudos,null,null,'.'),
	$infoRonda->AbsorvidoCupula>0?sprintf(GetString('la cúpula del planeta %s puntos, '),number_format($infoRonda->AbsorvidoCupula,null,null,'.')):'',
	$infoRonda->DanosCasco>0?sprintf(GetString('y los demás, %s puntos, han afectado al casco de las unidades'),number_format($infoRonda->DanosCasco,null,null,'.')):GetString('y el casco de las unidades no ha sufrido ningún daño'));
}
?>