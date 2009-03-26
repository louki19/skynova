<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');
include('src/alianza.php');

if(isset($_POST['nuevaAlianza']) && !empty($_POST['nombreAlianza']) && $jugador->Datos['Alianza']==0 )
{
	Alianza::CrearNuevaAlianza($_POST['nombreAlianza']);
}

if(is_numeric($_GET['alianza']))
{
	$alianza=new Alianza($_GET['alianza']);
}
else//Mostrar alianza del jugador
{
	if($jugador->Datos['Alianza']==0)
	{
		if(!isset($_GET['nueva']))
		{
			echo GetString('No estás registrado en ninguna alianza.').'<br><br>';
		}
		$solicitud=Solicitudes::ObtenerSolicitud($jugador->ID,1);
		if($solicitud!=false)
		{
			printf(GetString('Ya has enviado una solicitud de acceso a la alianza <a onclick="Mostrar(\'alianza.php?alianza=%d\')">%s</a>. Espera su respuesta.'),
			$solicitud,$DB->getRowProperty('alianzas',$solicitud,'Nombre'));
		}
		else
		{
			if(isset($_GET['nueva']))
			{
				?>
				<h2><?php EchoString('Crear nueva alianza');?></h2><br><br>
				<form method="POST" action="alianza.php">
				<input type="hidden" name="nuevaAlianza">
				<label><?php EchoString('Nombre de la alianza');?> <input type="text" name="nombreAlianza" maxlength="30" size="30"></label><br><br>
				<input type="submit" value=" <?php EchoString('Continuar') ?>" />
				</form>
				<?
			}
			else
			echo '<span style="font-weight:normal;">'.GetString('Si quieres, puedes <a onclick="Mostrar(\'buscar.php?filtro=alianza\')">unirte</a> a una alianza enviándole una solicitud de acceso, o <a onclick="Mostrar(\'alianza.php?nueva\')">crear</a> una alianza propia.').'</span';
		}
		exit;
	}
	$alianza=new Alianza($jugador->Datos['Alianza']);
}

$alianzaDelJugador=$alianza->ID==$jugador->Datos['Alianza'];

//Datos de la alianza actual
$datosAlianza=$alianza->ObtenerDatos();

// Cargar el rango
$rango=new Rango($jugador->Datos['RangoAlianza']);
if($alianzaDelJugador)
$rango->ObtenerCompetencias();
else
$rango->ObtenerCompetencias('AdministrarPactos,RepresentarAlianza');

//Procesar comandos
if(!empty($_GET) && $alianzaDelJugador==false)
{
	$alianzaJugador=new Alianza($jugador->Datos['Alianza']);//Alianza del jugador actual
	if($rango->PoseeCompetencia('AdministrarPactos'))
	{
		if(isset($_GET['cancelarPNA']))
		$alianzaJugador->FinalizarPNA($alianza->ID,$alianza->Nombre());
		if(isset($_GET['solicitudPna']))
		$alianzaJugador->SolicitudPNA($alianza->ID,$alianza->Nombre());
		else if(isset($_GET['finalizarGuerra']))
		$alianzaJugador->FinalizarGuerra($alianza->ID,$alianza->Nombre());
		else if(isset($_GET['retirarPna']))
		$alianzaJugador->RetirarSolicitudPNA($alianza->ID,$alianza->Nombre());
	}
	if(isset($_GET['declararGuerra'])  && $rango->PoseeCompetencia('RepresentarAlianza'))
	$alianzaJugador->IniciarGuerra($alianza->ID,$alianza->Nombre());
}
?>
<table class="allianceTable">
<tr>
<th><?php EchoString('Nombre'); ?></th>
<th><?php echo $datosAlianza['Nombre']; ?></th>
</tr>
<tr>
<td><?php EchoString('Puntos'); ?></td>
<td>
<?php 
$consulta=$DB->query('SELECT COUNT( * ) FROM `alianzas`');
$alianzasTotales=$consulta->fetch_row();

$ranking='<a onclick="Mostrar(\'estadisticas.php?mostrar=alianzas&marcar='.$datosAlianza['Ranking'].'&posiciones='.(((int)($datosAlianza['Ranking']/100))*100).'\')">'.$datosAlianza['Ranking'].'</a>';
printf(GetString('%s (Lugar %s de %s)'),$datosAlianza['Puntos'],$ranking,$alianzasTotales[0]);
?>
</td>
</tr>
<tr>
<td><?php EchoString('Miembros'); ?></td>
<td><?php 
echo $datosAlianza['Miembros'];

if($alianzaDelJugador && $rango->PoseeCompetencia('VerListaMiembros'))
echo ' <a onclick="Mostrar(\'listaMiembros.php\')">'.GetString('(Lista de miembros)').'</a>';
?></td>
</tr>
<?php
if($alianzaDelJugador)
{
	$infoRango='<div id="rango" class="allianceStatusInfo" style="height:0px;overflow: hidden;">';
	if($rango->PoseeCompetencia('Fundador'))
	$infoRango.=GetString('Eres el <font color="lime">fundador</font> de la alianza');
	$infoRango.=ObtenerCompetencia($rango,'VerListaMiembros', GetString('ver la lista de miembros'));
	$infoRango.=ObtenerCompetencia($rango,'AdministrarAlianza', GetString('administrar la alianza'));
	$infoRango.=ObtenerCompetencia($rango,'ExpulsarMiembro', GetString('expulsar miembros'));
	$infoRango.=ObtenerCompetencia($rango,'RevisarSolicitudes', GetString('revisar las solicitudes de acceso'));
	$infoRango.=ObtenerCompetencia($rango,'CrearCC', GetString('enviar correos circulares'));
	$infoRango.=ObtenerCompetencia($rango,'RepresentarAlianza', GetString('representar a la alianza'));
	$infoRango.=ObtenerCompetencia($rango,'AdministrarPactos', GetString('administrar los pactos y guerras'));
	$infoRango.='</div>';

	echo '<tr><td>'.GetString('Rango').'</td><td>'.$rango->Nombre().' <a id="infoRango" title="'.GetString('Mostrar información del rango').'" onclick = "MostrarInformacionRango();">[+]</a>'.$infoRango.'</td></tr>';
}
?>
<tr>
<td><?php EchoString('P&aacute;gina de la alianza'); ?></td>
<td><?php echo '<a target="_blank" href="'.$datosAlianza['UrlWeb'].'" >'.$datosAlianza['UrlWeb'].'</a>' ?></td>
</tr>

<tr>
<td><?php EchoString('Herramientas'); ?></td>
<td>
<?php
$pactos=$alianza->ObtenerRelaciones();

if($alianzaDelJugador)//Alianza propia
{
	if($rango->PoseeCompetencia('CrearCC'))
	{
		echo '<a onclick="Mostrar(\'enviarMensaje.php?circular\')" >'.GetString('Enviar mensaje circular').'</a><br/>';
	}
	if($rango->PoseeCompetencia('AdministrarAlianza'))
	{
		echo '<a onclick="Mostrar(\'administrarAlianza.php\')" >'.GetString('Administrar alianza').'</a>';
	}
}
else//Alianza externa
{
	if($jugador->Datos['Alianza']==0)//Jugador sin alianza, opciones para enviar solicitud
	{
		$solicitudAcceso=Solicitudes::ObtenerSolicitud($jugador->ID,1);
		if($solicitudAcceso==false && $datosAlianza['SolicitudesDenegadas']==0)//No se ha enviando ninguna solicitud
		{
			echo '<a onclick="Mostrar(\'solicitud.php?alianza='.$alianza->ID.'&tipo=acceso\')">'.GetString('Solicitar acceso a la alianza').'</a><br>';
		}
		else if($solicitudAcceso==$alianza->ID)//Ya se ha enviado una solicitud a esta alianza
		{
			echo '<a onclick="Mostrar(\'solicitud.php?alianza='.$alianza->ID.'&tipo=retirarAcceso\')">'.GetString('Retirar solicitud de acceso').'</a><br>';
		}
	}
	else
	{
		//Mostrar herramientas para alianzas que no son la propia
		if($rango->PoseeCompetencia('RepresentarAlianza'))
		{
			echo '<a onclick="Mostrar(\'enviarMensaje.php?alianza='.$alianza->ID.'\')" >'.GetString('Enviar mensaje a la alianza').'</a><br/>';
		}
		if($rango->PoseeCompetencia('AdministrarPactos'))
		{
			switch ($alianza->ObtenerRelacionesDisponibles($jugador->Datos['Alianza'],$pactos))
			{
				case 1:
					echo '<a onclick="Mostrar(\'?alianza='.$alianza->ID.'&cancelarPNA\')">'.GetString('Cancelar pacto de no agresión').'</a><br>';
					break;
				case 2:
					echo '<a onclick="Mostrar(\'?alianza='.$alianza->ID.'&finalizarGuerra\')">'.GetString('Finalizar la guerra').'</a>';
					break;
				case 3:
					echo '<a onclick="Mostrar(\'alianza.php?alianza='.$alianza->ID.'&retirarPna\')">'.GetString('Retirar solicitud de pacto de no agresión').'</a><br>';
					break;
				case 4:
					echo '<a onclick="Mostrar(\'?alianza='.$alianza->ID.'&solicitudPna\')">'.GetString('Solicitar pacto de no agresión').'</a><br>';
					echo '<a onclick="Mostrar(\'?alianza='.$alianza->ID.'&declararGuerra\')">'.GetString('Declarar guerra').'</a>';
					break;
			}
		}
	}
}
?>
</td>
</tr>
<?php 
if(!empty($datosAlianza['UrlLogo']))
echo '<tr><td colspan="2"><img class="allianceLogo" src="'.$datosAlianza['UrlLogo'].'"/></td></tr>';

if(isset($pactos['PNA']))
{
	echo '<tr><td colspan="2">'.GetString('Pactos de no agresi&oacute;n').'<br><br>';
	foreach ($pactos['PNA'] as $IdAlianza=>$nombre)
	{
		echo '<a onclick="Mostrar(\'alianza.php?alianza='.$IdAlianza.'\')">'.$nombre.'</a><br/>';
	}
	echo '</td></tr>';
}

if(isset($pactos[0]['Guerra']) || isset($pactos[1]['Guerra']))
{
	echo '<tr><td colspan="2">'.GetString('Guerras').'<br><br>';
	if(isset($pactos[0]['Guerra']))
	foreach ($pactos[0]['Guerra'] as $IdAlianza=>$nombre)
	{
		echo '<a onclick="Mostrar(\'alianza.php?alianza='.$IdAlianza.'\')">'.$nombre.'</a><br/>';
	}
	if(isset($pactos[1]['Guerra']))
	foreach ($pactos[1]['Guerra'] as $IdAlianza=>$nombre)
	{
		echo '<a onclick="Mostrar(\'alianza.php?alianza='.$IdAlianza.'\')">'.$nombre.'</a><br/>';
	}
	echo  '</td></tr>';
}

if(!empty($datosAlianza['SeccionExterna']))
echo '<tr><td colspan="2">'.DescomprimirTexto($datosAlianza['SeccionExterna']).'</td></tr>';

if($alianzaDelJugador && !empty($datosAlianza['SeccionInterna']))
echo '<tr><td colspan="2">'.DescomprimirTexto($datosAlianza['SeccionInterna']).'</td></tr>';
?>
</table>
<?php 

/* Funciones */

/**
 * Obtiene la descripcion sobre una competencia del rango
 *
 * @param Rango $rango
 */
function ObtenerCompetencia(&$rango, $nombreCompetencia,$texto)
{
	if($rango->PoseeCompetencia($nombreCompetencia))
	$resultado='<br><font color="lime">'.GetString('Puedes').'</font>';
	else
	$resultado='<br><font color="red">'.GetString('No puedes').'</font>';

	return $resultado.' '.$texto;
}

ActualizarDatosCabecera();
?>