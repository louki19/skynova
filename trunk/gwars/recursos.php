<?php
include('basePage.php');

$planeta->CargarRendimientos();

if(!empty($_POST))
{
	$planeta->Datos['Rendimientos'][1]=ObtenerRendimiento(1);
	$planeta->Datos['Rendimientos'][2]=ObtenerRendimiento(2);
	$planeta->Datos['Rendimientos'][3]=ObtenerRendimiento(3);
	$planeta->Datos['Rendimientos'][10]=ObtenerRendimiento(10);
	$planeta->Datos['Rendimientos'][11]=ObtenerRendimiento(11);
	$planeta->Datos['Rendimientos'][12]=ObtenerRendimiento(12);	
	$planeta->RecalcularProducciones();
	$planeta->GuardarCambios();
}
$nivelProduccion=$planeta->Datos['NivelProduccion'];

function ObtenerRendimiento($ID)
{
	$resultado;
	$valor=$_POST['rendimiento'.$ID];
	if(is_numeric($valor))
	$resultado= $valor;
	else
	$resultado= 100;

	return $resultado;
}

function MostrarNumero($Numero)
{
	$Valor=RedondearNumero($Numero);

	if ($Numero<0)
	echo '<font color="#ff0000">'.$Valor.'</font>';
	else if ($Numero>0)
	echo '<font color="#00ff00">'.$Valor.'</font>';
	else
	echo    $Valor;
}

?>
<h2>
<?php 
$textoPlaneta=$planeta->Datos['Luna']==1? GetString('la luna '):GetString(' el planeta ');
echo GetString('Producci&oacute;n de recursos en ').$textoPlaneta.$planeta->Nombre;
?>
</h2><br/>
<br/>
<form method="post">
<input type="hidden" name="recursos">
  <table class="resourcesTable">
    <tr>
      <th >&nbsp;</th>
      <th><?php EchoString('Metal'); ?></th>
      <th><?php EchoString('Cristal'); ?></th>
      <th><?php EchoString('Antimateria'); ?></th>
      <th><?php EchoString('Energ&iacute;a'); ?></th>
       <th><?php EchoString('Rendimiento'); ?></th>
    </tr>
    <tr>
      <td><?php EchoString('Ingresos b&aacute;sicos'); ?></td>
      <td><?php MostrarNumero(50*$configuracion['NivelProduccion']); ?></td>
      <td><?php MostrarNumero(30*$configuracion['NivelProduccion']); ?></td>
      <td>0</td>
      <td>0</td>
      <td></td>
    </tr> 
    <?php
    MostrarRecursosMina(1);
    MostrarRecursosMina(2);
    MostrarRecursosMina(3);
    MostrarRecursosMina(10);
    MostrarRecursosMina(11);
    MostrarRecursosMina(12);
  ?>
    <tr>
      <td> <?php EchoString('Nivel de producción');?></td>
      <td colspan="5" align="left"><div style="background-color: rgb(<?php echo floor(2.55*(100-$planeta->Datos['NivelProduccion'])).', '.floor(2*$planeta->Datos['NivelProduccion'])?>, 0); width: <?php echo $planeta->Datos['NivelProduccion']?>%; text-align:center;"><?php echo $planeta->Datos['NivelProduccion']?>%</div></td>
    </tr>
    <tr>
      <td colspan="6"> <input type="submit" value="<?php EchoString('Calcular'); ?>"></td>
    </tr>
  </table> </form>  <br />
  <table class="resourcesTable">
    <tr>
   <th colspan="5"><?php EchoString('Producción total');?></th>
    </tr>
    <tr>
      <th></th>
      <th><?php EchoString('Horaria');?></th>
      <th><?php EchoString('Diaria');?></th>
      <th><?php EchoString('Semanal');?></th>
      <th><?php EchoString('Mensual');?></th>
    </tr>
    <tr>
      <td><?php EchoString('Metal');?></td>
      <td><?php
      $produccionHora=$planeta->Datos['ProduccionMetal'];
      MostrarNumero($produccionHora);?></td>
        <td><?php MostrarNumero($produccionHora*24);?></td>
      <td><?php MostrarNumero($produccionHora*168);?></td>
      <td><?php MostrarNumero($produccionHora*720);?></td>
    </tr>
    <tr>
      <td><?php EchoString('Cristal');?></td>
      <td><?php
      $produccionHora=$planeta->Datos['ProduccionCristal'];
      MostrarNumero($produccionHora);?></td>
        <td><?php MostrarNumero($produccionHora*24);?></td>
      <td><?php MostrarNumero($produccionHora*168);?></td>
      <td><?php MostrarNumero($produccionHora*720);?></td>
    </tr>
    <tr>
      <td><?php EchoString('Antimateria');?></td>
      <td><?php
      $produccionHora=$planeta->Datos['ProduccionAntimateria'];
      MostrarNumero($produccionHora);?></td>
        <td><?php MostrarNumero($produccionHora*24);?></td>
      <td><?php MostrarNumero($produccionHora*168);?></td>
      <td><?php MostrarNumero($produccionHora*720);?></td>
    </tr>
     <tr>
      <td><?php EchoString('Energía');?></td>
      <td colspan="4"><?php
      MostrarNumero($planeta->Energia);echo ' / '.$planeta->EnergiaProducida();?></td>
    </tr>
  </table><br />
<table class="resourcesTable">
    <tr>
   <th colspan="4"><?php EchoString('Estado de los almacenes');?></th>
    </tr>
   <tr>
   <th><?php EchoString('Tipo');?></th>
   <th><?php EchoString('Capacidad');?></th>
   <th colspan="2"><?php EchoString('Porcentaje completado');?></th>
    </tr>
    <tr>
    <td><?php echo GetTechnology(90)->Name; ?></td>
     <td><?php echo RedondearNumero(CapacidadAlmacen(90,$planeta->Tecnologias[90])); ?></td>
    <?php
    $porcentaje= round($planeta->Metal/CapacidadAlmacen(90,$planeta->Tecnologias[90])*100,1);
       echo MostrarPorcentaje($porcentaje) ?>
    </tr>
        <tr>
    <td><?php echo GetTechnology(91)->Name; ?></td>
        <td><?php echo RedondearNumero(CapacidadAlmacen(91,$planeta->Tecnologias[91])); ?></td>
    <?php
    $porcentaje= round($planeta->Cristal/CapacidadAlmacen(91,$planeta->Tecnologias[91])*100,1);
     echo MostrarPorcentaje($porcentaje) ?>
    </tr>
        <tr>
    <td><?php echo GetTechnology(92)->Name; ?></td>
        <td><?php echo RedondearNumero(CapacidadAlmacen(92,$planeta->Tecnologias[92])); ?></td>
    <?php 
    $porcentaje= round($planeta->Antimateria/CapacidadAlmacen(92,$planeta->Tecnologias[92])*100,1);
    echo MostrarPorcentaje($porcentaje) ?>
    </tr>
</table>
<?php 

function MostrarPorcentaje($porcentaje)
{
	if($porcentaje>100)
	$porcentajeAjustado=100;
	else
	$porcentajeAjustado=$porcentaje;

	return '<td width="325" align="left"><div style="background-color: rgb('.floor(2.55*$porcentajeAjustado).', '.floor(2.55*(100-$porcentajeAjustado)).', 0); width: '.$porcentajeAjustado.'%; text-align:center;">'.$porcentaje.'%</div></td>';
}

function MostrarRecursosMina($ID)
{
	global $planeta;
	global $nivelProduccion;

	$nivelActual=$planeta->Tecnologias[$ID];
	if($nivelActual==0)
	return;
?>
<tr>
  <td><?php echo GetTechnology($ID)->Name.' ('.GetString('Nivel').' '.$nivelActual.')';?></td>
  <td><?php if($ID==1) echo '<font color="#00ff00">'.$planeta->ProduccionMina(1,$nivelProduccion).'</font>'; else echo 0;?></td>
  <td><?php if($ID==2) echo '<font color="#00ff00">'.$planeta->ProduccionMina(2,$nivelProduccion).'</font>';  else echo 0;?></td>
  <td><?php
  if($ID==3)
  echo '<font color="#00ff00">'.$planeta->ProduccionMina(3,$nivelProduccion).'</font>';
  else if($ID==11)
  {
  	$gasto=$planeta->GastoMina(11);
  	if($gasto!=0)
  	echo '<font color="#ff0000">-'.$gasto.'</font>';
  	else   echo '0';
  }
  else echo 0;?></td>
  <td><?php 
  if($ID<10)//Mina
  {
  	echo '<font color="#ff0000">'.$planeta->GastoMina($ID,$nivelProduccion).'/'.$planeta->GastoMina($ID,100).'</font>';
  }
  else//Productor energía
  echo '<font color="#00ff00">'.$planeta->ProduccionMina($ID,$nivelProduccion).'</font>';
  ?></td>
  <th> <select style="width:75px" name="rendimiento<?php echo $ID ?>" size="1">
      <?php 
      for($contador=10;$contador>=0;$contador--)
      {
      	if($planeta->Datos['Rendimientos'][$ID]/10==$contador)
      	echo '<option value="'.($contador*10).'" selected="true">'.($contador*10).'%</option>';
      	else
      	echo '<option value="'.($contador*10).'" >'.($contador*10).'%</option>';
      }
?>
    </select>
  </th>
</tr>
<?php      	
}

ActualizarDatosCabecera();
?>