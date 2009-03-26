<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('src/funciones.php');

$mensajesMostrados=ObtenerValorCookie('mensajesMostrados',10);
$opcionesBorrar=ObtenerValorCookie('borrar','borrarMostrados');

require('src/mensajes.php');
require('basePage.php');

if(!is_numeric($mensajesMostrados))
$mensajesMostrados=10;

$filtro=$DB->escape_string(key($_GET));

if(!empty($_POST))
{
	ProcesarPost();
}

if(empty($filtro))
{
	$_GET['todos']=0;
	$filtro='todos';
}

$mensajes=new Mensajes($jugador->ID);

$tiposMensajes=$mensajes->ContarMensajes();
?>
<form method="POST">
<?php EchoString('Mensajes a mostrar'); ?>&nbsp;
<select name="mensajesMostrados" onchange="form.submit()">
<option value="5" <?php if($mensajesMostrados==5)echo 'selected="true"';?>>5</option>
<option value="10" <?php if($mensajesMostrados==10)echo 'selected="true"';?>>10</option>
<option value="15" <?php if($mensajesMostrados==15)echo 'selected="true"';?>>15</option>
<option value="20" <?php if($mensajesMostrados==20)echo 'selected="true"';?>>20</option>
<option value="30" <?php if($mensajesMostrados==30)echo 'selected="true"';?>>30</option>
<option value="50" <?php if($mensajesMostrados==50)echo 'selected="true"';?>>50</option>
</select>
</form>
<form method="POST" action="mensajes.php?<?php echo $filtro;?>">
<select name="borrar">
<option value="borrarMostrados" <?php if($opcionesBorrar=='borrarMostrados')echo 'selected="true"';?>><?php EchoString('Borrar todos los mensajes mostrados'); ?></option>
<option value="borrarMarcados" <?php if($opcionesBorrar=='borrarMarcados')echo 'selected="true"';?>><?php EchoString('Borrar todos los mensajes marcados'); ?></option>
<option value="borrarSinMarcar" <?php if($opcionesBorrar=='borrarSinMarcar')echo 'selected="true"';?>><?php EchoString('Borrar todos los mensajes sin marcar'); ?></option>
<option value="borrarTodosTipo" <?php if($opcionesBorrar=='borrarTodosTipo')echo 'selected="true"';?>><?php EchoString('Borrar todos los mensajes de este tipo'); ?></option>
<option value="borrarTodos" <?php if($opcionesBorrar=='borrarTodos')echo 'selected="true"';?>><?php EchoString('Borrar todos los mensajes'); ?></option>
</select>
<input type="submit" value="<?php EchoString('Ok'); ?>" /><br /><br />
<?php
CabeceraTab();
Tab(GetString('Todos').MostrarNumeroMensajes('todos'),'todos');
Tab(GetString('Informes').MostrarNumeroMensajes('informe'),'informe');
Tab(GetString('Alianza').MostrarNumeroMensajes('alianza'),'alianza');
Tab(GetString('Flota').MostrarNumeroMensajes('flota'),'flota');
Tab(GetString('Jugadores').MostrarNumeroMensajes('jugador'),'jugador');
Tab(GetString('Batalla').MostrarNumeroMensajes('batalla'),'batalla');
Tab(GetString('Papelera').MostrarNumeroMensajes('papelera'),'papelera');
CierreTab();
?>    
<table class="messageTable">
<?php
if(!isset($tiposMensajes[$filtro]) || $tiposMensajes[$filtro]['Disponibles']==0)
echo GetString('No hay mensajes');
else
{
	$listaMensajes=$mensajes->ObtenerMensajes($filtro,$mensajesMostrados);

	if(count($listaMensajes)==0)
	{
		echo GetString('No hay mensajes');
	}
	else
	{
		foreach ($listaMensajes as $mensaje)
		{
			$mensajes->TablaDeMensaje($mensaje);
		}

		if(isset($jugador->MensajesSinLeerCambiados) && $jugador->MensajesSinLeerCambiados==true)
		{
			$jugador->Datos['MensajesSinLeer']=max($jugador->Datos['MensajesSinLeer'],0);
			$jugador->GuardarCambios();
			echo '<script type="text/javascript">ActualizarMensajes('.max($jugador->Datos['MensajesSinLeer'],0).');</script>';
		}
	}
}
?>
</table></form></div></div>
</div></td></tr></table>
<?php

function MostrarNumeroMensajes($tipo)
{
	global $tiposMensajes;

	if(!isset($tiposMensajes[$tipo]))
	return;

	$disponibles=$tiposMensajes[$tipo]['Disponibles'];
	$sinLeer=$tiposMensajes[$tipo]['SinLeer'];

	if(!empty($sinLeer))
	return ' ('.sprintf(GetString('%s %s de %s'),$sinLeer,($sinLeer==1 ?GetString('nuevo'):GetString('nuevos')),$disponibles).')';

	if(!empty($disponibles))
	return ' ('.$disponibles.')';
}

function ProcesarPost()
{
	global $DB;
	global $jugador;
	global $filtro;

	$mensajesABorrar=$_POST['borrar'];

	if($mensajesABorrar=='borrarTodos')
	{
		if($jugador->Datos['MensajesSinLeer']!=0)
		{
			$DB->setRowProperty('jugadores',$jugador->ID,'MensajesSinLeer',0);
			$jugador->Datos['MensajesSinLeer']=0;
		}

		$DB->query('DELETE FROM `mensajes` WHERE `IdDestino` = '.$jugador->ID);
	}
	else if($mensajesABorrar=='borrarTodosTipo' && $filtro=='todos')
	{
		if($jugador->Datos['MensajesSinLeer']!=0)
		{
			$DB->setRowProperty('jugadores',$jugador->ID,'MensajesSinLeer',0);
			$jugador->Datos['MensajesSinLeer']=0;
		}

		$sql='true';
	}
	else if($mensajesABorrar=='borrarTodosTipo')
	{
		$consulta=$consulta->first_assoc('SELECT COUNT(*) from `mensajes` where `IdDestino`='.$jugador->ID.' && `Tipo`="'.$filtro.'" && Leido=0');

		$sinleer=max($jugador->Datos['MensajesSinLeer']-$consulta[0],0);
		if($jugador->Datos['MensajesSinLeer']!=$sinleer)
		{
			$DB->setRowProperty('jugadores',$jugador->ID,'MensajesSinLeer',$sinleer);
			$jugador->Datos['MensajesSinLeer']=$sinleer;
		}

		$sql='`Tipo`="'.$filtro.'"';
	}
	else
	{
		foreach($_POST as $checkbox=>$valor)
		{
			if(is_numeric($checkbox)==false)
			continue;

			if($mensajesABorrar=='borrarMostrados')
			$sql.=' OR `ID` ='.$checkbox;
			if ($valor!=0 && $mensajesABorrar=='borrarMarcados')
			{
				$sql.=' OR `ID` ='.$checkbox;
			}
			else if($valor==0 && $mensajesABorrar=='borrarSinMarcar')
			{
				$sql.=' OR `ID` ='.$checkbox;
			}
		}
		if(!empty($sql))
		$sql='('.strstr($sql,'`ID').')';
	}

	if(!empty($sql))
	{
		if($filtro!='papelera')
		$DB->query('UPDATE `mensajes` SET `Tipo`="papelera" WHERE `IdDestino` = '.$jugador->ID.' && '.$sql);
		else
		$DB->query('DELETE FROM `mensajes` WHERE `IdDestino` = '.$jugador->ID.' && '.$sql);
	}
	echo '<script type="text/javascript">ActualizarMensajes('.max($jugador->Datos['MensajesSinLeer'],0).');</script>';
}
ActualizarDatosCabecera();
?>