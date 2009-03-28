<?php
//$_POST['299']=4;

include('basePage.php');
include('src/adminTecnologias.php');

//$_POST[299]=2;
$Cola=new Cola(3);

$Cola->MostrarTecnologiaEnConstruccion();
$Cola->MostrarCola();

echo '<br>';
CabeceraTab();
Tab(GetString('Todos'),'todos','FiltroHangar(\'todos\')');
Tab(GetString('General'),'general','FiltroHangar(\'general\')');
Tab(GetString('Militar'),'militar','FiltroHangar(\'militar\')');
CierreTab();
?>
<form>
<table class="buildTable"><tr><th colspan="3"><?php EchoString('Hangar'); ?></th></tr>
<?php
$Cola->TablaTecnologias();
?>
<tr>
<th colspan="2"><?php EchoString('Naves encargadas');?></th>
<th style="text-align:center"><input type="submit" value=" <?php EchoString('Realizar') ?>" /></th>
</tr>
</table>
</form>
<?php
echo '</div></div><br>';


ActualizarDatosCabecera();
?>
<script type="text/javascript">
var Filtro=FiltroHangar;
Filtro(filtroActual);
</script>