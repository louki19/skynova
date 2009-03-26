<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

$elementosMostrar=10;

$inicio=$_GET['inicio'];
if(is_numeric($inicio)==false || $inicio==1)
$inicio=0;
?>
<div id="estadisticas">
      <form method="get" action="">
        <table class="statisticTable">
          <tr>
            <th colspan="4"><?php EchoString('Filtro'); ?></th>
          </tr>
          <tr>
            <td><label><?php EchoString('Mostrar'); ?>
              <select name="mostrar">
                <option value="jugadores" <?php if($_GET['mostrar']=='jugadores') echo 'selected="true"'; echo '>'.GetString('Jugadores'); ?>
                </option>
                <option value="alianzas" <?php if($_GET['mostrar']=='alianzas') echo 'selected="true"'; echo '>'.GetString('Alianzas'); ?>
                </option>
              </select>
              </label></td>
            <td><label><?php EchoString('Inicio'); ?>
              <input type="text" name="inicio" value="<?php echo $inicio ?>">
              </select>
              </label></td>
            <td><input type="submit" value="<?php EchoString('Actualizar');?>"></td>
          </tr>
        </table>
      </form>
      <br/>
<?php 
if($_GET['mostrar']=='alianzas')
{
	$representanteAlianza=$DB->getRowProperty('rangosalianza',$jugador->Datos['RangoAlianza'],'RepresentarAlianza');
	echo '<table class="statisticTable"><tr>
          <th>'.GetString('Ranking').'</th><th>'.GetString('Miembros').'</th><th>'.GetString('Nombre').'</th><th>'.GetString('Puntos').'</th><th>'.GetString('Por miembro').'</th>';

	if($representanteAlianza!=0)
	echo '<th>'.GetString('Acciones').'</th>';
	echo '</tr>';
}
else
{
	echo '<table class="statisticTable"><tr>
          <th>'.GetString('Ranking').'</th><th>'.GetString('Jugador').'</th><th>'.GetString('Alianza').'</th>
          <th>'.GetString('Puntos').'</th><th>'.GetString('Acciones').'</th></tr>';
}

if($_GET['mostrar']=='alianzas')
$consulta=$DB->query('SELECT ID,Nombre,Puntos,Miembros FROM `alianzas` ORDER BY `Ranking` ASC LIMIT '.$inicio.' , '.$elementosMostrar);
else
$consulta=$DB->query('SELECT ID,Nombre,Puntos,Alianza FROM `jugadores` ORDER BY `Ranking` ASC LIMIT '.$inicio.' , '.$elementosMostrar);

if($consulta->num_rows()==0)
{
	echo '<tr><td colspan="10"><br>'.GetString('No hay datos para el filtro de búsqueda especificado').'</td></tr></table>';
}
else
{
	$contador=$inicio==0?1:$inicio;

	if($_GET['mostrar']=='alianzas')//Mostrar alianzas
	{
		while ($alianza = $consulta->fetch_assoc())
		{
			if($contador==$_GET['marcar'])
			$elemento='th';
			else
			$elemento='td';

			echo '<tr><td>'.$contador.'</td>
<'.$elemento.'><a onclick="Mostrar(\'alianza.php?alianza='.$alianza['ID'].'\')">'.$alianza['Nombre'].'</a></'.$elemento.'>
<'.$elemento.'>'.$alianza['Miembros'].'</'.$elemento.'>
<'.$elemento.'>'.$alianza['Puntos'].'</'.$elemento.'>
<'.$elemento.'>'.round($alianza['Puntos']/$alianza['Miembros']).'</'.$elemento.'>';

			if($representanteAlianza!=0)
			echo '<td>'.IconoEnviarMensajeAlianza($alianza,16).'</td>';

			echo '</tr>';

			$contador++;
		}
			echo '</table><br />';

		$totales=$DB->first_row('select count(*) from alianzas');
		echo Paginador($inicio,$elementosMostrar,$totales[0],'&mostrar=alianzas');
	}
	else//Mostrar jugadores
	{
		while ($jugadorRanking = $consulta->fetch_assoc())
		{
			if($contador==$_GET['marcar'])
			$elemento='th';
			else
			$elemento='td';

			$alianza=ObtenerNombreAlianza($jugadorRanking['Alianza']);

			echo '<tr><'.$elemento.'>'.$contador.'</'.$elemento.'>
    <'.$elemento.'>'.$jugadorRanking['Nombre'].'</'.$elemento.'>
    <'.$elemento.'>'.(!empty($alianza)? '<a onclick="Mostrar(\'alianza.php?alianza='.$jugadorRanking['Alianza'].'\')">'.$alianza.'</a>':'').'</'.$elemento.'>
    <'.$elemento.'>'.$jugadorRanking['Puntos'].'</'.$elemento.'>
    <'.$elemento.'>'.IconoEnviarMensajeJugador($jugadorRanking,16).'</'.$elemento.'></tr>';
			$contador++;
		}

		echo '</table><br />';

		$totales=$DB->first_row('select count(*) from jugadores');
		echo Paginador($inicio,$elementosMostrar,$totales[0],'&mostrar=jugadores');
	}
}
?>

</div>
<?php
ActualizarDatosCabecera();
?>