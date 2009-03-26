<?php

class Planeta
{
	/*
	EL valor Estado de la base de datos indica el tipo de planeta que es
	0-nada, planeta normal
	1-planeta destruido
	2-planeta normal asediado
	3-planeta desconolizado

	El Valor Luna de la base de datos indica

	0 - nada, planeta normal sin luna
	1 - el planeta es una luna
	2 - el planeta es normal con luna
	*/
	/**
	 * Crea una nueva instancia de la clase Planeta
	 *
	 * @param mixed $planeta Int o array desde el cual se cargan los datos del planeta
	 * @param int $fecha Fecha en la que se accede al planeta
	 * @param bool $modoRapido Indica si solo se cargan los componentes basicos (Recursos y tecnologias)
	 * @return Planeta
	 */
	function Planeta($planeta,$fecha=null,$modoRapido=false)
	{
		if(is_numeric($planeta))
		{
			if($modoRapido)
			{
				$consulta=$GLOBALS['DB']->first_assoc('select Metal,Cristal,Antimateria,Tecnologias,Construcciones,UltimaActualizacion from `planetas` where `ID`='.$planeta.' LIMIT 1;');
				$consulta['ID']=$planeta;
			}
			else
			$consulta=$GLOBALS['DB']->first_assoc('select * from `planetas` where `ID`='.$planeta.' LIMIT 1;');
		}
		else
		$consulta=$planeta;

		$this->ID=$consulta['ID'];
		$this->Datos=$consulta;
		$this->DatosOriginales=$consulta;

		if(isset($consulta['Nombre']))
		{
			$this->Nombre=$consulta['Nombre'];
			$this->Energia=$consulta['EnergiaLibre'];
		}
		if(isset($consulta['Metal']))
		$this->CalcularRecursos(false,$fecha);

		$this->Tecnologias=$this->DatosOriginales['Tecnologias']=unserialize($consulta['Tecnologias']);
	}

	var $ID;
	var $Datos;
	var $DatosOriginales;
	var $Nombre;
	var $Metal;
	var $Cristal;
	var $Antimateria;
	var $Energia;
	var $Tecnologias;

	/**
	 * Suma o resta los recursos especificados al planeta
	 *
	 */
	function ModificarRecursos($metal,$cristal,$antimateria,$fecha=null)
	{
		if($this->Datos['ProduccionMetal']==0 && $this->Datos['ProduccionCristal']==0 && $this->Datos['ProduccionAntimateria']==0 )
		return;

		if(!isset($fecha))
		$fecha=time();

		$this->CalcularRecursos(false,$fecha);

		$this->Metal=$this->Datos['Metal']+=$metal;
		$this->Cristal=$this->Datos['Cristal']+=$cristal;

		if($this->Datos['Antimateria']==0)
		{
			$this->Antimateria=$this->Datos['Antimateria']=$antimateria;
			$this->RecalcularProducciones();
		}
		else
		$this->Antimateria=$this->Datos['Antimateria']+=$antimateria;
	}

	/**
	 * Calcula los recursos desde la última actualización hasta la fecha actual o la indicada
	 *
	 * @param bool $guardar Guardar cambios en la base de datos
	 * @param int $fecha Fecha que se procesara, null para la actual
	 */
	function CalcularRecursos($guardar=false,$fecha=null)
	{
		if(!isset($fecha))
		$fecha=time();

		$tiempoTranscurrido=$fecha-$this->Datos['UltimaActualizacion'];

		$AntimateriaActual=$this->CalcularRecurso($tiempoTranscurrido,'Antimateria',92);
		if($AntimateriaActual<0)
		{
			//Antimateria agotada
			$tiempoTranscurridoAntesAgotamiento=round($this->Datos['Antimateria']/($this->Datos['ProduccionAntimateria']/3600));
			$this->Datos['Antimateria']=$this->Antimateria=0;
			$this->Datos['Metal']=$this->Metal=$this->CalcularRecurso($tiempoTranscurridoAntesAgotamiento,'Metal',90);
			$this->Datos['Cristal']=$this->Cristal=$this->CalcularRecurso($tiempoTranscurridoAntesAgotamiento,'Cristal',91);
			$this->RecalcularProducciones();

			$tiempoTranscurrido=$fecha-($this->Datos['UltimaActualizacion']+$tiempoTranscurridoAntesAgotamiento);
		}
		else
		$this->Datos['Antimateria']=$this->Antimateria=$AntimateriaActual;

		$this->Datos['Metal']=$this->Metal=$this->CalcularRecurso($tiempoTranscurrido,'Metal',90);
		$this->Datos['Cristal']=$this->Cristal=$this->CalcularRecurso($tiempoTranscurrido,'Cristal',91);

		$this->Datos['UltimaActualizacion']=$fecha;

		if($guardar)
		$this->GuardarCambios();
	}

	/**
	 * Privada. Calcula la cantidad actual de un recurso.
	 *
	 */
	function CalcularRecurso($tiempoTranscurrido,$idRecurso,$idAlmacen)
	{
		if(empty($this->Datos['Produccion'.$idRecurso]))
		return $this->Datos[$idRecurso];

		$limiteAlmacen=CapacidadAlmacen($idAlmacen,isset($this->Tecnologias[$idAlmacen])?$this->Tecnologias[$idAlmacen]:0);

		if($this->Datos[$idRecurso]>$limiteAlmacen*1.25)
		return $this->Datos[$idRecurso];

		$produccion=round(($this->Datos['Produccion'.$idRecurso]/3600)*$tiempoTranscurrido);

		if($this->Datos[$idRecurso]+$produccion>$limiteAlmacen*1.25)
		return $limiteAlmacen;
		else
		return (int)$this->Datos[$idRecurso]+$produccion;
	}

	/**
	 * Guarda todos los cambios realizados en la clase
	 *
	 */
	function GuardarCambios()
	{
		$this->Datos['Tecnologias']=$this->Tecnologias;
		GuardarCambios($this,'Planetas');
	}

	/**
	 * Devulve un valor bool que indica si hay naves en este planeta
	 *
	 */
	function HayNaves()
	{
		for($contador=300;$contador<320;$contador++)
		{
			if(isset($this->Tecnologias[$contador]) && !empty($this->Tecnologias[$contador]))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Devulve un valor bool que indica si el planeta esta trabajando (Contruyendo en edificos, hangar o defensa)
	 * 
	 * Posible valores de Construcciones
	 * 	-1: En espera para reunir los recursos
	 *   0 o nulo: Parado
	 * 	>0: Tecnología en construcción 
	 *
	 */
	function Trabajando($tipoCola)
	{
		if(!is_array($this->Datos['Construcciones']))//Cargar el array de construcciones
		{
			if(!empty($this->Datos['Construcciones']))
			$this->Datos['Construcciones']=unserialize($this->Datos['Construcciones']);
			else
			$this->Datos['Construcciones']=array();
		}

		return isset($this->Datos['Construcciones'][$tipoCola]) && $this->Datos['Construcciones'][$tipoCola]>0;
	}

	/**
	 * Carga los rendimientos de las minas
	 *
	 */
	function CargarRendimientos()
	{
		if(is_string($this->Datos['Rendimientos']))
		$this->DatosOriginales['Rendimientos']=$this->Datos['Rendimientos']=unserialize($this->Datos['Rendimientos']);
	}

	/**
	 * Calcula las producciones de las minas
	 *
	 */
	function RecalcularProducciones()
	{
		global $DB;
		global $configuracion;

		$this->CargarRendimientos();

		$energiaDisponible=$this->EnergiaProducida();
		$energiaNecesaria=$this->GastoMina(1)+$this->GastoMina(2)+$this->GastoMina(3);

		if($energiaDisponible>$energiaNecesaria)
		$nivelProduccion=100;
		else
		{
			if($energiaNecesaria==0)
			$nivelProduccion=0;
			else
			$nivelProduccion=round((100/$energiaNecesaria)*$energiaDisponible);

			if($nivelProduccion>100)$nivelProduccion=100;
			if($nivelProduccion<0)$nivelProduccion=0;
		}

		$this->Datos['NivelProduccion']=$nivelProduccion;
		$this->Datos['ProduccionMetal']=round(($this->ProduccionMina(1,$nivelProduccion)+50)*$configuracion['NivelProduccion']);
		$this->Datos['ProduccionCristal']=round(($this->ProduccionMina(2,$nivelProduccion)+30)*$configuracion['NivelProduccion']);
		$this->Datos['ProduccionAntimateria']=round(($this->ProduccionMina(3,$nivelProduccion)*$configuracion['NivelProduccion'])-$this->GastoMina(11));
		$this->Energia=$this->Datos['EnergiaLibre']=$energiaDisponible-$energiaNecesaria;
	}

	/**
	 * Energia producida por todas las plantas de producción del planeta
	 *
	 */
	function EnergiaProducida()
	{
		if($this->Antimateria<1)
		return $this->ProduccionMina(10)+$this->ProduccionMina(12);
		return $this->ProduccionMina(10)+$this->ProduccionMina(11)+$this->ProduccionMina(12);
	}

	/**
	 * Gasto de energia de una mina, y si es la planta de antimateria, gasto de antimateria.
	 */
	function GastoMina($ID)
	{
		if(!isset($this->Tecnologias[$ID]) || empty($this->Tecnologias[$ID]) || ($ID==11 && $this->Antimateria<1))
		return 0;

		if(isset($this->Datos['Rendimientos'][$ID])==false)
		$this->Datos['Rendimientos'][$ID]=100;

		return round(GastoEnergiaMina($ID,$this->Tecnologias[$ID])*($this->Datos['Rendimientos'][$ID]/100));
	}

	/**
	 * Produccion de una mina
	 */
	function ProduccionMina($ID,$nivelProduccion=100)
	{
		if(!isset($this->Tecnologias[$ID]) || empty($this->Tecnologias[$ID]) || $nivelProduccion==0 || ($ID==11 && $this->Antimateria<1))
		return 0;

		if(!isset($this->Datos['Rendimientos'][$ID]))
		$this->Datos['Rendimientos'][$ID]=100;

		$produccion=ProduccionMina($ID,$this->Tecnologias[$ID],$this)*($this->Datos['Rendimientos'][$ID]/100);

		if ($ID<10 && $nivelProduccion <100)
		$produccion*=$nivelProduccion/100;

		return round($produccion);
	}
}
?>