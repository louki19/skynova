<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

if(!empty($_GET))
{
	$textoBusqueda=$DB->escape_string($_GET['texto']);

	$inicio=$_GET['inicio'];
	if(!is_numeric($inicio))
	$inicio=0;
}
$tamañoPagina=15;

?>
<form method="get" action="buscar.php">
<table class="searchTable">
  <tr><th><?php EchoString('Buscar'); ?></th></tr>
  <tr><td>
    <select name="filtro">
     <option value="jugador" <?php if($_GET['filtro']=='jugador')echo 'selected="true"'?>><?php EchoString('Nombre del jugador'); ?></option>
     <option value="planeta" <?php if($_GET['filtro']=='planeta')echo 'selected="true"'?>><?php EchoString('Nombre del planeta'); ?></option>
     <option value="alianza" <?php if($_GET['filtro']=='alianza')echo 'selected="true"'?>><?php EchoString('Nombre de la alianza'); ?></option>
      </select>
    <input type="text" name="texto" value="<?php echo $textoBusqueda; ?>"/>&nbsp;
    <input type="submit" value="<?php EchoString('Buscar'); ?>" />   
   </td></tr>
  </table><br>
<?php 
if(!empty($_GET) && !empty($_GET['texto']))
{
echo GetString('Resultados de la búsqueda').':<br /><br />'; ?>
<table class="searchTable">
<?php
if($_GET['filtro']=='jugador')
{
	//Buscar Jugador
	$total=$DB->first_row("SELECT COUNT(*) FROM `jugadores` WHERE `Nombre` LIKE '%$textoBusqueda%'");

	$consulta=$DB->query("SELECT ID,Nombre,Alianza,Ranking,PlanetaPrincipal,(select `Nombre` from `alianzas` where `ID`=Alianza LIMIT 1) AS NombreAlianza FROM `jugadores` WHERE `Nombre` LIKE '%$textoBusqueda%' LIMIT $inicio , $tamañoPagina");

	if($consulta->num_rows()==0)
	echo '<tr><td colspan="10"><br>'.GetString('No hay resultados que coincidan con el criterio de búsqueda.').'</td></tr>';
	else
	{
		echo '<tr><th>'.GetString('Nombre').'</th><th>'.GetString('Alianza').'</th><th>'.GetString('Planeta').'</th><th>'.GetString('Coordenadas').'</th><th>'.GetString('Ranking').'</th><th>'.GetString('Op.').'</th></tr>';

		while($jugadorBusqueda =$consulta->fetch_assoc())
		{
			$principal=$DB->getRowProperties('planetas',$jugadorBusqueda['PlanetaPrincipal'],'Nombre,Galaxia,Sistema,Posicion');
			echo '<tr><td>'.$jugadorBusqueda['Nombre'].'</td>
			<td><a onclick="Mostrar(\'alianza.php?alianza='.$jugadorBusqueda['Alianza'].'\')">'.$jugadorBusqueda['NombreAlianza'].'</a></td>
			<td>'.$principal['Nombre'].'</td>
			<td>'.MostrarLocalizacionPlaneta($principal,true).'</td>
			<td>'.($jugadorBusqueda['Ranking']<2000?$jugadorBusqueda['Ranking']:'&nbsp;').'</td>
			<td>'.IconoEnviarMensajeJugador($jugadorBusqueda).'</td></tr>';
		}
		$datosMostrados=true;
	}	
}
else if($_GET['filtro']=='planeta')
{
	//Buscar Planetas
	$total=$DB->first_row("SELECT COUNT(*) FROM `planetas` WHERE `Nombre` LIKE '%$textoBusqueda%'");

	$consulta=$DB->query("SELECT ID,Jugador,Nombre,Galaxia,Sistema,Posicion,Luna FROM `planetas` WHERE `Nombre` LIKE '%$textoBusqueda%' LIMIT $inicio , $tamañoPagina");

	if($consulta->num_rows()==0)
	echo '<tr><td colspan="10"><br>'.GetString('No hay resultados que coincidan con el criterio de búsqueda.').'</td></tr>';
	else
	{
		echo '<tr><th>'.GetString('Nombre').'</th><th>'.GetString('Coordenadas').'</th><th>'.GetString('Jugador').'</th><th>'.GetString('Alianza').'</th><th>'.GetString('Ranking').'</th><th>'.GetString('Op.').'</th></tr>';

		while($planetaBusqueda =$consulta->fetch_assoc())
		{
			$dueño=$DB->first_assoc("SELECT ID,Nombre,Alianza,Ranking,(select `Nombre` from `alianzas` where `ID`=Alianza LIMIT 1) AS NombreAlianza FROM `jugadores` WHERE `ID`={$planetaBusqueda['Jugador']}");

			echo '<tr><td>'.($planetaBusqueda['Luna']==1?GetString('Luna').' ':'').$planetaBusqueda['Nombre'].'</td>
			<td>'.MostrarLocalizacionPlaneta($planetaBusqueda,true).'</td>
			<td>'.$dueño['Nombre'].'</td>
			<td><a onclick="Mostrar(\'alianza.php?alianza='.$dueño['Alianza'].'\')">'.$dueño['NombreAlianza'].'</a></td>
			<td>'.($dueño['Ranking']<2000?$dueño['Ranking']:'&nbsp;').'</td>
			<td>'.IconoEnviarMensajeJugador($dueño).'</td></tr>';
		}
		$datosMostrados=true;
	}	
}
else if($_GET['filtro']=='alianza')//Buscar alianzas
{
	$total=$DB->first_row("SELECT COUNT(*) FROM `alianzas` WHERE `Nombre` LIKE '%$textoBusqueda%'");

	$consulta=$DB->query("SELECT ID,Nombre,Etiqueta,Miembros,Puntos,Ranking,SolicitudesDenegadas FROM `alianzas` WHERE `Nombre` LIKE '%$textoBusqueda%' LIMIT $inicio , $tamañoPagina");

	if($consulta->num_rows()==0)
	echo '<tr><td colspan="10"><br>'.GetString('No hay resultados que coincidan con el criterio de búsqueda.').'</td></tr>';
	else
	{
		echo '<tr><th>'.GetString('Nombre').'</th><th>'.GetString('Etiqueta').'</th><th>'.GetString('Miembros').'</th><th>'.GetString('Puntos').'</th><th>'.GetString('Ranking').'</th>';

		$rango=$DB->getRowProperties('rangosalianza',$jugador->Datos['RangoAlianza'],'RepresentarAlianza');

		if($rango['RepresentarAlianza']!=0 || $jugador->Datos['Alianza']==0)
		echo '<td>'.GetString('Op.').'</td></tr>';

		while($alianzaBusqueda =$consulta->fetch_assoc())
		{
			echo '<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$alianzaBusqueda['ID'].'\')">'.$alianzaBusqueda['Nombre'].'</a></td>
			<td>'.$alianzaBusqueda['Etiqueta'].'</td>
			<td>'.$alianzaBusqueda['Miembros'].'</td>
			<td>'.$alianzaBusqueda['Puntos'].'</td>
			<td>'.$alianzaBusqueda['Ranking'].'</td>';

			if($rango['RepresentarAlianza']!=0)
			echo '<td>'.IconoEnviarMensajeAlianza($alianzaBusqueda).'</td>';
			else if($jugador->Datos['Alianza']==0 && $alianzaBusqueda['SolicitudesDenegadas']==0)
			echo '<td><a onclick="Mostrar(\'solicitud.php?alianza='.$alianzaBusqueda['ID'].'&tipo=acceso\')">'.GetString('Solicitar acceso').'</a><br></td>';
			else
			echo '<td>&nbsp;</td>';

			echo '</tr>';
		}
			$datosMostrados=true;
	}
}

echo '</table><br />';
if($datosMostrados)
echo Paginador($inicio,$tamañoPagina,$total[0],'&filtro='.$_GET['filtro'].'&texto='.$textoBusqueda);
}
 ?>
 </form>