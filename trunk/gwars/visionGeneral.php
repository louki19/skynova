<?php
if(!isset($jugador))
include('basePage.php');

$consulta=$DB->query('SELECT ID,Nombre,Galaxia,Sistema,Posicion,Imagen,Fondo,Luna FROM `planetas` WHERE `Jugador`='.$jugador->ID.' ORDER BY `Orden` ASC LIMIT 18');
$contador=0;
$planetas=array();
while (($planetaJugador = $consulta->fetch_assoc()))
{
	$planetas[$planetaJugador['ID']]=$planetaJugador;
}

//Panel de eventos
include('panelEventos.php');
?>
<a onclick="Mostrar('opcionesJugador.php')"><h2 title="<?php EchoString('Acceder al panel de control del usuario') ?>">Jugador <?php echo $jugador->Nombre ?></h2></a>
<table class="generalViewTable">
  <tr>
    <td><?php 
    //Mostrar la miniatura del planeta o la luna al lado del planeta actual
    $idPlanetaLunaActual=0;
    if($planeta->Datos['Luna']==2)//El planeta tiene luna
    {
    	foreach ($planetas as $planetasArray)
    	{
    		if($planetasArray['Luna'] ==1 && $planetasArray['Galaxia'] ==$planeta->Datos['Galaxia']  && $planetasArray['Sistema'] ==$planeta->Datos['Sistema']  && $planetasArray['Posicion'] ==$planeta->Datos['Posicion'] )
    		{
    			echo $planetasArray['Nombre'].'<br>'.ImagenPlaneta($planetasArray,true,true);	break;
    		}
    	}
    }
    else if($planeta->Datos['Luna']==1)//Es una luna
    {
    	foreach ($planetas as $planetasArray)
    	{
    		if($planetasArray['Luna'] ==2 && $planetasArray['Galaxia'] ==$planeta->Datos['Galaxia']  && $planetasArray['Sistema'] ==$planeta->Datos['Sistema']  && $planetasArray['Posicion'] ==$planeta->Datos['Posicion'] )
    		{
    			echo $planetasArray['Nombre'].'<br>'.ImagenPlaneta($planetasArray,true,true);
    			$idPlanetaLunaActual=$planetasArray['ID'];
    			break;
    		}
    	}
    }
    ?></td>
    <td><?php echo ImagenPlaneta($planeta->Datos,true,false,'generalViewPlanetImage',250,250); ?></td>
    <td>
        <table>
          <tr>
            <?php
            //Mostrar los planetas del jugador
            $contador=0;
            foreach ($planetas as $idPlanetaJugador=>$planetaJugador)
            {
            	if($idPlanetaJugador==$planeta->ID || $idPlanetaJugador==$idPlanetaLunaActual || $planetaJugador['Luna']==1)
            	continue;

            	$luna='';
            	if($planetaJugador['Luna']==2)//Este planeta tiene luna
            	{
            		foreach ($planetas as $planetasArray)
            		{
            			if($planetasArray['Luna'] ==1 && $planetasArray['Galaxia'] ==$planetaJugador['Galaxia']  && $planetasArray['Sistema'] ==$planetaJugador['Sistema']  && $planetasArray['Posicion'] ==$planetaJugador['Posicion'] )
            			{
            				$luna=ImagenPlaneta($planetasArray,true,true,'thumbnailMoon');
            				break;
            			}
            		}
            	}
            	echo '<td align="center">'.$planetaJugador['Nombre'].'<br />'.ImagenPlaneta($planetaJugador,true,true).'</td><td>'.$luna.'</td>';

            	$contador++;
            	if($contador%2==0)echo '</tr><tr>';
            }
    ?>
        </table>
    </td>
  </tr>
</table>
<a onclick="Mostrar('opcionesPlaneta.php')" title="<?php EchoString('Acceder al panel de control del planeta') ?>"><h2> <?php
if($planeta->Datos['Luna']==1)
echo GetString('Luna');
else
echo GetString('Planeta');
      echo' '.$planeta->Nombre; ?></h2></a>
<table class="generalViewTable">
  <tr>
    <td class="generalViewTableText1" width="50%"><?php EchoString('Di&aacute;metro'); ?></td>
    <td width="50%"><?php echo sprintf(GetString('%s km (%s / %s campos)'),$planeta->Datos['CamposTotales']*80,$planeta->Datos['CamposOcupados'],$planeta->Datos['CamposTotales']); ?></td>
  </tr>
  <tr>
    <td class="generalViewTableText1"><?php EchoString('Temperatura media'); ?></td>
    <td><?php echo $planeta->Datos['Temperatura'] ?>&deg;C</td>
  </tr>
  <tr>
    <td class="generalViewTableText1"><?php EchoString('Posici&oacute;n'); ?></td>
    <td><?php echo MostrarLocalizacionPlaneta($planeta->Datos,true); ?></td>
  </tr>
  <tr>
    <td class="generalViewTableText1"><?php EchoString('Puntos'); ?></td>
    <td><?php 
    $jugadoresTotales=$DB->first_row('SELECT Count(*) FROM `jugadores`');
    $ranking='<a onclick="Mostrar(\'estadisticas.php?mostrar=jugadores&posiciones='.(floor($jugador->Datos['Ranking']/100)*100).'&marcar='.$jugador->Datos['Ranking'].'\')">'.$jugador->Datos['Ranking'].'</a>';

    printf(GetString('%d (Lugar %s de %d)'), $jugador->Datos['Puntos'],$ranking,$jugadoresTotales[0]);
    echo '</td>';
    if(!empty($planeta->Datos['EscombrosMetal']) ||  !empty($planeta->Datos['EscombrosCristal']))
    {
	?>
	<tr>
    <td class="generalViewTableText1"><?php EchoString('Escombros'); ?></td>
    <td><?php printf(GetString('%s metal, %s cristal'),$planeta->Datos['EscombrosMetal'],$planeta->Datos['EscombrosCristal']) ?></td>
  </tr>
<?php } ?>
  </tr>
</table>
<?php
ActualizarDatosCabecera();
?>

