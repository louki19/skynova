<?php 
/*
Muestra el panel de administración de los eventos del jugador

Requiere la presencia de un array $planetas para cargar los nombres
*/
$metadatos=$jugador->CargarMetadatos();
$consulta=$DB->query('SELECT * FROM `eventos` WHERE `Jugador`='.$jugador->ID.' && `Fecha`>'.time().(isset($metadatos['SN'])?' && `Tipo`>6':'').' ORDER BY `Fecha` ASC');

if($consulta->num_rows()>0)
{
	$contador=0;
?>
<h2 onclick="ExpandirContraerPanelEventos()" style="cursor:pointer" title="<?php EchoString('Expandir y contraer el panel de eventos'); ?>"><?php EchoString('Eventos'); ?></h2>
<div id="panelEventos">
<table class="eventsTable">
<?php 
while($evento=$consulta->fetch_assoc())
{
	$arrayFechas.='finEventos['.$contador.']=new Date(new Date().getTime()+'.(($evento['Fecha']-time())*1000).');
';//Fecha local para finalización del evento

	//Procesar los datos del evento
	$datosEvento=unserialize($evento['Datos']);

	switch ($evento['Tipo'])
	{
		case 1://Final de edificio
		$infoEvento=sprintf(GetString('Fin de construcción de %s nivel %d en el planeta %s'),GetTechnology($datosEvento[0])->Name,$datosEvento[1],$planetas[$evento['Planeta']]['Nombre']);
		break;

		case 2://Final de investigación
		$infoEvento=sprintf(GetString('Fin de investigación de %s al nivel %d en el planeta %s'),GetTechnology($datosEvento[0])->Name,$datosEvento[1],$planetas[$evento['Planeta']]['Nombre']);
		break;

		case 3://Final de construccion de hangar
		case 4://Final de construccion de defensa
		$infoEvento=sprintf(GetString('Fin de construcción de %s en el planeta %s'),GetTechnology($datosEvento[0])->Name,$planetas[$evento['Planeta']]['Nombre']);
		break;

		case 7://Llegada de flota
		case 8://Retorno de flota
		include_once('src/flota.php');
		$flotaEvento=new FlotaEvento();
		$flotaEvento->CargarEvento($evento,$datosEvento);

		$naves='';
		foreach ($flotaEvento->Naves as $id=>$cantidad)
		{
			$naves.=GetTechnology($id)->Name.': '.$cantidad.'<br>';
		}

		$tip='<table class=\'fleetToolTip\'><tr><th colspan=\'3\'>'.GetString('Naves').'</th></tr><tr><td colspan=\'3\'>'.$naves.'</td></tr><tr><th colspan=\'3\'>'.GetString('Recursos').'</th></tr><tr><td><img src=\''.$jugador->UrlSkin.'images/metal.png\' /></td><td><img src=\''.$jugador->UrlSkin.'images/crystal.png\' /></td><td><img src=\''.$jugador->UrlSkin.'images/antimatter.png\' /></td></tr><tr><td>'.FormatearNumero($flotaEvento->Recursos[0]).'</td><td>'.FormatearNumero($flotaEvento->Recursos[1]).'</td><td>'.FormatearNumero($flotaEvento->Recursos[2]).'</td></tr>';
		$script='';
		if($evento['Tipo']==7)//Llegada
		{
			$tip.='<tr><th colspan=\'3\'>'.GetString('Hora prevista de regreso').'</th></tr><tr><td colspan=\'3\'><span id=\'fechaRetorno'.$evento['ID'].'\'></span> <input type=\'button\' value=\''.GetString('Retornar').'\'></td></tr>';

			$fechaRegreso=$evento['Fecha']+$flotaEvento->TiempoVuelo;
			$script='<script type=\'text/javascript\'>gid(\'fechaRetorno'.$evento['ID'].'\').innerHTML=formatDate(new Date(finEventos['.$contador.'].getTime()+'.($flotaEvento->TiempoVuelo*1000).'),'.(date('z',time())==date('z',$fechaRegreso)?'formatoTiempo':'formatoFecha').');</script>';
		}
		$tip.='</table>';

		if($evento['Jugador']==$jugador->ID)
		$tipoFlota=GetString('Una de tus flotas');
		else
		$tipoFlota=GetString('Una flota enemiga');

		$datosOrigen=DatosPlaneta($flotaEvento->IdOrigen);
		$datosDestino=DatosPlaneta($flotaEvento->IdDestino);


		if($evento['Tipo']==7)//Llegada
		$textoEvento=GetString('%s de %s llega a %s. La misión es: %s.');
		else//Regreso
		$textoEvento=GetString('%s vuelve de %s a %s. La misión era: %s.');

		$infoEvento='<span htmltip="true" tip="'.$tip.$script.'">'.sprintf($textoEvento,$tipoFlota,MostrarLocalizacionPlaneta($datosOrigen,true),MostrarLocalizacionPlaneta($datosDestino,true),$flotaEvento->TextoMision()).'</span>';
		break;
	}
	echo '<tr id="evento'.$contador.'"><td><span class="eventTime"></span><br/><span class="eventFinish"></span></td><td>'.$infoEvento.'</td></tr>';

	$contador++;
}
?>
</table></div>
<?php
if($contador>0)
{
	echo '<script type="text/javascript">
var finEventos=Array('.$contador.');
'.$arrayFechas.'CuentaAtrasEventos(true);
</script>';	
}
}

$datosPlanetas=array();
function DatosPlaneta($id)
{
	global $planetas;

	if(isset($planetas[$id]))
	return $planetas[$id];

	global $datosPlanetas;

	if(isset($datosPlanetas[$id]))
	{
		return $datosPlanetas[$id];
	}
	else
	{
		$datosPlanetas[$id]=$GLOBALS['DB']->getRowProperties('planetas',$id,'Nombre,Galaxia,Sistema,Posicion');
		return $datosPlanetas[$id];
	}
}
?>
