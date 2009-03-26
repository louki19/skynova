<?php
include('src/funciones.php');

$orden=ObtenerValorCookie('orden','Puntos');
$alineacion=ObtenerValorCookie('alinear','descendente');

$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

if(empty($jugador->Datos['Alianza']))
{
	MostrarError(GetString('No estás registrado en ninguna alianza'));
}

$alianza=$DB->getRow('alianzas',$jugador->Datos['Alianza']);

$rango=$DB->getRowProperties('rangosalianza',$jugador->Datos['RangoAlianza'],'AdministrarAlianza,ExpulsarMiembro,VerListaMiembros');
if(empty($rango['VerListaMiembros']))
{
	MostrarError(GetString('No tienes suficientes privilegios para poder acceder a esta sección'));
}

if($alineacion=='descendente')
$alineacionContraria='ascendente';
else
$alineacionContraria='descendente';

$_GET['expulsar']=43;
if(!empty($_GET))
ProcesarGet();

if(!empty($_POST))
ProcesarPost();
?>
<form method="post">
<table class="memberListTable">
  <tr>
    <th colspan="10"><?php echo GetString('Lista de miembros de la alianza').' '.$alianza['Nombre']; ?></th>
  </tr>
  <tr>
    <th><?php EchoString('N.'); ?></th>
    <th><a onclick="Mostrar('?orden=Nombre&alinear=<?php echo $alineacionContraria.'\')">'.GetString('Nombre'); ?></a></th>
    <th><a onclick="Mostrar('?orden=Rango&alinear=<?php echo $alineacionContraria.'\')">'.GetString('Rango'); 
    if($rango['AdministrarAlianza']!=0)
    echo '<a onclick="Mostrar(\'?editarRangos\')" title="'.GetString('Editar rangos de los jugadores').'"> [+]</a>'
    ?></th>
    <th><a onclick="Mostrar('?orden=Puntos&alinear=<?php echo $alineacionContraria.'\')">'.GetString('Puntos'); ?></th>
    <th><a onclick="Mostrar('?orden=Ranking&alinear=<?php echo $alineacionContraria.'\')">'.GetString('Ranking'); ?></th>
    <th><?php EchoString('Acción'); ?></th>
    <?php  	if($rango['AdministrarAlianza']!=0)
    {
    ?>
    <th><a onclick="Mostrar('?orden=UltimoAcceso&alinear=<?php echo $alineacionContraria.'\')">'.GetString('Ultimo acceso'); ?></th>    
     <th><?php EchoString('Opciones admin.'); ?></th>
 <?php }     ?>
  </tr>
<?php
if($alineacion=='descendente')
$alineacion='DESC';
else
$alineacion='ASC';

if($orden!='Nombre' && $orden!='Rango' && $orden!='Puntos' && $orden!='Ranking')
$orden='Puntos';

if($orden=='Rango')
$orden='RangoAlianza';

$consulta=$DB->query('SELECT ID,Nombre,Fundador FROM `rangosalianza` WHERE `Alianza`='.$alianza['ID']);

$opcionesRango;
while($rangoObtenido=$consulta->fetch_assoc())
{
	$rangos[$rangoObtenido['ID']]=$rangoObtenido;
}

$consulta=$DB->query('SELECT ID,RangoAlianza,Nombre,UltimoAcceso,Puntos,Ranking,PlanetaPrincipal FROM `jugadores` WHERE `Alianza`='.$alianza['ID'].'  ORDER BY `'.$DB->escape_string($orden).'` '.$DB->escape_string($alineacion).' LIMIT '.$alianza['Miembros']);
$numero=1;

while($miembro=$consulta->fetch_assoc())
{
	//$planetaPrincipal=$DB->getRowProperties('planetas',$miembro['PlanetaPrincipal'],'Galaxia,Sistema,Posicion');

	if($rango['AdministrarAlianza']!=0 && $rangos[$miembro['RangoAlianza']]['Fundador']!=1 && isset($_GET['editarRangos']))
	{
		$textoRango='<select name="rango'.$miembro['ID'].'">';

		foreach ($rangos as $rangoObtenido)
		{
			if($rangoObtenido['Fundador']!=1)
			{
				if($rangoObtenido['ID']!=$miembro['RangoAlianza'])
				$textoRango.='<option value="'.$rangoObtenido['ID'].'">'.$rangoObtenido['Nombre'].'</option>';
				else
				$textoRango.='<option value="'.$rangoObtenido['ID'].'" selected="true">'.$rangoObtenido['Nombre'].'</option>';
			}
		}

		$textoRango.='</select>';
	}
	else
	$textoRango=$rangos[$miembro['RangoAlianza']]['Nombre'];

	echo '<tr><td>'.$numero.'</td><td>'.$miembro['Nombre'].'</td><td>'.$textoRango.'</td><td>'.$miembro['Puntos'].'</td><td>'.$miembro['Ranking'].'</td>
    <td>'.IconoEnviarMensajeJugador($miembro).'</td>';
	if($rango['AdministrarAlianza']!=0)
	{
		if($rangos[$miembro['RangoAlianza']]['Fundador']==1)
		echo '<td>&nbsp;</td><td>&nbsp;</td>';
		else
		echo '<td>'.GetString('Hace').' '.TiempoInactivo($miembro).'</td>
    <td><a onclick="Mostrar(\'?expulsar='.$miembro['ID'].'\')">Expulsar</a></td>';
	}
	echo '</tr>';
	$numero++;
}
echo '</table>';
if($rango['AdministrarAlianza']!=0 && isset($_GET['editarRangos']))
echo '<input type="submit" value="'.GetString('Guardar').'">';
echo '</form>';


function TiempoInactivo($miembro)
{
	$tiempoUltimoAcceso=time()-$miembro['UltimoAcceso'];

	$tiempoInactivo=floor($tiempoUltimoAcceso/86400);

	if($tiempoInactivo==0)
	$tiempoInactivo=floor($tiempoUltimoAcceso/3600).' '.GetString('horas');
	else
	$tiempoInactivo.=' '.GetString('días');

	return $tiempoInactivo;
}

function ProcesarPost()
{
	global $DB;
	global $alianza;

	$consulta=$DB->query('SELECT ID,Nombre,Fundador FROM `rangosalianza` WHERE `Alianza`='.$alianza['ID']);

	while($rangoObtenido=$consulta->fetch_assoc())
	{
		$rangos[$rangoObtenido['ID']]=$rangoObtenido;
	}

	$consulta=$DB->query('SELECT ID,RangoAlianza FROM `jugadores` WHERE `Alianza`='.$alianza['ID'].' LIMIT '.$alianza['Miembros']);
	while($miembro=$consulta->fetch_assoc())
	{
		$valor=$_POST['rango'.$miembro['ID']];
		if(!empty($valor) && is_numeric($valor))
		{
			if($miembro['RangoAlianza']!=$valor && $rangos[$valor]['Fundador']!=1)
			{
				$DB->setRowProperty('jugadores',$miembro['ID'],'RangoAlianza',$valor);
			}
		}
	}
}

function ProcesarGet()
{
	global $alianza;
	global $rango;
	global $DB;

	if(isset($_GET['expulsar']) && is_numeric($_GET['expulsar']) && $rango['AdministrarAlianza'])
	{
		$jugador=$DB->getRowProperties('jugadores',$_GET['expulsar'],'ID,Nombre,Alianza,RangoAlianza');

		if($jugador['Alianza']==$alianza['ID'])
		{
			if($DB->getRowProperty('rangosalianza',$jugador['RangoAlianza'],'Fundador')==0)
			{
				include('src/mensaje.php');
				EnviarMensaje(GetString('Alianza').' '.$alianza['Nombre'],$jugador['ID'],GetString('Has sido expulsado de la alianza'),
				sprintf(GetString('Has sido expulsado de la alianza %s. Ponte en contacto con su administrador para conocer los motivos de esta expulsión.'),$alianza['Nombre']),
				'alianza');

$prop=array('Alianza'=>0,
				'RangoAlianza'=>0
				);
				$DB->setRowProperties('jugadores',$jugador['ID'],$prop);

				$DB->setRowProperty('alianzas',$alianza['ID'],'Miembros',$alianza['Miembros']-1);
				$alianza['Miembros']--;

				if($alianza['AvisosMiembros']!=1)
				{
					EnviarMensajeCircular($alianza['ID'],GetString('Alianza').' '.$alianza['Nombre'],GetString('Expulsión de un miembro'),
					sprintf(GetString('El jugador %s ha sido expulsado de la alianza.'),$jugador['Nombre']));
				}
			}
		}
	}
}
?>