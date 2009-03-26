<?php

/**
 * Administra los mensajes de un jugador y expone métodos estáticos para el envio de mensajes
 * 
 * Tipos de mensaje:
 * 
 * 1 - Alianza
 * 2 - Flota
 * 3 - Jugadores
 * 4 - Batalla
 * 5 - Papelera
 * 6 - Informe de espionaje
 *
 */
class Mensajes
{
	function Mensajes($jugador)
	{
		$this->idJugador=$jugador;
	}
	private $idJugador;
	/**
	 * @var bool
	 */
	public $MensajesSinLeer;

	/**
	 * Cuenta el numero de mensajes disponibles y la cantidad sin leer de cada uno
	 * @return array Resultado de la funcion
	 */
	function ContarMensajes()
	{
		global $DB;

		$consulta=$DB->query('SELECT Leido,COUNT(*) AS Cantidad,Tipo from `mensajes` where `IdDestino`='.$this->idJugador.' GROUP BY Tipo,Leido');

		$resultado=array();
		while($fila = $consulta->fetch_assoc())
		{
			$tipoActual=Mensajes::TipoMensaje($fila['Tipo']);
			$cantidad=$fila['Cantidad'];

			if($fila['Leido']==0)//Mensajes sin leer
			{
				$resultado[$tipoActual]['SinLeer']=$cantidad;
				$this->MensajesSinLeer=true;

				if($tipoActual!='papelera')
				$resultado['todos']['SinLeer']+=$cantidad;
			}

			$resultado[$tipoActual]['Disponibles']+=$cantidad;

			if($tipoActual!='papelera')
			$resultado['todos']['Disponibles']+=$cantidad;
		}
		return $resultado;
	}

	public static function TipoMensaje($nombre)
	{
		switch ($nombre)
		{
			case 'informe':	return 6;
			case 'alianza':	return 1;
			case 'flota':	return 2;
			case 'jugador':	return 3;
			case 'batalla':	return 4;
			case 'papelera':return 5;
			default:		return -1;
		}
	}

	/**
 * Obtiene todos los mensajes del jugador de un tipo especifico
 *
 * @param string $tipo Tipo de mensajes que se van a obtener, o null para todos
 * @param int $cantidad Cantidad de mensajes obtenidos
 * @return array Array con los mensajes obtenidos
 */
	public function ObtenerMensajes($tipo='todos',$cantidad=10)
	{
		global $DB;

		if($tipo=='todos')
		$sqlFiltro=' && `Tipo`!=5';
		else if($tipo=='informe')
		$sqlFiltro=' && `Tipo`=6';
		else if($tipo=='alianza')
		$sqlFiltro=' && `Tipo`=1';
		else if($tipo=='flota')
		$sqlFiltro=' && `Tipo`=2';
		else if($tipo=='jugador')
		$sqlFiltro=' && `Tipo`=3';
		else if($tipo=='batalla')
		$sqlFiltro=' && `Tipo`=4';
		else if($tipo=='papelera')
		$sqlFiltro=' && `Tipo`=5';

		$consulta=$DB->query('select * from `mensajes` where `IdDestino`='.$this->idJugador.' '.$sqlFiltro.' ORDER BY `FechaEnvio` DESC LIMIT '.$cantidad);

		if($this->MensajesSinLeer)
		$DB->query('UPDATE `mensajes` SET `Leido`=1 where `IdDestino`='.$this->idJugador.' '.$sqlFiltro.' ORDER BY `FechaEnvio` DESC LIMIT '.$cantidad);

		if($consulta->num_rows()==0)
		{
			return array();
		}
		else
		{
			$resultado=array();
			while ($mensaje = $consulta->fetch_assoc())
			{
				$resultado[]=$mensaje;
			}
			return $resultado;
		}
	}

	public function TablaDeMensaje($mensaje)
	{
		global $DB;
		global $jugador;

		if($mensaje['Tipo']==1)//Mensaje circular
		{
			$mensajeBase=$GLOBALS['DB']->getRow('mensajes',$mensaje['Asunto']);
			$mensaje['Asunto']=$mensajeBase['Asunto'];
			$mensaje['Contenido']=$mensajeBase['Contenido'];
			$mensaje['Origen']=$mensajeBase['Origen'];
		}

		$fecha=$mensaje['FechaEnvio'];

		if(is_numeric($mensaje['Origen']))//El origen es un jugador
		$usuario=LinkJugador($mensaje['Origen']);
		else
		$usuario=$mensaje['Origen'];

		$cabeceraMensaje=sprintf(GetString('<strong>De: %s</strong>, a las <strong>%s</strong> del <strong>%s</strong>'),$usuario,date(GetString('g:i:s'),$fecha ),date(GetString('j-n-Y'),$fecha));

		if(empty($mensaje['Leido']))//Mensaje sin leer
		{
			$textoSinLeer='<div style="float:left;margin-right:5px;"><img title="Mensaje sin leer" src="'.$jugador->UrlSkin.'images/unreadMessage.png" alt="Mensaje sin leer" /></div>';

			global $jugador;
			$jugador->Datos['MensajesSinLeer']--;
			$jugador->MensajesSinLeerCambiados=true;
		}

		$texto=DescomprimirTexto($mensaje['Contenido']);
		if($mensaje['Tipo']==6)//Informe de espionaje
		{
			$datos=unserialize($texto);
			if(count($datos)==1)
			{
				$texto=GetString('No se ha podido recolectar ninguna información sobre el planeta espiado.');
			}
			else
			{
				$texto='<table class="espionaje"><tr>
<th>'.GetString('Recursos').'</th>
<td><img src="'.$jugador->UrlSkin.'images/metal.png" /><br/>'.SeparadorMiles($datos[0][0]).'</td>
<td><img src="'.$jugador->UrlSkin.'images/crystal.png" /><br/>'.SeparadorMiles($datos[0][1]).'</td>
<td><img src="'.$jugador->UrlSkin.'images/antimatter.png" /><br/>'.SeparadorMiles($datos[0][2]).'</td>
<td><img src="'.$jugador->UrlSkin.'images/energy.png" /><br/>'.SeparadorMiles($datos[0][3]).'</td>
</tr>';

				if(!function_exists('ListaTecnologias'))
				{
					function ListaTecnologias($texto,$lista)
					{
						$texto='<tr><th>'.$texto.'</th><td colspan="4">';

						$contador=0;
						foreach($lista as $tecnologia=>$nivel)
						{
							$texto.=GetTechnology($tecnologia)->Name.': <b>'.$nivel.'</b>';
							$contador++;
							$texto.=$contador%2==0?'<br/>':'&nbsp;&nbsp;';
						}
						return $texto.'</td></tr>';
					}
				}

				if(isset($datos[2]))//Mostrar flotas
				$texto.=ListaTecnologias(GetString('Flotas'),$datos[2]);
				if(isset($datos[3]))//Mostrar defensas
				$texto.=ListaTecnologias(GetString('Defensas'),$datos[3]);
				if(isset($datos[4]))//Mostrar edificios
				$texto.=ListaTecnologias(GetString('Edificios'),$datos[4]);
				if(isset($datos[5]))//Mostrar investigaciones
				$texto.=ListaTecnologias(GetString('Investigación'),$datos[5]);

				$texto.='</table>';
			}
			$texto.='<br/>'.GetString('Probabilidad de ser detectada: ').$datos[0].'%';
		}
		else if($mensaje['Tipo']==7)//Informe de transporte propio
		{
			$datos=unserialize($texto);
			$mensaje['Asunto']=GetString('Llegada de una flota de transporte a su destino');

			$texto=sprintf(GetString('Tu flota de transporte (%s) ha llegado a %s y ha entregado los siguientes recursos:'),Mensajes::ListarElementos($datos[0]),$datos[2]).
			'<br/><br/>'.Mensajes::TablaRecursos($datos[1]);
		}
		else if($mensaje['Tipo']==8)//Retorno de flota
		{
			$datos=unserialize($texto);
			$mensaje['Asunto']=GetString('Retorno de flota');

			if($datos[1]==0)
			{
				$texto=sprintf(GetString('Tu flota (%s) ha regresado a %s sin recursos.'),Mensajes::ListarElementos($datos[0]),$datos[2]);
			}
			else
			{
				$texto=sprintf(GetString('Tu flota (%s) ha regresado a %s y ha entregado los siguientes recursos:'),Mensajes::ListarElementos($datos[0]),$datos[2]).
				'<br/><br/>'.Mensajes::TablaRecursos($datos[1]);
			}
		}
		else if($mensaje['Tipo']==9)//Flota desplegada
		{
			$datos=unserialize($texto);
			$mensaje['Asunto']=GetString('Flota desplegada');

			$texto=sprintf(GetString('Tu flota (%s) se ha desplegado en %s y ha entregado los siguientes recursos:')
			,Mensajes::ListarElementos($datos[0]),$datos[2]).'<br/><br/>'.Mensajes::TablaRecursos($datos[1]);
		}
		else if($mensaje['Tipo']==10)//Informe de reciclaje
		{
			$datos=unserialize($texto);
			$mensaje['Asunto']=GetString('Informe de reciclaje');

			$texto=sprintf(GetString('Tu flota de reciclaje (%s) ha llegado a los escombros en [%d:%d:%d] y ha recolectado los siguientes recursos:')
			,Mensajes::ListarElementos($datos[0]),$datos[2][0],$datos[2][1],$datos[2][2]).'<br/><br/>'.Mensajes::TablaRecursos($datos[1]);
		}

		echo '<tr class="messageHeader"><td>'.$textoSinLeer.'<span style="font-weight:normal;">'.$cabeceraMensaje.'<br> '.GetString('<strong>Asunto</strong>').': '.$mensaje['Asunto'].'</span></td>
<th width="25" style="vertical-align:middle; text-align:center;">
<input type="checkbox" name="'.$mensaje['ID'].'"/></th>
</tr><tr><td colspan="3" class="messageType'.$mensaje['Tipo'].'">'.$texto.'</tr>';
	}

	/*
	FUNCIONES ESTÁTICAS
	*/

	/**
 * Estática.Envia un mensaje al destino especificado y devuelve el ID de mensaje enviado
 *
 * @param mixed $origen Origen del mensaje
 * @param int $destino ID del jugador de destino
 * @param string $asunto 
 * @param string $contenido
 * @param int $tipo Tipo de mensaje
 * @param int $fecha
 * @return unknown
 */
	public static function EnviarMensaje($origen,$destino,$asunto,$contenido,$tipo,$fecha=null)
	{
		if(is_numeric($destino)==false)
		return null;

		global $DB;

		if(!isset($fecha))
		$fecha=time();

		$DB->query("INSERT INTO `mensajes` ( `ID` , `Asunto` , `Tipo` , `IdDestino` , `Origen`, `Leido` , `FechaEnvio` , `Contenido` )
VALUES (NULL , '".$DB->escape_string($asunto)."', '$tipo', '$destino',  '$origen',  '0', '$fecha', ".ComprimirTexto($contenido).");");

		$idObtenida=$DB->lastInsertId();

		if($destino!=-1)
		$DB->query("UPDATE `jugadores` SET `MensajesSinLeer`=MensajesSinLeer+1 where `ID`=$destino");

		return $idObtenida;
	}

	/**
	 * Estática.Envia un mensaje a una alianza
	 *
	 * @param int $origen 
	 * @param int $destino ID de la alianza destino
	 * @param string $asunto
	 * @param string $contenido
	 * @param bool $mensajeInterno Indica si el mensaje lo envia un jugador de la alianza o un representante de otra alianza
	 * @param int $rango Id del rango de los jugadores a los que se enviara el mensaje, null para todos
	 * @param string $competenciaNecesaria Competencia necesaria para recibir el mensaje
	 */
	public static function EnviarMensajeAlianza($origen,$destino,$asunto,$contenido,$mensajeInterno,$rango=null,$competenciaNecesaria=null,$fechaEnvio=null)
	{
		global $DB;

		$sqlRangos='';
		if(isset($rango))
		{
			if(is_numeric($rango)==false)
			MostrarError(GetString('Error de parámetros'),true);

			$sqlRangos=' && RangoAlianza='.$rango;
		}

		if(!isset($fechaEnvio))
		$fechaEnvio=time();

		if(isset($competenciaNecesaria))
		$sql='SELECT Jugadores.ID FROM `jugadores` JOIN rangosalianza on rangosalianza.ID=Jugadores.RangoAlianza WHERE Jugadores.Alianza='.$destino.' && rangosalianza.'.$competenciaNecesaria.'!=0';
		else
		$sql='SELECT ID FROM `jugadores` WHERE Alianza='.$destino.$sqlRangos;

		$consulta=$DB->query($sql);

		if($consulta->num_rows()==0)
		return;

		//Enviar un mensaje que sirve de base al resto
		if($mensajeInterno)
		$idMensajeBase=Mensajes::EnviarMensaje($origen,-1,GetString('Correo circular:').' '.$asunto,$contenido,'alianza');
		else
		{
			$consulta=$DB->first_row('SELECT Alianzas.Nombre From jugadores Join Alianzas ON Alianzas.ID=Jugadores.Alianza where Jugadores.ID='.$origen);
			$idMensajeBase=Mensajes::EnviarMensaje($origen,-1,GetString('Mensaje de la alianza '.$consulta[0].':').' '.$asunto,$contenido,'alianza',$fechaEnvio);
		}

		$sql='INSERT INTO `mensajes` (`ID` ,`Asunto` ,`Tipo` ,`IdDestino` ,`Origen` ,`TipoOrigen` ,`Leido` ,`FechaEnvio`)';
		$sql2='UPDATE `jugadores` SET `MensajesSinLeer`=MensajesSinLeer+1 where';
		while($miembroAlianza=$consulta->fetch_assoc())
		{
			$sql.="VALUES (NULL , '$idMensajeBase', 'alianza', '{$miembroAlianza['ID']}' , '".($mensajeInterno==true?1:2)."' , '2' , '0', '$fechaEnvio'),";

			$sql2.=' ID='.$miembroAlianza['ID'].' ||';
		}

		$sql=substr($sql,0,strlen($sql)-1).';';

		$DB->query($sql);
		$DB->query(substr($sql2,0,strlen($sql2)-2));
	}

	private static function  ListarElementos($array)
	{
		$res='';
		foreach ($array as $idNave=>$cantidad)
		{
			$res.=$cantidad.' '.GetTechnology($idNave)->Name.', ';
		}
		if(!empty($res))
		return substr($res,0,strlen($res)-2);
		return '';
	}

	private static function TablaRecursos($recursos)
	{
		$urlSkin=$GLOBALS['jugador']->UrlSkin;

		return '<table><tr><td><img src="'.$urlSkin.'images/metal.png" /><br/>'.number_format($recursos[0],null,null,'.').'</td>
<td><img src="'.$urlSkin.'images/crystal.png" /><br/>'.number_format($recursos[1],null,null,'.').'</td>'.
(isset($recursos[2])?'<td><img src="'.$urlSkin.'images/antimatter.png" /><br/>'.number_format($recursos[2],null,null,'.').'</td>':'').'
</tr></table>';
	}
}

?>