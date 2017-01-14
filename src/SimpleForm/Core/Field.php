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

require_once('Exception.php');
require_once('View.php');

abstract class SimpleForm_Core_Field
{
	/**
	 * Form control html id
	 * @var string
	 */
	public $Id = '';
	/**
	 * Form control type, usually used in <input type=""> 
	 * attr value, but unique type accross all form field types.
	 * @var string
	 */
	public $Type = '';
	/**
	 * Form control specific name, server as key in submitting process
	 * where is sended it's used completed value.
	 * @requires
	 * @var string
	 */
	public $Name = '';
	/**
	 * Form control value.
	 * @var string
	 */
	public $Value = '';
	/**
	 * Form control label visible text, text only.
	 * @var string
	 */
	public $Label = '';
	/**
	 * Location where to render <label> element.
	 * 'left' by default.
	 * @var string
	 */
	public $LabelSide = 'left'; // right | left
	/**
	 * Form control attribute required, determinating 
	 * if controll will be required to complete by user.
	 * @var bool
	 */
	public $Required = null;
	/**
	 * Form control attribute readonly, determinating
	 * if controll will be readonly to not completed by user,
	 * and readonly by submitting process - so only session value will be used.
	 * @var bool
	 */
	public $Readonly = FALSE;
	/**
	 * Form control attribute disabled, determinating
	 * if controll will be disabled to not completed by user,
	 * and disabled by submitting process - so only session value will be used.
	 * @var bool
	 */
	public $Disabled = FALSE;
	/**
	 * Boolean telling if field will be translated or not.
	 * If nothing is configured as boolean, $field->Form->Translate values is used.
	 * If $field->Translate is TRUE, translated are placeholders, label texts and error messages.
	 * @var bool
	 */
	public $Translate = NULL;
	/**
	 * Control/label rendering mode, defined in form by defaut as: 'normal'.
	 * Normal means label will be rendered before control, only for checkbox 
	 * and radio buttons labels will be rendered after controls.
	 * Another possible values are 'no-label' and 'label-around'.
	 * Use SimpleForm::FIELD_RENDER_MODE_NORMAL, SimpleForm::FIELD_RENDER_MODE_NO_LABEL and
	 * SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND.
	 * @var string
	 */
	public $RenderMode = NULL;
	/**
	 * Html element css class string value, more classes separated by space.
	 * @var string
	 */
	public $CssClasses = array();
	/**
	 * Collection with html <input> element additional attributes by array keys/values.
	 * @var array
	 */
	public $ControlAttrs = array();
	/**
	 * Collection with html <label> element additional attributes by array keys/values.
	 * @var array
	 */
	public $LabelAttrs = array();
	/**
	 * List of validator classes end-names or list of closure functions
	 * accepting arguments: $submitValue, $fieldName, SimpleForm_Core_Field & $field
	 * and returning safe value as result. Closure function should call 
	 * $field->Form->AddError() internaly if necessary and submitted value is not correct.
	 * All validator classes are located in directory: SimpleForm/Validators/...
	 * For validator class SimpleForm_Validators_Numeric is necessary only tu set 'Numeric'.
	 * @var string[]|Closure[]
	 */
	public $Validators = array();
	/**
	 * Field instance errors for rendering process.
	 * @var string[]
	 */
	public $Errors = array();
	/**
	 * Field relative template path without .phtml extension, 
	 * empty string by default to render field naturaly.
	 * If there is configured any path, relative from directory /App/Views/Scripts,
	 * field is rendered by custom template.
	 * @var string
	 */
	public $TemplatePath = '';
	/**
	 * Form field view, object container with variables from local context to render in template.
	 * Created automaticly inside SimpleForm_Core_Field before field rendering process.
	 * @var SimpleForm_Core_View|mixed
	 */
	public $View = NULL;
	/**
	 * Supporting javascript full class name.
	 * @var string
	 */
	public $JsClass = '';
	/**
	 * Supporting javascript file relative path.
	 * Replacement '__SIMPLE_FORM_DIR__' is in rendering process 
	 * replaced by SimpleForm library root dir or by any other
	 * reconfigured value from $this->Form->jsAssetsRootDir;
	 * @var string
	 */
	public $Js = '';
	/**
	 * Supporting css file relative path.
	 * Replacement '__SIMPLE_FORM_DIR__' is in rendering process
	 * replaced by SimpleForm library root dir or by any other
	 * reconfigured value from $this->Form->cssAssetsRootDir;
	 * @var string
	 */
	public $Css = '';
	/**
	 * Form instance where current fields is placed.
	 * @var SimpleForm
	 */
	public $Form = NULL;
	/**
	 * Core rendering templates storrage.
	 * Those templates are used in form natural rendering process, form custom
	 * template rendering process, natural field rendering process but not
	 * by custom field rendering process.
	 * @var array
	 */
	protected static $templates = array(
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	);
	/**
	 * Local $this context properties which is not possible 
	 * to configure throught constructor config array.
	 * @var string[]
	 */
	protected static $declaredProtectedProperties = array(
		'Id', 'View', 'Form', 'Field',
	);


	/* setters and getters ********************************************************************/

	/**
	 * Set field name, used to identify submitting value.
	 * @requires
	 * @param string $name 
	 * @return SimpleForm_Core_Field
	 */
	public function SetName ($name) {
		$this->Name = $name;
		return $this;
	}
	/**
	 * Set input type like: 'text', 'number', 'range'...
	 * @param string $type
	 * @return SimpleForm_Core_Field
	 */
	public function SetType ($type) {
		$this->Type = $type;
		return $this;
	}
	/**
	 * Set control label visible text.
	 * Translation will be processed internaly inside 
	 * Simpleform before rendering process by $this->Form->Translator();
	 * @param string $label
	 * @return SimpleForm_Core_Field
	 */
	public function SetLabel ($label) {
		$this->Label = $label;
		return $this;
	}
	/**
	 * Set label side - location where label will be rendered.
	 * By default $this->LabelSide is configured to 'left'.
	 * If you want to reconfigure it to different side, 
	 * next possible value is 'right'.
	 * @param string $labelSide
	 * @return SimpleForm_Core_Field
	 */
	public function SetLabelSide ($labelSide = 'right') {
		$this->LabelSide = $labelSide;
		return $this;
	}
	/**
	 * Set required boolean if field will be 
	 * required to complete by user for submit.
	 * @param bool $required 
	 * @return SimpleForm_Core_Field
	 */
	public function SetRequired ($required = TRUE) {
		$this->Required = $required;
		return $this;
	}
	/**
	 * Set read only boolean if field will be
	 * read only, not possible to complete by user for submit,
	 * result value will be used from session.
	 * @param bool $readonly 
	 * @return SimpleForm_Core_Field
	 */
	public function SetReadonly ($readonly = TRUE) {
		$this->Readonly = $readonly;
		return $this;
	}
	/**
	 * Set field render mode to render label normaly before control
	 * by value 'normal', which controls have mostly configured by default 
	 * or to render label around the control by value 'label-around' or
	 * to not render any label by value 'no-label'.
	 * Use SimpleForm class constants:
	 * - SimpleForm::FIELD_RENDER_MODE_NORMAL
	 * - SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND
	 * - SimpleForm::FIELD_RENDER_MODE_NO_LABEL
	 * @param string $renderMode 
	 * @return SimpleForm_Core_Field
	 */
	public function SetRenderMode ($renderMode = SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND) {
		$this->RenderMode = $renderMode;
		return $this;
	}
	/**
	 * Set control value, should be string or array, by field type implementation.
	 * @param string|array $value 
	 * @return SimpleForm_Core_Field
	 */
	public function SetValue ($value) {
		$this->Value = $value;
		return $this;
	}
	/**
	 * Get control value, should be string or array, by field type implementation.
	 * @return string|array
	 */
	public function GetValue () {
		return $this->Value;
	}
	/**
	 * Set translate to TRUE if you want to translate this field.
	 * It is necessary to set up any $form->Translator callable to
	 * translate cotnrol placeholder, label and error messages.
	 * @param bool $translate 
	 * @return SimpleForm_Core_Field
	 */
	public function SetTranslate ($translate = TRUE) {
		$this->Translate = $translate;
		return $this;
	}
	/**
	 * Set disabled boolean if field will be
	 * disabled for user, not possible to complete 
	 * by user for submit and disabled in submitting process,
	 * result value will be used from session.
	 * @param bool $readonly
	 * @return SimpleForm_Core_Field
	 */
	public function SetDisabled ($disabled) {
		$this->Disabled = $disabled;
		return $this;
	}
	/**
	 * Set value to control html class attribute.
	 * More classes is necessary to set as strings separated by spaces.
	 * @param string $cssClasses 
	 * @return SimpleForm_Core_Field
	 */
	public function SetCssClasses ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$this->CssClasses = $cssClasses;
		} else {
			$this->CssClasses = explode(' ', (string)$cssClasses);
		}
		return $this;
	}
	/**
	 * Add value to html class attribute.
	 * More classes is necessary to add as strings separated by spaces.
	 * @param string $cssClasses
	 * @return SimpleForm_Core_Field
	 */
	public function AddCssClass ($cssClass) {
		$this->CssClasses[] = $cssClass;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * Do not use system attributes as id, name, value, readonly, disabled, class...
	 * @param array $attrs 
	 * @return SimpleForm_Core_Field
	 */
	public function SetControlAttrs ($attrs = array()) {
		$this->ControlAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional control html attributes by key/value array.
	 * Do not use system attributes as id, name, value, readonly, disabled, class...
	 * Use specific setter for them.
	 * @param array $attrs
	 * @return SimpleForm_Core_Field
	 */
	public function AddControlAttr ($attr = array()) {
		$this->ControlAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return SimpleForm_Core_Field
	 */
	public function SetLabelAttrs ($attrs = array()) {
		$this->LabelAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return SimpleForm_Core_Field
	 */
	public function AddLabelAttr ($attr = array()) {
		$this->LabelAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set field validators collection, it shoud be validator class end-name in pascal
	 * case or closure function. All validators are located in SimpleForm/Validators/... 
	 * dir. So for validator class SimpleForm_Validators_Numeric is necessary only to set
	 * array('Numeric'). Or any validator shoud be defined as simple closure function
	 * accepting arguments: $submitValue, $fieldName, SimpleForm_Core_Field & $field
	 * and returnning safe value as result. This closure function shoud call 
	 * $field->Form->AddError(); whenever is necessary and values is not correct.
	 * @param string[]|Closure[] $validators 
	 * @return SimpleForm_Core_Field
	 */
	public function SetValidators ($validators = array()) {
		$this->Validators = $validators;
		return $this;
	}
	/**
	 * Add field validators, it shoud be validator class end-name in pascal
	 * case or closure function. All validators are located in SimpleForm/Validators/... 
	 * dir. So for validator class SimpleForm_Validators_Numeric is necessary only to set
	 * array('Numeric'). Or any validator shoud be defined as simple closure function
	 * accepting arguments: $submitValue, $fieldName, SimpleForm_Core_Field & $field
	 * and returnning safe value as result. This closure function shoud call
	 * $field->Form->AddError(); whenever is necessary and values is not correct.
	 * @param string|Closure,... $validators
	 * @return SimpleForm_Core_Field
	 */
	public function AddValidators () {
		$args = func_get_args();
		foreach ($args as $arg) $this->Validators[] = $arg;
		return $this;
	}
	/**
	 * Set template relative path without .phtml extension,
	 * if you want to render field by custom template.
	 * Empty string by default to render field naturaly.
	 * If there is configured any path, relative from directory /App/Views/Scripts,
	 * field is rendered by custom template.
	 * @param string $templatePath 
	 * @return SimpleForm_Core_Field
	 */
	public function SetTemplatePath ($templatePath = '') {
		$this->TemplatePath = $templatePath;
		return $this;
	}
	/**
	 * Set supporting javascript full class name.
	 * @param string $jsClass 
	 * @return SimpleForm_Core_Field
	 */
	public function SetJsClass ($jsClass) {
		$this->JsClass = $jsClass;
		return $this;
	}
	/**
	 * Set supporting javascript file relative path.
	 * Replacement '__SIMPLE_FORM_DIR__' is in rendering process
	 * replaced by SimpleForm library root dir or by any other
	 * reconfigured value from $this->Form->jsAssetsRootDir;
	 * @param string $jsFullFile
	 * @return SimpleForm_Core_Field
	 */
	public function SetJs ($jsFullFile) {
		$this->Js = $jsFullFile;
		return $this;
	}
	/**
	 * Set supporting css file relative path.
	 * Replacement '__SIMPLE_FORM_DIR__' is in rendering process
	 * replaced by SimpleForm library root dir or by any other
	 * reconfigured value from $this->Form->cssAssetsRootDir;
	 * @param string $cssFullFile
	 * @return SimpleForm_Core_Field
	 */
	public function SetCss ($cssFullFile) {
		$this->Css = $cssFullFile;
		return $this;
	}
	/**
	 * Add field error message.
	 * This method is always called internaly from SimpleForm
	 * in render preparing process. Do not use it. 
	 * To add form error properly, use $field->Form->AddError(); 
	 * method isntead.
	 * @param string $errorText 
	 * @return SimpleForm_Core_Field
	 */
	public function AddError ($errorText) {
		$this->Errors[] = $errorText;
		return $this;
	}


	/* core methods **************************************************************************/

    /**
     * Create new form control instance.
     * @param array $cfg config array with camel case 
	 *					 public properties and its values which you want to configure.
     * @throws SimpleForm_Core_Exception 
     */
    public function __construct ($cfg = array()) {
		static::$templates = (object) static::$templates;
		foreach ($cfg as $key => $value) {
			$propertyName = ucfirst($key);
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				$clsName = get_class($this);
				throw new SimpleForm_Core_Exception(
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
	 * This method  is called internaly from SimpleForm after field
	 * is added into form by $form->AddField(); method. Do not use it 
	 * if you are only user of this library.
	 * - check if field has any name, which is required
	 * - set up form and field id attribute by form id and field name
	 * - set up required
	 * @param SimpleForm $form 
	 * @throws SimpleForm_Core_Exception 
	 * @return void
	 */
	public function OnAdded (SimpleForm & $form) {
		if (!$this->Name) {
			$clsName = get_class($this);
			throw new SimpleForm_Core_Exception("No 'Name' defined for form field: '$clsName'.");
		}
		$this->Form = $form;
		$this->Id = implode(SimpleForm::HTML_IDS_DELIMITER, array(
			$form->Id,
			$this->Name
		));
		// if there is no specific required boolean - set required boolean by form
		$this->Required = is_null($this->Required) ? $form->Required : $this->Required ;
	}
	/**
	 * Set up field properties before rendering process.
	 * - set up field render mode
	 * - set up translation boolean
	 * - translate label if any
	 * @return void
	 */
	public function SetUp () {
		$form = $this->Form;
		$translator = $form->Translator;
		// if there is no specific render mode - set render mode by form
		if (is_null($this->RenderMode)) {
			$this->RenderMode = $form->FieldsDefaultRenderMode;
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


	/* rendering ******************************************************************************/

	/**
	 * Render field in full mode, naturaly or by custom template.
	 * @return string
	 */
	public function Render () {
		if ($this->TemplatePath) {
			return $this->RenderTemplate();
		} else {
			return $this->RenderNaturally();
		}
	}
	/**
	 * Render field by configured template.
	 * This method creates $view = new SimpleForm_Core_View
	 * sets all local context variables into it and renders it into string.
	 * @return string
	 */
	public function RenderTemplate () {
		$view = new SimpleForm_Core_View($this->Form);
		$this->Field = $this;
		$view->SetUp($this);
		return $view->Render($this->Form->TemplateTypePath, $this->TemplatePath);
	}
	/**
	 * Render field naturaly by render mode.
	 * Field shoud be rendered with label beside, label around 
	 * or without label by local field configuration. Also there 
	 * could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally () {
		$result = '';
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NORMAL && $this->Label) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_LABEL_AROUND && $this->Label) {
			$result = $this->RenderControlInsideLabel();
		} else if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL || !$this->Label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			if ($this->Form->ErrorsRenderMode !== SimpleForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($this->Form->ErrorsRenderMode !== SimpleForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}
	/**
	 * Render field control and label by local configuration in left or in right side,
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderLabelAndControl () {
		$result = "";
		if ($this->LabelSide == 'left') {
			$result = $this->RenderLabel() . $this->RenderControl();
		} else {
			$result = $this->RenderControl() . $this->RenderLabel();
		}
		$errors = $this->RenderErrors();
		if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}
	/**
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL) return $this->RenderControl();
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		$template = $this->LabelSide == 'left' ? static::$templates->togetherLabelLeft : static::$templates->togetherLabelRight;
		$result = $this->Form->View->Format($template, array(
			'id'		=> $this->Id, 
			'label'		=> $this->Label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
		$errors = $this->RenderErrors();
		if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($this->Form->ErrorsRenderMode == SimpleForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}
	/**
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		return $this->Form->View->Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
	/**
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->RenderMode == SimpleForm::FIELD_RENDER_MODE_NO_LABEL) return '';
		$attrsStr = $this->renderLabelAttrsWithFieldVars();
		return $this->Form->View->Format(static::$templates->label, array(
			'id'		=> $this->Id, 
			'label'		=> $this->Label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
	/**
	 * Render field specific errors only without control or label.
	 * @return string
	 */
	public function RenderErrors () {
		$result = "";
		if ($this->Errors && $this->Form->ErrorsRenderMode !== SimpleForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
			$result .= '<span class="errors">';
			foreach ($this->Errors as $key => $errorMessage) {
				$errorCssClass = 'error';
				if (isset($this->Fields[$key])) $errorCssClass .= " $key";
				$result .= "<span class=\"$errorCssClass\">$errorMessage</span>";
			}
			$result .= '</span>';
		}
		return $result;
	}


	/* protected renderers *******************************************************************/

	/**
	 * Complete HTML attributes and css classes strings for label element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is 
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also 
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * @param string[] $fieldVars 
	 * @return string
	 */
	protected function renderLabelAttrsWithFieldVars ($fieldVars = array()) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->LabelAttrs, $this->CssClasses
		);
	}
	/**
	 * Complete HTML attributes and css classes strings for control element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * @param string[] $fieldVars
	 * @return string
	 */
	protected function renderControlAttrsWithFieldVars ($fieldVars = array()) {
		return $this->renderAttrsWithFieldVars(
			$fieldVars, $this->ControlAttrs, $this->CssClasses, TRUE
		);
	}
	/**
	 * Complete HTML attributes and css classes strings for label/control element
	 * by selected field variables from $this field context
	 * only if called $fieldVars item in $this field context is
	 * something different then NULL value.
	 * Automaticly render into attributes and css classes also
	 * system field properties: 'Disabled', 'Readonly' and 'Required'
	 * in boolean mode. All named field context properties translate
	 * into attributes names and css classes strings from PascalCase into
	 * dashed-case.
	 * Only if fourth param is false, do not add system attributes in boolean 
	 * mode into attributes, only into css class.
	 * @param string[] $fieldVars 
	 * @param array $fieldAttrs 
	 * @param string $cssClasses 
	 * @param bool $controlRendering 
	 * @return string
	 */
	protected function renderAttrsWithFieldVars (
		$fieldVars = array(), $fieldAttrs = array(), $cssClasses = array(), $controlRendering = FALSE
	) {
		$attrs = array();
		foreach ($fieldVars as $fieldVar) {
			if (!is_null($this->$fieldVar)) {
				$attrName = MvcCore_Tool::GetDashedFromPascalCase($fieldVar);
				$attrs[$attrName] = $this->$fieldVar;
			}
		}
		$boolFieldVars = array('Disabled', 'Readonly', 'Required');
		foreach ($boolFieldVars as $fieldVar) {
			if ($this->$fieldVar) {
				$attrName = lcfirst($fieldVar);
				if ($controlRendering) $attrs[$attrName] = $attrName;
				$cssClasses[] = $attrName;
			}
		}
		$cssClasses[] = MvcCore_Tool::GetDashedFromPascalCase($this->Name);
		$attrs['class'] = implode(' ', $cssClasses);
		return SimpleForm_Core_View::RenderAttrs(
			array_merge($fieldAttrs, $attrs)
		);
	}
}
