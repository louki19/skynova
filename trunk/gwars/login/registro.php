<?php
//Iniciar sesion
session_start();
// indicar el charset del documento actual
header("Content-Type: text/html; charset=ISO-8859-1");
//Comprimir contenido con gzip
ob_start('ob_gzhandler');

include('../src/configuracion.php');
include('../src/fxforms.php');
include('../src/registro.php');
include('../src/planeta.php');
include('../src/funciones.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="../favicon.ico" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../combine.php?type=javascript&files=jquery.js"></script>
<title>Galactic Wars</title>
</head>
<body class="loginBody" style="text-align:center;">
 <img src="images/headerLogo.png" /><br />
  <br />
  <div id="principal" align="center">
<?php
$form=new FxForms();

$form->EmptyValueError=GetString('Por favor, rellena todos los campos marcados como requeridos');
$form->InvalidValue=GetString('El valor establecido no tiene el formato válido.');

$form->SubmitText=GetString('Registrar jugador');

$form->AddCategorie(new FxFormsCategorie(GetString('Datos obligatorios'),
array(
new FxFormsField('nick',GetString('Nombre en el juego'),'','text','','ValidarNombreUsuario','onkeypress="setTimeout(CheckPassSecurity,10);"'),
new FxFormsField('pass',GetString('Contraseña'),'','password','','CompararContraseñas','onkeypress="setTimeout(CheckPassSecurity,10);"'),
new FxFormsField('pass2',GetString('Repetir contraseña'),'','password'),
new FxFormsField('',GetString('Seguridad de la contraseña'),'','html','',null,'<div id="fortalezaPass">&nbsp;</div>'),
new FxFormsField('mail',GetString('Correo electrónico'),'','text',''),
new FxFormsField('licencia',GetString('Aceptas licencia de uso'),'licencia','checkbox','','ComprobarLicencia'),
),true));

$form->AddCategorie(new FxFormsCategorie('Datos opcionales',
array(
new FxFormsField('planeta',GetString('Nombre del planeta principal'),'Planeta principal','text',GetString('Nombre que tendrá tu primer planeta')),
),false));

if(!empty($_POST))//Guardar datos
{
	$datos=$form->ValidateResponse();

	if($datos!=false && count($datos)>0)//Validacion correcta
	{
		$jugador=RegistrarNuevoJugador($datos['nick'],$datos['pass'],$datos['mail']);
		
		$planeta=RegistrarNuevoPlaneta($jugador,$datos['planeta'],null,null,null,200,null,1000,500);
		
		$DB->setRowProperty('jugadores',$jugador,'PlanetaPrincipal',$planeta);
		
		//Registrado con éxito
		echo '<span style="color:lime">'.GetString('Registro completado con éxito, en unos momentos serás redireccionado a la página de login.').'</span><script type="text/javascript">setTimeout(\'document.location="index.php?user='.$_POST['nick'].'";\',2500);</script>';
		exit;
	}
}

$form->ShowForm();

function ValidarNombreUsuario($nombre)
{
	if(empty($nombre))
	return GetString('Debe especificar un nombre de usuario');

	$valor=ComprobarNombreUsuario($nombre);
	if($valor==1)
	return GetString('El nombre de usuario ya está en uso');
	if($valor==2)
	return GetString('El nombre de usuario no tiene un formato correcto');
	else
	return true;
}

function CompararContraseñas()
{
	if(empty($_POST['pass']))
	return GetString('Debe especificar una contraseña');

	if(isset($_POST['pass']) && strcmp($_POST['pass'],$_POST['pass2'])==0)
	return true;
	else
	return GetString('Las contraseñas no coinciden');
}

function ComprobarLicencia()
{
	if(isset($_POST['licencia']) && $_POST['licencia']=='licencia')
	return true;
	else
	return GetString('La licencia debe ser aceptada para poder continuar');
}
?>
<script type="text/javascript">
// Password strength meter
// Firas Kassem  phiras.wordpress.com || phiras at gmail {dot} com
// for more information : http://phiras.wordpress.com/2007/04/08/password-strength-meter-a-jquery-plugin/

function passwordStrength(password,username)
{
	score = 0

	//password == username
	if (password.toLowerCase()==username.toLowerCase()) return 5

	//password < 4
	if (password.length < 4 ) { return 1 }

	//password length
	score += password.length * 4
	score += ( checkRepetition(1,password).length - password.length ) * 1
	score += ( checkRepetition(2,password).length - password.length ) * 1
	score += ( checkRepetition(3,password).length - password.length ) * 1
	score += ( checkRepetition(4,password).length - password.length ) * 1

	//password has 3 numbers
	if (password.match(/(.*[0-9].*[0-9].*[0-9])/))  score += 5

	//password has 2 sybols
	if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) score += 5

	//password has Upper and Lower chars
	if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  score += 10

	//password has number and chars
	if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  score += 15
	//
	//password has number and symbol
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/))  score += 15

	//password has char and symbol
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/))  score += 15

	//password is just a nubers or chars
	if (password.match(/^\w+$/) || password.match(/^\d+$/) )  score -= 10

	//verifing 0 < score < 100
	if ( score < 0 )  score = 0
	if ( score > 100 )  score = 100

	if (score < 34 )  return 2
	if (score < 68 )  return 3
	return 4
}


// checkRepetition(1,'aaaaaaabcbc')   = 'abcbc'
// checkRepetition(2,'aaaaaaabcbc')   = 'aabc'
// checkRepetition(2,'aaaaaaabcdbcd') = 'aabcd'

function checkRepetition(pLen,str) {
	res = ""
	for ( i=0; i<str.length ; i++ ) {
		repeated=true
		for (j=0;j < pLen && (j+i+pLen) < str.length;j++)
		repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen))
		if (j<pLen) repeated=false
		if (repeated) {
			i+=pLen-1
			repeated=false
		}
		else {
			res+=str.charAt(i)
		}
	}
	return res
}

function CheckPassSecurity()
{
	tipo=passwordStrength($('#pass').val(),$('#nick').val());

	switch(tipo)
	{
		case 1:
		$('#fortalezaPass').html('<span style="color:red"><?php EchoString('Demasiado corta'); ?></span>');
		break;

		case 2:
		$('#fortalezaPass').html('<span style="color:red"><?php EchoString('Mala'); ?></span>');
		break;

		case 3:
		$('#fortalezaPass').html('<span style="color:green"><?php EchoString('Buena'); ?></span>');
		break;

		case 4:
		$('#fortalezaPass').html('<span style="color:lime"><?php EchoString('Óptima'); ?></span>');
		break;

		case 5:
		$('#fortalezaPass').html('<span style="color:red"><?php EchoString('Muy mala'); ?></span>');
		break;
	}
}

CheckPassSecurity();
</script>
</div>
</body>
</html>