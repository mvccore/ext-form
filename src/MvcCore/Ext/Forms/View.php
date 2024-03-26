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
	 * Rendered fieldset reference if view renders fieldset content.
	 * @var \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	protected $fieldset = NULL;

	/**
	 * Rendered form field reference if view is not form's view.
	 * @var \MvcCore\Ext\Forms\Field|NULL
	 */
	protected $field = NULL;

	/**
	 * Form fields or fieldsets to render in current form/fieldset level.
	 * @var \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	protected $children = [];

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
	 * @inheritDoc
	 * @return string
	 */
	public static function GetFormsDir () {
		return static::$formsDir;
	}

	/**
	 * @inheritDoc
	 * @param  string $formsDir
	 * @return string
	 */
	public static function SetFormsDir ($formsDir = 'Forms') {
		return static::$formsDir = $formsDir;
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public static function GetFieldsDir () {
		return static::$fieldsDir;
	}

	/**
	 * @inheritDoc
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
		$this->__protected = array_merge($this->__protected, [
			'fieldRendering'	=> FALSE,
			'fieldsetRendering'	=> FALSE,
			'formRenderMode'	=> \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE,
		]);
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\View
	 */
	public function GetView () {
		return $this->view;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\View $view
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetView (\MvcCore\IView $view) {
		/** @var \MvcCore\View $view */
		$this->view = $view;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm () {
		return $this->form;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Form $form
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		/** @var \MvcCore\Ext\Form $form */
		$this->form = $form;
		$this->__protected['formRenderMode'] = $form->GetFormRenderMode();
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function GetField () {
		return $this->field;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field = NULL) {
		/** @var \MvcCore\Ext\Forms\Field $field */
		$this->field = $field;
		$this->__protected['fieldRendering'] = $field !== NULL;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function GetFieldset () {
		return $this->fieldset;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset = NULL) {
		/** @var \MvcCore\Ext\Forms\Fieldset $fieldset */
		$fieldsetIsNotNull = $fieldset !== NULL;
		$this->fieldset = $fieldset;
		$this->__protected['fieldsetRendering'] = $fieldsetIsNotNull;
		$this->__protected['formRenderMode'] = $fieldsetIsNotNull
			? $fieldset->GetFormRenderMode()
			: $this->form->GetFormRenderMode();
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren () {
		return $this->children;
	}

	/**
	 * @inheritDoc
	 * @param  \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[] $children
	 * @return \MvcCore\Ext\Forms\View
	 */
	public function SetChildren (array $children) {
		$this->children = $children;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $name
	 * @return mixed
	 */
	public function & __get ($name) {
		/** @var array $store */
		$store = & $this->__protected['store'];
		$phpWithTypes = PHP_VERSION_ID >= 70400;
		// if property is in view store - return it
		if (array_key_exists($name, $store)) 
			return $store[$name];
		/** @var \ReflectionProperty $property */
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
					if (!$property->isPublic()) $property->setAccessible(TRUE); // protected or private
					$value = NULL;
					if ($phpWithTypes && $property->hasType()) {
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
					if ($phpWithTypes && $property->hasType()) {
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
		if ($this->view instanceof \MvcCore\IView) {
			$value = $this->view->__get($name);
			$store[$name] = & $value;
			return $value;
		}
		// return NULL, if property is not in local store an even anywhere else
		$store[$name] = NULL;
		$null = NULL;
		return $null;
	}

	/**
	 * @inheritDoc
	 * @param  string $name
	 * @return bool
	 */
	public function __isset ($name) {
		/** @var array $store */
		$store = & $this->__protected['store'];
		$phpWithTypes = PHP_VERSION_ID >= 70400;
		// if property is in view store - return TRUE
		if (array_key_exists($name, $store)) return TRUE;
		/** @var \ReflectionProperty $property */
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
	 * @inheritDoc
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
	 * @inheritDoc
	 * @return string
	 */
	public function RenderTemplate () {
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
		$formViewScript = $this->form->GetViewScript();
		$this->SetUpStore($this->view, FALSE);
		return $this->Render(
			static::$formsDir,
			is_bool($formViewScript) ? $this->form->GetId() : $formViewScript
		);
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderNaturally () {
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
		return implode('', [
			$this->RenderBegin(),
			$this->RenderErrorsAndContent(),
			$this->RenderEnd()
		]);
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderBegin () {
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
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
				$attrs[$this->EscapeAttr($key)] = $this->EscapeAttr($value);
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
		$this->form->SetFormTagRenderingStatus(TRUE);
		$result[] = $this->RenderCsrf();
		return implode('', $result);
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @return string
	 */
	public function RenderCsrf () {
		if (!$this->csrfEnabled) return '';
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
		$csrf = $this->form->GetCsrf();
		return '<input type="hidden" name="'.$csrf->name.'" value="'.$csrf->value.'" />';
	}

	/**
	 * @inheritDoc
	 * @deprecated
	 * @throws \Exception
	 * @return \stdClass
	 */
	public function GetCsrf () {
		return $this->form->GetCsrf();
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderErrorsAndContent () {
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
		$result = [];
		$formRenderModeTable = $this->__protected['formRenderMode'] === \MvcCore\Ext\IForm::FORM_RENDER_MODE_TABLE_STRUCTURE;
		if ($formRenderModeTable) {
			foreach ($this->children as $child) 
				if ($child instanceof \MvcCore\Ext\Forms\Fields\Hidden) 
					$result[] = $child->Render();
			$result[] = '<table border="0" cellspacing="0" cellpadding="0">';
		}
		$result[] = $this->RenderErrors();
		$result[] = $this->RenderContent();
		if ($formRenderModeTable) 
			$result[] = '</table>';
		return implode('', $result);
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderErrors () {
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());
		$result = [];
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		$fieldsetRendering = $this->__protected['fieldsetRendering'];
		$errors = [];
		if ($fieldsetRendering) {
			// fieldset begin rendering:
			if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AT_FIELDSET_BEGIN) {
				$childrenNames = array_keys($this->children);
				$allErrors = $this->form->GetErrors();
				foreach ($allErrors as $errorData) {
					if (count($errorData) < 2) continue;
					$errorFieldNames = $errorData[1];
					if (count(array_intersect($childrenNames, $errorFieldNames)) > 0) 
						$errors[] = $errorData;
				}
			}
		} else {
			// form begin rendering:
			if ($formErrorsRenderMode === \MvcCore\Ext\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
				$errors = $this->form->GetErrors();	
			} else {
				$allErrors = $this->form->GetErrors();
				foreach ($allErrors as $errorData) 
					if (!(count($errorData) > 1 && is_array($errorData[1]) && count($errorData[1]) > 0))
						$errors[] = $errorData;
			}
		}
		if ($errors) {
			$formRenderMode = $this->__protected['formRenderMode'];
			if ($formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_TABLE_STRUCTURE) {
				$result[] = '<thead class="errors">';
				foreach ($errors as $errorMessageAndFieldNames) {
					list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
					$result[] = '<tr><th colspan="2" class="error ' . implode(' ', $fieldNames) . '"><div class="error">'.$errorMessage.'</div></th></tr>';
				}
				$result[] = '</thead>';
			} else {
				// $formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE ||
				// $formRenderMode === \MvcCore\Ext\IForm::FORM_RENDER_MODE_NO_STRUCTURE
				$result[] = '<div class="errors">';
				foreach ($errors as $errorMessageAndFieldNames) {
					list($errorMessage, $fieldNames) = $errorMessageAndFieldNames;
					$result[] = '<div class="error ' . implode(' ', $fieldNames) . '">'.$errorMessage.'</div>';
				}
				$result[] = '</div>';
			}
		}
		return implode('', $result);
	}
	
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderContent () {
		/** @var \MvcCore\Ext\Forms\View $this */
		$this->form->DispatchStateCheck(\MvcCore\Ext\Form::DISPATCH_STATE_RENDERED, $this->form->GetSubmit());

		$formRenderMode = $this->__protected['formRenderMode'];
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
	 * @inheritDoc
	 * @return \array[] [\MvcCore\Ext\Forms\Fields\Hidden[], \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[], \MvcCore\Ext\Forms\Fields\ISubmit[]|\MvcCore\Ext\Forms\Fields\IReset[]]
	 */
	public function RenderContentGetFieldsGroups () {
		/** @var $hiddenFields \MvcCore\Ext\Forms\Fields\Hidden[] */
		$hiddenFields = [];
		/** @var $contentFieldsOrFieldsets \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[] */
		$contentFieldsOrFieldsets = [];
		/** @var $submitFields \MvcCore\Ext\Forms\Fields\ISubmit[]|\MvcCore\Ext\Forms\Fields\IReset[] */
		$submitFields = [];
		foreach ($this->children as $fieldName => $fieldOrFieldset) {
			if ($fieldOrFieldset instanceof \MvcCore\Ext\Forms\Fields\Hidden) {
				$hiddenFields[$fieldName] = $fieldOrFieldset;
			} else if (isset($this->submitFields[$fieldName]) || $fieldOrFieldset instanceof \MvcCore\Ext\Forms\Fields\IReset) {
				$submitFields[$fieldName] = $fieldOrFieldset;
			} else {
				$contentFieldsOrFieldsets[$fieldName] = $fieldOrFieldset;
			}
		}
		return [$hiddenFields, $contentFieldsOrFieldsets, $submitFields];
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderContentWithDivStructure () {
		/** @var \MvcCore\Ext\Forms\View $this */
		$result = [];
		list (
			$hiddenFields, $contentFieldsOrFieldsets, $submitFields
		) = $this->RenderContentGetFieldsGroups();
		if ($hiddenFields) {
			$result[] = '<div class="hiddens">';
			foreach ($hiddenFields as $field) $result[] = $field->Render();
			$result[] = '</div>';
		}
		if ($contentFieldsOrFieldsets) {
			$result[] = '<div class="controls">';
			$toolClass = $this->form->GetApplication()->GetToolClass();
			foreach ($contentFieldsOrFieldsets as $fieldOrFieldsetName => $fieldOrFieldset) {
				$fieldOrFieldsetNameDashed = $toolClass::GetDashedFromPascalCase($fieldOrFieldsetName);
				$result[] = '<div class="'.$fieldOrFieldsetNameDashed.'">';
				$result[] = $fieldOrFieldset->Render();
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
	 * @inheritDoc
	 * @return string
	 */
	public function RenderContentWithTableStructure () {
		/** @var \MvcCore\Ext\Forms\View $this */
		$result = [];
		list (
			/*$hiddenFields*/, $contentFieldsOrFieldsets, $submitFields
		) = $this->RenderContentGetFieldsGroups();
		$fieldRenderModeDefault = $this->form->GetFieldsRenderModeDefault();
		$fieldRenderModeDefaultNormal = $fieldRenderModeDefault === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL;
		if ($submitFields) {
			$result[] = '<tfoot class="submits"><tr>';
			$result[] = $fieldRenderModeDefaultNormal ? '<th class="empty"></td><td class="control">' : '<td colspan="2" class="control">';
			foreach ($submitFields as $field) $result[] = $field->Render();
			$result[] = '</td></tr></tfoot>';
		}
		if ($contentFieldsOrFieldsets) {
			$result[] = '<tbody class="controls">';
			$toolClass = $this->form->GetApplication()->GetToolClass();
			foreach ($contentFieldsOrFieldsets as $fieldOrFieldsetName => $fieldOrFieldset) {
				$fieldLabelSide = $fieldOrFieldset instanceof \MvcCore\Ext\Forms\Fields\ILabel
					? $fieldOrFieldset->GetLabelSide()
					: NULL;
				$fieldRenderMode = NULL;
				if ($fieldOrFieldset instanceof \MvcCore\Ext\Forms\Fields\ILabel) 
					$fieldRenderMode = $fieldOrFieldset->GetRenderMode();
				if ($fieldRenderMode === NULL)
					$fieldRenderMode = $fieldRenderModeDefault;
				$fieldRenderModeNormal = $fieldRenderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL;
					
				if ($fieldOrFieldset instanceof \MvcCore\Ext\Forms\Fields\IChecked) {
					if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
						$rowBegin = '<td class="control label control-and-label">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td><td class="empty"></td>';
					} else if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT) {
						$rowBegin = '<td class="empty"></td><td class="control label control-and-label">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td>';
					} else {
						$rowBegin = '<td colspan="2" class="control">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td>';
					}
				} else {
					if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
						$rowBegin = '<td class="label">';
						$labelAndControlSeparator = '</td><td class="control">';
						$rowEnd = '</td>';
					} else if ($fieldLabelSide === \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT) {
						$rowBegin = '<td class="control">';
						$labelAndControlSeparator = '</td><td class="label">';
						$rowEnd = '</td>';
					} else {
						$rowBegin = '<td colspan="2" class="control">';
						$labelAndControlSeparator = '';
						$rowEnd = '</td>';
					}
				}

				$fieldOrFieldsetNameDashed = $toolClass::GetDashedFromPascalCase($fieldOrFieldsetName);
				$result[] = '<tr class="'.$fieldOrFieldsetNameDashed.'">';
				if ($fieldRenderModeNormal) {
					$result[] = $rowBegin;
				} else if ($fieldRenderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
					$result[] = '<td colspan="2" class="control label control-inside-label">';
				} else {
					$result[] = '<td colspan="2" class="control no-label">';
				}
				$result[] = $fieldOrFieldset->Render($labelAndControlSeparator);
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
	 * @inheritDoc
	 * @return string
	 */
	public function RenderContentWithoutStructure () {
		/** @var \MvcCore\Ext\Forms\View $this */
		$result = [];
		foreach ($this->children as $fieldName => $fieldOrFieldset) 
			$result[] = $fieldOrFieldset->Render();
		return implode('', $result);
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function RenderEnd () {
		return $this->form->RenderEnd();
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 * @param  array             $attributes
	 * @param  callable|\Closure $escapeFn
	 * @return string
	 */
	public static function RenderAttrs (array $attributes = [], $escapeFn = NULL) {
		$result = [];
		if ($escapeFn != NULL) {
			foreach ($attributes as $attrName => $attrValue)
				$result[] = call_user_func($escapeFn, $attrName).'="'.call_user_func($escapeFn, $attrValue).'"';
		} else {
			foreach ($attributes as $attrName => $attrValue)
				$result[] = $attrName.'="'.$attrValue.'"';
		}
		return implode(' ', $result);
	}
}
