<?php
$tipoInicio=1;
include('basePage.php');

if(isset($_POST['nota']) && is_numeric($_POST['nota']))
{
	if(empty($_POST['asunto']) && empty($_POST['contenido']))
	exit;

	$insertar=true;
	if($_POST['nota']!=0)
	{
		$DB->query("Update notas Set asunto='".$DB->escape_string($_POST['asunto'])."', Fecha=".time().", Contenido=".ComprimirTexto($_POST['contenido'])." where ID={$_POST['nota']} && Jugador=$jugador->ID");

		if($DB->affected_rows()>0)
		$insertar=false;
	}
	if($insertar==true)
	{
		$DB->query("INSERT INTO `notas` ( `ID` , `Jugador` , `Asunto` , `Fecha` , `Contenido` )
VALUES (NULL , '{$jugador->ID}', '".$DB->escape_string($_POST['asunto'])."', ".time().", ".ComprimirTexto($_POST['contenido']).");");

		echo $DB->lastInsertId();
	}

	exit;
}

if(isset($_GET['nueva']))
{
	MostrarNota(0,array());
	exit;
}

if(isset($_GET['nota']) && is_numeric($_GET['nota']))
{
	if(isset($_GET['borrar']))//Borrar la nota
	{
		$DB->query('Delete from notas where ID='.$_GET['nota'].' && Jugador='.$jugador->ID);
	}
	else//Ver la nota
	{
		MostrarNota($_GET['nota'],$DB->getRow('notas',$_GET['nota']));
		exit;
	}
}
//Mostrar opciones normales
	?>
<table class="noteTable">
<tr>
  <th colspan="4""><?php EchoString('Herramientas'); ?></th>
</tr>
<tr>
  <td colspan="4"><a window="notas.php?nueva" windowTitle="<?php EchoString('Nota nueva'); ?>"><?php EchoString('Crear una nota nueva'); ?></a></td>
</tr>
<tr>
  <th colspan="4"><?php EchoString('Notas'); ?></th>
</tr>
<tr>
  <th><?php EchoString('Fecha'); ?></th>
  <th><?php EchoString('Asunto'); ?></th>
  <th><?php EchoString('Acción'); ?></th>
</tr>
<?php 

$consulta=$DB->query('SELECT * FROM `notas` WHERE `Jugador`='.$jugador->ID);

if($consulta->num_rows()==0)
{
	echo '<tr><td colspan="4"><br>'.GetString('No hay notas almacenadas').'</td></tr>';
}
else
{
	while($nota=$consulta->fetch_assoc())
	{
		echo '<tr>
          <td>'.date(GetString('j-n-Y G:i:s'),$nota['Fecha']).'</td>
          <td>'.$nota['Asunto'].'</td>
          <td><a window="notas.php?nota='.$nota['ID'].'" windowTitle="'.$nota['Asunto'].'">'.GetString('Ver').'</a>, <a onclick="Mostrar(\'?nota='.$nota['ID'].'&borrar\')">'.GetString('Borrar').'</a></td>
        </tr>';
	}
}

echo '</table>';

ActualizarDatosCabecera();


function MostrarNota($ID,$nota)
{
?>
<form method="post" onsubmit="return GuardarNota(this);">
<input type="hidden" name="nota" value="<?php echo $ID ?>">
<table class="noteTable">
<tr>
<th><?php EchoString('Asunto') ?></th>
<td><input type="text" size="40" maxlength="50" name="asunto" value="<?php echo $nota['Asunto'] ?>"/></td>
</tr>
<tr>
<th><?php EchoString('Contenido') ?></th>
<td><textarea name="contenido"><?php echo DescomprimirTexto($nota['Contenido']) ?></textarea>
</td>
</tr>
<tr>
<td colspan="2">
<input type="submit" value="<?php EchoString('Guardar') ?>" />
<input type=reset value="<?php EchoString('Restablecer') ?>"></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('textarea').Autoexpand([245,800]);
</script>
<?php
}
?>