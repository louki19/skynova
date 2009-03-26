<?php
include('basePage.php');

echo '<br>';
CabeceraTab();
Tab(GetString('Todas'),'todos','FiltroTipoTecnologia(\'todos\')',true);
Tab(GetString('Edificios'),'edificios','FiltroTipoTecnologia(\'edificios\')');
Tab(GetString('Investigaciones'),'investigaciones','FiltroTipoTecnologia(\'investigaciones\')');
Tab(GetString('Naves'),'naves','FiltroTipoTecnologia(\'naves\')');
Tab(GetString('Defensas'),'defensas','FiltroTipoTecnologia(\'defensas\')');
Tab(GetString('Edificios lunares'),'edificioslunares','FiltroTipoTecnologia(\'edificioslunares\')');
CierreTab();

echo '<div id="tablaedificios"><table class="technologyTable">
<tr><th width="40%">'.GetString('Construcción').'</th>
<th>'.GetString('Requisitos').'</th></tr>';
MostrarTecnologia(0,100);
echo '</table></div><div id="tablainvestigaciones"><table class="technologyTable">
<tr><th width="40%">'.GetString('Investigaci&oacute;n').'</th>
<th>'.GetString('Requisitos').'</th></tr>';
MostrarTecnologia(100,200);
echo '</table></div><div id="tablanaves"><table class="technologyTable">
<tr><th width="40%">'.GetString('Naves').'</th>
<th>'.GetString('Requisitos').'</th></tr>';
MostrarTecnologia(300,400);
echo '</table></div><div id="tabladefensas"><table class="technologyTable">
<tr><th width="40%">'.GetString('Defensas').'</th>
<th>'.GetString('Requisitos').'</th></tr>';
MostrarTecnologia(400,550);
echo '</table></div><div id="tablaedificioslunares"><table class="technologyTable">
<tr><th width="40%">'.GetString('Construcciones lunares').'</th>
<th>'.GetString('Requisitos').'</th></tr>';
MostrarTecnologia(80,90);
echo '</table></div></div></div>';

function MostrarTecnologia($inicio, $final)
{
	for($contador=$inicio;$contador<$final;$contador++)
	{
		$tecnologia = GetTechnology($contador);

		if (!empty($tecnologia))
		{
			$requisitos=MostrarRequisitos(RequisitosTecnologia($tecnologia->ID));
			echo '<tr><td><a onclick="Mostrar(\'descripcion.php?id='.$tecnologia->ID.'\')">'.$tecnologia->Name.'</a></td>
            <td>'.(empty($requisitos)?'&nbsp':$requisitos).'</td></tr>';
		}
	}
}

  function MostrarRequisitos($requisitos)
  {
  		if(empty($requisitos)) return;
  	global $planeta;

  	$resultado='';
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

?>
