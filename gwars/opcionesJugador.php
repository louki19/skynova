<?php
include('basePage.php');

$jugador->CargarMetadatos();

if(!empty($_POST))
{
	GuardarPost();
}

if(empty($_GET))
$_GET['opciones']=1;
CabeceraTab();
Tab(GetString('Opciones'),'opciones');
Tab(GetString('Temas y diseño'),'skin');
Tab(GetString('Planetas'),'planetas');
CierreTab();

if(isset($_GET['planetas']))
MostrarOpcionesPlanetas();
else if(isset($_GET['skin']))
MostrarOpcionesSkin();
else
MostrarOpciones();

echo  '</div>';
function MostrarOpcionesSkin()
{
	global $jugador;
?>
<form method="post"  action="">
  <table class="settingsTable">   
    <tr><th colspan="2"><?php EchoString('Ajustes de apariencia')?></th></tr>
      <tr><td><?php EchoString('Usar efectos de opacidad')?></td>
      <td><input type="checkbox" name="efectosOpacidad"<?php if(ObtenerValorCookie('efectosOpacidad',1)==1) echo ' checked="true"'?>></td></tr>
      <tr><td><?php EchoString('Tipo de menú')?></td>
      <td><select name="tipoMenu">
      <option value="0" <?php echo empty($jugador->Datos['MetaDatos']['Menu'])?'selected="true"':'' ?>><?php EchoString('Lista'); ?></option>
      <option value="1" <?php echo $jugador->Datos['MetaDatos']['Menu']==1?'selected="true"':'' ?>><?php EchoString('Dock inferior'); ?></option>
      <option value="2" <?php echo $jugador->Datos['MetaDatos']['Menu']==2?'selected="true"':'' ?>><?php EchoString('Dock superior'); ?></option>
      </select></td></tr>
    <tr><td colspan="2"><?php EchoString('URL del skin')?></td></tr>
    <tr><td><input type="text" name="urlSkin" size="75" value="<?php echo $jugador->UrlSkin;?>"/></td>
    <td><select name="skin"></select></td></tr>
    <tr><th colspan="2"><?php EchoString('Galaxia moderna')?></th>
    </tr>  
    <tr><td colspan="2"><?php EchoString('Skin de la galaxia moderna')?></td></tr>
    <tr><td><input type="text" name="urlSkinGalaxia" size="75" value="<?php echo $jugador->Datos['MetaDatos']['UrlSkinGalaxia'];?>"/></td>
    <td><select name="skinGalaxia"></select></td></tr>
  </table><br>
  <input type="submit" value="<?php EchoString('Guardar'); ?>">
<input type="reset" value="<?php EchoString('Restablecer'); ?>">
</form>
<?php 
}

function MostrarOpciones()
{
	global $jugador;
		?>
<form method="post" action="">
  <table class="settingsTable">
    <tr>
      <th colspan="2"><?php EchoString('Datos del usuario')?></th>
    </tr>
    <tr>
      <td><?php EchoString('Nick')?></td>
      <td><input size="35" type="text" name="nombreUsuario" value="<?php echo $jugador->Nombre;?>"/></td>
    </tr>
    <tr>
      <td><?php EchoString('Contraseña actual')?></td>
      <td><input size="35" type="password" name="contrasenaAntigua" /></td>
    </tr>
    <tr>
      <td><?php EchoString('Nueva contraseña')?></td>
      <td><input size="35" type="password" name="contrasenaNueva" /></td>
    </tr>
    <tr>
      <td><?php EchoString('Nueva contraseña (confirmación)')?></td>
      <td><input size="35" type="password" name="contrasenaNuevaConfirmada" /></td>
    </tr>
    <tr>
      <td><?php EchoString('E-mail')?></td>
      <td><input size="35" type="text" name="email" value="
<?php
if(!empty($jugador->Datos['MetaDatos']['EmailSC']))
echo $jugador->Datos['MetaDatos']['EmailSC'];
else echo $jugador->Datos['MetaDatos']['Email'];
?>"/></td>
    </tr>
    <tr>
      <td><?php EchoString('E-mail confirmado')?></td>
      <td><?php echo $jugador->Datos['MetaDatos']['Email'];?> </td>
    </tr>
    <tr>
      <td><?php echo sprintf(GetString('Id de %s'),'<a onclick="window.open(\'http://site.gravatar.com\')">Gravatar</a>'); ?></td>
      <td><input type="text" size="35" name="gravatarID" value="<?php echo isset($jugador->Datos['MetaDatos']['IdGravatar'])?$jugador->Datos['MetaDatos']['IdGravatar']:$jugador->Datos['MetaDatos']['Email'];?>" /></td>
    </tr>
    <tr>
      <td><?php EchoString('Avatar')?></td>
      <td><img src="http://www.gravatar.com/avatar.php?gravatar_id=<?php echo md5(isset($jugador->Datos['MetaDatos']['IdGravatar'])?$jugador->Datos['MetaDatos']['IdGravatar']:$jugador->Datos['MetaDatos']['Email']);?>&rating=G"></td>
    </tr>
    <tr>
      <th colspan="2"><?php EchoString('Opciones del juego')?></th>
    </tr>
    <tr>
      <td><?php EchoString('Mostrar sólo flotas en la visión general')?></td>
      <td><input size="35" type="checkbox" name="mostrarSoloNaves" <?php echo isset($jugador->Datos['MetaDatos']['SN'])?'checked="true"':''?>/></td>
    </tr>
    <tr>
      <td><?php EchoString('Sondas de espionaje enviadas al espiar')?></td>
      <td><input size="35" type="text" name="sondasEnviadasEspionaje" value="<?php echo $jugador->Datos['MetaDatos']['SondasEspiar'];?>"/></td>
    </tr>
    <tr>
      <th colspan="2""><?php EchoString('Opciones de cuenta')?></th>
    </tr>
    <tr>
      <td><?php EchoString('Activar modo vacaciones')?></td>
      <td><input type="checkbox" name="activarModoVacaciones" /></td>
    </tr>
    <tr>
      <td><?php EchoString('Borrar la cuenta')?></td>
      <td><input type="checkbox" name="borrarCuenta" /></td>
    </tr>
  </table><br>
  <input type="submit" value="<?php EchoString('Guardar') ?>">
<input type="reset" value="<?php EchoString('Restablecer') ?>">
</form>
<?php 
}

function MostrarOpcionesPlanetas()
{
	global $jugador;
	global $DB;
		?>
<style type="text/css">
.sortableitem
{
	cursor:move;
	width: 100%;
	list-style: none;
	display:block;
}
</style>
<form method="post" action="">
<ul>
    <?php 
    $consulta=$DB->query('SELECT ID,Nombre,Orden,Imagen FROM `planetas` WHERE `Jugador`="'.$jugador->ID.'" ORDER BY `Orden` ASC LIMIT 18');

    $contador=1;
    while (($planetaJugador = $consulta->fetch_assoc()))
    {
    	echo '<li class="sortableitem">'.$contador.'. 
      <input type="text" name="nombre'.$planetaJugador['ID'].'" value="'.$planetaJugador['Nombre'].'"/>
       <input type="text" name="imagen'.$planetaJugador['ID'].'" value="'.$planetaJugador['Imagen'].'"/>
    </li>';
    	$contador++;
    }
    ?>    
</ul>
<script type="text/javascript">
$(document).ready(
function () {
	$('ul').Sortable(
	{
		accept : 		'sortableitem',
		helperclass : 	'sorthelper',
		activeclass : 	'sortableactive',
		hoverclass : 	'sortablehover',
		opacity: 		0.8,
		fx:				200,
		axis:			'vertically',
		opacity:		0.4,
		revert:			true
	}
	)
}
);
</script>
<input type="submit" value="<?php EchoString('Guardar'); ?>">
<input type="reset" value="<?php EchoString('Restablecer'); ?>">
</form>
<br>
<form method="post"  action="">
<?php EchoString('Auto-ordenar por:'); ?>
<select name="autoOrdenar">
<option value="galaxia"><?php EchoString('Galaxia'); ?></option>
<option value="sistema"><?php EchoString('Sistema'); ?></option>
<option value="posicion"><?php EchoString('Posición'); ?></option>
<option value="campos"><?php EchoString('Campos'); ?></option>
<option value="camposOcupados"><?php EchoString('Campos ocupados'); ?></option>
<option value="tipo"><?php EchoString('Tipo de planeta'); ?></option>
<option value="nombre"><?php EchoString('Nombre'); ?></option>
<option value="temperatura"><?php EchoString('Temperatura'); ?></option>
<option value="metal"><?php EchoString('Metal'); ?></option>
<option value="cristal"><?php EchoString('Cristal'); ?></option>
<option value="antimateria"><?php EchoString('Antimateria'); ?></option>
<option value="prodMetal"><?php EchoString('Producción de metal'); ?></option>
<option value="prodCristal"><?php EchoString('Producción de cristal'); ?></option>
<option value="prodAntimateria"><?php EchoString('Producción de antimateria'); ?></option>
<option value="energia"><?php EchoString('Energía libre'); ?></option>
</select>
<select name="modoOrdenamiento">
<option value="ascend"><?php EchoString('Ascendente'); ?></option>
<option value="descend"><?php EchoString('Descendente'); ?></option>
</select>
<input type="submit" value="<?php EchoString('Aceptar'); ?>">
</form>
<?php 
}

function GuardarPost()
{
	global $jugador;
	global $DB;

	if(isset($_GET['planetas']))
	{
		if(isset($_POST['autoOrdenar']))
		{
			$orden='Orden';
			switch ($_POST['autoOrdenar'])
			{
				case 'campos':$orden='CamposTotales';break;
				case 'camposOcupados':$orden='CamposOcupados';break;
				case 'Temperatura':$orden='temperatura';break;
				case 'metal':$orden='Metal';break;
				case 'cristal':$orden='Cristal';break;
				case 'antimateria':$orden='Antimateria';break;
				case 'prodMetal':$orden='ProduccionMetal';break;
				case 'prodCristal':$orden='ProduccionCristal';break;
				case 'prodAntimateria':$orden='ProduccionAntimateria';break;
				case 'galaxia':$orden='Galaxia';break;
				case 'sistema':$orden='Sistema';break;
				case 'posicion':$orden='Posicion';break;
				case 'tipo':$orden='Luna';break;
				case 'nombre':$orden='Nombre';break;
				case 'energia':$orden='EnergiaLibre';break;
			}
			$modo='ASC';
			if($_POST['modoOrdenamiento']=='descend')
			$modo='DESC';
			$consulta=$DB->query('SELECT ID,Orden FROM `planetas` WHERE `Jugador`="'.$jugador->ID.'" ORDER BY `'.$orden.'` '.$modo.' LIMIT 18');

			$contador=1;

			while (($planetaJugador = $consulta->fetch_assoc()))
			{
				if((string)$contador!=$planetaJugador['Orden'])
				{
					$DB->setRowProperty('planetas',$planetaJugador['ID'],'Orden',$contador);
				}
				$contador++;
			}
		}
		else
		{
			$consulta=$DB->query('SELECT ID,Nombre,Orden,Imagen FROM `planetas` WHERE `Jugador`="'.$jugador->ID.'" ORDER BY `Orden` ASC LIMIT 18');

			while (($planetaJugador = $consulta->fetch_assoc()))
			{
				GuardarOpcionPlaneta('nombre',$planetaJugador,'Nombre',false);
				GuardarOpcionPlaneta('orden',$planetaJugador,'Orden',true);
				GuardarOpcionPlaneta('imagen',$planetaJugador,'Imagen',true);
			}
		}
	}
	else if(isset($_GET['skin']))
	{
		if($_POST['urlSkin']!=$jugador->UrlSkin)
		{
			$url=$_POST['urlSkin'];
			if(substr($url,strlen($url)-1,1)!='/')
			{
				$url.='/';
			}
			$DB->setRowProperty('jugadores',$jugador->ID,'UrlSkin',$url);
			$jugador->UrlSkin=$url;
		}

		setcookie('efectosOpacidad',$_POST['efectosOpacidad'], time() + 31536000);//Establecer cookie para un año

		if(isset($_POST['tipoMenu']) && is_numeric($_POST['tipoMenu']))
		{
			$jugador->Datos['MetaDatos']['Menu']=(int)$_POST['tipoMenu'];
			if($jugador->Datos['MetaDatos']['Menu']>2 || $jugador->Datos['MetaDatos']['Menu']<0)
			$jugador->Datos['MetaDatos']['Menu']=0;
		}

		if(!empty($_POST['urlSkinGalaxia']) && $jugador->Datos['MetaDatos']['SG']!=$_POST['urlSkinGalaxia'])
		{
			$jugador->Datos['MetaDatos']['SG']=$DB->escape_string($_POST['urlSkinGalaxia']);
		}

		$jugador->GuardarCambios();

		echo '<script type="text/javascript">window.location.reload();</script>';
	}
	else
	{
		if($_POST['nombreUsuario']!=$jugador->Nombre)
		{
			$jugador->Nombre=$jugador->Datos['Nombre']=$_POST['nombreUsuario'];
		}

		if(!empty($_POST['contrasenaAntigua']))
		{
			if(HashContraseña($_POST['contrasenaAntigua'])==$jugador->Datos['MetaDatos']['Pass'])
			{
				if($_POST['contrasenaNueva']==$_POST['contrasenaNuevaConfirmada'])
				{
					$jugador->Datos['MetaDatos']['P']=HashContraseña($_POST['contrasenaNueva']);
					$jugador->GuardarCambios();

					echo '<script type="text/javascript">alert("'.GetString('La contraseña se ha cambiado, por favor, inicia sesión de nuevo.').'");</script>';
					Session_destroy();
				}
				else
				echo '<script type="text/javascript">alert("'.GetString('Las contraseñas especificadas no coinciden').'");</script>';
			}
			else
			echo '<script type="text/javascript">alert("'.GetString('La contraseña del usuario no es correcta').'");</script>';
		}

		//Comprobar email
		if(!empty($_POST['email']))
		{
			if(!empty($jugador->Datos['MetaDatos']['EmailSC']) && $_POST['email']!=$jugador->Datos['MetaDatos']['EmailSC'])
			{
				$jugador->Datos['MetaDatos']['EmailSC']=$DB->escape_string($_POST['email']);
				$jugador->Datos['MetaDatos']['FechaEmailSC']=time();
			}
			else if( $_POST['email']!=$jugador->Datos['MetaDatos']['Email'])
			{
				$jugador->Datos['MetaDatos']['EmailSC']=$DB->escape_string($_POST['email']);
				$jugador->Datos['MetaDatos']['FechaEmailSC']=time();
			}
		}

		$jugador->Datos['MetaDatos']['IdGravatar']=$_POST['gravatarID'];

		if($_POST['mostrarSoloNaves']==1)
		$jugador->Datos['MetaDatos']['SN']=1;
		else
		unset($jugador->Datos['MetaDatos']['SN']);

		$jugador->GuardarCambios();
	}
}

function GuardarOpcionPlaneta($opcion,$planetaActual,$idSQL,$validarNumero)
{
	$valor=$_POST[$opcion.$planetaActual['ID']];
	if(empty($valor) || ($validarNumero && is_numeric($valor)==false))
	return;

	if($valor!=$planetaActual[$idSQL])
	{
		$GLOBALS['DB']->setRowProperty('planetas',$planetaActual['ID'],$idSQL,$valor);

		global $planeta;
		if($planetaActual['ID']==$planeta->ID)
		{
			$planeta->Datos[$idSQL]=$valor;

			echo '<script type="text/javascript">ActualizarCabecera();</script>';
		}
	}
}
?>
