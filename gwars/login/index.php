<?php
//Iniciar sesion
session_start();
// indicar el charset del documento actual
header("Content-Type: text/html; charset=ISO-8859-1");
//Comprimir contenido con gzip
//ob_start('ob_gzhandler');

include('../src/configuracion.php');

if(!empty($_POST) && !empty($_POST['jugador']) && !empty($_POST['pass']))
{
	include('../src/jugador.php');
	include('../src/funciones.php');
	include('../src/registro.php');

	$id=ObtenerJugador($_POST['jugador'],$_POST['pass']);

	if($id<0)
	{
		echo '<font color="red">';
		
		if($id==-1)
		EchoString('Usuario inexistente o contrase&#241;a incorrecta.');
		if($id==-2)
		EchoString('El usuario est&#225; baneado.');

		echo '</font>';
	}
	else
	{
		$_SESSION['id']=$id;
		$DB->setRowProperty('jugadores',$id,'IP',ip2long($_SERVER['REMOTE_ADDR']));
		?>
		<font color="lime"><?php EchoString('Login correcto, cargando p&#225;gina principal...') ?></font>
		<div><img src="images/loader.gif" alt="<?php EchoString('Cargando...');?>" title="<?php EchoString('Cargando...');?>"/></div>
		<script type="text/javascript">
		window.location="../index.php";
		</script>
		<?php
	}
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="../favicon.ico" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="../JavaScripts/jquery.js" language="JavaScript"> </script>
<script type="text/javascript">
function Login()
{
	params={
		url: 'index.php',
		type: "POST",
		data:'jugador='+$('#jugador').val()+'&pass='+$('#pass').val(),
		success:function(msg){	$('#resultados').html(msg); },
		error:function(ajaxObject,description){$('#resultados').html(description); }
	};

	$.ajax(params);
}
</script>
<title>Galactic Wars</title>
</head>
<body class="loginBody" style="text-align:center;">
 <img src="images/headerLogo.png" /><br />
  <br />
  <div id="principal" align="center">
   <form onsubmit="Login();return false;">
    <table width="500" class="generalTable" style="vertical-align:middle;">
      <tr>
        <td width="75"><?php echo GetString('Universo').' '.$configuracion['Universo'] ?></td>
        <td><label><?php EchoString('Jugador'); ?>:
          <input type="text" class="textInput" name="jugador" id="jugador" value="<?php echo isset($_GET['user'])?$_GET['user']:'' ?>"/>
          </label></td>
        <td><label><?php EchoString('Contrase&ntilde;a'); ?>:
          <input type="password" class="textInput" name="pass" id="pass"/>
          </label></td>
        <td style="vertical-align:bottom;"><input type="submit" value="Ok"/></td>
      </tr>
      <tr>
        <td colspan="4" style="text-align:left"><a href="registro.php"><?php EchoString('Registro de nuevo jugador');?></a></td>
      </tr>
    </table>
  </form>
</div><br/><br/>
<div id="resultados"></div>
</body>
</html>