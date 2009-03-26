<?php 
if(!strchr($_SERVER['REQUEST_URI'],'index.php') && !strchr($_SERVER['REQUEST_URI'],'cabecera.php'))
{
global $queries;
global $textoFooter;

$final=microtime(true);
echo "<div><span>$textoFooter<br/>TOTAL QUERIES: ".$queries."</span><br/><br/>";

foreach($_GET as $key=>$value)
{
	echo '<span>$_GET[\''.$key.'\']=\''.$value.'\';</span><br/>';
}

echo "<br/><br/><br/>";

foreach($_POST as $key=>$value)
{
	echo '<span>$_POST[\''.$key.'\']=\''.$value.'\';</span><br/>';
}

echo "<br/><br/><br/>";

foreach($_SERVER as $key=>$value)
{
	echo '<span>$_SERVER[\''.$key.'\']=\''.$value.'\';</span><br/>';
}

echo "Tiempo de generación ".($final-$GLOBALS['inicioCreacion'])." s</div>";
}
?>