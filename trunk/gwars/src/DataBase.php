<?php
/*
DataBase Manager by aNTRaX

Manage mysql databases from php
*/

/**
 * Conecta a una base de datos y devuelve la clase de conexión
 *
 * @return DatabaseMysqli
 */
function ConnectDataBase($host,$user,$pass,$db)
{
	if(class_exists('mysqli'))
	{
		$link = new mysqli($host,$user,$pass,$db);

		if (mysqli_connect_errno())
		{
			echo 'Conexión fallida con la base de datos: '.mysqli_connect_error();
			exit();
		}

		return new DatabaseMysqli($link);
	}
	else
	{
		$this->link=mysql_connect($host, $user,$pass) or die('Conexión fallida con la base de datos: ' . mysql_error());

		mysql_select_db($db) or die('No pudo seleccionarse la BD.');

		return new DatabaseMysql($link);
	}
}

class DataBase
{
	/**
 * Obtiene la primera fila de una consulta, en un array cuyos indices son los nombres de las columnas de la tabla
 * @return array
 */
	function first_assoc($sql)
	{
		$consulta=$this->query($sql);
		return $consulta->fetch_assoc();
	}


	/**
 * Obtiene la primera fila de una consulta, en un array cuyos indices son numeros
 * @return array
 */
	function first_row($sql)
	{
		$consulta=$this->query($sql);
		return $consulta->fetch_row();
	}

	//Funciones para manejo de datos de tablas

	/**
 * Obtiene todas las propiedades de una fila especificando su ID
 * @return array
 */
	function getRow($Tabla,$ID)
	{
		if($ID==0)
		return;

		if(is_numeric($ID))
		return $this->first_assoc("select * from `$Tabla` where `ID`=$ID LIMIT 1");
	}

	/**
 * Obtiene una propiedad de una fila especificando su ID
 * @return array
 */
	function getRowProperty($Tabla,$ID,$propiedad)
	{
		$datos=	$this->first_assoc("select `$propiedad` from `$Tabla` where `ID`=$ID LIMIT 1");
		return $datos[$propiedad];
	}

	/**
 * Obtiene varias propiedades de una fila especificando su ID
 * @return array
 */
	function getRowProperties($Tabla,$ID,$propiedades)
	{
		return $this->first_assoc("select $propiedades from `$Tabla` where `ID`=$ID LIMIT 1");
	}

	/**
 * Establece una propiedad en una fila
 */
	function setRowProperty($Tabla,$ID,$nombre,$valor)
	{
		if(is_numeric($valor))
		$this->query("UPDATE `$Tabla` SET `$nombre`=$valor where `ID`=$ID");
		else
		$this->query("UPDATE `$Tabla` SET `$nombre`='".$this->escape_string($valor)."' where `ID`=$ID");
	}

	/**
 * Establece varias propiedades en una fila
 */
	function setRowProperties($Tabla,$ID,$propiedades)
	{
		$sql='';
		foreach( $propiedades as $key => $value )
		{
			if(is_numeric($propiedades[$key]) || preg_match ('/^[0][x][0-9a-fA-F]+$/',$propiedades[$key])==1)
			{
				$sql.="`$key`=".$value.',';
			}
			else
			{
				$sql.="`$key`='".$this->escape_string($value).'\',';
			}
		}
		$sql="UPDATE `$Tabla` SET ".substr($sql,0,strlen($sql)-1)." where `ID`=$ID LIMIT 1";

		$this->query($sql);
	}

	/**
 * Borra una fila
 */
	function deleteRow($Tabla,$ID)
	{
		if(is_numeric($ID))
		{
			$this->query("DELETE FROM `$Tabla` WHERE `ID` = $ID LIMIT 1");
		}
	}
}

class DatabaseMysqli extends DataBase
{
	/**
	 * Link de la conexion
	 *
	 * @var mysqli
	 */
	var $link;
	function DatabaseMysqli($link)
	{
		$this->link=$link;
	}

	/**
 * Ejecuta una consulta
 * @return QueryResultMysqli
 */
	function query($sql)
	{
		//return new QueryResultMysqli($this->link->query($sql));

		//Debug
		$inicio=microtime(true);
		$result=new QueryResultMysqli($this->link->query($sql));
		$final=microtime(true);

		$tiempo=$final-$inicio;

		$GLOBALS['textoFooter'].="<br/>Consulta: $sql<br/>Tiempo ejecucion: $tiempo s<br/>Filas afectadas / obtenidas: {$this->affected_rows()} /  {$result->num_rows()}";
		$GLOBALS['queries']++;
		return $result;
	}

	function escape_string($string)
	{
		return $this->link->real_escape_string($string);
	}

	/**
	 * Filas afectadas por la ultima consulta
	 *
	 * @return int
	 */
	function affected_rows()
	{
		return $this->link->affected_rows;
	}

	/**
 * Obtiene el último indice añadido
 * @return integer
 */
	function lastInsertId()
	{
		return mysqli_insert_id($this->link);
	}

	/**
 * Obtiene el numero de campos devueltos en la ultima consulta
 * @return integer
 */
	function numFields()
	{
		return $this->link->field_count();
	}
}

class DatabaseMysql extends DataBase
{
	/**
	 * Link de la conexion
	 *
	 * @var resource
	 */
	var $link;
	function DatabaseMysql($link)
	{
		$this->link=$link;
	}

	/**
 * Ejecuta una consulta
 * @return QueryResultMySql
 */
	function query($sql)
	{
		return new QueryResultMySql(mysql_query($sql));
	}
	function escape_string($string)
	{
		return mysql_escape_string($string);
	}

	function affected_rows()
	{
		return mysql_affected_rows();
	}

	/**
 * Obtiene el último indice añadido
 * @return integer
 */
	function lastInsertId()
	{
		return mysql_insert_id();
	}

	/**
 * Obtiene el numero de campos devueltos en la ultima consulta
 * @return integer
 */
	function numFields()
	{
		return mysql_num_fields($this->value);
	}
}

/* Clases para los resultados */

class QueryResultMysqli
{
	/**
	 * Resultado de una query
	 *
	 * @var mysqli_result
	 */
	var $value;

	function QueryResultMysqli($value)
	{
		$this->value=$value;
	}

	function num_rows()
	{
		return $this->value->num_rows;
	}


	function fetch_array()
	{
		return $this->value->fetch_array();
	}

	function fetch_assoc()
	{
		return $this->value->fetch_assoc();
	}

	function fetch_row()
	{
		return $this->value->fetch_row();
	}


	function fetch_field()
	{
		return $this->value->fetch_field();
	}

	function close()
	{
		return $this->value->close();
	}

	/**
 * Obtiene el nombre de un campo
 * @return integer
 */
	function field_name($indice)
	{
		$columnas=$this->value->fetch_fields();
		return $columnas[$indice]->name;
	}
}

class QueryResultMySql
{
	var $value;

	function QueryResultMySql($value)
	{
		$this->value=$value;
	}

	function num_rows()
	{
		return mysql_num_rows($this->value);
	}


	function fetch_array()
	{
		return mysql_fetch_array($this->value);
	}

	function fetch_assoc()
	{
		return mysql_fetch_assoc($this->value);
	}

	function fetch_row()
	{
		return mysql_fetch_row($this->value);
	}


	function fetch_field()
	{
		return mysql_fetch_field($this->value);
	}

	function close()
	{
		return mysql_free_result($this->value);
	}

	/**
 * Obtiene el nombre de un campo
 * @return integer
 */
	function field_name($indice)
	{
		return mysql_field_name($this->value,$indice);
	}
}
?>