<?php include('basePage.php');

$tecnologia= GetTechnology($_GET['id']);

if (empty($tecnologia))
MostrarError(GetString('Error, tecnología inexistente.'),true);
?>

<table class="descriptionTable">
<tr>
  <th colspan="3"><?php echo $tecnologia->Name; ?></th>
</tr>
<tr>
  <td><img src="<?php echo $jugador->UrlSkin.'technology/'.$tecnologia->ID.'.png'?>" class="descriptionImage"/></td>
  <td style="text-align:left;"><?php echo $tecnologia->LongDescription;

  //Mostrar requisitos
  $requisitos=RequisitosTecnologia($tecnologia->ID);
  if (!empty($requisitos))
  {
  	echo '<br /><br />'.GetString('Requisitos').':<br />';
  	echo MostrarRequisitos($requisitos);
  }


  if ($tecnologia->ID>=250 && $tecnologia->ID<500)//Nave o defensa
  {
  	$caracteristicas=CaracteristicasBaseArmamento($tecnologia->ID);
  	MostrarFuegoRapidoArmamento();
  }
  echo '</td></tr></table>';
  //Mostrar informaciones extra
  if($tecnologia->ID==12)//Planta de microondas
  {
  	echo '<br>';
  	printf(GetString('Por cada satélite, las plantas de microondas producen en este planeta %d de energía'),
  	ProduccionSatelite($planeta));
  	echo '<br>';
  }
  if($tecnologia->ID==81 && $planeta->Tecnologias[81]!=0)//Sensor espacial
  {
  	echo '<br>';
  	printf(GetString('El alcance del sensor espacial de esta luna es de %d sistemas.'),
  	AlcanceSensorEspacial($planeta->Tecnologias[81]));

  	echo '<br>';
  }
  else if ($tecnologia->ID==13 || ($tecnologia->ID>=300 && $tecnologia->ID<500))//Nave o defensa
  {
  	MostrarCaracteristicasArmamento();
  }
  else if($tecnologia->ID>=90 && $tecnologia->ID<100)//Almacen
  {
  	MostrarCapacidadAlmacen($tecnologia->ID);
  }
  else if($tecnologia->ID<20 && $tecnologia->ID!=12)//Mina y productor de energía
  {
  	MostrarProduccionMina();
  }


  function MostrarRequisitos($requisitos)
  {
  	if(empty($requisitos)) return;
  	global $planeta;

  	$resultado;
  	foreach ($requisitos as $tecno=>$nivel)
  	{
  		$resultado.= '<a class="link" onclick="Mostrar(\'descripcion.php?id='.$tecno.'\')">';

  		if($planeta->Tecnologias[$tecno]>=$nivel)
  		$resultado.= '<font color="#00FF00">';
  		else
  		$resultado.= '<font color="#FF0000">';

  		$tecnologia=GetTechnology($tecno);
  		$resultado.= $tecnologia->Name.' (nivel '.$nivel.')</font></a><br/>';
  	}
  	return $resultado;
  }

  function MostrarCapacidadAlmacen($ID)
  {
  	global $planeta;

  	echo '<table class="descriptionTable"><tr><th>'.GetString('Nivel').'</th><th>'.GetString('Capacidad').'</th>';

  	$nivelTecnoPlaneta=$planeta->Tecnologias[$ID];
  	$nivel=$nivelTecnoPlaneta-5;
  	if($nivel<1)
  	$nivel=1;

  	$capacidadPlaneta=CapacidadAlmacen($ID,$nivelTecnoPlaneta);

  	for($contador=0;$contador<15;$contador++)
  	{
  		echo '<tr>';
  		if($nivel==$nivelTecnoPlaneta)
  		$separador='th';
  		else
  		$separador='td';

  		$capacidad=CapacidadAlmacen($ID,$nivel);
  		echo '<'.$separador.'>'.$nivel.'</'.$separador.'><'.$separador.'>'.RedondearNumero($capacidad).CompararProduccion($capacidadPlaneta,$capacidad).'</'.$separador.'>';
  		$nivel++;
  	}
  	echo '</table>';
  }

  function MostrarProduccionMina()
  {
  	global $tecnologia;
  	global $planeta;

  	$nivelTecnoPlaneta=$planeta->Tecnologias[$tecnologia->ID];
  	$nivel=$nivelTecnoPlaneta-5;
  	if($nivel<1)
  	$nivel=1;

  	echo '<table class="descriptionTable"><tr><th>'.GetString('Nivel').'</th><th>'.GetString('Producción / hora').'</th><th>'.GetString('Producción / día').'</th>';
  	if($tecnologia->ID<10)
  	echo '<th>'.GetString('Energía consumida').'</th>';
  	if($tecnologia->ID==11)
  	echo '<th>'.GetString('Antimateria consumida').'</th>';
  	echo '</tr>';

  	$produccionPlaneta=ProduccionMina($tecnologia->ID,$planeta->Tecnologias[$tecnologia->ID],$planeta);
  	if($tecnologia->ID<10 || $tecnologia->ID==11)
  	$gastoPlaneta=GastoEnergiaMina($tecnologia->ID,$planeta->Tecnologias[$tecnologia->ID]);

  	for($contador=0;$contador<15;$contador++)
  	{
  		echo '<tr>';
  		if($nivel==$nivelTecnoPlaneta)
  		$separador='th';
  		else
  		$separador='td';

  		$produccionActual=ProduccionMina($tecnologia->ID,$nivel,$planeta);

  		echo '<'.$separador.'>'.$nivel.'</'.$separador.'><'.$separador.'>'.$produccionActual.CompararProduccion($produccionPlaneta,$produccionActual).'</'.$separador.'>';
  		echo '</'.$separador.'><'.$separador.'>'.RedondearNumero($produccionActual*24).CompararProduccion($produccionPlaneta*24,$produccionActual*24).'</'.$separador.'>';

  		if($tecnologia->ID<10 || $tecnologia->ID==11)
  		{
  			$produccionActual=GastoEnergiaMina($tecnologia->ID,$nivel);
  			echo '<'.$separador.'>'.$produccionActual.CompararProduccion($produccionActual,$gastoPlaneta).'</'.$separador.'>';
  		}

  		echo '</tr>';

  		$nivel++;
  	}
  	echo '</table>';
  }

  function CompararProduccion($produccionPlaneta,$produccion)
  {
  	$resta=$produccion-$produccionPlaneta;

  	if($resta>0)
  	return ' <span style="color:#00FF00">(+'.RedondearNumero($resta).')</span>';
  	else if($resta<0)
  	return ' <span style="color:#FF0000">('.RedondearNumero($resta).')</span>';
  }

  function MostrarFuegoRapidoArmamento()
  {
  	global $caracteristicas;
  	global $tecnologia;

  	echo '<br /><br />';

  	if (!empty($caracteristicas->FuegoRapido))
  	{

  		//Mostrar el fuego rapido
  		foreach ($caracteristicas->FuegoRapido as $fuegoRapido=>$ataque)
  		{
  			//fuegoRapido es un RequisitoTecnologia

  			echo GetString('Fuego rápido contra ').GetTechnology($fuegoRapido)->Name.': <font color="#00FF00">'.$ataque.'</font><br/>';
  		}
  	}
  	//Mostrar las naves y defensas con fuego rapido contra esta
  	for($contador=250;$contador<500;$contador++)
  	{
  		$caracteristicasNaveActual= CaracteristicasBaseArmamento($contador);

  		if(empty($caracteristicasNaveActual))continue;

  		foreach ($caracteristicasNaveActual->FuegoRapido as $fuegoRapido=>$ataque)
  		{
  			//fuegoRapido es un RequisitoTecnologia
  			if ($fuegoRapido==$tecnologia->ID)
  			{
  				echo sprintf(GetString('Fuego rápido de %s contra este tipo de nave: '),GetTechnology($contador)->Name).'<font color="#FF0000">'.$ataque.'</font><br/>';
  				continue;
  			}
  		}
  	}
  }

  function MostrarCaracteristicasArmamento()
  {
  	global $caracteristicas;
  	global $tecnologia;

  	$caracteristicasActuales=CaracteristicasActualesArmamento($tecnologia->ID);
 	?>
    <table class="descriptionTable">
    <tr>
      <th><?php EchoString('Característica'); ?></th>
      <th><?php EchoString('Base'); ?></th>
      <th><?php EchoString('Actual'); ?></th>
    </tr>
    <tr title="<?php EchoString('Cantidad de daño que puede absorver la nave sin dañar el casco.'); ?>">
      <td><?php EchoString('Escudo'); ?></td>
      <td><?php echo number_format($caracteristicas->Escudo,0,'','.').' '.GetString('puntos'); ?> </td>
      <td><?php echo number_format($caracteristicasActuales->Escudo,0,'','.').' '.GetString('puntos'); ?> </td>
    </tr>
    <tr title="<?php EchoString('Cantidad de daño que puede recibir la nave cuando se queda sin escudo.'); ?>">
      <td><?php EchoString('Casco'); ?></td>
      <td><?php echo number_format($caracteristicas->Casco,0,'','.').' '.GetString('puntos'); ?> </td>
       <td><?php echo number_format($caracteristicasActuales->Casco,0,'','.').' '.GetString('puntos'); ?> </td>
    </tr>
    <tr title="<?php EchoString('Daño causado por cada disparo de la nave.'); ?>">
      <td><?php EchoString('Poder de ataque'); ?></td>
      <td><?php echo number_format($caracteristicas->Ataque,0,'','.').' '.GetString('ptos/disparo');?> </td>
        <td><?php echo number_format($caracteristicasActuales->Ataque,0,'','.').' '.GetString('ptos/disparo'); ?> </td>
    </tr>
    <?php 
    if ($tecnologia->ID==13 || ($tecnologia->ID>=300 && $tecnologia->ID<400))//Nave
	{ ?>
    <tr>
      <td><?php EchoString('Capacidad de carga'); ?></td>
      <td><?php echo number_format($caracteristicas->CapacidadCarga,0,'','.').' '.GetString('unidades');?> </td>
     <td><?php echo number_format($caracteristicasActuales->CapacidadCarga,0,'','.').' '.GetString('unidades');?> </td>
     </tr>
    <tr>
      <td><?php EchoString('Velocidad de vuelo'); ?></td>
      <td><?php echo number_format($caracteristicas->Velocidad,0,'','.').' '.GetString('km/s'); ?></td>
        <td><?php echo number_format($caracteristicasActuales->Velocidad,0,'','.').' '.GetString('km/s');; ?></td>
    </tr>
    <tr title="<?php EchoString('Antimateria consumida para mover la nave un sistema a la velocidad normal (100%)'); ?>">
      <td ><?php EchoString('Consumo de antimateria'); ?></td>
      <td><?php echo $caracteristicas->ConsumoCombustible.' '.GetString('unidades/sistema'); ?> </td>
        <td><?php echo number_format($caracteristicasActuales->ConsumoCombustible,0,'','.').' '.GetString('unidades/sistema'); ?> </td>
    </tr>
    <tr> </tr>
    <?php 
	}
	echo '</table>';
  }

  echo '<a onclick="history.go(-1);">'.GetString('Volver').'</a>';

  ActualizarDatosCabecera();
?>