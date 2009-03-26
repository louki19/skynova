<?php
set_time_limit(25000);

include('configuracion.php');
include('../src/funciones.php');
include('../src/jugador.php');
include('../src/eventos.php');
include('../src/planeta.php');
include('../src/tecnologia.php');

ActualizarServidor();

$comandoSQL;
function ActualizarServidor()
{
	global $DB;
	global $confBD;
	global $comandoSQL;

	//Borrar mensajes antiguos
	$DB->query('DELETE FROM `mensajes` WHERE `FechaEnvio` < '.(time()-2*24*3600));

	//Actualizar los jugadores
	$consulta=$DB->query('SELECT ID FROM `jugadores`');
	while($jugador=$consulta->fetch_row())
	{
		ProcesarJugador($jugador[0]);
	}
	$DB->query($comandoSQL);

	//Establecer el ranking
	$consulta=$DB->query('SELECT ID,Ranking FROM `jugadores` ORDER BY `Puntos` DESC');
	$ranking=1;
	$comandoSQL='';
	while($jugadorId=$consulta->fetch_row())
	{
		if($jugadorId[1]!=$ranking)
		$comandoSQL.='UPDATE `jugadores` SET `Ranking`='.$ranking.' where `ID`='.$jugadorId[0].' LIMIT 1;';
		$ranking++;
	}
	$DB->query($comandoSQL);

	//Actualizar alianzas
	$consulta=$DB->query('SELECT ID FROM `alianzas`');
	$comandoSQL='';
	while($alianza=$consulta->fetch_row())
	{
		ProcesarAlianza($alianza[0]);
	}
	$DB->query($comandoSQL);

	//Actualizar el ranking de alianzas
	$consulta=$DB->query('SELECT ID,Ranking FROM `alianzas` ORDER BY `Puntos` DESC');
	$ranking=1;
	$comandoSQL='';
	while($alianza=$consulta->fetch_row())
	{
		if($alianza[1]!=$ranking)
		$comandoSQL.='UPDATE `alianzas` SET `Ranking`='.$ranking.' where `ID`='.$alianza[0].' LIMIT 1;';
		$ranking++;
	}
	$DB->query($comandoSQL);

	//Optimizar la base de datos
	$consulta=$DB->query('SHOW TABLES FROM '.$confBD[0]);
	while ($row = $consulta->fetch_row())
	{
		$DB->query('OPTIMIZE TABLE `'.$row[0].'`');
	}

	//Copia de seguridad
	CopiaSeguridadBD();
}

function ProcesarJugador($idJugador)
{
	global $DB;
	global $comandoSQL;

	$jugador=new Jugador($idJugador);
	$jugador->CargarMetadatos();

	if(!empty($jugador->Datos['MetaDatos']['FechaEmailSC']) && time()-$jugador->Datos['MetaDatos']['FechaEmailSC']>604800)//Hace mas de una semana que se cambio el email
	{
		$jugador->Datos['MetaDatos']['Email']=$jugador->Datos['MetaDatos']['EmailSC'];
		unset($jugador->Datos['MetaDatos']['FechaEmailSC']);
		unset($jugador->Datos['MetaDatos']['EmailSC']);

		$comandoSQL.="UPDATE `jugadores` SET `MetaDatos`='".serialize($jugador->Datos['MetaDatos'])."' where `ID`=$idJugador LIMIT 1;";
	}

	//Calcular puntos
	$puntos=0;
	if(!empty($jugador->Tecnologias))
	{
		foreach ($jugador->Tecnologias as $tecno=>$nivel)
		{
			$coste=CosteTecnologia($tecno,$nivel);
			$costes=$coste[0]+$coste[1]+$coste[2];

			$puntos+=$costes/1000;
		}
	}
	$consulta=$DB->query('SELECT Tecnologias FROM `planetas` WHERE `Jugador`='.$jugador->ID.' LIMIT 18');
	while($datosPlaneta=$consulta->fetch_row())
	{
		$tecnos=unserialize($consulta[0]);

		if(!empty($tecnos))
		{
			foreach ($tecnos as $tecno=>$nivel)
			{
				$puntos+=array_sum(CosteTecnologia($tecno,$nivel))/1000;
			}
		}

		unset($planeta);
	}
	$comandoSQL.='UPDATE `jugadores` SET `Puntos`='.floor($puntos).' where `ID`='.$jugador->ID.' LIMIT 1;';
}

function ProcesarAlianza($id)
{
	global $DB;
	global $comandoSQL;

	$consulta=$DB->query('SELECT Puntos FROM `jugadores` WHERE `Alianza`='.$id);
	$puntosAlianza=0;
	while($jugador=$consulta->fetch_row())
	{
		$puntosAlianza+=$jugador[0];
	}

	$comandoSQL.='UPDATE `alianzas` SET `Puntos`='.round($puntosAlianza/1000).' where `ID`='.$id.' LIMIT 1;';
}

function CopiaSeguridadBD()
{
	global $DB;
	global $confBD;

	$consulta=$DB->first_row('show variables like "basedir"');
	$mysqldumpDir=$consulta[1].'bin/mysqldump';

	exec($mysqldumpDir.' --opt --password=' . $confBD[3] . ' --user=' . $confBD[2] . ' '. $confBD[0],$resultado);

	$backup;
	foreach($resultado as $linea)
	$backup.=$linea."\r\n";

	if(!empty($confBD[5]))
	mail($confBD[4],"Backup de la base de datos de Galactic Wars, del ".date('d - j - Y G:i',time()),$backup);

	if(!empty($confBD[4]))
	{
		$nombreArchivo='SqlBackup ('.date('d - j - Y G.i',time()).'.sql.gz';
		if($file = fopen($confBD[4].'/'.$nombreArchivo,  "w"))
		{
			fwrite($file,gzencode($backup,9,FORCE_GZIP));
			fclose($file);
		}
	}
}
?>