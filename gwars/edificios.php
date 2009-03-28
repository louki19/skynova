<?php
include('basePage.php');
include('src/adminTecnologias.php');

$Cola=new Cola(1);

$Cola->MostrarTecnologiaEnConstruccion();
$Cola->MostrarCola();

echo '<br>';
CabeceraTab();
Tab(GetString('Todos'),'todos','FiltroEdificios(\'todos\');');
Tab(GetString('Producción'),'produccion','FiltroEdificios(\'produccion\')');
Tab(GetString('Militares'),'militares','FiltroEdificios(\'militares\')');
Tab(GetString('Almacenes'),'almacenes','FiltroEdificios(\'almacenes\')');
CierreTab();

?>
<table class="buildTable"><tr><th colspan="3"><?php EchoString('Edificios'); ?></th></tr>
<?php
$Cola->TablaTecnologias();
echo '</table></div></div><br>';


ActualizarDatosCabecera();
?>
<script type="text/javascript">
var Filtro=FiltroEdificios;
Filtro(filtroActual);
</script>