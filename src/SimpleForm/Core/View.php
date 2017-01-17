<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

//require_once('MvcCore/View.php');

//require_once('Helpers.php');
//require_once('Configuration.php');

class SimpleForm_Core_View extends MvcCore_View
{
	/**
	 * @var SimpleForm
	 */
	public $Form = null;
	/**
	 * @var MvcCore_View|mixed
	 */
	public $View = null;

    public function __construct (SimpleForm & $form) {
		$ctrl = $form->Controller;
		if (class_exists('MvcCore_Controller') && $ctrl instanceof MvcCore_Controller) {
			parent::__construct($ctrl);
		} else {
			$this->Controller = $ctrl;
		}
		$this->Form = $form;
		include_once('Helpers.php');
		$this->View = SimpleForm_Core_Helpers::GetControllerView($ctrl);
	}
	/**
	 * Call public field method if exists under called name or try to call any parent view helper.
	 * @param string $method 
	 * @param mixed  $arguments 
	 * @return mixed
	 */
	public function __call ($method, $arguments) {
		if (isset($this->Field) && method_exists($this->Field, $method)) {
			return call_user_func_array(array($this->Field, $method), $arguments);
		} else {
			return parent::__call($method, $arguments);
		}
	}
	/**
	 * Render configured form template.
	 * @return string
	 */
	public function RenderTemplate () {
		return $this->Render($this->Form->TemplateTypePath, $this->Form->TemplatePath);
	}
	/**
	 * Render form naturaly by cycles inside php scripts.
	 * All form fields will be rendered inside empty <div> elements.
	 * @return string
	 */
	public function RenderNaturally () {
		return $this->RenderBegin() . $this->RenderErrors() . $this->RenderContent() . $this->RenderEnd();
	}
	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with csrf tokens.
	 * @return string
	 */
	public function RenderBegin () {
		$result = "<form";
		$attrs = array();
		$form = $this->Form;
		$formProperties = array('Id', 'Action', 'Method', 'Enctype');
		foreach ($formProperties as $property) {
			if ($form->$property) $attrs[strtolower($property)] = $form->$property;
		}
		if ($form->CssClass) $attrs['class'] = $form->CssClass;
		foreach ($form->Attributes as $key => $value) {
			if (!in_array($key, $formProperties)) $attrs[$key] = $value;
		}
		$attrsStr = self::RenderAttrs($attrs);
		if ($attrsStr) $result .= ' ' . $attrsStr;
		$result .= '>';
		$result .= $this->RenderCsrf();
		return $result;
	}
	/**
	 * Render hidden input with CSRF tokens.
	 * This method is not necessary to call, it's 
	 * called internaly by $form->View->RenderBegin();
	 * @return string
	 */
	public function RenderCsrf () {
		list ($name, $value) = $this->Form->SetUpCsrf();
		return '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	}
	/**
	 * Return current cross site request forgery hidden
	 * input name and it's value as stdClass.
	 * Result stdClass elements has keys 'name' and 'value'.
	 * @return stdClass
	 */
	public function GetCsrf () {
		return $this->Form->GetCsrf();
	}
	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into div.errors with div.error elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		$result = "";
		include_once('Configuration.php');
		if ($this->Form->Errors && $this->Form->ErrorsRenderMode == SimpleForm_Core_Configuration::ERROR_RENDER_MODE_ALL_TOGETHER) {
			$result .= '<div class="errors">';
			foreach ($this->Form->Errors as & $errorMessageAndFieldName) {
				$errorMessage = $errorMessageAndFieldName[0];
				$fieldName = isset($errorMessageAndFieldName[1]) ? '' : $errorMessageAndFieldName[1] ;
				$result .= '<div class="error ' . $fieldName . '">'.$errorMessage.'</div>';
			}
			$result .= '</div>';
		}
		return $result;
	}
	/**
	 * Render form content.
	 * Go through all $form->Fields and call $field->Render(); on every field
	 * and put it into an empty <div> element. Render each field in full possible
	 * way - naturaly by label configuration with possible errors configured beside
	 * or with custom field template.
	 * @return string
	 */
	public function RenderContent () {
		$result = "";
		$fieldRendered = "";
		foreach ($this->Form->Fields as $field) {
			$fieldRendered = $field->Render();
			include_once(__DIR__ . '/../Hidden.php');
			if (!($field instanceof SimpleForm_Hidden)) {
				$fieldRendered = "<div>".$fieldRendered."</div>";
			}
			$result .= $fieldRendered;
		}
		return $result;
	}
	/**
	 * Render form end.
	 * Render html closing </form> tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderEnd () {
		$result = "</form>";
		if ($this->Js) $result .= $this->Form->RenderJs();
		if ($this->Css) $result .= $this->Form->RenderCss();
		return $result;
	}
	/**
	 * Format string function.
	 * @param string $str template with replacements like {0}, {1}, {anyStringKey}...
	 * @param array $args each value under it's index is replaced as 
	 *					  string representation by replacement in form {arrayKey}
	 * @return string
	 */
	public static function Format ($str = '', array $args = array()) {
		foreach ($args as $key => $value) {
			$str = str_replace('{'.$key.'}', (string)$value, $str);
		}
		return $str;
	}
	/**
	 * Render content of html tag attributes by key/value array.
	 * @param array $atrributes 
	 * @return string
	 */
	public static function RenderAttrs (array $atrributes = array()) {
		$result = array();
		foreach ($atrributes as $attrName => $attrValue) {
			$result[] = $attrName.'="'.$attrValue.'"';
		}
		return implode(' ', $result);
	}
}
