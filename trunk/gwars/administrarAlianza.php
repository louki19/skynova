<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');
include('src/alianza.php');

if(empty($jugador->Datos['Alianza']))
MostrarError(GetString('No estás registrado en ninguna alianza'),true);

$claseAlianza=new Alianza($jugador->Datos['Alianza']);
$alianza=$claseAlianza->ObtenerDatos();

$rango=new Rango($jugador->Datos['RangoAlianza']);
$rango->ObtenerCompetencias('AdministrarAlianza,Fundador,RevisarSolicitudes,AdministrarPactos');

if(!empty($_POST))
GuardarPost();

if(empty($_GET))
$_GET['opciones']=1;

/**
 * Mostrar las pestañas
 */
CabeceraTab();
if($rango->PoseeCompetencia('AdministrarAlianza'))
{
	Tab(GetString('Opciones'),'opciones');
	Tab(GetString('Rangos'),'rangos');
	Tab(GetString('Sec. externa'),'secExterna');
	Tab(GetString('Sec. interna'),'secInterna');
}
if($rango->PoseeCompetencia('AdministrarPactos'))
{
	Tab(GetString('Pactos'),'pactos');
	Tab(GetString('Guerras'),'guerras');
}
if($rango->PoseeCompetencia('RevisarSolicitudes'))
{
	Tab(GetString('Solicitudes'),'solicitudes');
}
if($rango->PoseeCompetencia('Fundador'))
{
	Tab(GetString('Avanzadas'),'avanzadas');
}
CierreTab();

if(isset($_GET['pactos']))
{
	ComprobarRango('AdministrarPactos');
	MostrarPactos();
}
else if(isset($_GET['guerras']))
{
	ComprobarRango('AdministrarPactos');
	MostrarGuerras();
}
else if(isset($_GET['rangos']))
{
	ComprobarRango('AdministrarAlianza');
	MostrarRangos();
}
else if(isset($_GET['solicitudes']))
{
	ComprobarRango('RevisarSolicitudes');
	MostrarSolicitudes();
}
else if(isset($_GET['secInterna']))
{
	ComprobarRango('AdministrarAlianza');
	MostrarSeccion(1);
}
else if(isset($_GET['secExterna']))
{
	ComprobarRango('AdministrarAlianza');
	MostrarSeccion(2);
}
else if(isset($_GET['avanzadas']))
{
	ComprobarRango('Fundador');
	MostrarOpcionesAvanzadas();
}
else
{
	ComprobarRango('AdministrarAlianza');
	MostrarOpcionesAlianza();
}
echo '</div>';

/**
 * Fin de la presentacion
 *
 * Funciones:
 */

function ComprobarRango($id)
{
	if($GLOBALS['rango']->PoseeCompetencia($id))
	return;
	else
	MostrarError(GetString('No tienes suficientes privilegios para poder acceder a esta sección'));
}

function MostrarOpcionesAvanzadas()
{
	?>	
	<script type="text/javascript">
	function Preguntar(texto,accionRealizar)
	{
		nombre=prompt(texto+"\r\n<?php EchoString('Escribe \'ok\' para confirmar la acción'); ?>","");

		if(nombre=="ok")
		{
			Mostrar('?avanzadas',false,'realizar='+accionRealizar);
		}
		else if(nombre!=null)
		{
			alert("<?php EchoString('Escribe correctamente \'ok\' para realizar la acción.'); ?>")
		}
	}
	</script>
<form method="POST" action="" id="formularioAvanzado">
<input type="hidden" name="accionAvanzada" value="hola">
  <input type="button" onclick="Preguntar('<?php EchoString('¿Estás seguro de que quieres disolver la alianza? Esta acción no se puede deshacer, los miembros y tu quedaréis sin alianza y tendréis que buscar una nueva.'); ?>','disolver');" value="<?php EchoString('Disolver alianza');?>"><br><br>
 <input type="button" onclick="Preguntar('<?php EchoString('¿Estás seguro de que quieres disolver la alianza? Esta acción no se puede deshacer, los miembros y tu quedaréis sin alianza y tendréis que buscar una nueva.'); ?>','disolver');" value="<?php EchoString('Transferir el poder');?>">
</form>
<?php 
}

function MostrarRangos()
{
	global $alianza;
	global $DB;
	global $rango;

	$consulta=$DB->query('SELECT * FROM `rangosalianza` WHERE `Alianza`='.$alianza['ID']);
	$rangos;
	while($rangoAlianza=$consulta->fetch_assoc())
	$rangos[]=$rangoAlianza;

	echo '<form method="post"><table class="allianceSettingsTable">
	<tr><th rowspan="2">'.GetString('Capacidad').'</th>
	<td colspan="99">'.GetString('Nombre').'</td></tr><tr>';

	foreach($rangos as $rangoAlianza)
	{
		echo '<td><input type="text" maxlength="15" size="8" name="nombreRango'.$rangoAlianza['ID'].'" value="'.$rangoAlianza['Nombre'].'">';

		if($rangoAlianza['Fundador']!=0)
		echo '('.GetString('Fundador').')';
		echo '</td>';
	}
	echo '</tr>';

	MostrarCapacidadRango('AdministrarAlianza',GetString('Administrar la alianza'),$rangos);
	MostrarCapacidadRango('RepresentarAlianza',GetString('Representar la alianza'),$rangos);
	MostrarCapacidadRango('CrearCC',GetString('Crear correo circular'),$rangos);
	MostrarCapacidadRango('RevisarSolicitudes',GetString('Revisar las solicitudes de acceso'),$rangos);
	MostrarCapacidadRango('AdministrarPactos',GetString('Administrar pactos y guerras'),$rangos);
	MostrarCapacidadRango('ExpulsarMiembro',GetString('Expulsar miembros'),$rangos);
	MostrarCapacidadRango('VerListaMiembros',GetString('Ver la lista de miembros'),$rangos);

	echo '<tr><td>'.GetString('Borrar rango').'</td>';
	foreach($rangos as $rangoAlianza)
	{
		if($rangoAlianza['Fundador']==0)
		{
			global $jugador;
			echo '<td><img onclick="Mostrar(\'?rangos\',true,\'borrarRango='.$rangoAlianza['ID'].'\')" src="'.$jugador->UrlSkin.'images/delete.png" title="'.GetString('Borrar rango').'" style="cursor:pointer;"></td>';
		}
		else
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';

	echo '</table><br><input type="submit" value="'.GetString('Guardar').'" /></form><br><br>';

	echo '<span class="specialText">'.GetString('Crear nuevo rango').'</span>
	<form method="post" action=""><label>'.GetString('Nombre').': 
	<input type="text" maxlength="15" name="nombreNuevoRango">
	<input type="submit" value="'.GetString('Crear').'" /></label></form>';
}

function MostrarCapacidadRango($id,$texto,$rangos)
{
	echo '<tr><td>'.$texto.'</td>';

	foreach($rangos as $rango)
	{
		if($rango['Fundador']!=0)
		echo '<td>&nbsp;</td>';
		else
		{
			echo '<td><input type="checkbox"';

			if($rango[$id]!=0)
			echo ' checked="true"';

			echo ' name="rango'.$id.$rango['ID'].'"></td>';
		}
	}
}

function MostrarSeccion($id)
{
	global $alianza;
	global $tinymceLanguage;

	echo '<iframe class="htmlEditor" src="tinymce.php?seccion='.$id.'&lang='.$tinymceLanguage.'&formatDate='.GetString('%d-%m-%Y');
}

function MostrarPactos()
{
	$pactos=$GLOBALS['claseAlianza']->ObtenerRelaciones();
	?>
	<table class="allianceSettingsTable">
  <tr>
    <th colspan="2"><?php EchoString('Solicitudes de pactos de no agresión recibidas'); ?></th>
  </tr>
  <?php
  if(!isset($pactos[1]['SolicitudPNA']) || count($pactos[1]['SolicitudPNA'])==0)
  {
  	echo '<tr><td colspan="2"><br>'.GetString('No hay ninguna solicitud para un pacto de no agresión.').'</td></tr>';
  }
  else
  {
  	foreach ($pactos[1]['SolicitudPNA'] as $idAlianza=>$nombre)
  	{
  		echo '<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$idAlianza.'\')">'.$nombre.'</a></td><td><form method="post">
  		<input type="submit" name="aceptarPna'.$idAlianza.'" value="'.GetString('Aceptar').'" />
  		<input type="submit" name="rechazarPna'.$idAlianza.'" value="'.GetString('Rechazar').'" />
  		</form></td></tr>';
  	}
  }
   ?>
   <tr>
    <th colspan="2"><?php EchoString('Solicitudes de pactos de no agresión enviadas'); ?></th>
  </tr>
  <?php
  if(!isset($pactos[0]['SolicitudPNA']) || count($pactos[0]['SolicitudPNA'])==0)
  {
  	echo '<tr><td colspan="2"><br>'.GetString('No se ha mandado ninguna solicitud un pacto de no agresión.').'</td></tr>';
  }
  else
  {
  	foreach ($pactos[0]['SolicitudPNA'] as $idAlianza=>$nombre)
  	{
  		echo '<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$idAlianza.'\')">'.$nombre.'</a></td><td>
  		<form method="post"><input type="submit" name="retirarPna'.$idAlianza.'" value="'.GetString('Retirar').'" />'.'<br>
    </form></td></tr>';  		  		
  	}
  }
   ?>
    <tr>
    <th colspan="2"><?php EchoString('Pactos de no agresión vigentes'); ?></th>
  </tr>  
  <?php
  if(!isset($pactos['PNA']) || count($pactos['PNA'])==0)
  {
  	echo '<tr><td colspan="2"><br>'.GetString('No hay ningún pacto de no agresión vigente en la actualidad.').'</td></tr>';
  }
  else
  {
  	foreach ($pactos['PNA'] as $idAlianza=>$nombre)
  	{
  		echo '<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$idAlianza.'\')">'.$nombre.'</a></td><td>
  		<form method="post"><input type="submit" name="cancelarPna'.$idAlianza.'" value="'.GetString('Cancelar pacto').'" /><br>
    </form></td></tr>';
  	}
  }
  ?>
  </table></form>
	  <?php 
}

function MostrarGuerras()
{
	$pactos=$GLOBALS['claseAlianza']->ObtenerRelaciones();
	?>
	  <form method="post" action="">
<table class="allianceSettingsTable">
  <tr><th colspan="2"><?php EchoString('Guerras declaradas a otras alianzas'); ?></th></tr>
    <?php
    if(!isset($pactos[0]['Guerra']) || count($pactos[0]['Guerra'])==0)
    {
    	echo '<tr><td><br>'.GetString('No hay ninguna guerra declarada.').'</td></tr>';
    }
    else
    {
    	foreach ($pactos[0]['Guerra'] as $idAlianza=>$nombre)
    	{
    		echo '<tr><td><a onclick="Mostrar(\'alianza.php?alianza='.$idAlianza.'\')">'.$nombre.'</a></td>
    	<td><input type="submit" name="guerra'.$idAlianza.'" value="'.GetString('Finalizar la guerra').'" /></td></tr>';
    	}
    }
   ?>
    <tr><th colspan="2"><?php EchoString('Guerras declaradas a esta alianza'); ?></th></tr>
  <?php
  if(!isset($pactos[1]['Guerra']) || count($pactos[1]['Guerra'])==0)
  {
  	echo '<tr><td colspan="2"><br>'.GetString('Ninguna alianza ha declarado la guerra a esta alianza.').'</td></tr>';
  }
  else
  {
  	foreach ($pactos[1]['Guerra'] as $idAlianza=>$nombre)
  	{
  		echo '<tr><td colspan="2"><a onclick="Mostrar(\'alianza.php?alianza='.$idAlianza.'\')">'.$nombre.'</a></td></tr>';

  	}
  }
  ?>
  </table></form>
  <?php 
}

function MostrarSolicitudes()
{
	global $alianza;
	global $DB;
	?>
<script type="text/javascript">
function MostrarTextoRechazoSolicitud(idSolicitud)
{
	document.getElementById('textoSolicitud'+idSolicitud).style.display = 'none';
	document.getElementById('divMotivoRechazo'+idSolicitud).style.display = 'block';
	document.getElementById('botonRechazar'+idSolicitud).style.display = 'none';
	var obj=document.getElementsByName('solicitud'+idSolicitud)[0];
	obj.value="<?php EchoString('Enviar'); ?>";
	obj.name='rechazar'+idSolicitud;
	document.getElementsByName('motivoRechazo'+idSolicitud)[0].focus();
}
</script>
<table class="allianceSettingsTable">
  <tr>
    <th colspan="2"><?php EchoString('Solicitudes de acceso a la alianza'); ?></th>
  </tr>
  <?php 

  $consulta=$DB->query('SELECT * FROM `solicitudaccesoalianza` where `Alianza`='.$alianza['ID']);

  if($consulta->num_rows()==0)
  {
  	echo '<tr><td><br>'.GetString('No hay solicitudes de acceso a esta alianza').'</td></tr>';
  }
  else
  {
  	while($solicitud=$consulta->fetch_assoc())
  	{
  		$jugador=$DB->getRowProperties('jugadores',$solicitud['Jugador'],'Nombre,Ranking,Puntos');
    	?>
    <tr>
      <form method="post" name="" action="">
      <td style="text-align:left; font-weight:normal;"><input type="hidden" name="administrarSolicitudes">
        <?php printf(GetString('Jugador: <strong>%s</strong><br>Puntos: <strong>%d</strong> (Ranking <strong>%d</strong>)<br>'), $jugador['Nombre'],$jugador['Puntos'],$jugador['Ranking']); ?>
        <div id="textoSolicitud<?php echo $solicitud['Jugador']?>"><?php echo GetString('Texto de la solicitud').':<br>'.DescomprimirTexto($solicitud['Texto']); ?></div>
        <div id="divMotivoRechazo<?php echo $solicitud['Jugador']?>" style="display:none" align="center"><? EchoString('Motivo del rechazo'); ?>:<br />
         <textarea onfocus="TextAreaGrow(this)" name="motivoRechazo<?php echo $solicitud['Jugador']?>" cols="60"></textarea>
        </div></td>
      <td>
      <input type="submit" name="solicitud<?php echo $solicitud['Jugador'] ?>" value=" <?php EchoString('Aceptar') ?>" />
        <input type="button" onclick="MostrarTextoRechazoSolicitud(<?php echo $solicitud['Jugador']?>)" id="botonRechazar<?php echo $solicitud['Jugador']?>" value="<?php EchoString('Rechazar')?>"></td>
      </form></tr>
  <?php
  	}
  }
    ?>
</table>
<?php 
}

function MostrarOpcionesAlianza()
{
	global $alianza;
		?>
<form method="post"  action="">
  <table class="allianceSettingsTable">
    <tr>
      <th colspan="2"><?php EchoString('Opciones de la alianza'); ?></th>
    </tr>
    <tr>
      <td><?php EchoString('Nombre'); ?></td>
      <td><input type="text" name="nombreAlianza" size="35" value="<?php echo $alianza['Nombre']?>"></td>
    </tr>
    <tr>
      <td><?php EchoString('Logo'); ?></td>
      <td><input type="text" name="logo" size="80" value="<?php echo $alianza['UrlLogo']?>"></td>
    </tr>
    <tr>
      <td><?php EchoString('Pagina web'); ?></td>
      <td><input type="text" name="web" size="80" value="<?php echo $alianza['UrlWeb']?>"></td>
    </tr>
    <tr>
      <td><?php EchoString('Avisos sobre miembros'); ?></td>
      <td><select name="avisos">
          <option value="0"><?php EchoString('Activados (cuando entra o sale un miembro se crea un cc)'); ?></option>
          <option value="1" <?php 
          if($alianza['AvisosMiembros']!=0)
     echo 'selected="true"'; ?>> <?php EchoString('Desactivados (no se notifica el flujo de miembros)'); ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td><?php EchoString('Solicitudes de admisi&oacute;n'); ?></td>
      <td><select name="solicitudes">
          <option value="0"><?php EchoString('Permitidas (se pueden apuntar nuevos miembros)'); ?></option>
          <option value="1" <?php 
          if($alianza['SolicitudesDenegadas']!=0)
     echo 'selected="true"'; ?>> <?php EchoString('Denegadas (no se aceptan m&aacute;s miembros)'); ?></option>
        </select>
      </td>
    </tr>
        <tr>
      <td><?php EchoString('Plantilla para las solicitudes'); ?></td>
      <td><textarea name="plantillasSolicitudes" cols="50"><?php echo DescomprimirTexto($alianza['PlantillaSolicitud'])?></textarea>
      <script type="text/javascript">$('textarea').Autoexpand([400,800]);</script></td>
    </tr>
    <tr>
      <td colspan="2" ><input type="submit" value=" <?php EchoString('Guardar') ?>" />
        <input type="reset" value="<?php EchoString('Restablecer'); ?>"></td>
    </tr>
  </table>
</form>
<?php 
}

function GuardarPost()
{
	global $rango;

	if(isset($_GET['solicitudes']) && $rango->PoseeCompetencia('RevisarSolicitudes'))
	ProcesarSolicitudes();
	if(isset($_GET['avanzadas']) &&  $rango->PoseeCompetencia('Fundador'))
	ProcesarAccionAvanzada();
	if($rango->PoseeCompetencia('AdministrarPactos'))
	{
		if(isset($_GET['pactos']))
		ProcesarPactos();
		if(isset($_GET['guerras']))
		ProcesarGuerras();

	}
	if($rango->PoseeCompetencia('AdministrarAlianza'))
	{
		if(isset($_GET['opciones']))//Opciones generales
		{
			AlmacenarValor('nombreAlianza','Nombre');

			if( isset($_POST['logo']) && strncmp('http://',$_POST['logo'],7)!=0)
			$_POST['logo']='http://'.$_POST['logo'];
			AlmacenarValor('logo','UrlLogo');

			if( isset($_POST['web']) && strncmp('http://',$_POST['web'],7)!=0)
			$_POST['web']='http://'.$_POST['web'];
			AlmacenarValor('web','UrlWeb');

			AlmacenarValor('avisos','AvisosMiembros');
			AlmacenarValor('solicitudes','SolicitudesDenegadas');

			AlmacenarValor('plantillasSolicitudes','PlantillaSolicitud',true);
		}
		if(isset($_GET['rangos']))
		ProcesarRangos();
	}
}

function ProcesarRangos()
{
	global $DB;
	global $alianza;

	if(!empty($_POST['nombreNuevoRango']))
	{
		$nuevoNombre=$DB->escape_string($_POST['nombreNuevoRango']);
		$DB->query("INSERT INTO `rangosalianza` ( `ID` , `Alianza` , `Nombre` )VALUES ('' , '{$alianza['ID']}', '$nuevoNombre');");
	}
	else if(isset($_POST['borrarRango']) && is_numeric($_POST['borrarRango']))
	{
		$propiedades=$DB->getRowProperties('rangosalianza',$_POST['borrarRango'],'Alianza,Fundador');
		if($propiedades['Alianza']!=$alianza['ID'] || $propiedades['Fundador']==1)
		return;

		$DB->query("UPDATE `jugadores` SET `RangoAlianza`=0 where RangoAlianza=".$_POST['borrarRango']);
		$DB->deleteRow('rangosalianza',$_POST['borrarRango']);
	}
	else
	{
		$consulta=$DB->query('SELECT * FROM `rangosalianza` WHERE `Alianza`='.$alianza['ID']);
		$rangos;
		while($rango=$consulta->fetch_assoc())
		{
			$rangos[]=$rango;

			$nombreRango=$_POST['nombreRango'.$rango['ID']];
			if(!empty($nombreRango) && $rango['Nombre']!=$nombreRango)
			{
				$DB->setRowProperty('rangosalianza',$rango['ID'],'Nombre',$nombreRango);
			}
		}

		while($fila=$consulta->fetch_field())
		{
			if($fila->name=='Alianza' || $fila->name=='ID')
			continue;

			ProcesarRango($fila,$rangos);
		}
	}
}

function ProcesarRango($id,$rangos)
{
	global $DB;
	foreach($rangos as $rango)
	{
		if($rango['Fundador']!=0)
		continue;

		$valor=$_POST['rango'.$id->name.$rango['ID']]==1?1:0;
		if($valor!=$rango[$id->name])
		{
			$DB->setRowProperty('rangosalianza',$rango['ID'],$id->name,$valor);
		}
	}
}

function AlmacenarValor($idPost,$idSQL,$comprimir=false)
{
	global $alianza;
	global $DB;

	$valor=$_POST[$idPost];
	if($comprimir==true)
	$valor=ComprimirTexto(str_replace('\"','"',$valor),false);
	if(isset($valor) && $alianza[$idSQL]!=$valor)
	{
		$DB->setRowProperty('alianzas',$alianza['ID'],$idSQL,$valor);

		$alianza[$idSQL]=$valor;
	}
}

function ProcesarSolicitudes()
{
	global $alianza;
	global $DB;

	$consulta=$DB->query('SELECT * FROM `solicitudaccesoalianza` where `Alianza`='.$alianza['ID']);
	include_once('src/mensaje.php');

	while($solicitud=$consulta->fetch_assoc())
	{
		if(isset($_POST['solicitud'.$solicitud['Jugador']]))
		{
			AceptarSolicitudAcceso($alianza,$solicitud);
		}
		else if(isset($_POST['rechazar'.$solicitud['Jugador']]))
		{
			//Solicitud rechazada, enviar aviso al jugador

			//Enviar aviso al jugador
			$origen='alianza'.$idAlianzaActual;
			EnviarMensaje($origen,$solicitud['Jugador'],GetString('Tu solicitud de entrada en la alianza ha sido rechazada'),
			sprintf(GetString('La alianza %s no ha aceptado tu solicitud de acceso.<br><br>Motivo: %s'),$alianza['Nombre'],str_replace("\r\n",'<br>',$_POST['motivoRechazo'.$solicitud['Jugador']]),'alianza'));

			//Borrar la solicitud
			$DB->query("DELETE FROM `solicitudaccesoalianza` WHERE `Jugador` = {$solicitud['Jugador']} LIMIT 1");
		}
	}
}

function ProcesarAccionAvanzada()
{
	global $alianza;
	global $DB;

	include_once('src/mensaje.php');

	$accion=$_POST['realizar'];

	if($accion=='disolver')
	{
		DisolverAlianza($alianza);

		MostrarError(GetString('La alianza ha sido disuelta.'));
	}
}

function ProcesarGuerras()
{
	global $claseAlianza;

	foreach ($_POST as $key=>$value)
	{
		if(strncasecmp($key,'guerra',6)==0 && is_numeric(substr($key,6)))
		{
			$claseAlianza->FinalizarGuerra(substr($key,6));
			return;
		}
	}
}

function ProcesarPactos()
{
	global $claseAlianza;

	foreach ($_POST as $key=>$value)
	{
		if(strncasecmp($key,'cancelarPna',11)==0 && is_numeric(substr($key,11)))
		{
			$claseAlianza->FinalizarPNA(substr($key,11));
			return;
		}
		else if(strncasecmp($key,'aceptarPna',10)==0 && is_numeric(substr($key,10)))
		{
			$claseAlianza->AceptarSolicitudPNA(substr($key,10));
			return;
		}
		else if(strncasecmp($key,'rechazarPna',11)==0 && is_numeric(substr($key,11)))
		{
			$claseAlianza->RechazarSolicitudPNA(substr($key,11));
			return;
		}
		else if(strncasecmp($key,'retirarPna',10)==0 && is_numeric(substr($key,10)))
		{
			$claseAlianza->RetirarSolicitudPNA(substr($key,10));
			return;
		}
	}
}

ActualizarDatosCabecera();
?>