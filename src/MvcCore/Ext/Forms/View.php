<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms;

/**
 * Responsibility: create and extended MvcCore view instance to render form or
 * form field with custom view template. This view contains built-in properties:
 *  - `view`  - Containing parent controller view.
 *  - `form`  - Containing rendered form instance when form or field is rendered.
 *  - `field` - Containing rendered field instance when field is rendered.
 * This view also contains many built-in methods to render specific form parts:
 * - `RenderBegin()`   - Renders opening `<form>` tag with all configured
 *                       attributes.
 * - `RenderErrors()`  - Renders translated form errors.
 * - `RenderContent()` - Render all configured form fields from
 *                       `$this->form->GetFields()` array by calling `Render()`
 *                       method on every field instance.
 * - `RenderEnd()`     - Renders opening `<form>` tag and configured form
 *                       field's supporting js/css files.
 * - `static Format()`
 */
class View extends \MvcCore\View {

	/**
	 * Rendered form instance reference, which view belongs to.
	 * @var \MvcCore\Ext\Form|NULL
	 */
	protected $form = NULL;

	/**
	 * Rendered form field reference if view is not form's view.
	 * @var \MvcCore\Ext\Forms\Field|NULL
	 */
	protected $field = NULL;

	/**
	 * Controller view instance reference, which form belongs to.
	 * Every form is usually created inside MvcCore controller instance,
	 * and mostly every controller instance has it's own view.
	 * @var \MvcCore\View|NULL
	 */
	protected $view = NULL;

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
	 * @inheritDocs
	 * @return string
	 */
	public static function GetFormsDir () {
		return static::$formsDir;
	}

	/**
	 * @inheritDocs
	 * @param  string $formsDir
	 * @return string
	 */
	public static function SetFormsDir ($formsDir = 'Forms') {
		return static::$formsDir = $formsDir;
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public static function GetFieldsDir () {
		return static::$fieldsDir;
	}

	/**
	 * @inheritDocs
	 * @param  string $fieldsDir
	 * @return string
	 */
	public static function SetFieldsDir ($fieldsDir = 'Forms/Fields') {
		return static::$fieldsDir = $fieldsDir;
	}

	/**
	 * Creates form view instance.
	 */
	public function __construct () {
		/**
		 * Default flag if view is used for field rendering or only for form
		 * rendering. Default value is for form rendering - `FALSE`.
		 */
		$this->__protected['fieldRendering'] = FALSE;
	}

	/**
	 * @inheritDocs
	 * @return \MvcCore\View
	 */
	public function GetView () {
		return $this->view;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\View $view
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetView (\MvcCore\IView $view) {
		$this->view = $view;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm () {
		return $this->form;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Form $form
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		$this->form = $form;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function GetField () {
		return $this->field;
	}

	/**
	 * @inheritDocs
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field) {
		$this->field = $field;
		$this->__protected['fieldRendering'] = TRUE;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  string $name
	 * @return mixed
	 */
	public function __get ($name) {
		/** @var $store array */
		$store = & $this->__protected['store'];
		$phpWithTypes = PHP_VERSION_ID >= 70400;
		// if property is in view store - return it
		if (array_key_exists($name, $store))
			return $store[$name];
		/** @var $property \ReflectionProperty */
		// if property is not in store and this view is used for field rendering,
		// try to complete result with property by `$name` in `$this->field` instance:
		if (
			$this->__protected['fieldRendering'] &&
			$this->field instanceof \MvcCore\Ext\Forms\IField &&
			$fieldType = $this->getReflectionClass('field')
		) {
			if ($fieldType->hasProperty($name)) {
				$property = $fieldType->getProperty($name);
				if (!$property->isStatic()) {
					if (!$property->isPublic()) $property->setAccessible (TRUE); // protected or private
					$value = NULL;
					if (PHP_VERSION_ID >= 70400 && $property->hasType()) {
						if ($property->isInitialized($this->field))
							$value = $property->getValue($this->field);
					} else {
						$value = $property->getValue($this->field);
					}
					$store[$name] = & $value;
					return $value;
				}
			}
		}
		// if property is still not in store, try to complete result with property by
		// `$name` in `$this->form` instance:
		if (
			$this->form instanceof \MvcCore\Ext\IForm &&
			$formType = $this->getReflectionClass('form')
		) {
			if ($formType->hasProperty($name)) {
				$property = $formType->getProperty($name);
				if (!$property->isStatic()) {
					if (!$property->isPublic()) $property->setAccessible (TRUE); // protected or private
					$value = NULL;
					if (PHP_VERSION_ID >= 70400 && $property->hasType()) {
						if ($property->isInitialized($this->form))
							$value = $property->getValue($this->form);
					} else {
						$value = $property->getValue($this->form);
					}
					$store[$name] = & $value;
					return $value;
				}
			}
		}
		// if property is still not in store, try to complete result by given view
		// instance, which search in it's store and in it's controller instance:
		if ($this->view instanceof \MvcCore\IView)
			return $this->view->__get($name);
		// return NULL, if property is not in local store an even anywhere else
		return NULL;
	}

	/**
	 * @inheritDocs
	 * @param  string $name
	 * @return bool
	 */
	public function __isset ($name) {
		/** @var $store array */
		$store = & $this->__protected['store'];
		$phpWithTypes = PHP_VERSION_ID >= 70400;
		// if property is in view store - return TRUE
		if (array_key_exists($name, $store)) return TRUE;
		/** @var $property \ReflectionProperty */
		// if property is not in store and this view is used for field rendering,
		// try to complete result with property by `$name` in `$this->field` instance:
		if (
			$this->__protected['fieldRendering'] &&
			$this->field instanceof \MvcCore\Ext\Forms\IField &&
			$fieldType = $this->getReflectionClass('field')
		) {
			if ($fieldType->hasProperty($name)) {
				$property = $fieldType->getProperty($name);
				if (!$property->isStatic()) {
					if (!$property->isPublic())
						$property->setAccessible (TRUE); // protected or private
					$value = NULL;
					if ($phpWithTypes && $property->hasType()) {
						if ($property->isInitialized($this->field))
							$value = $property->getValue($this->field);
					} else {
						$value = $property->getValue($this->field);
					}
					$store[$name] = & $value;
					return TRUE;
				}
			}
		}
		// if property is still not in store, try to complete result with property by
		// `$name` in `$this->form` instance:
		if (
			$this->form instanceof \MvcCore\Ext\IForm &&
			$formType = $this->getReflectionClass('form')
		) {
			if ($formType->hasProperty($name)) {
				$property = $formType->getProperty($name);
				if (!$property->isStatic()) {
					if (!$property->isPublic())
						$property->setAccessible (TRUE); // protected or private
					$value = NULL;
					if ($phpWithTypes && $property->hasType()) {
						if ($property->isInitialized($this->form))
							$value = $property->getValue($this->form);
					} else {
						$value = $property->getValue($this->form);
					}
					$store[$name] = & $value;
					return TRUE;
				}
			}
		}
		// if property is still not in store, try to complete result by given view
		// instance, which search in it's store and in it's controller instance:
		if ($this->view instanceof \MvcCore\IView)
			return $this->view->__isset($name);
		// return FALSE, if property is not in local store an even anywhere else
		return FALSE;
	}

	/**
	 * @inheritDocs
	 * @param  string $method
	 * @param  mixed  $arguments
	 * @return mixed
	 */
	public function __call ($method, $arguments) {
		$field = $this->field;
		$form = $this->form;
		if (
			$this->__protected['fieldRendering'] &&
			$field instanceof \MvcCore\Ext\Forms\IField &&
			method_exists($field, $method)
		) {
			return call_user_func_array([$field, $method], $arguments);
		} else if (
			$form instanceof \MvcCore\Ext\IForm &&
			method_exists($form, $method)
		) {
			return call_user_func_array([$form, $method], $arguments);
		} else {
			return parent::__call($method, $arguments);
		}
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderTemplate () {
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);
		$formViewScript = $this->form->GetViewScript();
		return $this->Render(
			static::$formsDir,
			is_bool($formViewScript) ? $this->form->GetId() : $formViewScript
		);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderNaturally () {
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);
		$result = [$this->RenderBegin()];
		$formRenderModeTable = $this->form->GetFormRenderMode() === \MvcCore\Ext\IForm::FORM_RENDER_MODE_TABLE_STRUCTURE;
		if ($formRenderModeTable) {
			foreach ($this->form->GetFields() as $field) 
				if ($field instanceof \MvcCore\Ext\Forms\Fields\Hidden) 
					$result[] = $field->Render();
			$result[] = '<table border="0" cellspacing="0" cellpadding="0">';
		}
		$result[] = $this->RenderErrors();
		$result[] = $this->RenderContent();
		$result[] = $this->RenderEnd();
		if ($formRenderModeTable) 
			$result[] = '</table>';
		return implode('', $result);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderBegin () {
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);
		$result = ["<form"];
		$attrs = [];
		$form = $this->form;
		// standard attributes
		$formProperties = ['id', 'action', 'method', 'enctype', 'target', 'title'];
		foreach ($formProperties as $property) {
			$getter = 'Get'.ucfirst($property);
			$formPropertyValue = $form->$getter();
			if ($formPropertyValue !== NULL)
				$attrs[$property] = $formPropertyValue;
		}
		// css classes
		$formCssClasses = $form->GetCssClasses();
		if ($formCssClasses)
			$attrs['class'] = gettype($formCssClasses) == 'array'
				? implode(' ', (array) $formCssClasses)
				: $formCssClasses;
		// additional custom attributes completing
		foreach ($form->GetAttributes() as $key => $value) {
			if (!in_array($key, $formProperties))
				$attrs[$key] = $value;
		}
		// pseudo-boolean attributes completing
		$formAutoComplete = $form->GetAutoComplete();
		if ($formAutoComplete !== NULL)
			$attrs['autocomplete'] = $formAutoComplete ? 'on' : 'off';
		$formNoValidate = $form->GetNoValidate();
		if ($formNoValidate === TRUE)
			$attrs['novalidate'] = 'novalidate';
		$formAcceptCharsets = $form->GetAcceptCharsets();
		if (count($formAcceptCharsets) > 0)
			$attrs['accept-charset'] = implode(' ', $formAcceptCharsets);
		// boolean and additional attributes
		$attrsStr = self::RenderAttrs($attrs);
		if ($attrsStr) $result[] = ' ' . $attrsStr;
		$result[] = '>';
		$result[] = $this->RenderCsrf();
		return implode('', $result);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderCsrf () {
		if (!$this->csrfEnabled) return '';
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);
		$csrf = $this->form->GetCsrf();
		return '<input type="hidden" name="'.$csrf->name.'" value="'.$csrf->value.'" />';
	}

	/**
	 * @inheritDocs
	 * @return \stdClass
	 */
	public function GetCsrf () {
		return $this->form->GetCsrf();
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderErrors () {
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);
		$result = [];
		$errors = $this->form->GetErrors();
		if ($errors) {
			if ($this->form->GetErrorsRenderMode() !== \MvcCore\Ext\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
				$globalErrors = [];
				foreach ($errors as $errorData) 
					if (!(count($errorData) > 1 && is_array($errorData[1]) && count($errorData[1]) > 0))
						$globalErrors[] = $errorData;
				$errors = $globalErrors;
			}
			if ($errors) {
				$formRenderMode = $this->form->GetFormRenderMode();
				if ($formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE) {
					$result[] = '<div class="errors">';
					foreach ($errors as $errorMessageAndFieldNames) {
						list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
						$result[] = '<div class="error ' . implode(' ', $fieldNames) . '">'.$errorMessage.'</div>';
					}
					$result[] = '</div>';
				} else if ($formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_TABLE_STRUCTURE) {
					$result[] = '<thead class="errors">';
					foreach ($errors as $errorMessageAndFieldNames) {
						list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
						$result[] = '<tr><th colspan="2" class="error ' . implode(' ', $fieldNames) . '">'.$errorMessage.'</th></tr>';
					}
					$result[] = '</thead>';
				} else {
					foreach ($errors as $errorMessageAndFieldNames) {
						list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
						$result[] = '<span class="error ' . implode(' ', $fieldNames) . '">'.$errorMessage.'</span>';
					}
				}
			}
		}
		return implode('', $result);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContent () {
		/** @var $this \MvcCore\Ext\Forms\View */
		if ($this->form->GetDispatchState() < \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			$this->form->PreDispatch(FALSE);

		$formRenderMode = $this->form->GetFormRenderMode();
		if ($formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE) {
			$result = $this->RenderContentWithDivStructure();
		} else if ($formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_TABLE_STRUCTURE) {
			$result = $this->RenderContentWithTableStructure();
		} else {
			$result = $this->RenderContentWithoutStructure();
		}

		return $result;
	}

	/**
	 * @inheritDocs
	 * @return \array[] [\MvcCore\Ext\Forms\Fields\Hidden[], \MvcCore\Ext\Forms\Field[], \MvcCore\Ext\Forms\Fields\ISubmit[]]
	 */
	public function RenderContentGetFieldsGroups () {
		$allFields = $this->form->GetFields();
		/** @var $hiddenFields \MvcCore\Ext\Forms\Fields\Hidden[] */
		$hiddenFields = [];
		/** @var $controlFields \MvcCore\Ext\Forms\Field[] */
		$controlFields = [];
		/** @var $submitFields \MvcCore\Ext\Forms\Fields\ISubmit[] */
		$submitFields = [];
		foreach ($allFields as $fieldName => $field) {
			if ($field instanceof \MvcCore\Ext\Forms\Fields\Hidden) {
				$hiddenFields[$fieldName] = $field;
			} else if (isset($this->submitFields[$fieldName])) {
				$submitFields[$fieldName] = $field;
			} else {
				$controlFields[$fieldName] = $field;
			}
		}
		return [$hiddenFields, $controlFields, $submitFields];
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContentWithDivStructure () {
		/** @var $this \MvcCore\Ext\Forms\View */
		$result = [];
		list (
			$hiddenFields, $controlFields, $submitFields
		) = $this->RenderContentGetFieldsGroups();
		if ($hiddenFields) {
			$result[] = '<div class="hiddens">';
			foreach ($hiddenFields as $field) $result[] = $field->Render();
			$result[] = '</div>';
		}
		if ($controlFields) {
			$result[] = '<div class="controls">';
			foreach ($controlFields as $field) {
				$result[] = '<div>';
				$result[] = $field->Render();
				$result[] = '</div>';
			}
			$result[] = '</div>';
		}
		if ($submitFields) {
			$result[] = '<div class="submits">';
			foreach ($submitFields as $field) $result[] = $field->Render();
			$result[] = '</div>';
		}
		return implode('', $result);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContentWithTableStructure () {
		/** @var $this \MvcCore\Ext\Forms\View */
		$result = [];
		list (
			$hiddenFields, $controlFields, $submitFields
		) = $this->RenderContentGetFieldsGroups();
		$fieldRenderModeDefault = $this->form->GetFieldsRenderModeDefault();
		$fieldRenderModeDefaultNormal = $fieldRenderModeDefault === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL;
		if ($submitFields) {
			$result[] = '<tfoot class="submits"><tr>';
			$result[] = $fieldRenderModeDefaultNormal ? '<th class="empty"></td><td class="control">' : '<td colspan="2" class="control">';
			foreach ($submitFields as $field) $result[] = $field->Render();
			$result[] = '</td></tr></tfoot>';
		}
		if ($controlFields) {
			$result[] = '<tbody class="controls">';
			foreach ($controlFields as $field) {
				$fieldLabelSide = $field->GetLabelSide();
				$fieldRenderMode = NULL;
				if ($field instanceof \MvcCore\Ext\Forms\Fields\ILabel) 
					$fieldRenderMode = $field->GetRenderMode();
				if ($fieldRenderMode === NULL)
					$fieldRenderMode = $fieldRenderModeDefault;
				$fieldRenderModeNormal = $fieldRenderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL;
					
				if ($field instanceof \MvcCore\Ext\Forms\Fields\IChecked) {
					if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
						$rowBegin = '<td class="control label control-and-label">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td><td class="empty"></td>';
					} else {
						$rowBegin = '<td class="empty"></td><td class="control label control-and-label">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td>';
					}
				} else {
					if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
						$rowBegin = '<td class="label">';
						$labelAndControlSeparator = '</td><td class="control">';
						$rowEnd = '</td>';
					} else {
						$rowBegin = '<td class="control">';
						$labelAndControlSeparator = '</td><td class="label">';
						$rowEnd = '</td>';
					}
				}

				$result[] = '<tr>';
				if ($fieldRenderModeNormal) {
					$result[] = $rowBegin;
				} else if ($fieldRenderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
					$result[] = '<td colspan="2" class="control label control-inside-label">';
				} else {
					$result[] = '<td colspan="2" class="control no-label">';
				}
				$result[] = $field->Render($labelAndControlSeparator);
				if ($fieldRenderModeNormal) {
					$result[] = $rowEnd;
				} else {
					$result[] = '</td>';
				}
				$result[] = '</tr>';
			}
			$result[] = '</tbody>';
		}
		return implode('', $result);
	}
	
	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderContentWithoutStructure () {
		/** @var $this \MvcCore\Ext\Forms\View */
		$result = [];
		list (
			$hiddenFields, $controlFields, $submitFields
		) = $this->RenderContentGetFieldsGroups();
		if ($hiddenFields) 
			foreach ($hiddenFields as $field) 
				$result[] = $field->Render();
		if ($controlFields) 
			foreach ($controlFields as $field) 
				$result[] = $field->Render();
		if ($submitFields) 
			foreach ($submitFields as $field) 
			$result[] = $field->Render();
		return implode('', $result);
	}

	/**
	 * @inheritDocs
	 * @return string
	 */
	public function RenderEnd () {
		return $this->form->RenderEnd();
	}

	/**
	 * @inheritDocs
	 * @param string $str Template with replacements like `{0}`, `{1}`, `{anyStringKey}`...
	 * @param array $args Each value under it's index is replaced as
	 *                    string representation by replacement in form `{arrayKey}`
	 * @return string
	 */
	public static function Format ($str = '', array $args = []) {
		foreach ($args as $key => $value) {
			$pos = strpos($str, '{'.$key.'}');
			if ($pos !== FALSE)
				$str = substr($str, 0, $pos) . $value . substr($str, $pos + strlen($key) + 2);
		}
		return $str;
	}

	/**
	 * @inheritDocs
	 * @param  array $attributes
	 * @return string
	 */
	public static function RenderAttrs (array $attributes = []) {
		$result = [];
		foreach ($attributes as $attrName => $attrValue) {
			//if (gettype($attrValue) == 'array') $stop();
			$result[] = $attrName.'="'.$attrValue.'"';
		}
		return implode(' ', $result);
	}
}
