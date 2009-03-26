<?php
if(!isset($planeta))
{
	$tipoInicio=2;
	include('basePage.php');
}

$DB->query('UPDATE `planetas` SET `UltimoAcceso`='.time().' where `ID`='.$planeta->ID.' LIMIT 1');
?>
<table>
  <tr>
    <td onclick="ActualizarCabecera();" title="<?php EchoString('Pulsa aquí para actualizar los datos de la cabecera'); ?>"><?php echo ImagenPlaneta($planeta->Datos,false); ?></td>
    <td>
    <select class="selectPlanet" onChange="CambiarPlaneta(this.value)">       
    <?php 
    $consulta=$DB->query('SELECT ID,Nombre,Luna,Galaxia,Sistema,Posicion FROM `planetas` WHERE `Jugador`='.$jugador->ID.' ORDER BY `Orden` ASC LIMIT 18');

    while ($planetaJugador = $consulta->fetch_assoc())
    {
    	echo '<option value="'.$planetaJugador['ID'].'"';

    	if($planetaJugador['ID']==$planeta->ID)
    	echo ' selected="true"';

    	$nombre;
    	if($planetaJugador['Luna']==1)
    	$nombre=GetString('Luna').' '.$planetaJugador['Nombre'];
    	else
    	$nombre=$planetaJugador['Nombre'];

    	echo ">".MostrarLocalizacionPlaneta($planetaJugador,false,false)."</option>";
    }
    ?></select>
      </td><td><table>
        <tr class="resourceTable">
          <td><img src="<?php echo $jugador->UrlSkin ?>images/metal.png" title="<?php EchoString('Metal'); ?>"/></td>
          <td><img src="<?php echo $jugador->UrlSkin ?>images/crystal.png" title="<?php EchoString('Cristal'); ?>"/></td>
          <td><img src="<?php echo $jugador->UrlSkin ?>images/antimatter.png" title="<?php EchoString('Antimateria'); ?>"/></td>
          <td><img src="<?php echo $jugador->UrlSkin ?>images/energy.png" title="<?php EchoString('Energía'); ?>"/></td>
         </tr>
        <tr class="resourcesText">
          <td><?php echo GetString('Metal').'<br />'; echo MostrarCantidadRecusos(1,$planeta); ?></td>
          <td><?php echo GetString('Cristal').'<br />'; echo  MostrarCantidadRecusos(2,$planeta); ?></td>
          <td><?php echo GetString('Antimateria').'<br />'; echo  MostrarCantidadRecusos(3,$planeta); ?></td>
          <td><?php echo GetString('Energ&iacute;a').'<br />'; echo  MostrarCantidadRecusos(4,$planeta); ?></td>
        </tr>
      </table>
      <td><img class="messagesImage" id="imagenMensajes" onclick="Mostrar('mensajes.php')" htmlTip="true"/></td>
      <td><a id="textoMensajes" onclick="Mostrar('mensajes.php')" htmlTip="true">0</a>
       </td>
  </tr>
</table>
<?php ActualizarDatosCabecera(); ?>