<?php
/**
 * Crea y devuelve el resumen de una batalla
 * 
 * @param ResultadoBatalla $batalla
 * @param Flota $infoFlota Datos sobre el envio de la flota(Destino, distancia, consumo,...)
 * @return string
 */
function ResumenBatalla($batalla,$infoFlota=null)
{
	if($batalla->Ganador==0)
	$textoGanador=GetString('Batalla terminada en empate');
	else if($batalla->Ganador==1)
	{
		$textoGanador=GetString('Gana el atacante');

		if(array_sum($batalla->RecursosRobados)<=0)
		$textoRecursosRobados=GetString('El atacante no se ha llevado nada.');
		else
		$textoRecursosRobados=sprintf(GetString('Recursos robados: <strong>%s</strong> Metal, <strong>%s</strong> Cristal, <strong>%s</strong> Antimateria'),SeparadorMiles($batalla->RecursosRobados[0]),SeparadorMiles($batalla->RecursosRobados[1]),SeparadorMiles($batalla->RecursosRobados[2]));

		if($batalla->RecursosRestantes>0)
		$textoRecursosRobados.=' '.sprintf(GetString('(<strong>%s</strong> recursos restantes)'),SeparadorMiles($batalla->RecursosRestantes));

		$textoRecursosRobados.='<br/>';
	}
	else if($batalla->Ganador==2)
	$textoGanador=GetString('Gana el defensor');

	$escombros=array_sum($batalla->Escombros);
	$recicladores=ceil($escombros/CaracteristicasBaseArmamento(302)->CapacidadCarga);

	return '<h2>'.GetString('Resumen de batalla').'</h2><h3>'.$textoGanador.'</h3>'.
	sprintf(GetString('Escombros generados: <strong>%s</strong> Metal, <strong>%s</strong> Cristal (<strong>%s</strong> Recicladores)'),
	SeparadorMiles($batalla->Escombros[0]),SeparadorMiles($batalla->Escombros[1]),$recicladores).'<br/>'.$textoRecursosRobados.
	GetString('Probabilidad de luna:').' <strong>'.$batalla->ProbabilidadLuna.'%</strong>'.'<br/>'.
	sprintf(GetString('La batalla duró <strong>%s</strong> rondas'),$batalla->Rondas).
	'<h2>'.GetString('Gasto de la flota').'</h2>'
	.sprintf(GetString('Distancia recorrida: desde <strong>%d:%d:%d</strong> a <strong>%d:%d:%d</strong> (%s km).'),$infoFlota->Origen[0],$infoFlota->Origen[1],$infoFlota->Origen[2],$infoFlota->Destino[0],$infoFlota->Destino[1],$infoFlota->Destino[2],SeparadorMiles($infoFlota->Distancia)).'<br/>'
	.sprintf(GetString('Consumo: <strong>%s unidades</strong> de antimateria.'),SeparadorMiles($infoFlota->Consumo)).'<br/>'
	.sprintf(GetString('Capacidad de carga: <strong>%s unidades</strong>.'),SeparadorMiles($infoFlota->Capacidad)).'<br/>'
	.sprintf(GetString('Velocidad máxima de vuelo: <strong>%s km/s</strong>.'),SeparadorMiles($infoFlota->Velocidad)).'<br/>'
	.'<h2>'.GetString('Resumen de daños').'</h2>'.
	MostrarResumenNaves($batalla->UnidadesAtacante,GetString('Atacantes')).
	MostrarResumenNaves($batalla->UnidadesDefensor,GetString('Defensores')).
	'<h2>'.GetString('Resumen de pérdidas').'</h2>'.MostrarResumenPerdidas($batalla,$infoFlota);
}

/**
 * Crea el resumen de las naves iniciales y destruidas
 * 
 * @param Ejercito $Ejercito
 * @return string
 */
function MostrarResumenNaves($Ejercito,$texto)
{
	$resumen='<h3>'.$texto.'</h3>';

	foreach($Ejercito->Flotas as $flota)
	{
		$resumen.= '<h4>'.$flota->IdJugador.'</h4>';
		if(count($flota->Unidades)>0)
		{
			foreach($flota->Unidades as $unidad)
			{
				$resumen.= $unidad->Nombre.' <span style="color:coral">'.$unidad->CantidadInicioBatalla.'</span> ';
				if($unidad->Destruidas>=$unidad->CantidadInicioBatalla)
				$resumen.='<span style="color:red">'.GetString('Todas destruidas');
				else if($unidad->Destruidas==0)
				$resumen.='<span style="color:lime">'.GetString('Ninguna destruida');
				else
				$resumen.=GetString('Perdió').' <span style="color:red">'.$unidad->Destruidas;

				$resumen.='</span><br/>';
			}
			if(array_sum($flota->Perdidas)<=0)
			$resumen.= GetString('Sin pérdidas').'<br/><br/>';
			else
			$resumen.= sprintf(GetString('Pérdidas totales: %s Metal, %s Cristal, %s Antimateria'),
			SeparadorMiles($flota->Perdidas[0]),SeparadorMiles($flota->Perdidas[1]),SeparadorMiles($flota->Perdidas[2])).'<br/><br/>';
		}
		else
		$resumen.=GetString('Sin unidades');
	}
	return $resumen;
}

/**
 * Crea el resumen de las perdidas de los distintos ejercitos
 * 
 * @return string
 */
function MostrarResumenPerdidas($batalla,$infoFlota)
{
	$escombros=array_sum($batalla->Escombros);
	$batalla->UnidadesAtacante->Perdidas[2]+=$infoFlota->Consumo;
	$perdidasAtacante=array_sum($batalla->UnidadesAtacante->Perdidas);
	$perdidasDefensor=array_sum($batalla->UnidadesDefensor->Perdidas)+array_sum($batalla->RecursosRobados);
	$beneficiosAtacante=$escombros+array_sum($batalla->RecursosRobados)-$perdidasAtacante;
	$beneficiosDefensor=$escombros-$perdidasDefensor;

	$resultado=sprintf(GetString('Pérdidas totales del ejército atacante: <strong>%s unidades</strong> (%s Metal, %s Cristal, %s Antimateria)'),
	SeparadorMiles($perdidasAtacante),
	SeparadorMiles($batalla->UnidadesAtacante->Perdidas[0]),
	SeparadorMiles($batalla->UnidadesAtacante->Perdidas[1]),
	SeparadorMiles($batalla->UnidadesAtacante->Perdidas[2])).'<br/>'
	.sprintf(GetString('Rentabilidad del atacante (Con/sin reciclaje): %s (%s) / %s (%s) unidades.'),
	SeparadorMiles($beneficiosAtacante,true),
	SeparadorMiles(round($perdidasAtacante==0?$beneficiosAtacante:$beneficiosAtacante/$perdidasAtacante*100),true,'%'),
	SeparadorMiles($beneficiosAtacante-$escombros,true),
	SeparadorMiles(round($perdidasAtacante==0?$beneficiosAtacante-$escombros:($beneficiosAtacante-$escombros)/$perdidasAtacante*100),true,'%')).'<br/>'
	.sprintf(GetString('Pérdidas totales del ejército defensor: <strong>%s unidades</strong> (%s Metal, %s Cristal, %s Antimateria)'),
	SeparadorMiles($perdidasDefensor),
	SeparadorMiles($batalla->UnidadesDefensor->Perdidas[0]+$batalla->RecursosRobados[0]),
	SeparadorMiles($batalla->UnidadesDefensor->Perdidas[1]+$batalla->RecursosRobados[1]),
	SeparadorMiles($batalla->UnidadesDefensor->Perdidas[2]+$batalla->RecursosRobados[2])).'<br/>'
	.sprintf(GetString('Rentabilidad del defensor si recicla: %s (%s) unidades.'),
	SeparadorMiles($beneficiosDefensor,true),
	SeparadorMiles(round($perdidasDefensor==0?$beneficiosDefensor:$beneficiosDefensor/$perdidasDefensor*100),true,'%'));
	return $resultado;
}

function SeparadorMiles($numero,$color=false,$textoExtra=null)
{
	if($color==true)
	{
		if($numero>0)
		return '<span style="color:lime">'.number_format($numero,null,null,'.').$textoExtra.'</span>';
		if($numero<0)
		return '<span style="color:red">'.number_format($numero,null,null,'.').$textoExtra.'</span>';
		if($numero==0)
		return '<span style="color:coral">'.number_format($numero,null,null,'.').$textoExtra.'</span>';
	}
	else
	{
		if(isset($textoExtra))
		return number_format($numero,null,null,'.').$textoExtra;
		else
		return number_format($numero,null,null,'.');
	}
}
?>