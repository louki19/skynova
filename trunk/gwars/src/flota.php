<?php
/**
 * Expone metodos para representar y enviar flotas
 * 
 */
class Flota
{
	/**
	 * Velocidad máxima de vuelo de la flota
     * @var integer
     */
	var $Velocidad;
	/**
	 * Consumo de antimateria de la flota
     * @var integer
     */
	var $Consumo;
	/**
	 * Distancia recorrida
     * @var integer
     */
	var $Distancia;
	/**
	 * Capacidad de carga
     * @var integer
     */
	var $Capacidad;
	/**
	 * Array asociativo IdNave=>Cantidad de las naves de la flota
     * @var array
     */
	var $Naves;
	/**
	 * Id del jugador propietario de la flota
     * @var int
     */
	var $Propietario;
	/**
	 * Coordenadas de origen de la flota
     * @var array
     */
	var $Origen;
	/**
	 * Id del planeta de origen de la flota
     * @var int
     */
	var $IdOrigen;
	/**
	 * Coordenadas de destino de vuelo de la flota
     * @var array
     */
	var $Destino;
	/**
	 * ID del jugador propietario del planeta destino
     * @var array
     */
	var $JugadorDestino;
	/**
	 * Tipo de planeta de destino (0 - Planeta, 1 - Luna, 2 - Escombros)
	 */
	var $TipoDestino;
	/**
	 * Mision de la flota
	 *
	 */
	var $Mision;
	/**
	 * Recursos que transporta la flota
	 *
	 * @var array
	 */
	var $Recursos;
	/**
	 * Datos del planeta de destino
	 *
	 * @var array
	 */
	var $PlanetaDestino;
	/**
	 * Misiones disponibles para la flota actual
	 *
	 * @var array
	 */
	var $MisionesDisponibles;

	/**
	 * Indica si ha habido un error al cargar la flota
	 *
	 */
	var $Error;
	var $TextoError;
	function Error($texto)
	{
		$this->Error=true;
		$this->TextoError=$texto;
	}

	/**
	 * Constructor. Genera una clase flota a partir del array de datos especificado
	 *
	 * @param int $tipoDestino
	 * @param int $velocidad Porcentage de velocidad de la flota
	 * @param int $propietario Jugador propietario de la flota
	 * @param array $origen Coordenadas del planeta de origen
	 * @param Planeta $planetaOrigen Datos del planeta de origen
	 * @param array $destino Coordenadas del planeta destino
	 * @param array $recursosTransportados Recursos que lleva la flota
	 * @param bool $modoAnalisis Indica si la flota se usa para simular una batalla
	 * @return Flota
	 */
	function Flota(&$naves,$tipoDestino,$velocidad,$propietario,$origen,$destino,$planetaOrigen,$mision=null,$recursosTransportados=null,$modoAnalisis=false)
	{
		$caracteristicasNaves=array();
		foreach($naves as $idNave=>$cantidad)//Cargar las naves de la flota
		{
			if(empty($cantidad) || !is_numeric($cantidad))
			continue;

			$cantidad=(int)$cantidad;

			if($modoAnalisis==false && $planetaOrigen->Tecnologias[$idNave]<$cantidad)//Comprobar si hay naves suficientes
			$cantidad=$planetaOrigen->Tecnologias[$idNave];

			$caracteristicasNaves[$idNave]=CaracteristicasActualesArmamento($idNave);

			if($caracteristicasNaves[$idNave]->Velocidad==0)
			continue;

			$this->Capacidad+=$cantidad*$caracteristicasNaves[$idNave]->CapacidadCarga;

			if(empty($this->Velocidad) || $caracteristicasNaves[$idNave]->Velocidad<$this->Velocidad)
			$this->Velocidad=$caracteristicasNaves[$idNave]->Velocidad;

			$this->Naves[$idNave]=$cantidad;
		}

		if(!isset($this->Naves) || count($this->Naves)==0)
		{
			$this->Error(GetString('No se han especificado naves para la flota.'));
			return;
		}
		//Ajustar el tipo de destino
		$this->TipoDestino=$tipoDestino;
		$this->Propietario=$propietario;

		//Velocidad de la flota
		if(!is_numeric($velocidad))
		$velocidad=100;
		if($velocidad>100)$velocidad=100;
		if($velocidad<10)$velocidad=10;
		$velocidad=round($velocidad/10)*10;
		$this->Velocidad=(int)round($this->Velocidad*($velocidad/100));

		//Cargar las coordenadas de origen y destino, y calcular la distancia
		if($modoAnalisis==false && (!is_numeric($destino[0]) || !is_numeric($destino[1]) || !is_numeric($destino[2])))
		{
			$this->Error(GetString('Las coordenadas de destino no son válidas.'));
			return;
		}
		if($modoAnalisis==false && (!is_numeric($origen[0]) || !is_numeric($origen[1]) || !is_numeric($origen[2])))
		{
			$this->Error(GetString('Las coordenadas de origen no son válidas.'));
			return;
		}

		$this->Origen=array((int)$origen[0],(int)$origen[1],(int)$origen[2]);
		$this->Destino=array((int)$destino[0],(int)$destino[1],(int)$destino[2]);
		$this->IdOrigen=$planetaOrigen->ID;

		$this->Distancia=Flota::CalcularDistancia($this->Origen[0],$this->Origen[1],$this->Origen[2],
		$this->Destino[0],$this->Destino[1],$this->Destino[2]);

		//Cargar el planeta de destino
		if($modoAnalisis==false)
		{
			$this->PlanetaDestino=$GLOBALS['DB']->query('SELECT ID,Jugador,Nombre,EscombrosMetal,EscombrosCristal FROM `planetas`  WHERE`Galaxia`='.$this->Destino[0].' && '.'`Sistema`='.$this->Destino[1].' && '.'`Posicion`='.$this->Destino[2].($tipoDestino==1?' && Luna=1':'').' LIMIT 1');

			if($modoAnalisis==false && $this->PlanetaDestino->num_rows()==0)//El planeta no existe
			{
				if((isset($this->Naves[303]) && $this->Naves[303]>0) || (isset($this->Naves[304]) && $this->Naves[304]>0))
				$this->PlanetaDestino=false;
				else
				{
					$this->Error(GetString('El planeta de destino no existe.'));
					return;
				}
			}
			else
			{
				$this->PlanetaDestino=$this->PlanetaDestino->fetch_assoc();
				$this->JugadorDestino=$this->PlanetaDestino['Jugador'];
			}

			if($this->TipoDestino==2 && empty($this->PlanetaDestino['EscombrosMetal']) && empty($this->PlanetaDestino['EscombrosCristal']))
			{
				$this->Error(GetString('No hay escombros en las coordenadas especificadas.'));
				return;
			}
		}

		//Calcular consumo
		$this->Consumo=0;
		if(isset($this->Naves) && is_array($this->Naves))
		{
			foreach($this->Naves as $idNave=>$cantidad)
			{
				$consumoUnidad=1+round((($this->Distancia*$caracteristicasNaves[$idNave]->ConsumoCombustible*$this->Velocidad)/($caracteristicasNaves[$idNave]->Velocidad*137277000)));
				$this->Consumo+=$cantidad*$consumoUnidad;
			}
		}
		$this->Capacidad-=$this->Consumo;

		if($modoAnalisis==false && $this->Capacidad<0)//No cabe el combustible
		{
			$this->Error(GetString('No hay espacio suficiente para el combustible en las naves asignadas.'));
			return;
		}

		if($modoAnalisis==false && $planetaOrigen->Antimateria<$this->Consumo)
		{
			$this->Error(sprintf(GetString('No hay combustible suficiente en el planeta, se necesitan %s unidades de antimateria.'),number_format($this->Consumo,null,null,'.')));
			return;
		}

		//Cargar las misiones disponibles para esta flota y la mision especificada
		if($modoAnalisis==false)
		{
			$this->MisionesDisponibles=Flota::MisionesDisponibles($this->PlanetaDestino,$this->TipoDestino,$this->Naves);
			if(count($this->MisionesDisponibles)==0)
			{
				$this->Error(GetString('No hay misiones disponibles para el destino especificado.'));
				return;
			}
		}
		
		if(isset($mision))
		{
			if($modoAnalisis==false && !is_numeric($mision))
			{
				$this->Error(GetString('La misión especificada para la flota no es válida.'));
				return;
			}

			$this->Mision=(int)$mision;

			//Comprobar si la mision asignada es valida
			if($modoAnalisis==false && array_key_exists($this->Mision,$this->MisionesDisponibles)==false)//Mision inválida
			{
				$this->Error(GetString('La misión especificada para la flota no es válida.'));
				return;
			}
		}

		//Cargar los recursos
		if($modoAnalisis==false && isset($recursosTransportados))
		{
			$this->Recursos=array();
			$this->Recursos[0]=min((int)$recursosTransportados[0],$planetaOrigen->Metal);
			$this->Recursos[1]=min((int)$recursosTransportados[1],$planetaOrigen->Cristal);
			$this->Recursos[2]=min((int)$recursosTransportados[2],$planetaOrigen->Antimateria);

			//Ajustar los recursos asignados
			if(array_sum($this->Recursos)>$this->Capacidad)
			{
				$sobrante=array_sum($this->Recursos)-$this->Capacidad;

				for($recurso=0;$recurso<2;$recurso++)
				{
					if($this->Recursos[$recurso]>=$sobrante)
					{
						$this->Recursos[$recurso]-=$sobrante;
						$sobrante=0;
					}
					else
					{
						$sobrante-=$this->Recursos[$recurso];
						$this->Recursos[$recurso]=0;
					}
					if($sobrante<=0)
					break;
				}
			}
		}
	}

	/**
	 * Genera una flota válida para los eventos del juego
	 * @return FlotaEvento
	 */
	function GenerarFlotaEvento()
	{
		$resultado=new FlotaEvento();
		$resultado->IdDestino=(int)$this->PlanetaDestino['ID'];
		$resultado->IdOrigen=(int)$this->IdOrigen;
		$resultado->JugadorDestino=(int)$this->JugadorDestino;
		$resultado->Mision=(int)$this->Mision;
		$resultado->Naves=$this->Naves;
		$resultado->Recursos=$this->Recursos;
		return $resultado;
	}

	/*
	Funciones estáticas
	*/

	/**
	 * Estática. Genera una clase flota a partir del array de datos especificado. Usada para generar flotas de datos POST
	 *
	 * @param bool $modoAnalisis Indica si la flota se usa para simular una batalla
	 * @return Flota
	 */
	function FlotaArray($array,$modoAnalisis=false)
	{
		global $planeta;

		$naves=array();
		foreach($array as $key=>$value)//Cargar las naves de la flota
		{
			if(empty($value) || !is_numeric($value))
			continue;

			if(strstr($key,'nave')!=false)
			{
				$naves[(int)substr($key,4)]=$value;
			}
			if(strstr($key,'a')!=false && is_numeric(substr($key,1)))
			{
				$naves[(int)substr($key,1)]=$value;
			}
		}

		$tipoDestino=0;
		if($array['tipoDestino']=='escombros')
		$tipoDestino=2;
		if($array['tipoDestino']=='luna')
		$tipoDestino=1;

		return new Flota($naves,$tipoDestino,$array['velocidad'],$GLOBALS['jugador']->ID,
		array($array['galaxiaO'],$array['sistemaO'],$array['posicionO']),
		array($array['galaxiaD'],$array['sistemaD'],$array['posicionD']),
		$planeta,isset($array['mision'])?$array['mision']:null,
		isset($array['metal'])?array($array['metal'],$array['cristal'],$array['antimateria']):null
		,$modoAnalisis);
	}

	/**
	 * Estática. Obtiene la lista de misiones disponibles del planeta actual con el planeta especificado
	 * 
	 * Tipos de misiones
	 * 
	 * 1 - transportar -
	 * 2 - desplegar - 
	 * 3 - reciclar -
	 * 4 - comerciar
	 * 
	 * 10 - atacar
	 * 11 - espiar -
	 * 12 - asediar 
	 * 13 - orbitar
	 * 14 - destruir
	 * 15 - analizar planeta despoblado -
	 * 16 - colonizar
	 * 
	 * 20 - analizar con el sensor
	 * 21 - lanzar misiles
	 * 
	 * @param array $planetaDestino Datos del planeta de destino, false para un planeta inexistente
	 * @param array $navesEnviadas Array con las naves enviadas
	 * @param int $tipoDestino 	Tipo de planeta de destino (0 - Planeta, 1 - Luna, 2 - Escombros)
	 * @return array Array asociativo del tipo IdMision=>Texto
	 */
	static function MisionesDisponibles($planetaDestino=false,$tipoDestino,$navesEnviadas=null)
	{
		global $planeta;

		if(isset($navesEnviadas))
		$naves=$navesEnviadas;
		else
		$naves=$planeta->Tecnologias;

		if($planetaDestino==false && $tipoDestino==0 && count($naves)==1 && isset($naves[304]) && $naves[304]>0)
		return array(16=>GetString('Colonizar'));
		if($planetaDestino==false && $tipoDestino==0 && (isset($naves[303]) && $naves[303]>0))
		return array(15=>GetString('Analizar'));

		if($tipoDestino==2 && (isset($naves[302]) && $naves[302]>0))//Escombros
		{
			return array(3=>GetString('Reciclar'));
		}
		else if($planeta->ID==$planetaDestino['ID'])//Mismo planeta
		{
			return array();
		}

		$res=array();
		if($planetaDestino['Jugador']==$planeta->Datos['Jugador'])//Planeta del jugador
		{
			$res[1]=GetString('Transportar');
			$res[2]=GetString('Desplegar');
		}
		else //Planeta enemigo
		{
			if(isset($naves[303]) && $naves[303]>0 && count($naves)==1)
				return array(11=>GetString('Espiar'));				
			
			if($GLOBALS['jugador']->Datos['Alianza']!=0)
			$compañeroAlianza=$GLOBALS['jugador']->Datos['Alianza']==$GLOBALS['DB']->getRowProperty('jugadores',$planetaDestino['Jugador'],'Alianza');
			else
			$compañeroAlianza=false;

			$res[1]=GetString('Transportar');
			$res[4]=GetString('Comerciar');			

			$hayNaves=false;
			for($contador=300;$contador<320;$contador++)
			{
				if(isset($naves[$contador]) && $naves[$contador]>0)
				{
					$hayNaves=true;
					break;
				}
			}

			if($hayNaves)
			{
				$res[10]=GetString('Atacar');

				if($compañeroAlianza)
				$res[13]=GetString('Orbitar');
				else
				$res[12]=GetString('Asediar');
			}

			if(isset($naves[501]) && $naves[501]>0)
			$res[21]=GetString('Ataque con misiles');

			if(isset($naves[81]) && $naves[81]>0)
			$res[20]=GetString('Analizar con el sensor');

			if($tipoDestino==1 && (isset($naves[312]) && $naves[312]>0))
			$res[14]=GetString('Destruir');
		}
		return $res;
	}

	/**
	 * Estática. Obtiene el nombre de una mision
	 *
	 * @return string Texto
	 */
	function TextoMision($mision)
	{
		switch ($mision) {
			case 1:	return GetString('Transportar');
			case 2:	return GetString('Desplegar');
			case 3:	return GetString('Reciclar');
			case 4:	return GetString('Comerciar');
			case 10:return GetString('Atacar');
			case 11:return GetString('Espiar');
			case 12:return GetString('Asediar');
			case 13:return GetString('Orbitar');
			case 14:return GetString('Destruir');
			case 15:return GetString('Analizar');
			case 16:return GetString('Colonizar');
			case 20:return GetString('Analizar con el sensor');
			case 21:return GetString('Ataque con misiles');
		}
	}

	/**
	 * Estática. Calcula la distancia entre dos planetas
	 *
	 */
	function CalcularDistancia($galaxiaO,$sistemaO,$posicionO,$galaxiaD,$sistemaD,$posicionD)
	{
		if($galaxiaO!=$galaxiaD)//Distinta galaxia
		{
			$galaxiasDistancia=min(abs($galaxiaO+9-$galaxiaD),abs($galaxiaO-$galaxiaD));
			return 352609000+(116304500*($galaxiasDistancia-1));
		}
		else
		{
			if($sistemaO!=$sistemaD)//Distinto sistema
			{
				$sistemasDistancia=min(abs($sistemaO+499-$sistemaD),abs($sistemaO-$sistemaD));
				return 137277000+(1361100*($sistemasDistancia-1));
			}
			else
			{
				if($posicionO!=$posicionD)//Distinta posicion
				{
					return 78442800+(130740*(abs($posicionO-$posicionD)-1));
				}
				else
				{
					return 384400;
				}
			}
		}
	}
}

/**
 * Representa los datos de una flota obtenida de la base de datos
 *
 */
class FlotaEvento
{
	var $Naves;
	var $IdOrigen;
	/**
	 * ID del planeta destino
	 */
	var $IdDestino;
	var $Recursos;
	var $Mision;
	var $TiempoVuelo;
	var $JugadorDestino;

	function TextoMision()
	{
		return Flota::TextoMision($this->Mision);
	}

	function NombrePlanetaDestino()
	{
		if(!isset($this->NombreDestino))
		$this->NombreDestino=$GLOBALS['DB']->getRowProperty('planetas',$this->IdDestino,'Nombre');

		return $this->NombreDestino;
	}

	function NombrePlanetaOrigen()
	{
		if(!isset($this->NombreOrigen))
		$this->NombreOrigen=$GLOBALS['DB']->getRowProperty('planetas',$this->IdOrigen,'Nombre');

		return $this->NombreOrigen;
	}
	
	public function CapacidadCarga()
	{
		$capacidad=0;
		foreach ($this->Naves as $idNave=>$cantidad)
		{
		$capacidad+=CaracteristicasBaseArmamento($idNave)->CapacidadCarga*$cantidad;
		}
		return $capacidad-array_sum($this->Recursos);
	}

	/**
	 * Obtiene la lista de naves de la flota en formato cadena
	 *
	 */
	function ListaNaves()
	{
		$res='';
		foreach ($this->Naves as $idNave=>$cantidad)
		{
			$res.=$cantidad.' '.GetTechnology($idNave)->Name.', ';
		}
		if(!empty($res))
		return substr($res,0,strlen($res)-2);
		return '';
	}
	function CargarEvento($evento,$datosEvento)
	{
		$this->IdOrigen=$datosEvento[0];
		$this->IdDestino=$evento['Planeta'];
		$this->Naves=$datosEvento[1];
		$this->Recursos=$datosEvento[2];
		$this->Mision=$datosEvento[3];
		$this->TiempoVuelo=$datosEvento[4];
		$this->JugadorDestino=$datosEvento[5];
	}
}
?>