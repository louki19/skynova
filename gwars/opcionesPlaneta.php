<?php include('basePage.php'); 

if(!empty($_POST['nombre']) || !empty($_POST['orden']) || !empty($_POST['pass'.$planeta->ID]))
{
	if(!empty($_POST['nombre']) && $_POST['nombre']!=$planeta->Datos['Nombre'])
	$planeta->Datos['Nombre']=$_POST['nombre'];

	if(!empty($_POST['orden']) && $_POST['orden']!=$planeta->Datos['Orden'])
	$planeta->Datos['Orden']=$_POST['orden'];
	
	$planeta->GuardarCambios();

	if(!empty($_POST['pass'.$planeta->ID]) && isset($_POST['borrarColonia']))
	{
		if(HashContraseña($_POST['pass'.$planeta->ID])==$jugador->Datos['Pass'])
		{
			echo 'BORRAR PLANETA';
			exit;
		}
		else
		MostrarError(GetString('La contraseña del usuario no es correcta'));
	}

	echo '<script language=JavaScript>setTimeout("Mostrar(\'visionGeneral.php\')",500);ActualizarCabecera();</script>';
}
else
{	?>
<h2>
<?php 
$textoPlaneta=$planeta->Datos['Luna']==1? GetString('la luna '):GetString(' el planeta ');
echo GetString('Opciones de').' '.$textoPlaneta.$planeta->Nombre;
?>
</h2>
<center>
<form method="post" action="">
<table class="generalTable" width="450">
  <tr>
    <td><?php EchoString('Nombre'); ?></td>
    <td><input type="text" name="nombre" value="<?php echo $planeta->Nombre?>"/></td>
  </tr>
  <tr>
    <td><?php EchoString('Orden de aparición'); ?></td>
    <td><input type="text" name="orden" value="<?php echo $planeta->Datos['Orden']?>"/></td>
  </tr>
  <tr>
      <td colspan="2"><input type="submit" value="<?php EchoString('Guardar'); ?>"></td>
  </tr>
</table><br />
<br />
<?php 
if($jugador->Datos['PlanetaPrincipal']!=$planeta->ID)
{ ?>
<table class="generalTable" width="450">
  <tr>
    <th colspan="2"><?php EchoString('Borrar planeta'); ?></th>
  </tr>
  <tr>
    <td><?php EchoString('Contraseña de usuario'); ?></td>
    <td><input type="password" name="pass<?php echo $planeta->ID;?>"/></td>
  </tr>
  <tr>
      <td colspan="2"><input type="submit" name="borrarColonia" value="<?php EchoString('Borrar colonia'); ?>"></td>
  </tr>
</table>
</center>
<?php 
}
echo '</form>';
}
?>