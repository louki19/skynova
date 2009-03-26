<?php
if(!isset($planeta) || !isset($flota))//Comprobar que se ha cargado desde flota.php
exit;

//Crear campos vacios para los parametros de la flota
echo '<form method="post" name="flota">';
foreach($_POST as $key => $value)
{
	if(!empty($value) && $key!='capacidadCarga')
	{
		echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
	}
}
?>
<table class="fleetTable">
<tr><th><?php EchoString('Misión'); ?></th>
<th><?php EchoString('Recursos enviados'); ?></th></tr>
<tr><td>
<?php
//Mostrar misiones disponibles
$primer=true;
foreach($flota->MisionesDisponibles as $id=>$texto)
{
	echo '<label><input type="radio" name="mision" value="'.$id.'"'.($primer?' checked="true"':'').'/>'.$texto.'</label><br />';
	$primer=false;
}
?>
</td><td>
<table width="100%">
<tr><td><?php EchoString('Capacidad'); ?></td>
<td colspan="2"><?php echo number_format($flota->Capacidad,0,'','.').' '.GetString('unidades'); ?></td></tr>
<tr><td><?php EchoString('Restantes'); ?></td>
<td colspan="2"><span id="unidadesRestantes"><?php echo number_format($flota->Capacidad,0,'','.').'</span> '.GetString('unidades'); ?></td></tr>
<tr><td><?php EchoString('Metal'); ?></td><td><input type="text" onkeyup="ControlNumerico(this);RecursosCambiados();" id="recurso1" name="metal"></td>
<td><a onclick="EstablecerRecurso(1);"><? EchoString('Máx.'); ?></a></td></tr>
<tr><td><?php EchoString('Cristal'); ?></td><td><input type="text" onkeyup="ControlNumerico(this);RecursosCambiados();"  id="recurso2" name="cristal"></td>
<td><a onclick="EstablecerRecurso(2);"><? EchoString('Máx.'); ?></a></td></tr>
<tr><td><?php EchoString('Antimateria'); ?></td><td><input type="text" onkeyup="ControlNumerico(this);RecursosCambiados();" id="recurso3" name="antimateria"></td>
<td><a onclick="EstablecerRecurso(3);"><? EchoString('Máx.'); ?></a></td></tr>
<tr><td colspan="3"><a onclick="TodosLosRecursos()"><?php EchoString('Todos los recursos posibles'); ?></a></td></tr>
</table></td></tr>

<tr><th colspan="2"><input type="submit" id="enviarFlota" value="<?php EchoString('Enviar'); ?>"></th></tr>
</table>
</form>
<br/>
<table  class="fleetTable">
    <tr>
      <th colspan="4"><?php EchoString('Información de flota'); ?></th>
    </tr>
 	<tr><td><?php EchoString('Destino'); ?></td>
    <td colspan="3"><?php
    if($flota->TipoDestino==2)
    echo GetString('Escombros');
    else
    echo ($flota->TipoDestino==1?GetString('Luna'):GetString('Planeta')).' '.$flota->PlanetaDestino['Nombre']
    ?>&nbsp;[<?php echo $flota->Destino[0].':'.$flota->Destino[1].':'.$flota->Destino[2] ?>]</td></tr>
    <tr><td><?php EchoString('Distancia'); ?></td>
    <td colspan="3"><?php echo number_format($flota->Distancia,0,null,'.').' '.GetString('kilómetros') ?></td></tr>
    <tr><td><?php EchoString('Capacidad de carga'); ?></td>
    <td colspan="3"><?php echo number_format($flota->Capacidad,0,null,'.').' '.GetString('unidades')?></td></tr>
    <tr><td><?php EchoString('Consumo de antimateria'); ?></td>
    <td colspan="3"><?php echo number_format($flota->Consumo,0,null,'.').' '.GetString('unidades');?></td></tr>
    <tr><td><?php EchoString('Tiempo de vuelo'); ?></td>
    <td colspan="3"><?php echo SegundosAFecha(round($flota->Distancia/$flota->Velocidad)) ?></td></tr>
    <tr><td><?php EchoString('Hora de llegada'); ?></td>
    <td colspan="3"><span style="color:lime;" id="horaLlegada">0</span></td> </tr>
    <tr><td><?php EchoString('Hora de regreso'); ?></td>
    <td colspan="3"><span style="color:lime;" id="horaRegreso">0</span></td></tr>
    <tr><td><?php EchoString('Velocidad máxima de la flota'); ?></td>
    <td colspan="3"><?php echo number_format($flota->Velocidad,0,null,'.').' '.GetString('km/s') ?></td></tr>
</table>
<script type="text/javascript">
var capacidadCarga=<?php echo $flota->Capacidad ?>;
var metalPlaneta=<?php echo $planeta->Metal ?>;
var cristalPlaneta=<?php echo $planeta->Cristal ?>;
var antimateriaPlaneta=<?php echo $planeta->Antimateria ?>;
var segundosVuelo=<?php echo $flota->Distancia/$flota->Velocidad ?>;
RecursosCambiados();
MostrarFechasLlegada();
</script>
<?php
ActualizarDatosCabecera();
?>