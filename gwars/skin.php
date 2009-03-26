<?php
include('src/funciones.php');
if(!empty($_GET['skin']))
{
	$archivo=file_get_contents($_GET['skin'].'info.html');

	$archivo=str_replace('%url%',$_GET['skin'],$archivo);

	echo '<div>'.LimitarEtiquetas($archivo).'</div>';
}
else
EchoString('No se han podido cargar los datos del skin correctamente.');
?>