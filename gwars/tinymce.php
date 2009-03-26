<?php
$tipoInicio=1;//No cargar los datos del planeta actual
include('basePage.php');

$rango=$DB->getRowProperty('rangosalianza',$jugador->Datos['RangoAlianza'],'AdministrarAlianza');
if(empty($rango))
exit;

if(isset($_POST['html']))
{
	print_r($_POST);
	$texto=str_replace('\"','"',$_POST['html']);

	$valor=ComprimirTexto(LimitarEtiquetas($texto),false);
	if($_GET['seccion']==1)
	$DB->setRowProperty('alianzas',$jugador->Datos['Alianza'],'SeccionInterna',$valor);
	else if($_GET['seccion']==2)
	$DB->setRowProperty('alianzas',$jugador->Datos['Alianza'],'SeccionExterna',$valor);
}
else
{
	if($_GET['seccion']==1)
	$texto=$DB->getRowProperty('alianzas',$jugador->Datos['Alianza'],'SeccionInterna');
	else if($_GET['seccion']==2)
	$texto=$DB->getRowProperty('alianzas',$jugador->Datos['Alianza'],'SeccionExterna');

	if(!empty($texto))
	$texto=DescomprimirTexto($texto);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="<?php echo $jugador->UrlSkin  ?>style.css" rel="stylesheet" type="text/css">
<body>
<div align="center">
<script type="text/javascript" src="../tinymce/tiny_mce_gzip.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE_GZ.init({
	languages:'<?php echo $_GET['lang'] ?>',
	plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
	disk_cache : true,
	debug : false
});
</script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "tinymce",
	theme : "advanced",
	plugin_insertdate_dateFormat : "<?php echo $_GET['formatDate'] ?>",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	theme_advanced_buttons1_add_before : "save,newdocument,separator",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,spellchecker,cite,abbr,acronym,del,ins,|,visualchars,nonbreaking",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : true,
	apply_source_formatting : true
});
</script>
<form method="post" action="">
  <textarea name="html" tinymce="true" rows="15" cols="80"><?php echo $texto; ?></textarea>
  <br />
  <input type="submit" value="<?php EchoString('Guardar'); ?>">
  &nbsp;
  <input type="reset" value="<?php EchoString('Restablecer'); ?>">
</form>
</div>
</body>