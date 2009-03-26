<?php
include('basePage.php');
include('src/adminTecnologias.php');

//$_GET['tecno']=100;
$Cola=new Cola(2);

$Cola->MostrarTecnologiaEnConstruccion();

echo '<br>';
CabeceraTab();
Tab(GetString('Todos'),'todos','FiltroInvestigaciones(\'todos\')');
Tab(GetString('Generales'),'generales','FiltroInvestigaciones(\'generales\')');
Tab(GetString('Militares'),'militares','FiltroInvestigaciones(\'militares\')');
Tab(GetString('Motores'),'motores','FiltroInvestigaciones(\'motores\')');
Tab(GetString('Tecnologías'),'tecnologias','FiltroInvestigaciones(\'tecnologias\')');
CierreTab();
?>
<table class="buildTable"><tr><th colspan="3"><?php EchoString('Investigaciones'); ?></th></tr>
<?php

$Cola->TablaTecnologias();

echo '</table></div></div><br>';
$Cola->MostrarCola();

ActualizarDatosCabecera();
?>
<script type="text/javascript">
var Filtro=FiltroInvestigaciones;
Filtro(filtroActual);
</script>