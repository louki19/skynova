<?php
if($_GET['controles']==1)
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

$vistaGalaxia=ObtenerValorCookie('vista',1);

$galaxia=$_GET['galaxia'];
if(empty($galaxia) || !is_numeric($galaxia))
$galaxia=$planeta->Datos['Galaxia'];

if($galaxia<1)
$galaxia=1;
if($galaxia>9)
$galaxia=9;

$sistema=$_GET['sistema'];
if(empty($sistema)|| !is_numeric($sistema))
$sistema=$planeta->Datos['Sistema'];

if($sistema<1)
$sistema=1;
if($sistema>499)
$sistema=499;

// VistaGalaxia: 1 - vista moderna   !=1 - vista clasica
if($vistaGalaxia!=1)
MostrarGalaxiaClasica($galaxia,$sistema);
else
MostrarGalaxiaModerna($galaxia,$sistema);
?>
<script type="text/javascript">
gid('tipoGalaxia').value="<?php echo $vistaGalaxia==1?GetString('Vista clásica'):GetString('Vista moderna'); ?>";
gid('tipoVista').value=<?php echo $vistaGalaxia==2?1:2 ?>;
gid('galaxia').value=<?php echo $galaxia ?>;
gid('sistema').value=<?php echo $sistema ?>;
ajaxTooltip.scanElements();
</script>
<?php

function MostrarGalaxiaModerna($galaxia,$sistema)
{
	$consulta=$GLOBALS['DB']->query("SELECT Planetas.ID,Planetas.Nombre,Planetas.Jugador,Planetas.Galaxia,Planetas.Sistema,Planetas.Posicion,Planetas.Imagen,Planetas.Luna,Planetas.EscombrosMetal,Planetas.EscombrosCristal,Jugadores.Nombre AS NombreJugador,Jugadores.Alianza FROM `planetas` JOIN jugadores ON planetas.Jugador=Jugadores.ID WHERE `Galaxia`=$galaxia && `Sistema`=$sistema LIMIT 20");

	echo '<div class="spaceImage">';
	while ($planetaGalaxia = $consulta->fetch_assoc())
	{
		global $jugador;

		$posicion = $planetaGalaxia['Posicion'];

		if($planetaGalaxia['Luna']==1)//Es una luna
		{
			echo '<img class="Moon" id="Luna'.$posicion.'" src="'.$jugador->UrlSkin.'images/moon_small.png" tip="tooltip.php?tipo=luna&id='.$planetaGalaxia['ID'].'"/>';
			continue;
		}

		echo '<img class="Planet" id="Planeta'.$posicion.'" tip="tooltip.php?tipo=planetaGalaxia&id='.$planetaGalaxia['ID'].'" src="'.$jugador->UrlSkin.'planets/'.TipoPlaneta($planetaGalaxia['Posicion']).$planetaGalaxia['Imagen'].'.png" />
<table id="Tabla'.$posicion.'" class="'.((isset($_GET['posicion']) && $_GET['posicion']==$posicion)?'highlightedPlanet':'PlanetTable').'">
<tr><td>'.$posicion.'. '.$planetaGalaxia['Nombre'].'</td></tr>
<tr><td><span style="cursor:pointer" tip="tooltip.php?tipo=jugador&id='.$planetaGalaxia['Jugador'].'">'.$planetaGalaxia['NombreJugador'].'</span></td></tr>
<tr><td>'.(!empty($planetaGalaxia['Alianza'])?'<span style="cursor:pointer" tip="tooltip.php?tipo=alianza&id='.$planetaGalaxia['Alianza'].'">'.ObtenerNombreAlianza($planetaGalaxia['Alianza']).'</span>':'&nbsp;').'</td></tr></table>';
		
		if(!empty($planetaGalaxia['EscombrosMetal']) || !empty($planetaGalaxia['EscombrosCristal']))
		{
			echo '<img class="Debris" id="Escombros'.$posicion.'" tip="tooltip.php?tipo=escombros&id='.$planetaGalaxia['ID'].'" src="'.$jugador->UrlSkin.'images/debris_small.png"/>';
		}
	}
	echo '</div>';
}

function MostrarGalaxiaClasica($galaxia,$sistema)
{
	global $jugador;
	?>
  <table class="galaxyTable">
    <tr>
      <th colspan="8"><?php printf(GetString('Sistema solar en %s:%s'),$galaxia,$sistema); ?></th>
    </tr>
    <tr>
      <th><?php EchoString('Pos') ?></th>
      <th><?php EchoString('Planeta')?></th>
      <th><?php EchoString('Nombre (Actividad)')?></th>
      <th><?php EchoString('Luna')?></th>
      <th><?php EchoString('Escombros')?></th>
      <th><?php EchoString('Jugador (Estado)')?></th>
      <th><?php EchoString('Alianza')?></th>
      <th><?php EchoString('Acci&oacute;n')?></th>
    </tr>
	<?php
	$consulta=$GLOBALS['DB']->query("SELECT Planetas.ID,Planetas.Nombre,Planetas.Jugador,Planetas.Galaxia,Planetas.Sistema,Planetas.Posicion,Planetas.Imagen,Planetas.Luna,Planetas.EscombrosMetal,Planetas.EscombrosCristal,Jugadores.Nombre AS NombreJugador,Jugadores.Alianza FROM `planetas` JOIN jugadores ON planetas.Jugador=Jugadores.ID WHERE `Galaxia`=$galaxia && `Sistema`=$sistema && `Luna`!=1  ORDER BY `Posicion` ASC LIMIT 10");

	$posicion=1;
	while ($planetaGalaxia = $consulta->fetch_assoc())
	{
		while($planetaGalaxia['Posicion']>$posicion)
		{
			echo "<tr><td>$posicion</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			$posicion++;
		}
		if($planetaGalaxia['Posicion']==$posicion)
		{

?>
<tr <?php if($_GET['posicion']==$planetaGalaxia['Posicion']) echo 'class="galaxyHighlightedPlanet"'?>>
<td><?php echo $planetaGalaxia['Posicion'] ?></td>
<td><img class="classicGalaxyPlanetImage" src="<?php echo $jugador->UrlSkin.'planets/'.TipoPlaneta($planetaGalaxia['Posicion']).$planetaGalaxia['Imagen'] ?>.png" tip="tooltip.php?tipo=planetaGalaxia&id=<?php echo $planetaGalaxia['ID'] ?>"></td>
<td><?php echo $planetaGalaxia['Nombre']?></td>
<td><?php
if($planetaGalaxia['Luna']==2)//El planeta tiene luna
{
	echo '<img class="classicGalaxyImage" src="'.$jugador->UrlSkin.'images/moon_small.png" tip="tooltip.php?tipo=luna&id='.$planetaGalaxia['ID'].
	'&galaxia='.$planetaGalaxia['Galaxia'].'&sistema='.$planetaGalaxia['Sistema'].'&posicion='.$planetaGalaxia['Posicion'].'"/>';
}
?></td>
<td><?php
if(!empty($planetaGalaxia['EscombrosMetal']) ||  !empty($planetaGalaxia['EscombrosCristal']))
{
	echo '<img class="classicGalaxyImage" src="'.$jugador->UrlSkin.'images/debris_small.png" tip="tooltip.php?tipo=escombros&id='.$planetaGalaxia['ID'].'" />';
}
?></td>
<td><?php echo '<span style="cursor:pointer" tip="tooltip.php?tipo=jugador&id='.$planetaGalaxia['Jugador'].'">'.$planetaGalaxia['NombreJugador'].'</span>';?></td>
<td>
<?php
if(!empty($planetaGalaxia['Alianza']))
echo '<span style="cursor:pointer" tip="tooltip.php?tipo=alianza&id='.$planetaGalaxia['Alianza'].'">'.ObtenerNombreAlianza($planetaGalaxia['Alianza']).'</span>';
?>
</td>
<td></td>
</tr>
<?php
		}
		$posicion++;
	}
	while($posicion<=10)
	{
		echo "<tr><td>$posicion</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		$posicion++;
	}
?>
    <tr>
      <th colspan="99"><?php echo $consulta->num_rows().' '.GetString('Planetas habitados') ?></th>
    </tr>
  </table>
<?php
}

ActualizarDatosCabecera();
?>