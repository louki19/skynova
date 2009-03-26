<?php

class Solicitudes
{
	/**
	 * Tipos de solicitudes
	 * 
	 * 1 - Acceso alianza
	 *
	 */
	function EnviarNuevaSolicitud()
	{}

	function AceptarSolicitud()
	{}

	/**
	 * Obtiene el destino de una solicitud, o false si no existe.
	 */
	function ObtenerSolicitud($origen,$tipo,$destino=null)
	{
		if(isset($destino))
		$consulta=$GLOBALS['DB']->first_assoc('SELECT `Destino` FROM `solicitudes` WHERE `Origen` = '.$origen.' && `Destino` = '.$destino.' && `Tipo` = '.$tipo.' LIMIT 1');
		else
		$consulta=$GLOBALS['DB']->first_assoc('SELECT `Destino` `solicitudes` WHERE `Origen` = '.$origen.' && `Tipo` = '.$tipo.' LIMIT 1');

		if($consulta->num_rows()>0)
		return $consulta['Destino']>0;
		else return false;
	}

	/**
	 * Borra una solicitud y devuelve si se ha borrado algun elemento de la base de datos o no
	 *
	 */
	function BorrarSolicitud($origen,$destino,$tipo)
	{
		global $DB;

		$DB->query('DELETE FROM `solicitudes` WHERE `Origen` = '.$origen.' && `Destino` = '.$destino.' && `Tipo` = '.$tipo.' LIMIT 1');

		return $DB->affected_rows()>0;
	}
}

?>