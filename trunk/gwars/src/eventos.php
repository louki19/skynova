 <?php

 /*
 Datos de los eventos

 Tipos de eventos:
 1-final de edificio							(IdTecno,Cantidad)
 2-final de investigacion						(IdTecno,Cantidad,TiempoProduccion)
 3-final de construccion en hangar  			(IdTecno,Cantidad)
 4-final de construccion en defensa  			(IdTecno,Cantidad)
 5-inicio de edifcio
 6-inicio de investigacion
 7-llegada de flota propia
 8-retorno de flota propia
 9-llegada de flota enemiga


 AL VOLVER FLOTA A PLANETA, COMPROBAR LA COLA DE CONSTRUCCION
 USAR EVENTOS SIN ID
 */

 class Eventos
 {
 	/**
 * Array con los datos de los eventos a procesar
 */
 	var $eventos;
 	/**
 * Fecha limite hasta la que se procesan los eventos. Normalmente, es el tiempo actual.
 */
 	var $fechaLimite;

 	/**
 * Procesa los eventos de un jugador hasta la fecha limite especificada
 */
 	function Procesar($idJugador,$fechaLimite)
 	{
 		global $DB;
 		$this->fechaLimite=$fechaLimite;

 		$consulta=$DB->query('SELECT * FROM `eventos` WHERE `Jugador`='.$idJugador.'  && `Fecha`<='.$fechaLimite);

 		if($consulta->num_rows()>0)
 		{
 			$this->eventos=array();
 			while($evento=$consulta->fetch_assoc())
 			{
 				$this->CrearEvento($evento['Fecha'],$evento);
 			}
 			$DB->query('DELETE FROM `eventos` WHERE `Jugador`='.$idJugador.' && `Fecha`<='.$fechaLimite);

 			$this->FiltrarEventos();
 		}
 	}

 	/**
 * Procesa todos los eventos ya sucedidos del jugador
 */
 	function FiltrarEventos()
 	{
 		ksort($this->eventos);

 		foreach($this->eventos as $key=>$evento)
 		{
 			$tamañoArray=count($this->eventos)-1;//Se cuenta el evento actual como borrado
 			$datosEvento=unserialize($evento['Datos']);
 			$fechaEvento=$evento['Fecha'];

 			switch ($evento['Tipo'])
 			{

 				case 1://Fin de edificio
 				case 2://Final de investigación
 				case 3://Final de hangar
 				case 4://Final de defensa
 				$this->FinConstruccion($evento,$fechaEvento,$datosEvento);
 				break;

 				case 5://Inicio de edificio
 				case 6://Inicio de investigación
 				$this->ConstruirSiguienteCola(null,$evento,$fechaEvento,$evento['Tipo']-4);
 				break;

 				case 7://Llegada de una flota a su destino
 				case 8://Retorno de flota al planeta original
 				$this->LlegadaFlota($evento,$datosEvento,$fechaEvento);
 				break;
 			}
 			//Borrar el evento
 			unset($this->eventos[$key]);

 			if($tamañoArray!=count($this->eventos))//Colección modificada
 			{
 				$this->FiltrarEventos();
 				return;
 			}
 		}
 	}

 	/**
  * Estática. Añade a la cola o inicia la construccion de una nave o defensa
  * @param Planeta $planeta Planeta donde se inicia la construcción
  * @return string Cadena SQL para añadir datos a la cola
 */
 	static function ContruccionHangar($id,$cantidad,$tipoCola,$planeta)
 	{
 		$coste=CosteTecnologia($id);

 		$cantidad=min($cantidad,MaximaCantidadRealizable($coste,$planeta));

 		if($cantidad==0)
 		return;

 		$tiempo=TiempoProduccionTecnologia($id,0,$coste,$planeta->Tecnologias);

 		$coste[0]*=$cantidad;
 		$coste[1]*=$cantidad;
 		$coste[2]*=$cantidad;

 		$hangarOcupado=true;
 		if($planeta->Trabajando($tipoCola)==false)//Planeta desocupado
 		{
 			$hangarOcupado=false;
 			$planeta->Datos['Construcciones'][$tipoCola]=$id;
 		}

 		//Gastar recursos
 		$planeta->ModificarRecursos(-$coste[0],-$coste[1],-$coste[2],time());
 		$planeta->GuardarCambios();

 		if($hangarOcupado==true)//Construyendo naves, añadir a la cola
 		{
 			//Añadir a la cola
 			$GLOBALS['DB']->query("INSERT INTO `colas` (`Jugador` , `Planeta` , `Cantidad` , `Tecnologia` , `Tipo` )
 			VALUES ('{$GLOBALS['jugador']->ID}', '{$planeta->ID}', '$cantidad', '$id', '$tipoCola');");
 		}
 		else//Iniciar la construccion
 		{
 			$fechaFinalizacion=time()+$tiempo;
 			$datos=array($id,$cantidad);

 			//Crear evento de finalizacion
 			Eventos::CrearEvento($fechaFinalizacion,array(
 			'Jugador'=>$GLOBALS['jugador']->ID,
 			'Planeta'=>$planeta->ID,
 			'Fecha'=>$fechaFinalizacion,
 			'Tipo'=>$tipoCola,
 			'Datos'=>serialize($datos)),false);
 		}
 	}

 	/**
 * Finaliza una construcción y comprueba si hay algún elemento más en la cola
 */
 	function FinConstruccion($evento,$fechaEvento,$datosEvento)
 	{
 		global $DB;
 		if($evento['Tipo']==2)//Es una investigacion
 		{
 			$jugador=new Jugador($evento['Jugador'],true);
 			$jugador->Tecnologias[$datosEvento[0]]=$datosEvento[1];
 			$jugador->Datos['Investigando']=0;

 			if($datosEvento[0]==115)//Red intergalactica construida
 			{
 				//Obtener la suma de todos los laboratorios
 				$planetas=$DB->query('SELECT `Tecnologias` FROM `planetas` WHERE Jugador='.$jugador->ID);
 				$nivel=0;
 				while($planeta=$planetas->fetch_row())
 				{
 					$tecnos=unserialize($planeta[0]);
 					$nivel+=$tecnos[20];
 				}
 				$jugador->Tecnologias[20]=$nivel;
 			}

 			$jugador->GuardarCambios();

 			$this->ConstruirSiguienteCola(null,$evento,$fechaEvento,2);
 		}
 		else if($evento['Tipo']==3 || $evento['Tipo']==4)//Final de hangar o defensa
 		{
 			$planeta=new Planeta($DB->getRowProperties('planetas',$evento['Planeta'],'ID,Construcciones,Tecnologias'));
 			$planeta->Trabajando($evento['Tipo']);//Cargar las construcciones

 			//Tiempo de produccion de una sola nave
 			$tiempoProduccion=TiempoProduccionTecnologia($datosEvento[0],1,CosteTecnologia($datosEvento[0]),$planeta->Tecnologias);

 			$hechas=floor(($this->fechaLimite-$fechaEvento)/$tiempoProduccion)+1;

 			if(!isset($planeta->Tecnologias[$datosEvento[0]]))
 			$planeta->Tecnologias[$datosEvento[0]]=0;

 			if($hechas>=$datosEvento[1])//Se han hecho mas de las restantes, por tanto, se ha acabado la construcción
 			{
 				//Fecha en la que se acabó de construir
 				$fechaFinalizacion=$fechaEvento+(($datosEvento[1]-1)*$tiempoProduccion);
 				$planeta->Tecnologias[$datosEvento[0]]+=$datosEvento[1];

 				//Comprobar si hay mas elementos en cola
 				$consulta=$DB->query('select * from colas where `Planeta`='.$evento['Planeta'].' && `Tipo`='.$evento['Tipo'].' ORDER BY `ID` LIMIT 1');
 				if($consulta->num_rows()<=0)//No hay mas naves en cola
 				{
 					unset($planeta->Datos['Construcciones'][$evento['Tipo']]);
 				}
 				else//Iniciar la siguiente construccion
 				{
 					$consulta=$consulta->fetch_assoc();
 					$datosEvento=array($consulta['Tecnologia'],$consulta['Cantidad']);
 					$planeta->Datos['Construcciones'][$evento['Tipo']]=$consulta['Tecnologia'];

 					//Calcular las ya hechas antes del tiempo limite
 					$tiempoProduccion=TiempoProduccionTecnologia($consulta['Tecnologia'],1,CosteTecnologia($consulta['Tecnologia']),$planeta->Tecnologias);
 					$hechas=floor(($this->fechaLimite-$fechaFinalizacion)/$tiempoProduccion);
 					if($hechas>0)//Ya se han hecho algunas naves antes del tiempo limite
 					{
 						if($hechas>$datosEvento[1])
 						$hechas=$datosEvento[1];

 						$fechaFinalizacionSiguienteCola=$fechaFinalizacion+$tiempoProduccion*$hechas;
 					}
 					else//Aun no se ha hecho ninguna nave de las siguientes de la cola
 					{
 						$fechaFinalizacionSiguienteCola=$fechaFinalizacion+$tiempoProduccion;
 					}

 					//Crear evento
 					$this->CrearEvento($fechaFinalizacionSiguienteCola,array('Jugador'=>$evento['Jugador'],'Planeta'=>$evento['Planeta'],
 					'Fecha'=>$fechaFinalizacionSiguienteCola,'Tipo'=>$evento['Tipo'],'Datos'=>serialize($datosEvento)));

 					//Borrar de la cola
 					$DB->query('DELETE FROM `colas` WHERE `ID` = '.$consulta['ID'].' LIMIT 1');
 				}
 			}
 			else//Aun quedan algunas por hacer, actualizar las ya hechas
 			{
 				$datosEvento[1]=$datosEvento[1]-$hechas;
 				$planeta->Tecnologias[$datosEvento[0]]+=$hechas;
 				$fechaFinalizacion=$fechaEvento+($hechas*$tiempoProduccion);//Fecha de finalizacion de la siguiente, ya fuera del limite de tiempo

 				$this->CrearEvento($fechaFinalizacion,array('Jugador'=>$evento['Jugador'],'Planeta'=>$evento['Planeta'],
 				'Fecha'=>$fechaFinalizacion,'Tipo'=>$evento['Tipo'],'Datos'=>serialize($datosEvento)));
 			}
 			$planeta->GuardarCambios();
 		}
 		else//Es un edificio
 		{
 			$planeta=new Planeta($evento['Planeta'],$fechaEvento);
 			$incremento=$datosEvento[1]-$planeta->Tecnologias[$datosEvento[0]];
 			$planeta->Tecnologias[$datosEvento[0]]=$datosEvento[1];

 			if($planeta->Tecnologias[$datosEvento[0]]<=0)
 			unset($planeta->Tecnologias[$datosEvento[0]]);

 			if($planeta->Trabajando(1))//Cargar construcciones
 			unset($planeta->Datos['Construcciones'][1]);

 			if($datosEvento[0]<20)//Mina
 			$planeta->RecalcularProducciones();

 			if($datosEvento[0]==20)//Laboratorio, mejorar la red
 			{
 				$jugador=new Jugador($evento['Jugador'],true);
 				if(isset($jugador->Tecnologias[115]) && $jugador->Tecnologias[115]>0)
 				{
 					$jugador->Tecnologias[20]+=$incremento;
 					$jugador->GuardarCambios();
 				}
 			}

 			$this->ConstruirSiguienteCola($planeta,$evento,$fechaEvento,1);

 			$planeta->GuardarCambios();
 		}
 	}

 	/**
 * Comprueba si hay edificios o investigaciones en la cola y los inicia si es posible
 * @param Planeta $planeta Planeta donde se inicia la construcción
 * @param array $evento Datos del evento sobre la construccion
 * @param Jugador $jugador Jugador dueño del planeta del evento (Solo para investigaciones)
 * @return bool Indica si se ha iniciado o no alguna construccion
 */
 	function ConstruirSiguienteCola($planeta,$evento,$fechaEvento,$tipoCola,$jugador=null)
 	{
 		global $DB;

 		if($tipoCola==2)//Investigaciones
 		$consulta=$DB->query('select * from colas where `Jugador`='.$evento['Jugador'].' && `Tipo`= 2 ORDER BY ID LIMIT 1');
 		else//Construcciones y hangar
 		$consulta=$DB->query('select * from colas where `Planeta`='.$evento['Planeta'].' && `Tipo`='.$tipoCola.' ORDER BY ID LIMIT 1');

 		if($consulta->num_rows()==0)
 		return false;

 		if($tipoCola==2)//Cargar los datos del jugador
 		{
 			if(!isset($jugador))
 			$jugador=new Jugador($evento['Jugador'],true);

 			if($jugador->Datos['Investigando']!=0)
 			return false;
 		}
 		else
 		{
 			$jugador;
 			$jugador->ID=$evento['Jugador'];
 		}

 		$proximo=$consulta->fetch_assoc();//Proximo elemento de la cola

 		if(!isset($planeta) || $planeta->ID!=$proximo['Planeta'])
 		{
 			if($tipoCola==2)
 			$planeta=new Planeta($proximo['Planeta'],$fechaEvento,true);
 			else
 			$planeta=new Planeta($proximo['Planeta'],$fechaEvento);
 		}

 		if($tipoCola==1 && $planeta->Trabajando(1))//Comprobar si no esta en construccion
 		{
 			return false;
 		}

 		if($this->IniciarConstruccion($planeta,$fechaEvento,$proximo['Tecnologia'],$proximo['Cantidad'],$tipoCola,$jugador,false)==true)
 		{
 			$DB->deleteRow('colas',$proximo['ID']);
 			return true;
 		}
 		return false;
 	}

 	/**
 * Inicia una nueva construcción de una tecnología
 * 
 * Si esta construyendo
 *		Añadir a la cola
 *	Sino
 *	{
 *		Si no se puede construir
 *			Crear evento de posible inicio
 *		Sino
 *			Iniciar La construccion
 *	}
 * 
 * @param Planeta $planeta Planeta donde se inicia la construcción
 * @param Jugador $jugador Jugador que inicia la construccion
 * @param integer $fecha Fecha de inicio de la construcción
 * @return bool Indica si se ha iniciado o no la construcción indicada
 */
 	function IniciarConstruccion(&$planeta,$fecha,$idTecno,$cantidad,$tipoEvento,$jugador,$añadirCola)
 	{
 		if($tipoEvento==1 && $planeta->Datos['CamposOcupados']==$planeta->Datos['CamposTotales'])
 		return false;

 		if($tipoEvento==2)
 		$trabajando=$jugador->Datos['Investigando']!=0;
 		else
 		$trabajando=$planeta->Trabajando($tipoEvento);

 		//Comprobar si se puede iniciar o no
 		if($trabajando && $añadirCola)//Añadir a la cola
 		{
 			$GLOBALS['DB']->query("INSERT INTO `colas` (`Jugador` , `Planeta` , `Cantidad` , `Tecnologia` , `Tipo` )
						VALUES ('{$jugador->ID}', '{$planeta->ID}', '$cantidad', '$idTecno', '$tipoEvento');");	

 			return false;
 		}
 		else
 		{
 			if($tipoEvento==1)//Construccion de edificio
 			{
 				if(!isset($planeta->Tecnologias[$idTecno]))
 				$planeta->Tecnologias[$idTecno]=0;

 				$nivel=$planeta->Tecnologias[$idTecno]+$cantidad;
 			}
 			else if($tipoEvento==2)//Investigacion
 			{
 				if(!isset($jugador->Tecnologias[$idTecno]))
 				$jugador->Tecnologias[$idTecno]=0;

 				$nivel=$jugador->Tecnologias[$idTecno]+$cantidad;
 			}

 			$coste=CosteTecnologia($idTecno,$nivel);

 			if(TipoTecnologia($idTecno)==3 && $nivel>1)//Tecnologia de un solo nivel
 			{
 				$this->CrearEvento($fecha,array('Jugador'=>$jugador->ID,'Planeta'=>$planeta->ID,'Fecha'=>$fecha,'Tipo'=>$tipoEvento+4));
 				return true;
 			}

 			if($coste[0]>$planeta->Metal || $coste[1]>$planeta->Cristal || $coste[2]>$planeta->Antimateria)
 			{
 				//No se puede construir, establecer un posible inicio
 				$tiempoNecesario=TiempoNecesarioInicioConstruccion($planeta->Datos,$idTecno,$nivel,$coste);

 				if($tiempoNecesario<0)//No se sabe cuando se puede iniciar
 				return false;
 				$posibleInicio=$fecha+$tiempoNecesario+1;

 				$this->CrearEvento($posibleInicio,array('Jugador'=>$jugador->ID,'Planeta'=>$planeta->ID,
 				'Fecha'=>$posibleInicio,
 				'Tipo'=>$tipoEvento+4));

 				if($tipoEvento==1)//Construcción
 				{
 					$planeta->Trabajando(1);
 					$planeta->Datos['Construcciones'][1]=-1;
 					$planeta->GuardarCambios();
 				}
 				else if($tipoEvento==2)//Investigación
 				{
 					$jugador->Datos['Investigando']=-1;
 					$jugador->GuardarCambios();
 				}

 				if($añadirCola==true)
 				{
 					$GLOBALS['DB']->query("INSERT INTO `colas` (`Jugador` , `Planeta` , `Cantidad` , `Tecnologia` , `Tipo` )
						VALUES ('{$jugador->ID}', '{$planeta->ID}', '$cantidad', '$idTecno', '$tipoEvento');");
 				}

 				return false;
 			}
 			else //Iniciar la construccion
 			{
 				$tiempoProduccion=TiempoProduccionTecnologia($idTecno,$nivel,$coste,$planeta->Tecnologias);
 				$finConstruccion=$fecha+$tiempoProduccion;

 				$datos=array($idTecno,$nivel);
 				if($tipoEvento==2)//Investigacion,añadir tiempo de produccion
 				$datos[]=$tiempoProduccion;

 				$this->CrearEvento($finConstruccion,array('Jugador'=>$jugador->ID,'Planeta'=>$planeta->ID,
 				'Fecha'=>$finConstruccion,
 				'Tipo'=>$tipoEvento,
 				'Datos'=>serialize($datos)));

 				$planeta->ModificarRecursos(-$coste[0],-$coste[1],-$coste[2],$fecha);
 				if($tipoEvento==1)//Construcción
 				{
 					$planeta->Trabajando(1);//Cargar datos de construcciones
 					$planeta->Datos['Construcciones'][1]=$idTecno;
 					$planeta->Datos['CamposOcupados']+=$cantidad;
 				}

 				$planeta->GuardarCambios();

 				if($tipoEvento==2)//Investigación
 				{
 					$jugador->Datos['Investigando']=$idTecno;
 					$jugador->GuardarCambios();
 				}

 				return true;
 			}
 		}
 	}



 	/**
 	 * Envia la flota especificada
 	 *
 	 * @param Flota $flota Flota que se va a enviar
 	 * @param Planeta $planeta Planeta de origen de la flota
 	 */
 	public function EnviarFlota($flota,$planeta,$fecha=null)
 	{
 		if(!isset($fecha))
 		$fecha=time();

 		$tiempoVuelo=(int)round($flota->Distancia/$flota->Velocidad);
 		$llegada=(int)($fecha+$tiempoVuelo);


 		if($flota->Mision==15 || $flota->Mision==16)//Crear el planeta de destino para misiones de analisis o colonizacion
 		{
 			$recrear=$flota->PlanetaDestino!=false && $flota->Mision==15 && $flota->PlanetaDestino['Jugador']==0 && (time()-$GLOBALS['DB']->getRowProperty('planetas',$flota->PlanetaDestino['ID'],'UltimoAcceso'))>604800;
 			if($recrear)
 			$GLOBALS['DB']->deleteRow('planetas',$flota->PlanetaDestino['ID']);

 			//Crear el planeta de destino si no existe o si fue creado hace mas de una semana
 			if($flota->PlanetaDestino==false || $recrear)
 			{
 				include_once('registro.php');

 				$flota->PlanetaDestino['ID']=RegistrarNuevoPlaneta(null,'',$flota->Destino[0],$flota->Destino[1],$flota->Destino[2]);
 			}
 		}

 		$datos=array((int)$flota->IdOrigen,$flota->Naves,
 		$flota->Recursos,(int)$flota->Mision,(int)$tiempoVuelo,(int)$flota->JugadorDestino);

 		$evento=array(
 		'Jugador'=>$flota->Propietario,
 		'Planeta'=>$flota->PlanetaDestino['ID'],
 		'Fecha'=>$llegada,
 		'Tipo'=>7,
 		'Datos'=>serialize($datos));

 		if(isset($this))
 		$this->CrearEvento($llegada,$evento);
 		else
 		Eventos::CrearEvento($llegada,$evento,false);

 		//Quitar las naves del planeta
 		foreach ($flota->Naves as $idNave=>$cantidad)
 		{
 			$planeta->Tecnologias[$idNave]-=$cantidad;
 			if($planeta->Tecnologias[$idNave]<=0)
 			unset($planeta->Tecnologias[$idNave]);
 		}

 		$planeta->ModificarRecursos(-$flota->Recursos[0],-$flota->Recursos[1],-$flota->Recursos[2]);

 		$planeta->GuardarCambios();
 	}
 	/**
 	 * Llegada de una flota a su destino
 	 * 
 	 */
 	function LlegadaFlota($evento,$datosEvento,$fechaEvento)
 	{
 		include_once('src/mensajes.php');
 		include_once('src/flota.php');
 		global $DB;

 		$flotaEvento=new FlotaEvento();
 		$flotaEvento->CargarEvento($evento,$datosEvento);

 		$retorno=$evento['Tipo']==8;
 		$dejarRecursos=false;

 		//Procesar los datos del jugador de destino
 		if($flotaEvento->JugadorDestino!=null && $flotaEvento->JugadorDestino!=$evento['Jugador'])
 		{
 			$eventos=new Eventos();
 			$eventos->Procesar($flotaEvento->JugadorDestino,$fechaEvento);
 		}

 		//Cargar datos
 		$planetaDestino=new Planeta($evento['Planeta'],$fechaEvento);
 		$nombreJugador=$DB->getRowProperty('jugadores',$evento['Jugador'],'Nombre');

 		$tipoMensaje=2;//Tipo de mensaje por defecto: Flota

 		if($retorno==false)//Llegada de flota al planeta destino
 		{
 			if($planetaDestino->Datos==false)//No hay planeta
 			{
 				$dejarRecursos=false;
 				$mensaje=sprintf(GetString('Tu flota ha llegado a %s pero no ha encontrado ningún planeta en esa órbita. La misión ha sido abortada.')
 				,MostrarLocalizacionPlaneta($planetaDestino->Datos,true));
 				$asunto=GetString('Misión abortada');
 			}
 			else
 			{
 				switch ($flotaEvento->Mision)
 				{
 					case 1://Transportar
 					$dejarRecursos=true;

 					$tipoMensaje=7;
 					$mensaje=serialize(array(
 					0=>$flotaEvento->Naves,
 					1=>$flotaEvento->Recursos,
 					2=>MostrarLocalizacionPlaneta($planetaDestino->Datos,false,true)));

 					$mensajeDestino=sprintf(GetString('La flota del jugador %s ha llegado al planeta %s y ha entregado los siguientes recursos:<br/>Metal: %s<br/>Cristal: %s<br/>Antimateria: %s')
 					,$nombreJugador.' '.IconoEnviarMensajeJugador($evento['Jugador']),$planetaDestino->Nombre,SeparadorMiles($flotaEvento->Recursos[0]),SeparadorMiles($flotaEvento->Recursos[1]),SeparadorMiles($flotaEvento->Recursos[2]));
 					$asuntoDestino=GetString('Llegada de flota de transporte enemiga');
 					break;

 					case 2://Desplegar
 					$dejarRecursos=true;
 					$retorno=true;

 					$tipoMensaje=9;
 					$mensaje=serialize(array(
 					0=>$flotaEvento->Naves,
 					1=>$flotaEvento->Recursos,
 					2=>MostrarLocalizacionPlaneta($planetaDestino->Datos,false,true)));

 					break;

 					case 3://Reciclar
 					$capacidad=$flotaEvento->CapacidadCarga();
 					$escombros=array(
 					'EscombrosMetal'=>$planetaDestino->Datos['EscombrosMetal'],
 					'EscombrosCristal'=>$planetaDestino->Datos['EscombrosCristal']
 					);

 					$cristalRecolectado=min($escombros['EscombrosCristal'],$capacidad);
 					$capacidad-=$cristalRecolectado;
 					$metalRecolectado=min($escombros['EscombrosMetal'],$capacidad);

 					$escombros['EscombrosCristal']-=$cristalRecolectado;
 					$escombros['EscombrosMetal']-=$metalRecolectado;

 					$DB->setRowProperties('planetas',$flotaEvento->IdDestino,$escombros);

 					$tipoMensaje=10;
 					$mensaje=serialize(array(
 					0=>$flotaEvento->Naves,
 					1=>array($metalRecolectado,$cristalRecolectado),
 					2=>array($planetaDestino->Datos['Galaxia'],$planetaDestino->Datos['Sistema'],$planetaDestino->Datos['Posicion'])));

 					break;

 					case 11://Espiar
 					$tipoMensaje=6;
 					$tecnologiasDestino=unserialize($DB->getRowProperty('jugadores',$flotaEvento->JugadorDestino,'Tecnologias'));
 					$tecnologiasOrigen=unserialize($DB->getRowProperty('jugadores',$evento['Jugador'],'Tecnologias'));
 					$cantidadSondas=$flotaEvento->Naves[303];
 					$diferenciaNiveles=($tecnologiasDestino[100]-$tecnologiasOrigen[100])*($tecnologiasDestino[100]-$tecnologiasOrigen[100])*($tecnologiasDestino[100]>=$tecnologiasOrigen[100]?1:-1);

 					$asunto=sprintf(GetString('Informe de espionaje del planeta %s'),MostrarLocalizacionPlaneta($planetaDestino->Datos,true));

 					$cantidadNaves=0;
 					for($contador=305;$contador<=500;$contador++)
 					{
 						if(isset($planetaDestino->Datos['Tecnologia'][$contador]))
 						$cantidadNaves+=$planetaDestino->Datos['Tecnologia'][$contador];
 					}

 					$probabilidadDetectar=mt_rand(0,min(100,round($cantidadNaves/pow(2,(($tecnologiasDestino[100]-$tecnologiasOrigen[100])*-1)+2))));

 					if($cantidadSondas<$diferenciaNiveles)//Sin resultados
 					{
 						$mensaje=serialize(array(0=>$probabilidadDetectar));
 					}
 					else
 					{
 						$mensaje=array();
 						$mensaje[0]=$probabilidadDetectar;
 						$mensaje[1]=array($planetaDestino->Metal,$planetaDestino->Cristal,$planetaDestino->Antimateria,$planetaDestino->Energia);

 						if(!function_exists('ArrayTecnologias'))
 						{
 							function ArrayTecnologias($lista,$tecnologias)
 							{
 								$res=array();
 								foreach($lista as $tecnologia)
 								{
 									if(isset($tecnologias[$tecnologia]) && $tecnologias[$tecnologia]>0)
 									{
 										$res[$tecnologia]=$tecnologias[$tecnologia];
 									}
 								}
 								return $res;
 							}
 						}

 						if($cantidadSondas>=$diferenciaNiveles+2)//Mostrar flotas
 						$mensaje[]=ArrayTecnologias(ObtenerTecnologias(3),$planetaDestino->Tecnologias);
 						if($cantidadSondas>=$diferenciaNiveles+3)//Mostrar defensas
 						$mensaje[]=ArrayTecnologias(ObtenerTecnologias(4),$planetaDestino->Tecnologias);
 						if($cantidadSondas>=$diferenciaNiveles+5)//Mostrar edificios
 						$mensaje[]=ArrayTecnologias(ObtenerTecnologias(1,$planetaDestino),$planetaDestino->Tecnologias);
 						if($cantidadSondas>=$diferenciaNiveles+7)//Mostrar investigaciones
 						{
 							$tecnosJugador=unserialize($DB->getRowProperty('jugadores',$flotaEvento->JugadorDestino,'Tecnologias'));
 							$mensaje[]=ArrayTecnologias(ObtenerTecnologias(2),$tecnosJugador);
 						}
 						$mensaje=serialize($mensaje);
 					}

 					if (mt_rand(0,100)<=$probabilidadDetectar) {//Sonda detectada
 						//Simular la batalla !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 					}

 					break;

 					case 15://Analizar planeta
 					if(empty($planetaDestino->Datos['Jugador']))
 					{
 						$mensaje=sprintf(GetString('Análisis superficial del planeta en %s<br/>Campos de construcción: %s<br/>Temperatura media: %s')
 						,MostrarLocalizacionPlaneta($planetaDestino->Datos,true),$planetaDestino->Datos['CamposTotales'],$planetaDestino->Datos['Temperatura']);
 					}
 					else
 					{
 						$mensaje=sprintf(GetString('El planeta en %s ya ha sido colonizado, por lo que no se puede llevar a cabo ningún análisis de su superficie.')
 						,MostrarLocalizacionPlaneta($planetaDestino->Datos,true));
 					}
 					$asunto=GetString('Informe de análisis de planeta');
 					break;

 					case 16://Colonizar
 					$asunto=GetString('Informe de colonización');
 					if(empty($planetaDestino->Datos['Jugador']))
 					{
 						$flotaEvento->Naves[304]-=1;//Quitar un colonizador

 						$propiedades=array(
 						'Jugador'=>$evento['Jugador'],
 						'Estado'=>0,
 						'Metal'=>500+$flotaEvento->Recursos[0],
 						'Cristal'=>100+$flotaEvento->Recursos[1],
 						'Antimateria'=>$flotaEvento->Recursos[2]);
 						$DB->setRowProperties('planetas',$flotaEvento->IdDestino,$propiedades);

 						$mensaje=sprintf(GetString('El planeta en %s ha sido colonizado, ahora puedes asignarle un nombre y empezar a construir en él.')
 						,MostrarLocalizacionPlaneta($planetaDestino->Datos,false));
 					}
 					else
 					{
 						$mensaje=sprintf(GetString('El planeta en %s ya ha sido colonizado por otro jugador. Tus colonizadores volverán a su planeta de origen.')
 						,MostrarLocalizacionPlaneta($planetaDestino->Datos,true));
 					}
 					break;
 				}
 			}
 		}
 		else//Retorno de flota a su planeta de origen
 		{
 			$dejarRecursos=true;

 			$tipoMensaje=8;
 			$mensaje=serialize(array(
 			0=>$flotaEvento->Naves,
 			1=>array_sum($flotaEvento->Recursos)<=0?0:$flotaEvento->Recursos,
 			2=>MostrarLocalizacionPlaneta($planetaDestino->Datos,false,true)));
 		}

 		//Enviar mensaje al jugador propietario
 		Mensajes::EnviarMensaje(GetString('Centro de control de flotas'),$evento['Jugador'],$asunto,$mensaje,$tipoMensaje,$fechaEvento);;

 		//Enviar mensaje al jugador de destino
 		if(isset($mensajeDestino))
 		Mensajes::EnviarMensaje(GetString('Centro de control de flotas'),$flotaEvento->JugadorDestino,$asuntoDestino,$mensajeDestino,2,$fechaEvento);;

 		if($dejarRecursos)//Modificar los recursos del planeta
 		{
 			$planetaDestino->Trabajando(1);//Cargar el estado de las construcciones
 			$planetaDestino->ModificarRecursos($flotaEvento->Recursos[0],$flotaEvento->Recursos[1],$flotaEvento->Recursos[2],$fechaEvento);

 			//Comprobar si se puede iniciar una construccion en espera
 			if(isset($planetaDestino->Datos['Trabajando'][1]) && $planetaDestino->Datos['Construcciones'][1]==-1)
 			{
 				//Esperando recursos para construir
 				$evento=array('Jugador'=>$flotaEvento->JugadorDestino,
 				'Planeta'=>$planetaDestino->ID,'Tipo'=>1);

 				$this->ConstruirSiguienteCola($planetaDestino,$evento,$fechaEvento,1);

 				//Borrar el antiguo evento de inicio
 				$DB->query("DELETE FROM `eventos` WHERE `Jugador`=$flotaEvento->JugadorDestino && `Planeta`=$planetaDestino->ID && `Tipo`=5");
 			}
 			if($DB->getRowProperty('jugadores',$flotaEvento->JugadorDestino,'Investigando')==-1)
 			{
 				//Esperando para investigar
 				$evento=array('Jugador'=>$planetaDestino->Datos['Jugador'],
 				'Planeta'=>$planetaDestino->ID,'Tipo'=>2);

 				$this->ConstruirSiguienteCola($planetaDestino,$evento,$fechaEvento,2,null);

 				//Borrar el antiguo evento de inicio
 				$DB->query("DELETE FROM `eventos` WHERE `Jugador`=$flotaEvento->JugadorDestino && `Planeta`=$planetaDestino->ID && `Tipo`=6");
 			}
 		}
 		if($retorno)//Volver a establecer la naves en el planeta
 		{
 			if($flotaEvento->Mision!=16)//En la colonización no se devuelven las naves
 			{
 				foreach($flotaEvento->Naves as $idNave=>$cantidad)
 				{
 					if(!isset($planetaDestino->Tecnologias[$idNave]))
 					$planetaDestino->Tecnologias[$idNave]=$cantidad;
 					else
 					$planetaDestino->Tecnologias[$idNave]+=$cantidad;
 				}
 			}
 		}
 		$planetaDestino->GuardarCambios();

 		//Enviar de regreso
 		if($retorno==false)
 		{
 			if(array_sum($flotaEvento->Naves)>0)
 			{
 				$llegada=(int)($fechaEvento+$flotaEvento->TiempoVuelo);

 				$datos=array((int)$flotaEvento->IdDestino,$flotaEvento->Naves,
 				$flotaEvento->Recursos,(int)$flotaEvento->Mision,0,(int)$flota->JugadorDestino);

 				$evento=array(
 				'Jugador'=>$evento['Jugador'],
 				'Planeta'=>$flotaEvento->IdOrigen,
 				'Fecha'=>$llegada,
 				'Tipo'=>8,
 				'Datos'=>serialize($datos));

 				$this->CrearEvento($llegada,$evento);
 			}
 		}
 	}

 	/**
 * Inserta un nuevo evento si ya se ha producido en la lista de eventos, sino en la base de datos.
 * La entrada "datos" dentro del array evento debe estar serializada
 * 
 * @param bool $comprobar Indica si se comprueba la fecha del evento para añadirlo a la base de datos o no
 */
 	function CrearEvento($fecha,$evento,$comprobar=true)
 	{
 		if($comprobar && $fecha<=$this->fechaLimite)
 		{
 			if(isset($this->eventos[$fecha]))
 			{
 				$this->CrearEvento('0'.(string)$fecha,$evento);
 			}
 			else
 			{
 				$this->eventos[$fecha]=$evento;
 			}
 		}
 		else
 		{
 			$GLOBALS['DB']->query("INSERT INTO `eventos` ( `Jugador` , `Planeta` , `Fecha` , `Tipo` , `Datos` )
VALUES ('{$evento['Jugador']}', '{$evento['Planeta']}', '{$evento['Fecha']}', '{$evento['Tipo']}', '{$evento['Datos']}');");
 		}
 	}
 }
?>