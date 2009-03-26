<?php

/**
 * Funciones para el manejo de alianzas
 *
 */
class Alianza
{
	/**
	 * ID de la alianza que se está procesando
	 * @var int
	 */
	var $ID;
	/**
	 * Nombre de la alianza actual. Se carga al llamar a ObtenerDatos();
	 * @var string
	 */
	var $Nombre;
	function Nombre()
	{
		return isset($this->Nombre)?$this->Nombre:$this->Nombre=$GLOBALS['DB']->getRowProperty('alianzas',$this->ID,'Nombre');
	}

	/**
	 * Constructor de la clase
	 *
	 * @param int $ID
	 * @param string $nombre
	 * @return Alianza
	 */
	function Alianza($ID,$nombre=null)
	{
		$this->ID=$ID;

		if(isset($nombre))
		$this->Nombre=$nombre;
	}

	/**
	 * Obtiene los datos de esta alianza
	 */
	function ObtenerDatos()
	{
		$datos=$GLOBALS['DB']->first_assoc('select * from `alianzas` where `ID`='.$this->ID.' LIMIT 1');
		$this->Nombre=$datos['Nombre'];
		return $datos;
	}

	/**
	 * Obtiene las relaciones (PNA, guerras) de esta alianza con otras en un array multidimensional
	 * 
	 * Array
	 * [PNA]
	 *    [0] Relaciones emisoras (la alianza actual administra la relación)
	 * 		[SolicitudPNA]
	 * 	  	[Guerra]
	 * 	 [1] Relaciones receptoras (la alianza esta forzada, como en las guerras)
	 */
	function ObtenerRelaciones()
	{
		$relaciones= $GLOBALS['DB']->query('select *,(SELECT Nombre FROM alianzas where ID=Alianza1 LIMIT 1) AS Nombre1,(SELECT Nombre FROM alianzas where ID=Alianza2 LIMIT 1) AS Nombre2 from `relacionesalianza` where `Alianza1`='.$this->ID.' || `Alianza2`='.$this->ID);

		$resultado=array();

		while($relacion=$relaciones->fetch_assoc())
		{
			if($relacion['Alianza1']==$this->ID)//Esta alianza es la emisora
			{
				if($relacion['Tipo']==1)//PNA
				$resultado['PNA'][$relacion['Alianza2']]=$relacion['Nombre2'];
				else if($relacion['Tipo']==2)//Solicitud de PNA
				$resultado[0]['SolicitudPNA'][$relacion['Alianza2']]=$relacion['Nombre2'];
				else if($relacion['Tipo']==3)//Guerra
				$resultado[0]['Guerra'][$relacion['Alianza2']]=$relacion['Nombre2'];
			}
			else if($relacion['Alianza2']==$this->ID)//Esta alianza es la receptora
			{
				if($relacion['Tipo']==1)//PNA
				$resultado['PNA'][$relacion['Alianza1']]=$relacion['Nombre1'];
				else if($relacion['Tipo']==2)//Solicitud de PNA
				$resultado[1]['SolicitudPNA'][$relacion['Alianza1']]=$relacion['Nombre1'];
				else if($relacion['Tipo']==3)//Guerra
				$resultado[1]['Guerra'][$relacion['Alianza1']]=$relacion['Nombre1'];
			}
		}

		return $resultado;
	}

	/**
	 * Obtiene las relaciones que se pueden llevar a cabo con una alianza
	 *
	 * @param int $alianza Alianza con la que se buscan los pactos
	 * @param array $pactos Pactos de la alianza actual
	 */
	function ObtenerRelacionesDisponibles($alianza,$pactos)
	{
		$hayPNA=(isset($pactos['PNA']) && array_key_exists($alianza,$pactos['PNA']));
		$hayGuerraInevitable	=(isset($pactos[0]['Guerra']) && array_key_exists($alianza,$pactos[0]['Guerra']));
		$hayGuerraDeclarada=(isset($pactos[1]['Guerra']) && array_key_exists($alianza,$pactos[1]['Guerra']));
		$haySolicitudPna=(isset($pactos[1]['SolicitudPNA']) && array_key_exists($alianza,$pactos[1]['SolicitudPNA']));
		$SolicitudPnaEnviada=(isset($pactos[0]['SolicitudPNA']) && array_key_exists($alianza,$pactos[0]['SolicitudPNA']));

		if($hayPNA)//Hay pacto con la alianza actual
		{
			return 1;//Hay un pacto, la unica opcion es cancelarlo
		}
		else if($hayGuerraInevitable==false)
		{
			if($hayGuerraDeclarada)
			{
				return 2;//Hay una guerra, pero se puede cancelar
			}
			else if($haySolicitudPna)
			{
				return 3;//Hay una solicitud de PNA, se puede retirar
			}
			else if($SolicitudPnaEnviada==false)
			{
				return 4;//No hay relaciones, se puede declarar guerra o solicitar pna
			}
		}
		return 0;//No hay ninguna relacion posible
	}

	/**
	 * Solicita un PNA a la alianza especificada
	 */
	function SolicitudPNA($alianza,$nombre=null)
	{
		$otraAlianza=new Alianza($alianza);
		if($otraAlianza->ObtenerRelacionesDisponibles($this->ID,$otraAlianza->ObtenerRelaciones())!=4)
		return;

		//Establecer la solicitud
		$this->EstablecerRelacion($this->ID,$alianza,2);

		include_once('mensaje.php');

		//Enviar mensajes a la alianza
		EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Pacto de no agresión'),
		sprintf(GetString('La alianza %s ha solicitado un pacto de no agresión a nuestra alianza.'),
		$this->Nombre()));

		EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Pacto de no agresión'),
		sprintf(GetString('Hemos solicitado un pacto de no agresión a la alianza %s. Por favor, no atacar a sus miembros.'),
		isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));
	}

	/**
	 * Retira una solicitud de PNA de la alianza especificada
	 */
	function RetirarSolicitudPNA($alianza,$nombre=null)
	{
		$otraAlianza=new Alianza($alianza);
		if($otraAlianza->ObtenerRelacionesDisponibles($this->ID,$otraAlianza->ObtenerRelaciones())!=3)
		return;

		if($this->BorrarRelacion($this->ID,$alianza,2))
		{
			include_once('mensajes.php');

			//Enviar mensajes a las alianzas
			EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('La alianza %s ha retirado su solicitud para un pacto de no agresión.'),
			$this->Nombre()));

			EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('Hemos retirado la solicitud de pacto de no agresión con la alianza %s.'),
			isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));
		}
	}

	/**
	 * Rechaza la solicitud de PNA de la alianza especificada
	 */
	function RechazarSolicitudPNA($alianza,$nombre=null)
	{
		if($this->BorrarRelacion($alianza,$this->ID,2))
		{
			include_once('mensaje.php');

			//Enviar mensajes a las alianzas
			EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('La alianza %s ha rechazado nuestra solicitud de pacto de no agresión.'),
			$this->Nombre()));

			EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('La solicitud de pacto de no agresión de la alianza %s ha sido denegada.'),
			isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));
		}
	}

	/**
	 * Acepta una solicitud de PNA vigente
	 */
	function AceptarSolicitudPNA($alianza,$nombre=null)
	{
		//Borrar la solicitud y comprobar de que existe
		if($this->BorrarRelacion($alianza,$this->ID,2))
		{
			include_once('mensaje.php');

			//Enviar mensajes a las alianzas
			EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('Nuestra alianza ha establecido un pacto de ayuda y no agresión con la alianza %s. Por favor, evitad conflictos y ataques con sus miembros.'),
			isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));

			EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Pacto de no agresión'),
			sprintf(GetString('La alianza %s ha aceptado nuestra solicitud de pacto de no agresión. Por favor, evitad conflictos y ataques con sus miembros.'),
			$this->Nombre()));

			//Establecer el pacto
			$this->EstablecerRelacion($alianza,$this->ID,1);
		}
	}

	/**
	 * Borra el pacto de no agresión con la alianza especificada
	 */
	function FinalizarPNA($alianza,$nombre=null)
	{
		$otraAlianza=new Alianza($alianza);
		if($otraAlianza->ObtenerRelacionesDisponibles($this->ID,$otraAlianza->ObtenerRelaciones())!=1)
		return;

		if($this->BorrarRelacion($this->ID,$alianza,1,false))
		{
			include_once('mensaje.php');

			//Enviar mensajes a las alianzas
			EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Fin del pacto de no agresión'),
			sprintf(GetString('El pacto de no agresión con la alianza %s ha sido cancelado. Ahora podéis atacar a sus miembros sin problemas.'),
			isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));

			EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Fin del pacto de no agresión'),
			sprintf(GetString('La alianza %s ha cancelado el pacto de no agresión. Ahora podéis atacar a sus miembros sin problemas.'),
			$this->Nombre()));
		}
	}

	/**
	 * Inicia una guerra con la alianza especificada
	 */
	function IniciarGuerra($alianza,$nombre=null)
	{
		$otraAlianza=new Alianza($alianza);
		if($otraAlianza->ObtenerRelacionesDisponibles($this->ID,$otraAlianza->ObtenerRelaciones())!=4)
		return;

		include_once('mensaje.php');

		//Enviar mensajes a las alianzas
		EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Comienza la guerra'),
		sprintf(GetString('Nuestra alianza ha declarado la guerra a %s. La batalla acaba de comenzar, destruid a tantos miembros como podáis.'),
		isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));

		EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Fin de la guerra'),
		sprintf(GetString('La alianza %s nos ha declarado la guerra. La batalla acaba de comenzar, destruid a tantos miembros como podáis.'),
		$this->Nombre()));

		$this->EstablecerRelacion($this->ID,$alianza,3);
	}


	/**
	 * Finaliza la guerra con la alianza especificada
	 */
	function FinalizarGuerra($alianza,$nombre=null)
	{
		$otraAlianza=new Alianza($alianza);
		if($otraAlianza->ObtenerRelacionesDisponibles($this->ID,$otraAlianza->ObtenerRelaciones())!=2)
		return;

		if($this->BorrarRelacion($this->ID,$alianza,3))
		{
			include_once('mensaje.php');

			//Enviar mensajes a las alianzas
			EnviarMensajeCircular($this->ID,GetString('Alianza'),GetString('Fin de la guerra'),
			sprintf(GetString('Nuestra alianza ha declarado la paz a %s. La guerra ha acabado.'),
			isset($nombre)?$nombre:$GLOBALS['DB']->getRowProperty('alianzas',$alianza,'Nombre')));

			EnviarMensajeCircular($alianza,GetString('Alianza'),GetString('Fin de la guerra'),
			sprintf(GetString('La alianza %s nos ha declarado la paz. La guerra ha acabado.'),
			$this->Nombre()));
		}
	}

	/**
	 * Establece una relacion entre dos alianzas
	 *
	 * @param int $alianza1 Alianza que realizo la peticion de PNA o guerra
	 * @param int $alianza2 Alianza que acepta el pacto o es atacada
	 * @param int $tipo Tipo de relacion: 1 - PNA / 2 - Guerra / 3 - Solicitud de PNA
	 */
	function EstablecerRelacion($alianza1,$alianza2,$tipo)
	{
		$GLOBALS['DB']->query("REPLACE INTO `relacionesalianza` ( `Alianza1` , `Alianza2`, `Tipo` )VALUES ('$alianza1', '$alianza2','$tipo')");
	}

	/**
	 * Borra la relacion entre dos alianzas, y devuelve un valor si se han modificado filas
	 *
	 * @param int $alianza1 Alianza que realizo la peticion de PNA o guerra
	 * @param int $alianza2 Alianza que acepta el pacto o es atacada
	 */
	function BorrarRelacion($alianza1,$alianza2,$tipo,$estricto=true)
	{
		global $DB;

		if($estricto)
		$DB->query("DELETE FROM `relacionesalianza` WHERE `Alianza1` =$alianza1  && `Alianza2` =$alianza2 && `Tipo`=$tipo LIMIT 1");
		else
		$DB->query("DELETE FROM `relacionesalianza` WHERE (`Alianza1` =$alianza1 || `Alianza1` =$alianza2)  && (`Alianza2` =$alianza1 || `Alianza2` =$alianza2) && `Tipo`=$tipo LIMIT 1");

		return $DB->affected_rows()>0;
	}

	/**
	 * Acepta la solicitud de acceso del jugador especificada si existe
	 *
	 */
	function AceptarSolicitudAcceso($IdJugador)
	{
		if(Solicitudes::BorrarSolicitud($IdJugador,$this->ID,1))
		{
			global $DB;
			//Jugador aceptado
			if($DB->getRowProperty('alianzas',$this->ID,'AvisosMiembros')==1)
			{
				//Enviar mensaje circular
				$datosJugador=$DB->getRowProperties('jugadores',$IdJugador,'Nombre,PlanetaPrincipal');
				$localizacion=$DB->getRowProperties('planetas',$datosJugador['PlanetaPrincipal'],'Galaxia,Sistema,Posicion');

				EnviarMensajeCircular($this->ID,$origen,
				GetString('Demos la bienvenida a un nuevo miembro'),
				sprintf(GetString('El jugador %s se ha unido a nuestra alianza.<br/><br/>Podeis encontrar su planeta principal en: %s<br/>¡Bienvenido!'),$datosJugador['Nombre'],MostrarLocalizacionPlaneta($localizacion)));
			}
			$DB->setRowProperty('jugadores',$IdJugador,'Alianza',$this->ID);
			$DB->query('UPDATE `alianzas` SET `Miembros`=`Miembros`+1 WHERE ID='.$this->ID);

			//Enviar aviso al jugador
			EnviarMensaje('Alianza '.$this->Nombre,$IdJugador,GetString('Has sido aceptado en la alianza').' '.Nombre(),
			sprintf(GetString('Enhorabuena, has sido aceptado como miembro de la alianza %s.'),Nombre()),'alianza');
		}
	}

	/**
	 * Disuelve esta alianza
	 */
	function Disolver()
	{
		global $DB;

		$origenMensaje=GetString('Alianza').' '.$alianza['Nombre'];

		EnviarMensajeCircular($this->ID,$origenMensaje,GetString('La alianza ha sido disuelta'),
		sprintf(GetString('La alianza %s ha sido disuelta. Búscate una nueva alianza.'),$this->Nombre));


		$DB->query('UPDATE `jugadores` SET `Alianza`=0, `RangoAlianza`=0 where Alianza='.$this->ID);
		$DB->query('DELETE FROM `pna` WHERE `Alianza1` = '.$this->ID.' || `Alianza2` = '.$this->ID);
		$DB->query('DELETE FROM `guerras` WHERE `Alianza1` = '.$this->ID.' || `Alianza2` = '.$this->ID);
		$DB->query('DELETE FROM `solicitudaccesoalianza` WHERE `Alianza` = '.$alianza['ID']);
		$DB->query('DELETE FROM `rangosalianza` WHERE `Alianza` = '.$this->ID);
		$DB->query('DELETE FROM `solicitudpna` WHERE `Alianza1` = '.$this->ID.' || `Alianza2` = '.$this->ID);

		//Borrar alianza
		$DB->deleteRow('alianzas',$this->ID);
	}

	/**
	 * Crea una nueva alianza con el nombre especificado
	 *
	 */
	function CrearNuevaAlianza($nombre)
	{
		global $DB;
		global $jugador;

		$DB->query("INSERT INTO `alianzas` ( `ID` , `Nombre` , `Puntos` , `SeccionInterna` , `SeccionExterna` , `UrlLogo` , `UrlWeb` , `Miembros` , `Ranking` , `SolicitudesDenegadas` , `AvisosMiembros`  )
VALUES (NULL , '{$DB->escape_string($nombre)}' , 0 , NULL , NULL , '{$jugador->UrlSkin}images/headerLogo.png' , NULL , '1', '0','0', '0');");
		$idAlianza=$DB->lastInsertId();
		$DB->query("INSERT INTO `rangosalianza` ( `ID` , `Alianza` , `Nombre` , `Fundador` , `RepresentarAlianza` , `AdministrarAlianza` , `RevisarSolicitudes` , `CrearCC` , `AdministrarPactos` , `ExpulsarMiembro`  , `VerListaMiembros` )
VALUES (NULL, '$idAlianza', '".GetString('Fundador')."', '1', '1', '1', '1', '1', '1', '1', '1');");
		$idRango=$DB->lastInsertId();

		$DB->setRowProperties('jugadores',$jugador->ID,array(
		'Alianza'=>$idAlianza,
		'RangoAlianza'=>$idRango));

		$jugador->Datos['Alianza']=$idAlianza;
		$jugador->Datos['RangoAlianza']=$idRango;

		return $idAlianza;
	}
}

class Rango
{
	var $competencias;
	var $IdRango;

	function Rango($IdRango)
	{
		$this->IdRango=$IdRango;
	}

	/**
	 * Obtiene el nombre del rango representado
	 */
	function Nombre()
	{
		if($this->IdRango==0)//Novato
		return GetString('Novato');
		else
		{
			if(isset($this->competencias['Nombre']))
			return $this->competencias['Nombre'];
			else
			return $GLOBALS['DB']->getRowProperty('rangosalianza',$this->IdRango,'Nombre');
		}
	}

	/**
	 * Carga las competencias desde la base de datos. Llamar sin parametros para cargar todos los datos del rango actual
	 *
	 */
	function ObtenerCompetencias($competencias=null)
	{
		if($this->IdRango==0)//Novato
		return;

		global $DB;

		if(isset($competencias))
		{
			if(isset($this->competencias))
			array_merge($this->competencias,$DB->getRowProperties('rangosalianza',$this->IdRango,$competencias));
			else
			$this->competencias=$DB->getRowProperties('rangosalianza',$this->IdRango,$competencias);
		}
		else
		$this->competencias=$DB->getRow('rangosalianza',$this->IdRango);
	}

	/**
	 * Obtiene un valor que indica si el rango actual es capaz de realizar una competencia
	 *
	 * @param string $tipo Tipo de compentencia
	 * @return bool
	 */
	function PoseeCompetencia($tipo)
	{
		if($this->IdRango==0)//Novato
		return false;

		if(!isset($this->competencias[$tipo]))
		$this->ObtenerCompetencias($tipo);

		return $this->competencias[$tipo]!=0;
	}

	/*
	* ESTATICAS
	*/
	/**
	 * Estatica. Obtiene la si el rango especificado posee la competencia indicada
	 *
	 * @param unknown_type $idRango
	 * @param unknown_type $competencia
	 */
	function PoseeCompetenciaRango($idRango,$competencia)
	{
		global $DB;

		if($idRango==0)
		return false;
		if($DB->getRowProperty('rangosalianza',$idRango,$competencia)!=0)
		return true;

		return false;
	}
}
?>