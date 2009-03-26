<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php'); 

$alianza=$_GET['alianza'];

if(is_numeric($alianza)==false)
MostrarError(GetString('Error de parámetros'),true);

$rango=$DB->getRowProperties('rangosalianza',$jugador->Datos['RangoAlianza'],'RepresentarAlianza');

if($_GET['tipo']=='pna' && empty($rango['RepresentarAlianza']))
MostrarError(GetString('No tienes suficientes privilegios para poder acceder a esta sección'));

if($_GET['tipo']=='retirarAcceso')
{
	include_once('src/alianzas.php');
	$idAlianza=ExisteSolicitudAccesoAlianza($jugador->ID)!=0;
	if($idAlianza!=0)
	{
		$DB->query('DELETE FROM `solicitudaccesoalianza` WHERE `Jugador` = '.$jugador->ID);
		
		MostrarError('<font color="lime">'.GetString('La solicitud de acceso ha sido borrada.').'</font>');
	}
	else
	MostrarError(GetString('No hay ninguna solicitud de acceso a una alianza.'),true);
}


if(!empty($_POST))
{
		include_once('src/mensaje.php');
		
	if($_GET['tipo']=='pna')
	{
		$consulta=$DB->first_row('SELECT COUNT( * ) FROM `solicitudpna` WHERE `Alianza1` ='.$jugador->Datos['Alianza'].' && `Alianza2` ='.$alianza);

		if($consulta[0]>0)
		MostrarError('<font color="red">'.GetString('Ya hay una solicitud enviada a esta alianza.',true).'</font>');
		else
		{
			$DB->query("INSERT INTO `solicitudpna` ( `Alianza1` , `Alianza2` , `Texto` )
VALUES ('{$jugador->Datos['Alianza']}', '$alianza', '".ComprimirTexto($_POST['texto'])."');");

			EnviarMensajeAlianzaRangoEspecifico($alianza,GetString('Administración de la alianza'),
			GetString('Nueva solicitud de pacto de no agresión'),
			sprintf(GetString('La alianza %s ha enviado una solicitud para un pacto de no agresión. Pulsa <a onclick="Mostrar(\'administrarAlianza.php?pactos\')">aquí</a> para revisarlo.'),
			$jugador->Nombre),'alianza','AdministrarPactos');

			MostrarError('<font color="lime">'.GetString('Solicitud de pacto de no agresión enviada con éxito').'</font>');
		}
	}
	else if($_GET['tipo']=='acceso')
	{
		include_once('src/alianzas.php');
		if(ExisteSolicitudAccesoAlianza($jugador->ID)!=0)
		{
			MostrarError('<font color="red">'.GetString('Ya hay una solicitud enviada a una alianza.',true).'</font>');
		}
		else if($DB->getRowProperty('alianzas',$alianza,'SolicitudesDenegadas')==0)
		{
			$DB->query("INSERT INTO `solicitudaccesoalianza` ( `Alianza` , `Jugador` , `Texto` )VALUES ('$alianza', '{$jugador->ID}', '".ComprimirTexto($_POST['texto'])."');");

			EnviarMensajeAlianzaRangoEspecifico($alianza,GetString('Administración de la alianza'),
			GetString('Nueva solicitud de acceso'),
			sprintf(GetString('El jugador %s ha enviado una nueva solictud de acceso. Pulsa <a onclick="Mostrar(\'administrarAlianza.php?solicitudes\')">aquí</a> para revisarla.'),
			$jugador->Nombre),'alianza','RevisarSolicitudes');

			MostrarError('<font color="lime">'.GetString('Solicitud de acceso enviada con éxito').'</font>');
		}
	}
}
else
{
?>
<form method="POST" action="">
<table class="requestTable">
<tr>
<th colspan="2"><?php EchoString('Envío de solicitud'); ?></th>
</tr>
<tr>
<td><?php EchoString('Tipo de solicitud');?></td>
<td>
<?php 
if($_GET['tipo']=='pna')
EchoString('Solicitud de pacto de no agresión');
else if($_GET['tipo']=='acceso')
EchoString('Acceso a la alianza');
?>
</td>
</tr>
<tr>
<td><?php EchoString('Texto de la solicitud'); ?></td>
<td><textarea onfocus="TextAreaGrow(this)" name="texto" cols="50"><?php 
if($_GET['tipo']=='acceso')
echo DescomprimirTexto($DB->getRowProperty('alianzas',$alianza,'PlantillaSolicitud'));
?></textarea></td>
</tr>
<tr><td colspan="2"><input type="submit" value="<?php EchoString('Enviar'); ?>"></td></tr>
</table>
</form>
<?php
} ?>