<?php include('basePage.php');

/* PONER UN LIMITE DE NAVES AL SIMULAR */

CabeceraTab();
Tab(GetString('Parámetros de batalla'),'tparametros','FiltroAnalisisBatalla(\'parametros\')',true);
if(isset($_REQUEST['galaxiaO']))
{
	Tab(GetString('Resultado'),'tresultado','FiltroAnalisisBatalla(\'resultado\')');
	Tab(GetString('Informe de batalla'),'tinforme','FiltroAnalisisBatalla(\'informe\')');
}
CierreTab();
?>
<input type="button" onclick="$('simularBatalla').click();" value="<?php EchoString('Simular'); ?>"><br/><br/>
<div id="parametros">
<form>
<table class="battleSimulator">
<tr><td>&nbsp;</td><th><?php EchoString('Atacante') ?></th><th><?php EchoString('Defensor') ?></th>
</tr>
<tr><th colspan="3"><?php EchoString('Parámetros') ?></th></tr>
<tr><td><?php EchoString('Coordenadas') ?></td>
<td>
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="1" size="4" type="text" name="galaxiaO" value="<?php echo is_numeric($_REQUEST['galaxiaO'])? $_REQUEST['galaxiaO'] : $planeta->Datos['Galaxia'] ?>">
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="3" size="4" type="text" name="sistemaO" value="<?php echo is_numeric($_REQUEST['sistemaO'])? $_REQUEST['sistemaO'] : $planeta->Datos['Sistema'] ?>">
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="2" size="4" type="text" name="posicionO" value="<?php echo is_numeric($_REQUEST['posicionO'])? $_REQUEST['posicionO'] : $planeta->Datos['Posicion'] ?>">
</td>
<td>
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="1" size="4" type="text" name="galaxiaD" value="<?php echo $_REQUEST['galaxiaD'] ?>">
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="3" size="4" type="text" name="sistemaD" value="<?php echo $_REQUEST['sistemaD'] ?>">
<input onkeyup="ControlNumerico(this)" onBlur="ControlDistintoCero(this);" maxlength="2" size="4" type="text" name="posicionD" value="<?php echo $_REQUEST['posicionD'] ?>">
</td></tr>
<tr><td><?php EchoString('Metal') ?></td>
<td>&nbsp;</td>
<td><input type="text" size="10" onkeyup="ControlNumerico(this);" name="metal" value="<?php echo $_REQUEST['metal'] ?>"/></td></tr>
<tr><td><?php EchoString('Cristal') ?></td>
<td>&nbsp;</td>
<td><input type="text" size="10" onkeyup="ControlNumerico(this);" name="cristal" value="<?php echo $_REQUEST['cristal'] ?>"/></td></tr>
<tr><td><?php EchoString('Antimateria') ?></td>
<td>&nbsp;</td>
<td><input type="text" size="10" onkeyup="ControlNumerico(this);" name="antimateria" value="<?php echo $_REQUEST['antimateria'] ?>"/></td></tr>
<tr><th colspan="3"><?php EchoString('Tecnologías') ?></th></tr>
<?php
Fila(array(102,103,104,108,109,110));
echo '<tr><th colspan="3">'.GetString('Naves').'</th></tr>';
Fila(array(299,300,301,302,303,304,305,306,307,308,309,310,311,312));
echo '<tr><th colspan="3">'.GetString('Defensas').'</th></tr>';
Fila(array(400,401,402,403,404,405,406,502));
echo '</table><br/>
<input type="submit" id="simularBatalla" value="'.GetString('Simular batalla').'" />
</form></div>';
?>
<script type="text/javascript">
if(filtroActual!=null && $(filtroActual)!=null && document.getElementById(filtroActual)!=null)
FiltroAnalisisBatalla(filtroActual);
else
FiltroAnalisisBatalla('parametros');
</script>
<?php
if(isset($_REQUEST['galaxiaO']))
{
	include('src/batalla.php');
	include('src/flota.php');
	include('src/batalla_resumen.php');
	$infoFlota=Flota::FlotaArray($_REQUEST,true);

	$atacantes=array();
	$defensores=array();
	for($contador=100;$contador<505;$contador++)
	{
		if(isset($_REQUEST['a'.$contador]) && is_numeric($_REQUEST['a'.$contador]))
		$atacantes[$contador]=(int)$_REQUEST['a'.$contador];

		if(isset($_REQUEST['d'.$contador]) && is_numeric($_REQUEST['d'.$contador]))
		$defensores[$contador]=(int)$_REQUEST['d'.$contador];
	}
	$batalla=Batalla(
	array('Atacante 1'=>$atacantes),
	array('Defensor 1'=>$defensores),
	array((int)$_REQUEST['metal'],(int)$_REQUEST['cristal'],(int)$_REQUEST['antimateria'])
	);
	
	echo '<div id="resultado" style="display:none;text-align:left;font-weight:normal;">'.ResumenBatalla($batalla,$infoFlota).'</div>
	<div id="informe" style="display:none;">'.$batalla->Informe.'</div>';
}

function Fila($arrayIds)
{
	global $jugador;
	global $planeta;

	foreach ($arrayIds as $id)
	{
		$tecno=GetTechnology($id);

		if(empty($tecno))
		return;

		if(($id>=400 && $id<500) || $id==299 || $id==502)//Defensas y satelites
		$textoAtacante='&nbsp;';
		else
		{
			if(isset($_REQUEST['a'.$id]))
			$cantidad=$_REQUEST['a'.$id];
			else if($id>=100 && $id<250)
			$cantidad=$jugador->Tecnologias[$id];
			else
			$cantidad=$planeta->Tecnologias[$id];

			if($planeta->Tecnologias[$id]!=0 && !($id>=100 && $id<250))//Mostrar por defecto la cantidad de naves en el planeta
			$textoDisponibles=' ('.$planeta->Tecnologias[$id].' '.GetString('disponibles').')';
			else
			$textoDisponibles='';

			$textoAtacante='<input type="text" onkeyup="ControlNumerico(this);" size="10" name="a'.$id.'" value="'.$cantidad.'"/>';
		}

		echo '<tr><td><strong>'.$tecno->Name.'</strong>'.$textoDisponibles.'</td>
<td>'.$textoAtacante.'</td>
<td><input type="text" size="10" onkeyup="ControlNumerico(this);" name="d'.$id.'" value="'.$_REQUEST['d'.$id].'"/></td></tr>';
	}
}
?>