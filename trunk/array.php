<?php
if(isset($_GET['fechas']))
{
	?>
	<form method="POST" action="?fechas">
	<label>Numero de fecha: <input size="50"  type="text" name="fechaNum"
value="<?php echo isset($_POST['fechaText'])?date('U',strtotime($_POST['fechaText'])):(isset($_POST['fechaNum'])?$_POST['fechaNum']:time())	?>"></label><br>
		<input type="submit" value="Convertir a fecha">
		</form>
		<form method="POST" action="?fechas">
<label>Texto: <input size="50" type="text" name="fechaText" 
value="<?php echo isset($_POST['fechaNum'])?date('r',$_POST['fechaNum']):(isset($_POST['fechaText'])?$_POST['fechaText']:date('r',time()))	?>"></label><br>
	<input type="submit" value="Convertir a numero">
	</form>
	<?php
	exit;
}

if(isset($_POST['AC']))
{
	$arr;

	for($contador=0;$contador<sizeof($_POST)+1000;$contador++)
	{
		if(isset($_POST['id'.$contador]))
		{
			$valor=$_POST['valor'.$contador];
			if(unserialize($valor)!=null)
			$arr[$_POST['id'.$contador]]=unserialize($valor);
			else if(is_numeric($valor))
			$arr[$_POST['id'.$contador]]=intval($valor);
			else
			$arr[$_POST['id'.$contador]]=$valor;
		}
	}

	$comp=gzcompress(serialize($arr),6);
	$_POST['codigo']=serialize($arr);
	$_POST['CA']=1;
}
if($_POST['des']==1)
{
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	header("Content-Type: application/force-download");
	header("Content-Length: " . strlen($comp));
	header("Content-Disposition: attachment; filename=codigo.bin");
	echo gzcompress(serialize($arr),6);
	exit;
}
?>
	<script type="text/javascript">	
	function anadirRegistro()
	{
		elementoDIV=document.createElement('div');
		elementoDIV.innerHTML='<label>ID:<input type=\'text\' name=\'id'+valorContador+'\' value=\''+valorContador+'\'></label><label>Valor:<input type=\'text\' name=\'valor'+valorContador+'\' value=\''+valorContador+'\'></label><input type=\'button\' onclick=\'borrarFila('+valorContador+')\' value=\'-\'><br>';
		document.forms[1].appendChild(elementoDIV);

		valorContador++;
		document.forms[1].submit();
	}
	function borrarFila(fila)
	{
		elm=document.getElementsByName('id'+fila)[0];
		elm.value='';
		//document.forms[1].removeChild(elm);
		elm2=document.getElementsByName('valor'+fila)[0];
		elm2.value='';
		//	document.forms[1].removeChild(elm2);

		document.forms[1].submit();
	}
	</script>
	<form method="POST" action="">
	<label>Codigo:<input type="text" name="codigo" size="150" value="<?php echo htmlentities(stripslashes($_POST['codigo'])) ?>"></label>
	<br>
	<input type="submit" value="Procesar" name="CA">
	</form>
		<?php
		function MostrarVariable($var)
		{
			echo str_replace("\n",'<br>',print_r($var,1));
		}

		if(!empty($_POST['codigo']))
		{
			$unser=unserialize(str_replace('\\','',$_POST['codigo']));
			if(!empty($unser))
			$varObtenida=$unser;
			else
			{
				try
				{
					$codigoHex=$_POST['codigo'];
					if(substr($_POST['codigo'],0,2)=='0x')
					$varObtenida=unserialize(gzuncompress(pack("H*", substr($_POST['codigo'],2))));
				}
				catch(Exception $e)
				{
					$varObtenida=0;
				}
				if(empty($varObtenida))
				$varObtenida=unserialize(gzuncompress(base64_decode($_POST['codigo'])));
			}

			$arr=$varObtenida;
			$comp=gzcompress(serialize($varObtenida),6);
		}
		$numCampos=sizeof($varObtenida);
		if(!is_numeric($numCampos))
		$numCampos=$_GET['campos'];
		if(!is_numeric($numCampos))
		$numCampos=10;

		if(!empty($comp))
		echo '<h3>Comprimido hex ('.strlen(bin2hex($comp)).' bytes)</h3><strong>0x'.bin2hex($comp).'</strong><h3>Serializado ('.strlen(serialize($arr)).' bytes)</h3><strong>'.serialize($arr).'</strong><h3>Base64 ('.strlen(base64_encode($comp)).' bytes)</h3>'.base64_encode($comp).'<h3>Comprimido ('.strlen(addslashes($comp)).' bytes)  <input type="button" onclick="document.forms[1].des.value=1;document.forms[1].submit();document.forms[1].des.value=0;" value="Descargar"></h3>'.addslashes($comp).'<br><br><br>';
		echo '<form method="POST" action="?campos='.$numCampos.'"><input type="hidden" name="AC" value="1"><input type="hidden" name="des" value="0">';
		$contador=0;
		if(!empty($varObtenida))
		{
			foreach($varObtenida as $key=>$value)
			{
			?>
		<label>ID: <input type="text" name="id<?php  echo $contador?>" value="<?php echo $key ?>"></label>
		<label>Valor: <input type="text" name="valor<?php  echo $contador?>" value="<?php echo  is_array($value)?serialize($value):$value; ?>"></label>
		<input type="button" onclick="borrarFila(<?php  echo $contador?>)" value="-"><br>
		<?php
		$contador++;
			}
		}
		else
		{
			for(;$contador<2;$contador++)
			{
		?>
		<label>ID:<input type="text" name="id<?php  echo $contador?>" value="<?php echo $contador ?>"></label>
		<label>Valor:<input type="text" name="valor<?php  echo $contador?>" value="<?php echo  $contador; ?>"></label>
				<input type="button" onclick="borrarFila(<?php  echo $contador?>)" value="-"><br>
		<?php
			}
			$contador++;
		}
	?>

	<br>
			<script type="text/javascript">
			var valorContador=<?php echo $contador?>;
	</script>
	
		<input type="button" onclick="anadirRegistro();" value="Añadir un campo">
		<input type="submit" value="Generar códigos" name="AC">
		</form>
		<a onclick="window.open('?fechas')" href="#">Modo fechas</a>
<?php
/*
echo 'DEBUG:<br>';
MostrarVariable($_POST);*/
?>