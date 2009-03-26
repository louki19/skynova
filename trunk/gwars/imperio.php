<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('src/funciones.php');
$filtro=ObtenerValorCookie('filtroImperio','todos');

include('basePage.php');

$consulta=$DB->query('SELECT * FROM `planetas` WHERE `Jugador`='.$jugador->ID.' ORDER BY `Orden` ASC LIMIT 18');

$planetas=array();
while($planeta=$consulta->fetch_assoc())
{
	$planetas[]=new Planeta($planeta);
}
?>
<div align="left">
<?php echo GetString('Filtro').': '; ?>
<select onchange="Mostrar('imperio.php?filtroImperio='+this.value,true)">
<option value="todos"<?php if($filtro=='todos')echo 'selected="true"';?>><?php EchoString('Mostrar todo'); ?></option>
<option value="general"<?php if($filtro=='general')echo 'selected="true"';?>><?php EchoString('Sólo información general'); ?></option>
<option value="edificios"<?php if($filtro=='edificios')echo 'selected="true"';?>><?php EchoString('Sólo edificios'); ?></option>
<option value="investigaciones"<?php if($filtro=='investigaciones')echo 'selected="true"';?>><?php EchoString('Sólo investigaciones'); ?></option>
<option value="hangar"<?php if($filtro=='hangar')echo 'selected="true"';?>><?php EchoString('Sólo hangar'); ?></option>
<option value="defensas"<?php if($filtro=='defensas')echo 'selected="true"';?>><?php EchoString('Sólo defensas'); ?></option>
<option value="hangarDefensas"<?php if($filtro=='hangarDefensas')echo 'selected="true"';?>><?php EchoString('Sólo hangar y defensas'); ?></option>
</select>
</div>
<table class="empireTable">
<?php
MostrarInformacionPlanetas();

switch ($filtro)
{
	case 'todos':
		MostrarInformacionRecursos();
		MostrarInformacionEdificiosProduccion();
		MostrarInformacionEdificiosMilitares();
		MostrarInformacionEdificiosLuna();
		MostrarInformacionInvestigaciones();
		MostrarInformacionHangarGeneral();
		MostrarInformacionHangarMilitar();
		MostrarInformacionDefensa();
		break;
	case 'general':
		MostrarInformacionRecursos();
		break;
	case 'edificios':
		MostrarInformacionEdificiosProduccion(true);
		MostrarInformacionEdificiosMilitares(true);
		MostrarInformacionEdificiosLuna(true);
		break;
	case 'investigaciones':
		MostrarInformacionInvestigaciones(true);
		break;
	case 'hangar':
		MostrarInformacionHangarGeneral(true);
		MostrarInformacionHangarMilitar(true);
		break;
	case 'defensas':
		MostrarInformacionDefensa(true);
		break;
	case 'hangarDefensas':
		MostrarInformacionHangarGeneral();
		MostrarInformacionHangarMilitar();
		MostrarInformacionDefensa();
		break;
}

echo '</table><script type="text/javascript">';

//Datos para la actualización de los recursos
foreach ($planetas as $planetaCargado)
{
	?>
	datosRecursos['metal<?php echo $planetaCargado->ID?>']=new Array('metal<?php echo $planetaCargado->ID?>',<?php echo $planetaCargado->Metal ?>,<?php echo $planetaCargado->Datos['ProduccionMetal'] ?>/3600,<?php echo CapacidadAlmacen(90,$planetaCargado->Tecnologias[90]) ?>);
	datosRecursos['cristal<?php echo $planetaCargado->ID?>']=new Array('cristal<?php echo $planetaCargado->ID?>',<?php echo $planetaCargado->Cristal ?>,<?php echo $planetaCargado->Datos['ProduccionCristal'] ?>/3600,<?php echo CapacidadAlmacen(91,$planetaCargado->Tecnologias[91]) ?>);
	datosRecursos['antimateria<?php echo $planetaCargado->ID?>']=new Array('antimateria<?php echo $planetaCargado->ID?>',<?php echo $planetaCargado->Antimateria ?>,<?php echo $planetaCargado->Datos['ProduccionAntimateria'] ?>/3600,<?php echo CapacidadAlmacen(92,$planetaCargado->Tecnologias[92]) ?>);
	<?php
}

echo '</script>';
ActualizarDatosCabecera(false);

//Funciones

function MostrarInformacionPlanetas()
{
	global $planetas;
	global $filtro;
	?>
	<tr><th colspan="0"><?php EchoString('Planetas'); ?></th>
	</tr><tr><td width="450">&nbsp;</td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.ImagenPlaneta($planeta->Datos,false,true).'</td>';
	}
	?>
	</tr><tr><td><?php EchoString('Nombre'); ?></td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.$planeta->Nombre.'</td>';
	}
	?>
	</tr><tr><td><?php EchoString('Coordenadas'); ?></td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.MostrarLocalizacionPlaneta($planeta->Datos,true).'</td>';
	}
	echo '</tr>';
	if($filtro=='general' || $filtro=='todos')
	{
		?>
		<tr><td><?php EchoString('Campos'); ?></td>
		<?php
		foreach ($planetas as $planeta)
		{
			echo '<td>'.$planeta->Datos['CamposOcupados'].' / '.$planeta->Datos['CamposTotales'].'</td>';
		}
		?></tr>
		<?php
	}
}

function MostrarInformacionRecursos()
{
	global $planetas;
	global $jugador;
	?>
	<tr><th colspan="0"><?php EchoString('Recursos'); ?></th>
	</tr><tr><td><img src="<?php echo $jugador->UrlSkin ?>images/metal.png" title="<?php EchoString('Metal'); ?>"/></td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.MostrarCantidadRecusos(1,$planeta,false,'metal'.$planeta->ID).'</td>';
	}
	?>
	</tr><tr><td><img src="<?php echo $jugador->UrlSkin ?>images/crystal.png" title="<?php EchoString('Cristal'); ?>"/></td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.MostrarCantidadRecusos(2,$planeta,false,'cristal'.$planeta->ID).'</td>';
	}
	?>
	</tr><tr><td><img src="<?php echo $jugador->UrlSkin ?>images/antimatter.png" title="<?php EchoString('Antimateria'); ?>"/></td>
	<?php
	foreach ($planetas as $planeta)
	{
		echo '<td>'.MostrarCantidadRecusos(3,$planeta,false,'antimateria'.$planeta->ID).'</td>';
	}
	?>
	</tr><tr><td><img src="<?php echo $jugador->UrlSkin ?>images/energy.png" title="<?php EchoString('Energía'); ?>"/></td>
	<?php
	foreach ($planetas as $planeta)
	{
		$planeta->CargarRendimientos();
		echo '<td>'.MostrarCantidadRecusos(4,$planeta,true).'</td>';
	}

	echo '</tr>';
}

function MostrarInformacionEdificiosProduccion($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(1,20,GetString('Edificios de producción'),$mostrarNoDisponibles);
}

function MostrarInformacionEdificiosMilitares($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(20,90,GetString('Edificios militares'),$mostrarNoDisponibles);
}

function MostrarInformacionInvestigaciones($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(100,200,GetString('Investigaciones'),$mostrarNoDisponibles);
}

function MostrarInformacionHangarGeneral($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(250,305,GetString('Hangar general'),$mostrarNoDisponibles);
}

function MostrarInformacionHangarMilitar($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(305,400,GetString('Hangar militar'),$mostrarNoDisponibles);
}

function MostrarInformacionDefensa($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(400,502,GetString('Defensa'),$mostrarNoDisponibles);
}

function MostrarInformacionEdificiosLuna($mostrarNoDisponibles=false)
{
	return MostrarInformacionTecnologia(200,300,GetString('Edificios lunares'),$mostrarNoDisponibles);
}

function MostrarInformacionTecnologia($inicio,$final,$texto,$mostrarNoDisponibles)
{
	global $planetas;

	$html='';
	for($tecnologia=$inicio;$tecnologia<$final;$tecnologia++)
	{
		$tecno=GetTechnology($tecnologia);
		if(empty($tecno))
		continue;

		$algunoDistintoCero=false;
		$cadena= '<tr><td>'.$tecno->Name.'</td>';
		foreach($planetas as $planeta)
		{
			if(isset($planeta->Tecnologias[$tecnologia]) &&!empty($planeta->Tecnologias[$tecnologia]))
			{
				$algunoDistintoCero=true;
			}
			$cadena.='<td>'.$planeta->Tecnologias[$tecnologia].'</td>';
		}
		if($algunoDistintoCero==true)
		$html.=$cadena.'</tr>';
	}
	if(!empty($html))
	{
		echo '<tr><th colspan="0">'.$texto.'</th></tr>'.$html;
		return true;
	}
	else
	{
		if($mostrarNoDisponibles)
		echo '<tr><th colspan="99">'.$texto.'</th></tr>
		<tr><td colspan="0">'.GetString('No hay datos que mostrar').'</td></tr>';
		return false;
	}
}
?>
