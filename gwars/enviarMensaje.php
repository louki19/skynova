<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

$tipoDestinatario; //1 - Jugador 2 - Alianza
$destino;
$circular=false;
if(isset($_GET['alianza']))
{
	$tipoDestinatario=2;
	$destino=$_GET['alianza'];
}
if(isset($_GET['jugador']))
{
	$tipoDestinatario=1;
	$destino=$_GET['jugador'];
}
if(isset($_GET['circular']))
{
	$tipoDestinatario=2;
	$destino=$jugador->Datos['Alianza'];
	$circular=true;
}

if(is_numeric($destino)==false)
MostrarError(GetString('Error de parámetros'),true);

if($tipoDestinatario==2 && $destino!=$jugador->Datos['Alianza'] && Rango::PoseeCompetenciaRango($jugador->Datos['RangoAlianza'],'RepresentarAlianza')==false)
MostrarError(GetString('No tienes privilegios para representar a tu alianza.'),true);

if(isset($_POST['contenido']))
{
	include('src/mensajes.php');

	$_POST['contenido']=htmlentities(str_replace("\r\n",'<br>',$_POST['contenido']));
	$_POST['asunto']=htmlentities($_POST['asunto']);
	if($tipoDestinatario==2)//Enviar mensaje a alianza
	{
		if(isset($_POST['rangosDestino']) && is_numeric($_POST['rangosDestino']))
		$rangos=$_POST['rangosDestino'];
		else
		$rangos=null;

		Mensajes::EnviarMensajeAlianza($jugador->ID,$destino,$_POST['asunto'],$_POST['contenido'],$circular,$rangos,$circular==true?null:'RepresentarAlianza');
	}
	else if($tipoDestinatario==1)//Enviar a un solo jugador
	{
		Mensajes::EnviarMensaje($jugador->ID,$destino,$_POST['asunto'],$_POST['contenido'],'jugador');
	}
	MostrarError('<font color="#00ff00">'.GetString('Mensaje enviado con éxito').'</font>');
}
else
{
?>

<form name="mensaje" method="POST" action="">
<table class="sendMessageTable">
  <tr><th colspan="2"><? EchoString('Enviar mensaje'); echo $circular==true?' '.GetString('circular'):''; ?></th></tr>
    <tr><td><? EchoString('Destinatario'); ?></td>
    <td><?php 
    if($tipoDestinatario==2 )//Alianza
    {
    	if($destino!=$jugador->Datos['Alianza'])
    	{
    		echo GetString('Alianza').' '.$DB->getRowProperty('alianzas',$destino,'Nombre');
    	}
    	else
    	{
    		echo '<select name="rangosDestino"><option value="todos">'.GetString('Todos los miembros').'</option>';

    		if($destino==$jugador->Datos['Alianza'] )
    		{
    			$consulta=$DB->query("select `Nombre`, `ID` from `rangosalianza` where `Alianza`=$destino");
    			while($rango=$consulta->fetch_assoc())
    			{
    				echo '<option value="'.$rango['ID'].'">'.GetString('Solo del rango').' '.$rango['Nombre'].'</option>';
    			}
    		}
    		echo '</select>';
    	}
    }
    else if($tipoDestinatario==1)
    {
    	$jugadorInfo=$DB->getRowProperties('jugadores',$destino,'Nombre,MetaDatos');
    	$metadatos=unserialize($jugadorInfo['MetaDatos']);

    	echo $jugadorInfo['Nombre'].'&nbsp;<img src="http://www.gravatar.com/avatar.php?gravatar_id='.md5(isset($metadatos['IdGravatar'])?$metadatos['IdGravatar']:$metadatos['Email']).'&rating=G"></div>';
    }
    ?></td></tr>
    <tr><td><? EchoString('Asunto'); ?></td>
    <td><input type="text" name="asunto" size="75"></td></tr>
  <tr><td><? EchoString('Contenido'); ?></td>
    <td><textarea onfocus="TextAreaGrow(this)" name="contenido" class="textAreaSendMessage" cols="75"></textarea></td></tr>
  <tr><td colspan="2"><input type="submit" value="<? EchoString('Enviar'); ?>"></td></tr>
</table>
</form>
<?php
}
?>