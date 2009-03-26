<?php
include('basePage.php');
include('src/adminTecnologias.php');

$Cola=new Cola(4);

$Cola->MostrarTecnologiaEnConstruccion();

echo '<br>';

CabeceraTab();
Tab(GetString('Todos'),'todos','FiltroDefensa(\'todos\')');
Tab(GetString('Cañones'),'canones','FiltroDefensa(\'canones\')');
Tab(GetString('Otros'),'otros','FiltroDefensa(\'otros\')');
CierreTab();
?>
<form>
<table class="buildTable"><tr><th colspan="3"><?php EchoString('Defensas'); ?></th></tr>
<?php
$Cola->TablaTecnologias();
?>
<tr>
<th colspan="2"><?php EchoString('Defensas encargadas');?></th>
<th style="text-align:center"><input type=submit value="Realizar"></th>
</tr>
</table>
</form>
<?php
echo '</div></div><br>';
$Cola->MostrarCola();

ActualizarDatosCabecera();
?>
<script type="text/javascript">
var Filtro=FiltroDefensa;
Filtro(filtroActual);
</script>