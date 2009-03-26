<?php
include('basePage.php');

if(isset($_POST['mision']))//Enviar flota
{
	include_once('src/flota.php');

	$flota=Flota::FlotaArray($_POST);

	if(isset($flota->Error) && $flota->Error==true)
	{
		echo '<font color="red">'.GetString('Error').': '.$flota->TextoError.'</font><br>';
		break;
	}
	else//Enviar flota
	{
		Eventos::EnviarFlota($flota,$planeta,time());
?>
	<div id="flotaEnviada">
	<font color="lime"><?php EchoString('Flota enviada con éxito') ?></font></div>
	<script language="JavaScript">
	BackgroundFade(2500,40,"flotaEnviada",255,204,51, 37,36,42);
	setTimeout(function(){if(document.getElementById("flotaEnviada"))document.getElementById("flotaEnviada").style.display="none";},15000);
	</script>	
<?php
	}
}
else//Mostrar las opciones del envio de flota
{
	if(isset($_POST['galaxiaD']) && !empty($_POST['galaxiaD']) && !empty($_POST['sistemaD']) && !empty($_POST['posicionD']))
	{
		for($contador=290;$contador<320;$contador++)
		{
			if(!empty($_POST['nave'.$contador]))//Hay al menos una nave enviada
			{
				include_once('src/flota.php');

				$_POST['galaxiaO']=$planeta->Datos['Galaxia'];
				$_POST['sistemaO']=$planeta->Datos['Sistema'];
				$_POST['posicionO']=$planeta->Datos['Posicion'];

				$flota=Flota::FlotaArray($_POST);
				if(isset($flota->Error) && $flota->Error==true)
				{
					echo '<font color="red">'.GetString('Error').': '.$flota->TextoError.'</font><br>';
					break;
				}
				else
				{
					include('src/enviarFlota.php');
					exit;
				}
			}
		}
	}
}
if($planeta->HayNaves()==false)
MostrarError(GetString('No hay naves en el planeta'),true);
?>
<h2><?php EchoString('Nueva misión'); ?></h2>
<form name="flota" method="post">
  <table class="fleetTable">
    <tr>
      <th colspan="4"><?php EchoString('Naves'); ?></th>
    </tr>
    <tr>
      <td width="300"><?php EchoString('Nave'); ?></td>
      <td><?php EchoString('Cantidad'); ?></td>
      <td><?php EchoString('Enviar'); ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php
    $datosJavascript='';
    for($contador=300;$contador<320;$contador++)
    {
    	if(!empty($planeta->Tecnologias[$contador]))
    	{
    		$cantidad=$planeta->Tecnologias[$contador];
    		$tecnologia=GetTechnology($contador);
    		$caracteristicasNave=CaracteristicasActualesArmamento($contador);
    		echo '<tr><td tip="tooltip.php?tipo=nave&id='.$contador.'">'.$tecnologia->Name.'</td>
            <td>'.$planeta->Tecnologias[$contador].'</td>
            <td><input type="text" name="nave'.$contador.'" size="10" value="'.$_REQUEST['nave'.$contador].'" onblur="ComprobarLimite(this,'.$cantidad.')" onkeyup="ControlNumerico(this);ActualizarInformacion();" maxlength="6"/></td>
            <td><input type="button" id="max'.$contador.'" value="m&aacute;x." onclick="document.flota.nave'.$contador.'.value='.$cantidad.'; ActualizarInformacion();"></td></tr>';

    		$datosJavascript.=",Array($contador, {$caracteristicasNave->Velocidad}, {$caracteristicasNave->CapacidadCarga}, {$caracteristicasNave->ConsumoCombustible}, '{$tecnologia->Name}',$cantidad, {$caracteristicasNave->Ataque})";
    	}
    }
    $datosJavascript=substr($datosJavascript,1);
    ?>
    <tr>
      <td><a onclick="NingunaNave();"><?php EchoString('Ninguna'); ?></a></td>
      <td><a onclick="NingunaNave();TodasLasNaves(305);"><?php EchoString('Todas las naves de guerra'); ?></a></td>
      <td colspan="2"><a onclick="TodasLasNaves(250);"><?php EchoString('Todas'); ?></a></td>
    </tr>
    <tr>
      <th colspan="4"><?php EchoString('Destino'); ?></th>
    </tr>
    <tr>
      <td><?php EchoString('Destino'); ?></td>
      <td colspan="3">
      <input onkeyup="ControlNumerico(this);ActualizarInformacion();" onBlur="ControlDistintoCero(this);" maxlength="1" size="4" type="text" name="galaxiaD" value="<?php echo isset($_REQUEST['galaxiaD'])?$_REQUEST['galaxiaD']:$planeta->Datos['Galaxia'] ?>">
        <input onkeyup="ControlNumerico(this);ActualizarInformacion();" onBlur="ControlDistintoCero(this);" maxlength="3" size="4" type="text" name="sistemaD" value="<?php echo isset($_REQUEST['sistemaD'])?$_REQUEST['sistemaD']:$planeta->Datos['Sistema'] ?>">
        <input onkeyup="ControlNumerico(this);ActualizarInformacion();" onBlur="ControlDistintoCero(this);" maxlength="2" size="4" type="text" name="posicionD" value="<?php echo isset($_REQUEST['posicionD'])?$_REQUEST['posicionD']:$planeta->Datos['Posicion'] ?>">
        <select name="tipoDestino" onchange="ActualizarInformacion();">
          <option value="planeta" <?php echo $_REQUEST['tipoDestino']=='planeta'?'selected="true" ':'' ?>><?php EchoString('Planeta'); ?></option>
          <option value="escombros" <?php echo $_REQUEST['tipoDestino']=='escombros'?'selected="true" ':'' ?>><?php EchoString('Escombros'); ?></option>
          <option value="luna" <?php echo $_REQUEST['tipoDestino']=='luna'?'selected="true" ':'' ?>><?php EchoString('Luna'); ?></option>
        </select>
        <a id="linkAccesoRapido" onclick="MostrarPlanetasAccesoRapido();" title="<?php EchoString('Mostrar coordenadas de acceso rápido') ?>">[+]</a>
   <div id="accesoRapido" style="height:0px;overflow: hidden;">
   <br/><?php PlanetasAccesoRapido(); ?>
        </div>
      </td></tr>
    <tr>
      <td><?php EchoString('Velocidad'); ?></td>
      <td colspan="3"><select name="velocidad" onchange="ActualizarInformacion();" onkeyup="ActualizarInformacion();">
          <option value="100">100%</option><option value="90">90%</option><option value="80">80%</option><option value="70">70%</option><option value="60">60%</option><option value="50">50%</option><option value="40">40%</option><option value="30">30%</option><option value="20">20%</option><option value="10">10%</option>
        </select></td>
    </tr>
        <tr>
      <td><?php EchoString('Distancia'); ?></td>
      <td colspan="3"><span id="distancia">0</span> <?php EchoString('kilómetros');?></td>
    </tr>
    <tr>
      <td><?php EchoString('Capacidad de carga'); ?></td>
      <td colspan="3"><span id="capacidadCargaNaves">0</span> <?php EchoString('unidades');?></td>
    </tr>
    <tr>
      <td><?php EchoString('Tiempo de vuelo'); ?></td>
      <td colspan="3"><span id="tiempoVuelo">0</span></td>
    </tr>
    <tr>
      <td><?php EchoString('Hora de llegada'); ?></td>
      <td colspan="3"><span style="color:lime;" id="horaLlegada">0</span></td>
    </tr>
    <tr>
      <td><?php EchoString('Hora de regreso'); ?></td>
      <td colspan="3"><span style="color:lime;" id="horaRegreso">0</span></td>
    </tr>
    <tr>
      <td><?php EchoString('Consumo de antimateria'); ?></td>
      <td colspan="3"><span id="consumo">0</span> <?php EchoString('unidades');?></td>
    </tr>
    <tr>
      <td><?php EchoString('Velocidad máxima de la flota'); ?></td>
      <td colspan="3"><span id="velocidadMaxima">0</span> <?php EchoString('km/s');?> <span id="naveLenta"></span></td>
    </tr>
        <tr>
      <td><?php EchoString('Poder de ataque de la flota'); ?></td>
       <td colspan="3"><span id="poderAtaque">0</span></td>
    </tr>
    <tr>
      <td colspan="4">
        <input type="button" onclick="EnviarFlota();" value="<?php EchoString('Continuar'); ?>"/></td>
    </tr>
  </table>
</form>
<script type="text/javascript">
var planetaActual=Array(<?php echo "{$planeta->Datos['Galaxia']},{$planeta->Datos['Sistema']},{$planeta->Datos['Posicion']}" ?>);
var datos=Array(
<?php echo $datosJavascript;?>
);
var planetaEsLuna=<?php echo $planeta->Datos['Luna']==1?'true':'false'?>;
var tipoDestinoCambiado=false;
ActualizarInformacion();
</script>
<?php 
function PlanetasAccesoRapido()
{
	global $DB;
	global $jugador;

	$consulta=$DB->query('SELECT Nombre,Luna,Galaxia,Sistema,Posicion FROM `planetas` WHERE `Jugador`="'.$jugador->ID.'" ORDER BY `Orden` ASC LIMIT 18');

	echo '<table class="rapidAccessTable"><tr><th colspan="2">'.GetString('Coordenadas de acceso rápido').'</th></tr><tr>';

	$contador=0;
	while (($planetaJugador = $consulta->fetch_assoc()))
	{
		if($planetaJugador['Luna']==1)
		echo "<td><a onclick=\"EstablecerDestino({$planetaJugador['Galaxia']},{$planetaJugador['Sistema']},{$planetaJugador['Posicion']},'luna');ActualizarInformacion();\">".MostrarLocalizacionPlaneta($planetaJugador,false,true).'</a></td>';
		else
		echo "<td><a onclick=\"EstablecerDestino({$planetaJugador['Galaxia']},{$planetaJugador['Sistema']},{$planetaJugador['Posicion']},'planeta');ActualizarInformacion();\">".MostrarLocalizacionPlaneta($planetaJugador,false,false).'</a></td>';

		$contador++;
		if($contador%2==0)
		echo '</tr><tr>';
	}

	echo '</tr></table><br>';
}

ActualizarDatosCabecera();
?>