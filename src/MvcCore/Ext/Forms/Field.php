<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms;

//require_once('Exception.php');
//require_once('View.php');

abstract class Field implements \MvcCore\Ext\Forms\IField
{
	use \MvcCore\Ext\Forms\Field\Props;
	use \MvcCore\Ext\Forms\Field\Getters;
	use \MvcCore\Ext\Forms\Field\Setters;
	use \MvcCore\Ext\Forms\Field\Rendering;
	
    /**
     * Create new form control instance.
     * @param array $cfg config array with camel case
	 *					 public properties and its values which you want to configure.
	 * @throws \InvalidArgumentException
     */
    public function __construct ($cfg = array()) {
		static::$Templates = (object) static::$Templates;
		foreach ($cfg as $key => $value) {
			$propertyName = ucfirst($key);
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				$clsName = get_class($this);
				throw new \InvalidArgumentException(
					"Property: '$propertyName' is protected, class: '$clsName'."
				);
			} else {
				$this->$propertyName = $value;
			}
		}
	}
	/**
	 * Set any nondeclared property dynamicly
	 * to get it in view by rendering process.
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set ($name, $value) {
		$this->$name = $value;
	}
	/**
	 * This method  is called internaly from \MvcCore\Ext\Form after field
	 * is added into form by $form->AddField(); method. Do not use it
	 * if you are only user of this library.
	 * - check if field has any name, which is required
	 * - set up form and field id attribute by form id and field name
	 * - set up required
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Form & $form) {
		if (!$this->Name) {
			$clsName = get_class($this);
			include_once('Exception.php');
			throw new \InvalidArgumentException("No 'Name' defined for form field: '$clsName'.");
		}
		$this->Form = $form;
		$this->Id = implode(\MvcCore\Ext\Forms\IForm::HTML_IDS_DELIMITER, array(
			$form->Id,
			$this->Name
		));
		// if there is no specific required boolean - set required boolean by form
		$this->Required = is_null($this->Required) ? $form->Required : $this->Required ;
		return $this;
	}
	/**
	 * Set up field properties before rendering process.
	 * - set up field render mode
	 * - set up translation boolean
	 * - translate label if any
	 * @return void
	 */
	public function PreDispatch () {
		$form = $this->Form;
		$translator = $form->Translator;
		// if there is no specific render mode - set render mode by form
		if (is_null($this->RenderMode)) {
			$this->RenderMode = $form->GetDefaultFieldsRenderMode();
		}
		// translate only if Translate options is null or true and translator handler is defined
		if (
			(is_null($this->Translate) || $this->Translate === TRUE || $form->Translate) &&
			!is_null($translator)
		) {
			$this->Translate = TRUE;
		} else {
			$this->Translate = FALSE;
		}
		if ($this->Translate && $this->Label) {
			$this->Label = call_user_func($translator, $this->Label, $form->Lang);
		}
	}
}
