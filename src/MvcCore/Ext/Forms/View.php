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

class View extends \MvcCore\View
{
	/**
	 * Rendered form instance reference, which view belongs to.
	 * @var \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm|NULL
	 */
	protected $form = NULL;

	/**
	 * Controller view instance reference, which form belongs to.
	 * Every form is usually created inside MvcCore controller instance,
	 * and mostly every controller instance has it's own view.
	 * @var \MvcCore\View|\MvcCore\Interfaces\IView|NULL
	 */
	protected $view = NULL;

	/**
	 * Originaly declared internal view properties to protect their
	 * possible overwriting by `__set()` or `__get()` magic methods.
	 * @var array
	 */
	protected static $protectedProperties = [
		'form'				=> 1,
		'field'				=> 1,
		'view'				=> 1,
		'_controller'		=> 1,
		'_store'			=> 1,
		'_helpers'			=> 1,
		'_content'			=> 1,
		'_renderedFullPaths'=> 1,
	];

	/**
	 * Global views forms directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms"`, so scripts app path
	 * is `"/App/Views/Forms"`.
	 * @var string
	 */
	protected static $formsDir = 'Forms';

	/**
	 * Global views fields directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms/Fields"`, so 
	 * scripts app path is `"/App/Views/Forms/Fields"`.
	 * @var string
	 */
	protected static $fieldsDir = 'Forms/Fields';

	/**
	 * Get global views forms directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms"`, so scripts app path
	 * is `"/App/Views/Forms"`.
	 * @return string
	 */
	public static function GetFormsDir () {
		return static::$formsDir;
	}

	/**
	 * Set global views forms directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms"`, so scripts app path
	 * is `"/App/Views/Forms"`.
	 * @param string $formsDir
	 * @return string
	 */
	public static function SetFormsDir ($formsDir = 'Forms') {
		return static::$formsDir = $formsDir;
	}

	/**
	 * Get global views fields directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms/Fields"`, so 
	 * scripts app path is `"/App/Views/Forms/Fields"`.
	 * @return string
	 */
	public static function GetFieldsDir () {
		return static::$fieldsDir;
	}

	/**
	 * Set global views fields directory placed by default
	 * inside `"/App/Views"` directory.
	 * Default value is `"Forms/Fields"`, so 
	 * scripts app path is `"/App/Views/Forms/Fields"`.
	 * @param string $fieldsDir
	 * @return string
	 */
	public static function SetFieldsDir ($fieldsDir = 'Forms/Fields') {
		return static::$fieldsDir = $fieldsDir;
	}

	/**
	 * Get controller instance as reference.
	 * @return \MvcCore\View|\MvcCore\Interfaces\IView
	 */
	public function & GetView () {
		return $this->view;
	}

	/**
	 * Set controller and it's view instance.
	 * @param \MvcCore\View|\MvcCore\Interfaces\IView $view
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function & SetView (\MvcCore\Interfaces\IView & $view) {
		$this->view = & $view;
		return $this;
	}
	
	/**
	 * Get form instance to render.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & GetForm () {
		return $this->form;
	}
	
	/**
	 * Set form instance to render.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		$this->form = & $form;
		return $this;
	}

	/**
	 * Call public field method if exists under called name or try to call any parent view helper.
	 * @param string $method
	 * @param mixed  $arguments
	 * @return mixed
	 */
	public function __call ($method, $arguments) {
		if (isset($this->field) && method_exists($this->field, $method)) {
			return call_user_func_array([$this->field, $method], $arguments);
		} else {
			return parent::__call($method, $arguments);
		}
	}

	/**
	 * Render configured form template.
	 * @return string
	 */
	public function RenderTemplate () {
		$formViewScript = $this->form->GetViewScript();
		return $this->Render(
			static::$formsDir, 
			is_bool($formViewScript) ? $this->form->GetId() : $formViewScript
		);
	}

	/**
	 * Render form naturaly by cycles inside php scripts.
	 * All form fields will be rendered inside empty <div> elements.
	 * @return string
	 */
	public function RenderNaturally () {
		return $this->RenderBegin() 
			. $this->RenderErrors() 
			. $this->RenderContent() 
			. $this->RenderEnd();
	}

	/**
	 * Render form begin.
	 * Render opening <form> tag and hidden input with csrf tokens.
	 * @return string
	 */
	public function RenderBegin () {
		$result = "<form";
		$attrs = [];
		$form = & $this->form;
		$formProperties = ['id', 'action', 'method', 'enctype'];
		foreach ($formProperties as $property) {
			$getter = 'Get'.ucfirst($property);
			$formPropertyValue = $form->$getter();
			if ($formPropertyValue) 
				$attrs[$property] = $formPropertyValue;
		}
		$formCssClass = $form->GetCssClass();
		if ($formCssClass) 
			$attrs['class'] = $formCssClass;
		foreach ($form->GetAttributes() as $key => $value) {
			if (!in_array($key, $formProperties)) 
				$attrs[$key] = $value;
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
		list ($name, $value) = $this->form->SetUpCsrf();
		return '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	}

	/**
	 * Return current CSRF (Cross Site Request Forgery) hidden
	 * input name and it's value as `\stdClass`.
	 * Result `\stdClass` has keys: `name` and `value`.
	 * @return \stdClass
	 */
	public function GetCsrf () {
		return $this->form->GetCsrf();
	}

	/**
	 * Render form errors.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into `div.errors` with `div.error` elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors () {
		$result = '';
		$errors = & $this->form->GetErrors();
		if ($errors && $this->form->GetErrorsRenderMode() == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
			$result .= '<div class="errors">';
			foreach ($errors as & $errorMessageAndFieldNames) {
				list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
				$result .= '<div class="error ' . implode(' ', $fieldNames) . '">'.$errorMessage.'</div>';
			}
			$result .= '</div>';
		}
		return $result;
	}

	/**
	 * Render form content - form fields.
	 * Go through all `$form->fields` and call `$field->Render();` on every field
	 * and put it into an empty `<div>` element. Render each field in full possible
	 * way - naturaly by label configuration with possible errors configured beside
	 * or with custom field template.
	 * @return string
	 */
	public function RenderContent () {
		$result = "";
		$fieldRendered = "";
		foreach ($this->form->GetFields() as & $field) {
			$fieldRendered = $field->Render();
			if (!($field instanceof \MvcCore\Ext\Forms\Fields\Hidden)) {
				$fieldRendered = "<div>".$fieldRendered."</div>";
			}
			$result .= $fieldRendered;
		}
		return $result;
	}

	/**
	 * Render form end.
	 * Render html closing `</form>` tag and supporting javascript and css files
	 * if is form not using external js/css renderers.
	 * @return string
	 */
	public function RenderEnd () {
		return '</form>' 
			. $this->form->RenderSupportingJs() 
			. $this->form->RenderSupportingCss();
	}

	/**
	 * Format string function.
	 * @param string $str Template with replacements like `{0}`, `{1}`, `{anyStringKey}`...
	 * @param array $args Each value under it's index is replaced as
	 *					  string representation by replacement in form `{arrayKey}`
	 * @return string
	 */
	public static function Format ($str = '', array $args = []) {
		foreach ($args as $key => $value) {
			$pos = strpos($str, '{'.$key.'}');
			$str = substr($str, 0, $pos) . $value . substr($str, $pos + strlen($key) + 2);
		}
		return $str;
	}

	/**
	 * Render content of html tag attributes by key/value array.
	 * @param array $atrributes
	 * @return string
	 */
	public static function RenderAttrs (array $atrributes = []) {
		$result = [];
		foreach ($atrributes as $attrName => $attrValue)
			$result[] = $attrName.'="'.$attrValue.'"';
		return implode(' ', $result);
	}
}
