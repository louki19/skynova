<?php
/*
Valores del campo metadatos:
['P'] -> Contraseña del jugador
['Menu'] -> Tipo de menu usado
['SG'] -> Skin para la galaxia
['Email']
['EmailSC'] -> Email sin confirmar

El campo estado indica varias cosas

0 - jugador normal
1 - jugador baneado
2 - jugador en vacaciones
3 - jugador inactivo
4 - jugador inactivo mas de 30 dias
5 - jugador GO
*/

class Jugador
{

	/**
	 * Crea una nueva instancia de la clase Jugador
	 *
	 * @param mixed $jugador Int o array desde el cual se cargan los datos
	 * @param bool $modoRapido Indica si solo se cargan los componentes basicos (Tecnologias)
	 * @return Planeta
	 */
	function Jugador($jugador,$modoRapido=false)
	{
		global $DB;

		if(is_numeric($jugador))
		{
			if($modoRapido)
			{
				$consulta=$GLOBALS['DB']->first_assoc('select Tecnologias,Investigando from `jugadores` where `ID`='.$jugador.' LIMIT 1;');
				$consulta['ID']=$jugador;
			}
			else
			$consulta=$GLOBALS['DB']->first_assoc('select * from `jugadores` where `ID`='.$jugador.' LIMIT 1;');
		}
		else
		$consulta=$jugador;

		$this->ID=$consulta['ID'];

		$this->Datos=$consulta;
		$this->DatosOriginales=$consulta;

		if($modoRapido==false)
		{
			$this->Nombre=$consulta['Nombre'];
			$this->UrlSkin=$consulta['UrlSkin'];
		}

		$this->DatosOriginales['Tecnologias']=$this->Tecnologias=unserialize($consulta['Tecnologias']);
	}

	var $ID;
	var $Datos;
	var $DatosOriginales;
	var $Nombre;
	var $UrlSkin;

	/**
	 * Carga datos del jugador como email, hash de la contraseña, etc.
	 *
	 */
	function CargarMetadatos()
	{
		if(is_string($this->Datos['MetaDatos']))
		$this->DatosOriginales['MetaDatos']=$this->Datos['MetaDatos']=unserialize($this->Datos['MetaDatos']);

		return $this->Datos['MetaDatos'];
	}

	/**
	 * Guarda todos los cambios realizados en la clase
	 *
	 */
	function GuardarCambios()
	{
		$this->Datos['Tecnologias']=$this->Tecnologias;
		GuardarCambios($this,'Jugadores');
	}
}
?>