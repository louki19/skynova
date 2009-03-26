<?php 

class FxForms
{
	/**
	 * Categorias del formulario
	 *
	 * @var FxFormsCategorie
	 */
	private $categories;

	/**
	 * Texto mostrado en el boton para enviar el formulario
	 *
	 * @var string
	 */
	public $SubmitText;
	
	public $Method;
	public $Action;

	/**Strings**/
	public $EmptyValueError='Por favor, rellena todos los campos marcados como requeridos.';
	public $InvalidValue='El valor establecido no tiene el formato válido.';

	public function __construct()
	{
		$this->categories=array();
		$this->errors=array();
		$this->Method='post';
		$this->Action='';
	}

	/**
	 * Add a categorie to the form
	 *
	 * @param FxFormsCategorie $categorie
	 */
	public function AddCategorie(FxFormsCategorie $categorie)
	{
		$this->categories[]=$categorie;
	}

	public function ShowForm()
	{
		$code='<form method="'.$this->Method.'" action="'.$this->Action.'" class="fxform" '.$this->formAttributes.'>';

		foreach($this->categories as $categorie)
		{
			$code.= '<fieldset><legend>'.$categorie->Name.'</legend>';

			foreach($categorie->Fields as $field)
			{
				if(strcasecmp('hidden',$field->Type)==0)
				{
					$code.="<input type='hidden' id='$field->Name' name='$field->Name' value='$field->Value' $field->OptionalAttributes/>";
					continue;
				}

				$code.= '<div>';

				if(isset($this->errors[$field->Name]))//Mostrar error
				{
					$code.= '<p class="error">'.$this->errors[$field->Name].'</p>';
					$class='class="fieldError"';
				}
				else $class='';

				$code.= '<label for="'.$field->Name.'">'.$field->Label.'</label>';

				if((!$categorie->Required && isset($_REQUEST[$field->Name])) || ($categorie->Required && !empty($_REQUEST[$field->Name])))
				$valor=$_REQUEST[$field->Name];
				else
				$valor=$field->Value;

				if(strcasecmp('textarea',$field->Type)==0)
				{
					$code.="<textarea $class id='$field->Name' name='$field->Name' $field->OptionalAttributes>$valor</textarea>";
				}
				else if(strcasecmp('select',$field->Type)==0)
				{
					$code.="<select $class id='$field->Name'  style='width: 350px;' name='$field->Name' $field->OptionalAttributes>";
					foreach($field->Options as $value=>$text)
					{
						if(strcasecmp("$value","$valor")==0)
						$code.="<option value='$value' selected='selected'>$text</option>";
						else
						$code.="<option value='$value'>$text</option>";
					}
					$code.='</select>';
				}
				else if(strcasecmp('radio',$field->Type)==0)
				{
					foreach($field->Options as $value=>$text)
					{
						$checked=$value==$valor?'checked="checked"':'';
						$code.="<label><input type='$field->Type' value='$value' id='$field->Name' name='$field->Name' $checked $field->OptionalAttributes/> $text</label>";
					}
				}
				else if(strcasecmp('html',$field->Type)==0)
				{
					$code.=$field->OptionalAttributes;
				}
				else
				{
					if(strcasecmp('checkbox',$field->Type)==0 && isset($_REQUEST[$field->Name]))
					$field->OptionalAttributes.='checked="true"';
					
					$code.="<input $class type='$field->Type' value='$valor'  style='width: 350px;' id='$field->Name' name='$field->Name' $field->OptionalAttributes/>";
				}
				
				if(!empty($field->ExtraCode))
				$code.=$field->ExtraCode;

				if(!empty($field->Description))
				{
					$code.='<p class="fieldDescription">'.$field->Description.'</p>';
				}
				$code.='</div>';
			}

			$code.= '</fieldset>';
		}

		$code.= '<input type="submit" '.(!empty($this->SubmitText)?'value="'.$this->SubmitText.'"':'').'/>
</form>';

		echo $code;
	}

	/**
	 * Comprueba los datos recibidos del formulario y devuelve un array si son correctos
	 *
	 * @param $onlyChanges Indica si solo se añaden al array resultado los valores que cambian respecto al valor por defecto
	 * @param $escapeFunction Funcion empleada para escapar los datos obtenidos
	 * @return bool,array
	 */
	public function ValidateResponse($onlyChanges=true,$escapeFunction=null)
	{
		if(empty($_REQUEST))
		return false;

		$res=array();

		foreach($this->categories as $categorie)
		{
			foreach($categorie->Fields as $field)
			{
				if(strcasecmp('html',$field->Type)==0)
				continue;

				$valor=$_REQUEST[$field->Name];

				if(empty($valor))
				{
					if($categorie->Required)
					{
						if(isset($field->ValidateFunction))//Si posee codigo de validacion, usarlo
						{
							$retorno=call_user_func($field->ValidateFunction,$valor);

							if(is_string($retorno))
							{
								$this->SetError($retorno,$field->Name);
								continue;
							}
							else if($retorno==false)
							{
								$this->SetError($this->InvalidValue,$field->Name);
								continue;
							}
							else
							continue;
						}
						else
						$this->SetError($this->EmptyValueError,$field->Name);
					}
					else//No requerido
					{
						if(isset($escapeFunction))
						$res[$field->Name]=call_user_func($escapeFunction,$valor);
						else
						$res[$field->Name]=$valor;
					}

					continue;
				}


				if(empty($valor))
				$valor=$field->Value;

				if(isset($field->ValidateFunction))//Usar la funcion definida por el usuario para validar el campo
				{
					$retorno=call_user_func($field->ValidateFunction,$valor);

					if(is_string($retorno))
					{
						$this->SetError($retorno,$field->Name);
						continue;
					}
					else if($retorno==false)
					{
						$this->SetError($this->InvalidValue,$field->Name);
						continue;
					}
				}

				if($onlyChanges && $valor==$field->Value && strcasecmp('hidden',$field->Type)!=0)
				continue;

				if(isset($escapeFunction))
				$res[$field->Name]=call_user_func($escapeFunction,$valor);
				else
				$res[$field->Name]=$valor;
			}
		}

		return $this->HasErrors()?false:$res;
	}


	/**
	 * Obtiene todos los campos que forman el formulario
	 *
	 */
	public function GetAllFields()
	{
		$res=array();

		foreach($this->categories as $categorie)
		{
			foreach($categorie->Fields as $field)
			{
				$res[]=$field;
			}
		}

		return $res;
	}


	/**
	 * Establece un error en el formulario
	 *
	 */
	public function SetError($errorText,$fieldName)
	{
		$this->errors[$fieldName]=$errorText;
	}
	private $errors;

	public function HasErrors()
	{
		return count($this->errors)>0;
	}
}

class FxFormsCategorie
{
	/**
	 * Name for the categoria
	 * Nombre de la categoría
	 *
	 * @var string
	 */
	public $Name;
	/**
	 * Indica si los campos contenidos en esta categoría son obligatorios de rellenar
	 *
	 * @var bool
	 */
	public $Required;

	/**
	 * Campos que contiene la categoria
	 *
	 * @var array
	 */
	public $Fields;

	/**
	 * Inicializa una nueva instancia de la clase
	 *
	 * @param string $name
	 * @param array $fields Array de objetos FxFormsField con los campos del formulario
	 * @param bool $required
	 */
	public function __construct($name,$fields,$required=false)
	{
		$this->Name=$name;
		$this->Fields=$fields;
		$this->Required=$required;
	}

	/**
	 * Add a field to the current categorie
	 *
	 * @param FxFormsField $field
	 */
	public function AddField(FxFormsField $field)
	{
		$this->Fields[]=$field;
	}
}

class FxFormsField
{
	/**
	 * Tipo de campo. Si el valor de esta variable es 'html', solo se mostrará el codigo incluido en la variable OptionalAttributes. Los campos tipo 'html' no se validaran
	 *
	 * @var string
	 */
	public $Type;
	public $Label;
	public $Description;
	public $Name;
	/**
	 * Valor por defecto del campo. 
	 *
	 * @var array
	 */
	public $Value;
	/**
	 * Si el tipo de campo es Select o Radio entonces esta variable es un array asociativo $valor=>$texto con las distinas opciones del campo
	 *
	 * @var array
	 */
	public $Options;
	/**
	 * Atributos opcionales que se situaran en el campo
	 *
	 * @var string
	 */
	public $OptionalAttributes;
	/**
	 * Funcion llamada para validar el campo actual. La funcion debe contener un parametro, que sera el valor 
	 * obtenido para el campo, y debera devolver un valor true si es correcto el valor, o una cadena con el texto del error
	 *
	 * @var callaback
	 */
	public $ValidateFunction;

	public function __construct($name,$label,$value='',$type='text',$description='',$validateFunction=null,$optionalAttributes='',$options=null)
	{
		$this->Name=$name;
		$this->Label=$label;
		$this->Value=$value;
		$this->Type=$type;
		$this->Description=$description;
		$this->OptionalAttributes=$optionalAttributes;
		$this->ValidateFunction=$validateFunction;
		$this->Options=$options;
	}
}

?>