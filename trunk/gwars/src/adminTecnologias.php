<?php
include('src/tecnologia.php');
/*
$tiempoAproximado;//Tiempo aproximado de construccion de la cola
$evento;//Evento de construccion actual


COMPROBAR EL EVENTO DE INICIO AUTOMATICO DE TECNO EN COLA CUANDO NO HAY RECURSOS
*/


/**
 * Administra colas de construccion e investigación
 */
class Cola
{
	private $tiempoAproximado;
	/**
	 * Evento de construcción de la tecnología actual
	 *
	 */
	private $evento;
	private $tipoCola;
	/**
	 * Tecnologias en el planeta más tecnologías puestas en cola
	 *
	 */
	public $TecnologiasEnCola;
	/**
	 * Cola de construccion
	 *
	 */
	public $Cola;

	/**
 * Administra una cola de construccion
 * @param integer $tipo Tipo de tecnologias administradas por la cola.
 * 
 * Tipos de cola
*1 - construccion de edificio
*2 - investigacion
*3 - hangar
*4 - defensas
 */
	function __construct($tipo)
	{
		global $planeta;

		$this->tipoCola=$tipo;


		if($tipo==2)//Investigaciones
		{
			$this->TecnologiasEnCola=$GLOBALS['jugador']->Tecnologias;
			if(empty($this->TecnologiasEnCola[20]))
			$this->TecnologiasEnCola[20]=$planeta->Tecnologias[20];//Necesario para calcular el tiempo de producción
		}
		else
		{
			$this->TecnologiasEnCola=$planeta->Tecnologias;
		}

		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		//si se estan ampliando el hangar los nanos o el laboratorio, no dejar
		if($tipo<3)//Construcciones y edificios
		{
			if(isset($_GET['tecno']) && is_numeric($_GET['tecno']))//Añadir una nueva construcción
			$this->AñadirCola($_GET['tecno']);
			else if(isset($_GET['quitarCola']) && is_numeric($_GET['quitarCola']))//Quitar un elemento de la cola
			$this->QuitarCola($_GET['quitarCola']);
			else if(isset($_GET['cancelar']))//Cancelar la producción actual
			$this->CancelarTecnologiaActual();
		}
		else//Hangar y defensas
		{
			if(!empty($_POST))
			{
				$construido=false;
				foreach($_POST as $key=>$value)
				{
					if(is_numeric($key) && is_numeric($value) && $value>0)
					{
						//if(TecnologiaDisponible($key)==false)
						//continue;

						if(in_array($key,ObtenerTecnologias($this->tipoCola,$planeta)))
						{
							Eventos::ContruccionHangar((int)$key,(int)$value,$this->tipoCola,$planeta);
							$construido=true;
						}
					}
				}
			}
		}
		$this->CargarTecnologiaEnConstruccion();
		$this->CargarCola();
	}

	/**
	 * Carga la tecnologia que esta en construccion
	 *
	 */
	function CargarTecnologiaEnConstruccion()
	{
		if($this->tipoCola==2)
		{
			global $jugador;

			if($jugador->Datos['Investigando']>0)
			$consulta=$GLOBALS['DB']->query('select * from eventos where `Jugador`='.$jugador->ID.' && `Tipo`='.$this->tipoCola.' LIMIT 1');
		}
		else
		{
			global $planeta;

			if($planeta->Trabajando($this->tipoCola))
			$consulta=$GLOBALS['DB']->query('select * from eventos where `Planeta`='.$planeta->ID.' && `Tipo`='.$this->tipoCola.' LIMIT 1');
		}

		if(isset($consulta) && $consulta->num_rows()>0)
		{
			$this->evento=$consulta->fetch_assoc();
			$datos=unserialize($this->evento['Datos']);

			$this->TecnologiasEnCola[$datos[0]]=$datos[1];
		}
	}

	/**
	 * Carga los datos de la cola
	 *
	 */
	function CargarCola()
	{
		global $planeta;

		if($this->tipoCola==2)
		{
			global $jugador;
			if(!isset($jugador->Datos['Investigando']) || $jugador->Datos['Investigando']==0)//No hay nada en cola
			return;
		}
		else
		{
			$planeta->Trabajando($this->tipoCola);
			if(!isset($planeta->Datos['Construcciones'][$this->tipoCola]) || $planeta->Datos['Construcciones'][$this->tipoCola]==0)//Construcciones y hangares
			{
				return;
			}
		}
		global $DB;

		//Cargar la cola
		if($this->tipoCola==2)//Investigaciones
		$consulta=$DB->query('select * from colas where `Jugador`='.$jugador->ID.' && `Tipo`='.$this->tipoCola.' ORDER BY `ID`');
		else//Construcciones y hangares
		$consulta=$DB->query('select * from colas where `Planeta`='.$planeta->ID.' && `Tipo`='.$this->tipoCola.' ORDER BY `ID`');

		$this->tiempoAproximado=0;
		while($accionCola = $consulta->fetch_assoc())//Carga la cola y el tiempo aproximado restante
		{
			$this->Cola[]=$accionCola;

			if($this->tipoCola<3)//Construcciones e investigaciones
			{
				$this->TecnologiasEnCola[$accionCola['Tecnologia']]+=$accionCola['Cantidad'];
				$this->tiempoAproximado+=TiempoProduccionTecnologia($accionCola['Tecnologia'],$this->TecnologiasEnCola[$accionCola['Tecnologia']]+$accionCola['Cantidad'],0,$this->TecnologiasEnCola);
			}
			else
			$this->tiempoAproximado+=TiempoProduccionTecnologia($accionCola['Tecnologia'],0,0,$planeta->Tecnologias)*$accionCola['Cantidad'];
		}
	}

	//Cancela la construccion o investigacion de la tecnologia actual
	function CancelarTecnologiaActual()
	{
		global $DB;
		global $jugador;

		$this->CargarTecnologiaEnConstruccion();

		if($this->evento['Planeta']!=$GLOBALS['planeta']->ID)
		{
			$planeta=new Planeta($this->evento['Planeta'],time());
		}
		else
		global $planeta;

		if(isset($this->evento))
		{
			$this->TecnologiasEnCola=$planeta->Tecnologias;
			$DB->query("DELETE FROM `eventos` WHERE `Jugador`={$this->evento['Jugador']} && `Fecha`={$this->evento['Fecha']} && `Tipo`={$this->evento['Tipo']} LIMIT 1");
			$datos=unserialize($this->evento['Datos']);

			$coste=CosteTecnologia($datos[0],$datos[1]);
			$planeta->ModificarRecursos($coste[0],$coste[1],$coste[2]);

			if($this->tipoCola==1)//Construcciones
			{
				if($planeta->Trabajando(1))
				unset($planeta->Datos['Construcciones'][1]);

				$planeta->Datos['CamposOcupados']--;
			}
			else if($this->tipoCola==2)//Investigaciones
			{
				$jugador->Datos['Investigando']=0;
			}

			$eventos=new Eventos();
			$eventos->ConstruirSiguienteCola($planeta,array('Planeta'=>$planeta->ID,'Jugador'=>$jugador->ID),time(),$this->tipoCola,$jugador);

			$planeta->GuardarCambios();

			if($this->tipoCola==2)
			$jugador->GuardarCambios();

			$this->evento=null;//Vaciar
		}
	}

	/**
 * Añade una nueva tecnologia a la cola, y si no hay ninguna en construccion, la inicia
 */
	function AñadirCola($tecno)
	{
		/*if(TecnologiaDisponible($tecno)==false)
		return; !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

		global $planeta;

		if(!in_array($tecno,ObtenerTecnologias($this->tipoCola,$planeta)))//Comprobar que la tecnologia corresponte a este tipo de cola
		return;

		if(TipoTecnologia($tecno)==3 && $planeta->Tecnologias[$tecno]>0)//Tecnología de un sólo nivel
		return;

		$eventos=new Eventos();
		$eventos->IniciarConstruccion($planeta,time(),$tecno,1,$this->tipoCola,$GLOBALS['jugador'],true);
	}

	/**
 * Quita una tecnología de la cola
 */
	function QuitarCola($idTecno)
	{
		global $planeta;

		if($this->tipoCola==1)//Edificio
		{
			$GLOBALS['DB']->query('DELETE FROM `colas` WHERE `Planeta` = '.$planeta->ID." && `Tecnologia` = $idTecno && `Cantidad` = 1 LIMIT 1");

			$planeta->Trabajando(1);
			if($planeta->Datos['Construcciones'][1]==-1)//Esperando para iniciar la construccion
			{
				$eventos=new Eventos();
				$eventos->ConstruirSiguienteCola($planeta,array('Planeta'=>$planeta->ID,'Jugador'=>$GLOBALS['jugador']->ID),time(),1);
			}
		}
		else if($this->tipoCola==2 && is_numeric($_GET['planeta']))//Investigacion
		{
			global $jugador;
			$GLOBALS['DB']->query("DELETE FROM `colas` WHERE `Jugador` = {$jugador->ID} && `Planeta` = {$_GET['planeta']}  && `Tecnologia` = $idTecno LIMIT 1");

			if($jugador->Datos['Investigando']==-1)//Esperando para iniciar la investigacion
			{
				$eventos=new Eventos();
				$eventos->ConstruirSiguienteCola($planeta,array('Planeta'=>$_GET['planeta'],'Jugador'=>$GLOBALS['jugador']->ID),time(),2,$jugador);
			}
		}
	}

	/**
 * Muestra información sobre la tecnología en construcción actual
 */
	function MostrarTecnologiaEnConstruccion()
	{
		if(!isset($this->evento))
		return;

		global $planeta;
		global $jugador;
		global $DB;

		$datos=unserialize($this->evento['Datos']);

		$cantidad=$datos[1];
		$tecnologia=GetTechnology($datos[0]);

		if($this->tipoCola==2)//Investigaciones
		$tiempoProduccion=$datos[2];
		else
		$tiempoProduccion=TiempoProduccionTecnologia($datos[0],$cantidad,CosteTecnologia($datos[0],$cantidad),$planeta->Tecnologias);

		$fechaFinalizacion=$this->evento['Fecha'];

		if(date('z',time())==date('z',$fechaFinalizacion))//Acaba en el mismo dia
		$formatoDia=GetString('H:i:s');
		else
		$formatoDia=GetString('j/m H:i:s');


		if($this->evento['Planeta']==$planeta->ID)
		$nombrePlaneta=$planeta->Nombre;
		else
		$nombrePlaneta=$DB->getRowProperty('planetas',$this->evento['Planeta'],'Nombre');
?>
<table class="currentQueueItemTable" id="tecnologiaActual">
  <tr>
    <th colspan="3"><a onclick="Filtro('cola')"><?php $this->tipoCola==2?EchoString('Investigación actual'):EchoString('Construcción actual'); ?></a></th>
  </tr>
  <tr>
  <td width="10"><img class="currentQueueImage" src="<?php echo $jugador->UrlSkin.'technology/'.$tecnologia->ID.'.png'?>"/></td>
    <td id="currentQueueInfo">
<?php
if($this->tipoCola==1)//Edificio
{
	echo sprintf(GetString('%s, nivel %d'),$tecnologia->Name,$cantidad);
}
else if($this->tipoCola==3 || $this->tipoCola==4)//Hangar y defensas
{
	echo $tecnologia->Name;
	if(($cantidad-1)>0)
	echo '<span id="restantes"> (<span>'.($cantidad-1).'</span> '.GetString('unidades restantes').')</span>';
}
else if($this->tipoCola==2)//Investigacion
{
	echo sprintf(GetString('Investigando %s, nivel %d (%s)'),$tecnologia->Name,$cantidad,$nombrePlaneta);
}
?><br />
<div id="tiempos"><?php EchoString('Tiempo restante')?>:&nbsp;<span id="tiempoRestante"></span><br />
<?php echo GetString('Finalización').': <span id="fechaFinalizacion">'.date($formatoDia,$fechaFinalizacion).'</span>'; ?>
<div id="progreso" class="progressDiv" style="background-color: rgb(0, 255, 0); width: 0%; text-align:center;">0%</div>
</div>
<div id="accionCompletada" style="display:none"><a onclick="Mostrar('<?php echo $_SERVER['SCRIPT_NAME'] ?>',false,null,true);"><?php EchoString('Hecho')?></a> </div></td>
<?php
if($this->tipoCola<3)//Si se pueden cancelar
echo '<td class="buildHeader" id="cancelCurrentQueue"><a onclick="Mostrar(\'?cancelar\')">'.GetString('Cancelar').'</a></td>';
?>
</tr>
</table>
<script type="text/javascript">
var tiempoProduccion=<?php echo $tiempoProduccion ?>;
var fechaFinalizacion=Math.round(new Date().getTime()/1000)+<?php echo $fechaFinalizacion-time(); ?>;
ProgresoCola();
</script>
<?php
	}

	/**
 * Muestra información sobre los elementos en cola
 */
	function MostrarCola()
	{
		if(!isset($this->Cola) || count($this->Cola)==0)
		return;

		global $planeta;

		echo '<table class="queueTable" id="tablaCola"><tr><th colspan="3"><a onclick="Filtro(\'cola\')">'.($this->tipoCola==2?GetString('Cola de investigación'):GetString('Cola de construcción'));

		if(($this->tipoCola==2 && $GLOBALS['jugador']->Datos['Investigando']==-1)
		|| ($this->tipoCola!=2 && $planeta->Datos['Construcciones'][$this->tipoCola]==-1))
		{
			echo ' - '.GetString('Esperando para reunir los recursos suficientes');
		}
		echo '</a></th></tr>';

		if(!empty($this->tiempoAproximado))
		{
			echo '<tr><th colspan="3">'.GetString('Tiempo aproximado para completar la cola').': '.SegundosAFecha($this->tiempoAproximado).'</th></tr>';
		}

		$contador=1;
		$datos=unserialize($this->evento['Datos']);

		if($this->tipoCola==2)
		{
			global $jugador;
			$nivelesTecno=$jugador->Tecnologias;
			$nombres[$planeta->ID]=$planeta->Nombre;
		}
		else
		{
			$nivelesTecno=$planeta->Tecnologias;
		}
		$nivelesTecno[$datos[0]]=$datos[1];//Añadir la tecno en construccion


		foreach ($this->Cola as $accionCola)
		{
			$nivelesTecno[$accionCola['Tecnologia']]+=$accionCola['Cantidad'];

			$textoPlaneta='';
			if($this->tipoCola==2)//Investigación
			{
				if(!isset($nombres[$accionCola['Planeta']]))
				$nombres[$accionCola['Planeta']]=$GLOBALS['DB']->getRowProperty('planetas',$accionCola['Planeta'],'Nombre');

				$textoPlaneta=' ('.$nombres[$accionCola['Planeta']].')';
				$linkQuitar='&planeta='.$accionCola['Planeta'];
			}
			if($this->tipoCola<3)//Edificios e investigaciones
			{
				echo '<tr><td>'.$contador.'.</td><td>'.sprintf(GetString('%s, nivel %s'),GetTechnology($accionCola['Tecnologia'])->Name,$nivelesTecno[$accionCola['Tecnologia']].$textoPlaneta).'</td><td><a onclick="Mostrar(\'?quitarCola='.$accionCola['Tecnologia'].$linkQuitar.'\')">'.GetString('Quitar').'</a></td></tr>';
			}
			else//Hangar y defensas
			{
				echo '<tr><td>'.$contador.'.</td><td>'.sprintf(GetString('%s, %s unidades'),GetTechnology($accionCola['Tecnologia'])->Name,$accionCola['Cantidad']).'</td></tr>';
			}
			$contador++;
		}

		echo '</table>';
	}

	/**
 * Crea una tabla que muestra información sobre una tecnologia, y la opcion de ampliar su nivel
 * @param $arrayIds array Lista de ids de tecnologias que se van a mostrar
 */
	function TablaTecnologias()
	{
		global $jugador;
		global $planeta;

		$arrayIds=ObtenerTecnologias($this->tipoCola,$planeta);

		foreach ($arrayIds as $ID)
		{
			//if(TecnologiaDisponible($ID)==false)
			//continue;

			$tipoTecno=TipoTecnologia($ID);

			$tecnologia=GetTechnology($ID);
			$nivelTecno=0;
			if($tipoTecno==4)//Investigacion
			$nivelTecno=$jugador->Tecnologias[$ID];
			else if(isset($planeta->Tecnologias[$ID]))
			$nivelTecno=$planeta->Tecnologias[$ID];

			if(isset($this->TecnologiasEnCola[$ID]))
			$nivelEnCola=$this->TecnologiasEnCola[$ID];
			else
			$nivelEnCola=$nivelTecno;

			if($tipoTecno==3 && ($nivelTecno>=1 || $nivelEnCola>=1))//Construcción de un solo nivel
			{
				$nivelTecno=$nivelEnCola=1;
				$coste=CosteTecnologia($ID,1);
			}
			else
			$coste=CosteTecnologia($ID,$nivelEnCola+1);

			//Calcular si hay recursos suficientes para la construccion
			$recursosSuficientes=true;
			$tiempoNecesario=0;
			if($coste[0]>$planeta->Metal || $coste[1]>$planeta->Cristal || $coste[2]>$planeta->Antimateria)
			{
				$recursosSuficientes=false;
				$tiempoNecesario=TiempoNecesarioInicioConstruccion($planeta->Datos,$ID,$nivelEnCola+1);
			}

			//Texto del nivel o cantidad de la tecnologia actual
			$textoNivelOCantidad='&nbsp;';
			if(empty($nivelTecno))
			$textoNivelOCantidad='';
			else if ($ID==12)//Planta de microondas
			$textoNivelOCantidad.=sprintf('(%d satélites disponibles)',$nivelTecno);
			else if ($tipoTecno==1)
			$textoNivelOCantidad.=sprintf('(Nivel %d)',$nivelTecno);
			else
			$textoNivelOCantidad.=sprintf('(%d disponibles)',$nivelTecno);

			//Texto de ampliar tecnologia
			$textoAmpliar='';

			if($ID<100 && $planeta->Datos['CamposOcupados']+count($this->Cola)>=$planeta->Datos['CamposTotales'])
			{
				$textoAmpliar= '<span style="color:red;">'.GetString('Planeta ocupado por completo').'</span>';
			}
			else if ($ID==114)//Graviton
			{
				$costeEnergia=CosteTecnologia($ID,  $nivelTecno+1);
				$costeEnergia=$costeEnergia[0];
				if($costeEnergia>$planeta->Energia)
				$recursosSuficientes=false;
			}

			switch ($tipoTecno)
			{
				case 2://Tecnologias por cantidad
				if($recursosSuficientes)
				{
					$textoAmpliar='<input type="text" name="'.$ID.'" maxlength="6" size="6" value="0"><br>('.FormatearNumero(MaximaCantidadRealizable($coste,$planeta)).' '.GetString('máx.').')';
				}
				break;

				default:
					if($tipoTecno==3 && $nivelEnCola>0)
					{
						$textoAmpliar=GetString('Sólo se puede construir una vez');
					}
					else
					{
						if($tiempoNecesario==-1)//No se produce antimateria
						break;

						$estilo='';
						if($recursosSuficientes==false)
						$estilo='style="color:red;" ';

						$textoAmpliar.='<a '.$estilo.'class="buildLink" onclick="Mostrar(\'?tecno='.$ID.'\')">';

						if (empty($nivelEnCola) || $nivelEnCola==0)
						{
							if ($ID>=100 && $ID<200)//Investigación
							$textoAmpliar.=GetString('Investigar').'</a>';
							else
							$textoAmpliar.=GetString('Construir').'</a>';
						}
						else
						$textoAmpliar.=GetString('Ampliar<br />al nivel').' '.($nivelEnCola+1).'</a>';
					}
					break;
			}
?><tr id="tecno<?php echo $ID ?>">
  <td><a onclick="Mostrar('descripcion.php?id=<?php echo $ID ?>')"><img class="technologyImage" src="<?php echo $jugador->UrlSkin ?>technology/<?php echo $ID ?>.png" /></a></td>
  <td><a onclick="Mostrar('descripcion.php?id=<?php echo $ID ?>')"><strong><?php echo $tecnologia->Name ?></strong></a><?php echo $textoNivelOCantidad ?><br />
    <?php echo $tecnologia->ShortDescription.'<br />'.GetString('Requiere').': '.$this->ObtenerTextoRecursosNecesarios($coste).'<br />'.($tipoTecno==4?GetString('Tiempo de investigaci&oacute;n'):GetString('Tiempo de construcci&oacute;n')).': '.SegundosAFecha(TiempoProduccionTecnologia($ID,$nivelEnCola+1,$coste,$this->TecnologiasEnCola));
    if($recursosSuficientes==false)
    {
    	if($tiempoNecesario==-1)
    	echo '<br />'.GetString('El planeta no produce los recursos suficientes para realizar esta tecnología.');
    	else
    	echo '<br />'.GetString('Tiempo necesario para reunir los recursos suficientes').': <strong>'.SegundosAFecha($tiempoNecesario).'</strong>';
    }
    ?>
    </td><td class="buildHeader"><?php echo $textoAmpliar ?></td></tr>
	<?php
		}
	}

	/**
 * Obtiene el texto que muestra, en la tabla de tecnologia, los recursos necesarios para iniciar la construcción del siguiente nivel
 */
	function ObtenerTextoRecursosNecesarios($coste)
	{
		//Devuelve el texto que muestra los recursos para construir. Ejemplo: Metal: 9.447 Cristal: 3.779 Antimateria: 1.889
		global $planeta;

		$requisitos='';
		if (isset($coste[3]))//Graviton
		{
			if($coste[3]>$planeta->EnergiaProducida())
			$requisitos.=GetString('Energía').':<strong><font color="red"> '.number_format($coste[3],0,'','.').'</font></strong> ';
			else
			$requisitos.=GetString('Energía').':<strong> '.number_format($coste[3],0,'','.').'</strong> ';
		}
		else
		{
			if ($coste[0]!=0)
			{
				if($coste[0]>$planeta->Metal)
				$requisitos.=GetString('Metal').': <strong><font color="red">'.number_format($coste[0],0,'','.').'</font></strong> ';
				else
				$requisitos.=GetString('Metal').': <strong>'.number_format($coste[0],0,'','.').'</strong> ';
			}
			if ($coste[1]!=0)
			{
				if($coste[1]>$planeta->Cristal)
				$requisitos.=GetString('Cristal').': <strong><font color="red">'.number_format($coste[1],0,'','.').'</font></strong> ';
				else
				$requisitos.=GetString('Cristal').': <strong>'.number_format($coste[1],0,'','.').'</strong> ';
			}
			if ($coste[2]!=0)
			{
				if($coste[2]>$planeta->Antimateria)
				$requisitos.=GetString('Antimateria').': <strong><font color="red">'.number_format($coste[2],0,'','.').'</font></strong> ';
				else
				$requisitos.=GetString('Antimateria').': <strong>'.number_format($coste[2],0,'','.').'</strong> ';
			}
		}
		return $requisitos;
	}

}


?>
