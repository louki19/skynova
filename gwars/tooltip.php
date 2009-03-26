<?php
$id=$_GET['id'];
if(is_numeric($id)==false)
exit;

if($_GET['tipo']!='planetaGalaxia' && $_GET['tipo']!='luna' && $_GET['tipo']!='escombros')
$tipoInicio=2;//No cargar nada

include('basePage.php');
switch ($_GET['tipo'])
{
	case 'nave':
		ToolTipNave($id);
		break;
	case 'planetaJugador':
		ToolTipPlaneta($id);
		break;
	case 'planetaGalaxia':
		ToolTipPlanetaGalaxia($id);
		break;
	case 'escombros':
		ToolTipEscombros($id);
		break;
	case 'jugador':
		ToolTipJugador($id);
		break;
	case 'alianza':
		ToolTipAlianza($id);
		break;
	case 'luna':
		ToolTipLuna($id);
		break;
}

function ToolTipNave($id)
{
	//$nave es una clase Technology
	global $jugador;
	$nave=GetTechnology($id);
	$caracteristicas= CaracteristicasActualesArmamento($id);

	$textoCaracteristicas=
	'<strong>'.GetString('Capacidad de carga').'</strong>: '.$caracteristicas->CapacidadCarga.
	'<br><strong>'.GetString('Velocidad').'</strong>: '.$caracteristicas->Velocidad.
	'<br><strong>'.GetString('Consumo de combus.').'</strong>: '.$caracteristicas->ConsumoCombustible.
	'<br><strong>'.GetString('Casco').'</strong>: '.$caracteristicas->Casco.
	'<br><strong>'.GetString('Escudo').'</strong>: '.$caracteristicas->Escudo.
	'<br><strong>'.GetString('Poder de ataque').'</strong>: '.$caracteristicas->Ataque;

	echo '<table class="shipToolTipTable">
<tr>
<th colspan="2">'.$nave->Name.'</th>
</tr>
<tr>
<td rowspan="2">
<a onclick="Mostrar(\'descripcion.php?id='.$nave->ID.'\')">
<img class="shipToolTipImage" src="'.$jugador->UrlSkin.'/technology/'.$nave->ID.'.png" />
</a>
</td>
</tr>
<tr>
<td>'.$textoCaracteristicas.'</td>
</tr>
</table>';
}


function ToolTipPlaneta($id)
{
	$planeta=new Planeta($id);

	if($planeta->Datos['Jugador']!=$_SESSION['id'])
	return;

	echo '<table class="planetToolTip"><tr>
<th colspan="4">'.MostrarLocalizacionPlaneta($planeta->Datos,true).'</th>
</tr><tr><td><img src="'.$_GET['skin'].'images/metal.png"/></td>
<td><img src="'.$_GET['skin'].'images/crystal.png"/></td>
<td><img src="'.$_GET['skin'].'images/antimatter.png"/></td>
<td><img src="'.$_GET['skin'].'images/energy.png"/></td></tr>
<tr><td>'.number_format($planeta->Metal,0,'.','.').'</td>
<td>'.number_format($planeta->Cristal,0,'.','.').'</td>
<td>'.number_format($planeta->Antimateria,0,'.','.').'</td>
<td>'.number_format($planeta->Energia,0,'.','.').'</td></tr>';

	if($planeta->Trabajando(1))
	{
		echo '<tr><th colspan="4">'.GetString('Edificio en construcción').'</th>
<tr><td colspan="4">'.GetTechnology($planeta->Datos['Construcciones'][1])->Name.'</td></tr>';
	}
		if($planeta->Trabajando(3))
	{
		echo '<tr><th colspan="4">'.GetString('Nave en construcción').'</th>
<tr><td colspan="4">'.GetTechnology($planeta->Datos['Construcciones'][3])->Name.'</td></tr>';
	}
		if($planeta->Trabajando(4))
	{
		echo '<tr><th colspan="4">'.GetString('Defensa en construcción').'</th>
<tr><td colspan="4">'.GetTechnology($planeta->Datos['Construcciones'][4])->Name.'</td></tr>';
	}

	$flota='';
	$contador=0;
	for($nave=315;$nave>298;$nave--)
	{
		if($contador>6)
		{
			$flota.='...';
			break;
		}
		if(isset($planeta->Datos['Tecnologia'][$nave]) && $planeta->Datos['Tecnologia'][$nave]>0)
		{
			$flota.=GetTechnology($nave)->Name.', '.$planeta->Tecnologias[$nave].'<br>';
			$contador++;
		}
	}
	if(!empty($flota))
	echo '<tr><th colspan="4">'.GetString('Flotas en el planeta').'</th>
</tr><tr><td colspan="4">'.$flota.'</td></tr></table>';
	
	//MOSTRAR LOS ESCOMBROS QUE HAY
		if(!empty($planeta->Datos['EscombrosMetal']) || !empty($planeta->Datos['EscombrosCristal']))
	{
		echo '<tr><th colspan="4">'.GetString('Escombros').'</th></tr>
<tr><td rowspan="2"><img src="'.$_GET['skin'].'images/debris_small.png"/></td><td colspan="0">'.GetString('Metal').': '.$planeta->Datos['EscombrosMetal'].'</td></tr>
<tr><td colspan="0">'.GetString('Cristal').': '.$planeta->Datos['EscombrosCristal'].'</td></tr>';
	
	}

}

function ToolTipPlanetaGalaxia($id)
{
	global $DB;

	$planeta=$DB->getRowProperties('planetas',$id,'ID,Nombre,Galaxia,Sistema,Posicion,Imagen,Fondo');

	echo '<table class="toolTipTable" style="vertical-align:middle;">
<tr><th colspan="2">'.MostrarLocalizacionPlaneta($planeta).'</th></tr>
<tr><td rowspan="2"><div align="center">'.ImagenPlaneta($planeta,false,false).'</div></td>';

	if($planeta['Jugador']!=$GLOBALS['jugador']->ID)
	echo AccionesPlaneta($planeta);

	echo'</table>';
}

function ToolTipEscombros($id)
{
	global $jugador;
	global $planeta;
	global $DB;

	$planetaGalaxia=$DB->getRowProperties('planetas',$id,'ID,Galaxia,Sistema,Posicion,EscombrosMetal,EscombrosCristal');
	$localizacion = '['.$planetaGalaxia['Galaxia'].':'.$planetaGalaxia['Sistema'].':'.$planetaGalaxia['Posicion'].']';

	echo '<table width="275" class="toolTipTable"><tr>
<th colspan="2">'.GetString('Escombros').' '.$localizacion.'</th></tr>
<tr><td width="100" height="100" rowspan="4" style="vertical-align:middle;">
<img src="'.$jugador->UrlSkin.'images/debris.jpg" width="100" height="100"/></td>
<th>'.GetString('Recursos').'</th></tr>
<tr><td>'.GetString('Metal').': '.$planetaGalaxia['EscombrosMetal'].'</td></tr>
<tr><td>'.GetString('Cristal').': '.$planetaGalaxia['EscombrosCristal'].'</td></tr>';

	if($planeta->Tecnologias[302]>0)
	echo '<tr><td>Recolectar</td></tr>';

	echo '</table>';
}

function ToolTipLuna($id)
{
	global $planeta;
	global $DB;


	if(is_numeric($_GET['galaxia']) && is_numeric($_GET['sistema']) && is_numeric($_GET['posicion']))
	{
		$consulta=$DB->query('SELECT * FROM `planetas` WHERE `Galaxia`='.$_GET['galaxia'].' && `Sistema`='.$_GET['sistema'].' && `Posicion`='.$_GET['posicion'].' && `Luna`=1 LIMIT 1');
		$luna=$consulta->fetch_assoc();
	}
	else
	$luna=$DB->getRowProperties('planetas',$id,'ID,Nombre,Jugador,Galaxia,Sistema,Posicion,Luna,Fondo,CamposTotales,Temperatura');
	
	echo '<table width="275" class="toolTipTable">
<tr ><th colspan="2">'.MostrarLocalizacionPlaneta($luna).'</th>
</tr><tr><td rowspan="5"><div align="center">'.ImagenPlaneta($luna,false,false).'</div>
</td><th>'.GetString('Informaci&oacute;n').'</th></tr>
<tr><td>'.GetString('Tama&ntilde;o').': '.($luna['CamposTotales']*80).'</td></tr>
<tr><td>'.GetString('Temperatura').': '.$luna['Temperatura'].'&ordm; </td></tr>';

	if($luna['Jugador']!=$GLOBALS['jugador']->ID)
	echo AccionesPlaneta($luna);
	echo '</table';
}

function AccionesPlaneta($planetaGalaxia)
{
	global $jugador;
	global $planeta;

	$res='';

	if($planeta->Tecnologias[303]>0)
	$res=GetString('Espiar').'<br />';

	if($planeta->HayNaves())
	{
		$res.=GetString('Atacar').'<br />';
		$res.=GetString('Transportar').'<br />';
	}
	if($planetaGalaxia['Luna']==1 && $planeta->Tecnologias[312]>0)
	$res.=GetString('Destruir').'<br />';

	if($planeta->Tecnologias[81]>0)
	$res.=GetString('Analizar con el sensor').'<br />';


	if(!empty($res))
	return '<th>'.GetString('Acciones').'</th><tr><td>'.$res.'</td></tr>';
}

function ToolTipJugador($id)
{
	global $DB;

	$jugadorInfo=$DB->getRowProperties('jugadores',$id,'Nombre,Ranking,MetaDatos');
	$metadatos=unserialize($jugadorInfo['MetaDatos']);

	if(!empty($jugadorInfo['Ranking']) && $jugadorInfo['Ranking']<=1500)
	$ranking=' '.GetString('en el ranking').' '.$jugadorInfo['Ranking'];

	echo '<table width="300" class="toolTipTable">
<tr><td rowspan="3"><img src="http://www.gravatar.com/avatar.php?gravatar_id='.md5(isset($metadatos['IdGravatar'])?$metadatos['IdGravatar']:$metadatos['Email']).'&rating=G"></td>
<th>'.GetString('Jugador').' '.$jugadorInfo['Nombre'].$ranking.'</th></tr>';

	if($jugadorInfo['ID']==$_SESSION['ID'])
	{
		echo '<tr><td><a onclick="Mostrar(\'enviarMensaje.php?jugador='.$id.'\')">'.GetString('Enviar mensaje').'</a></td></tr>';
	}

	if(!empty($jugadorInfo['Ranking']) && $jugadorInfo['Ranking']<=1500)
	{
		$posicion=floor($jugadorInfo['Ranking']/100)*100;
		echo '<tr><td><a onclick="Mostrar(\'estadisticas.php?mostrar=jugadores&marcar='.$jugadorInfo['Ranking'].'&posiciones='.$posicion.'\')">'.GetString('Buscar en estadisticas').'</a></td></tr>';
	}
	echo '</table>';
}

function ToolTipAlianza($id)
{
	global $DB;

	$alianza=$DB->getRowProperties('alianzas',$id,'Nombre,Ranking,Puntos,Miembros,UrlLogo');

	if(!empty($alianza['Ranking']) && $alianza['Ranking']<=1500)
	$ranking=' '.GetString('en el ranking').' '.$alianza['Ranking'];

	echo '<table width="350" class="toolTipTable">
<tr><td colspan="2"><strong>'.GetString('Alianza').' '.$alianza['Nombre'].$ranking.'</strong></td></tr>
<tr><td rowspan="50"><img height="140" width="140" src="'.$alianza['UrlLogo'].'"/></td>
<th>'.GetString('Información').'</th></tr>
<tr><td>'.$alianza['Puntos'].' '.GetString('puntos').'<br/>
'.$alianza['Miembros'].' '.GetString('miembros').'</td></tr>
<tr><th>'.GetString('Herramientas').'</th></tr>
<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$id.'\')">'.GetString('Ver informacion de alianza').'</a><br />
<a onclick="Mostrar(\'estadisticas.php?mostrar=alianzas&posiciones='.(floor($alianza['Ranking']/100)*100).'\')">'.GetString('Buscar en estadisticas').'</a><br />';

	$consulta=$DB->first_row('SELECT rangosalianza.RepresentarAlianza FROM `jugadores` JOIN rangosalianza ON rangosalianza.ID = jugadores.RangoAlianza WHERE jugadores.ID = '.$_SESSION['id']);
	if($id!=$GLOBALS['jugador']->Datos['Alianza'] && $consulta[0]!=0)
	{
		echo '<a onclick="Mostrar(\'enviarMensaje.php?alianza='.$id.'\')" >'.GetString('Enviar mensaje a la alianza').'</a><br/>';
	}
	echo '</tr></table>';
}
?>
