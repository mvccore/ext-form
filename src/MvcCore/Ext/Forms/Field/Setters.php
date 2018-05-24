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

namespace MvcCore\Ext\Forms\Field;

trait Setters
{
	/**
	 * Set field name, used to identify submitting value.
	 * @requires
	 * @param string $name
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetName ($name) {
		$this->Name = $name;
		return $this;
	}
	/**
	 * Set input type like: 'text', 'number', 'range'...
	 * @param string $type
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetLabelSide ($labelSide = 'right') {
		$this->LabelSide = $labelSide;
		return $this;
	}
	/**
	 * Set required boolean if field will be
	 * required to complete by user for submit.
	 * @param bool $required
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * Use \MvcCore\Ext\Form class constants:
	 * - \MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL
	 * - \MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND
	 * - \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL
	 * @param string $renderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetRenderMode ($renderMode = \MvcCore\Ext\Form\Core\Configuration::FIELD_RENDER_MODE_LABEL_AROUND) {
		$this->RenderMode = $renderMode;
		return $this;
	}
	/**
	 * Set control value, should be string or array, by field type implementation.
	 * @param string|array|mixed $value
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetDisabled ($disabled) {
		$this->Disabled = $disabled;
		return $this;
	}
	/**
	 * Set value to control html class attribute.
	 * More classes is necessary to set as strings separated by spaces.
	 * @param string $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function AddCssClass ($cssClass) {
		$this->CssClasses[] = $cssClass;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * Do not use system attributes as id, name, value, readonly, disabled, class...
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function AddControlAttr ($attr = array()) {
		$this->ControlAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetLabelAttrs ($attrs = array()) {
		$this->LabelAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function AddLabelAttr ($attr = array()) {
		$this->LabelAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set field validators collection, it shoud be validator class end-name in pascal
	 * case or closure function. All validators are located in /Form/Validators/...
	 * dir. So for validator class \MvcCore\Ext\Form\Validators\Numeric is necessary only to set
	 * array('Numeric'). Or any validator shoud be defined as simple closure function
	 * accepting arguments: $submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field
	 * and returnning safe value as result. This closure function shoud call
	 * $field->Form->AddError(); whenever is necessary and values is not correct.
	 * @param string[]|\Closure[] $validators
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetValidators ($validators = array()) {
		$this->Validators = $validators;
		return $this;
	}
	/**
	 * Add field validators, it shoud be validator class end-name in pascal
	 * case or closure function. All validators are located in /Form/Validators/...
	 * dir. So for validator class \MvcCore\Ext\Form\Validators\Numeric is necessary only to set
	 * array('Numeric'). Or any validator shoud be defined as simple closure function
	 * accepting arguments: $submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field
	 * and returnning safe value as result. This closure function shoud call
	 * $field->Form->AddError(); whenever is necessary and values is not correct.
	 * @param string|Closure,... $validators
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
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
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}
	/**
	 * Set supporting javascript full class name.
	 * @param string $jsClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetJsClass ($jsClass) {
		$this->JsClass = $jsClass;
		return $this;
	}
	/**
	 * Set supporting javascript file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->jsAssetsRootDir;
	 * @param string $jsFullFile
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetJs ($jsFullFile) {
		$this->Js = $jsFullFile;
		return $this;
	}
	/**
	 * Set supporting css file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->cssAssetsRootDir;
	 * @param string $cssFullFile
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function SetCss ($cssFullFile) {
		$this->Css = $cssFullFile;
		return $this;
	}
	/**
	 * Add field error message.
	 * This method is always called internaly from \MvcCore\Ext\Form
	 * in render preparing process. Do not use it.
	 * To add form error properly, use $field->Form->AddError();
	 * method isntead.
	 * @param string $errorText
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Core\Field
	 */
	public function AddError ($errorText) {
		$this->Errors[] = $errorText;
		return $this;
	}
}
