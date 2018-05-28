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
	 * Set field id, completed from form name and field name.
	 * @param string $id
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetId ($id = NULL) {
		$this->id = $id;
		return $this;
	}
	/**
	 * Set field name, used to identify submitting value.
	 * @requires
	 * @param string $name
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetName ($name = NULL) {
		$this->name = $name;
		return $this;
	}
	/**
	 * Set input type like: 'text', 'number', 'range'...
	 * @param string $type
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetType ($type = NULL) {
		$this->type = $type;
		return $this;
	}
	/**
	 * Set control label visible text.
	 * Translation will be processed internaly inside
	 * Simpleform before rendering process by $this->Form->Translator();
	 * @param string $label
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabel ($label = NULL) {
		$this->label = $label;
		return $this;
	}
	/**
	 * Set label side - location where label will be rendered.
	 * By default $this->LabelSide is configured to 'left'.
	 * If you want to reconfigure it to different side,
	 * next possible value is 'right'.
	 * @param string $labelSide
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabelSide ($labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT) {
		$this->labelSide = $labelSide;
		return $this;
	}
	/**
	 * Set required boolean if field will be
	 * required to complete by user for submit.
	 * @param bool $required
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRequired ($required = TRUE) {
		$this->required = $required;
		return $this;
	}
	/**
	 * Set read only boolean if field will be
	 * read only, not possible to complete by user for submit,
	 * result value will be used from session.
	 * @param bool $readonly
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetReadOnly ($readOnly = TRUE) {
		$this->readOnly = $readOnly;
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRenderMode ($renderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
		$this->renderMode = $renderMode;
		return $this;
	}
	/**
	 * Set control value, should be string or array, by field type implementation.
	 * @param string|array|mixed $value
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value) {
		$this->value = $value;
		return $this;
	}
	/**
	 * Set disabled boolean if field will be
	 * disabled for user, not possible to complete
	 * by user for submit and disabled in submitting process,
	 * result value will be used from session.
	 * @param bool $readonly
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetDisabled ($disabled) {
		$this->disabled = $disabled;
		return $this;
	}
	/**
	 * Set value to control html class attribute.
	 * More classes is necessary to set as strings separated by spaces.
	 * @param string $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCssClasses ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$this->cssClasses = $cssClasses;
		} else {
			$this->cssClasses = explode(' ', (string)$cssClasses);
		}
		return $this;
	}
	/**
	 * Add value to html class attribute.
	 * More classes is necessary to add as strings separated by spaces.
	 * @param string $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddCssClass ($cssClass) {
		$this->cssClasses[] = $cssClass;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * Do not use system attributes as id, name, value, readonly, disabled, class...
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetControlAttrs ($attrs = array()) {
		$this->controlAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional control html attributes by key/value array.
	 * Do not use system attributes as id, name, value, readonly, disabled, class...
	 * Use specific setter for them.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddControlAttr ($attr = array()) {
		$this->controlAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabelAttrs ($attrs = array()) {
		$this->labelAttrs = $attrs;
		return $this;
	}
	/**
	 * Add any additional control html attributes by key/value array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddLabelAttr ($attr = array()) {
		$this->labelAttrs[] = $attr;
		return $this;
	}
	/**
	 * Set list of validator classes ending names.
	 * Validator class must exist in default validators namespace:
	 * - `\MvcCore\Ext\Forms\Validators\`
	 * of it must exist in another configured validators namespaces by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every validator class has t implement interface `\MvcCore\Ext\Forms\IValidator`
	 * or it could be extended from base abstract validator class `\MvcCore\Ext\Forms\Validator`.
	 * @param \string[] $validators
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValidators ($validators = array()) {
		$this->validators = & $validators;
		return $this;
	}
	/**
	 * Add list of validator classes ending names.
	 * Validator class must exist in default validators namespace:
	 * - `\MvcCore\Ext\Forms\Validators\`
	 * of it must exist in another configured validators namespaces by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every validator class has t implement interface `\MvcCore\Ext\Forms\IValidator`
	 * or it could be extended from base abstract validator class `\MvcCore\Ext\Forms\Validator`.
	 * @param \string[] $validators,...
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddValidators (/* ...$validators */) {
		$this->validators = array_unique(array_merge($this->validators, func_get_args()));
		return $this;
	}
	/**
	 * Set template relative path without .phtml extension,
	 * if you want to render field by custom template.
	 * Empty string by default to render field naturaly.
	 * If there is configured any path, relative from directory /App/Views/Scripts,
	 * field is rendered by custom template.
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}
	/**
	 * Set supporting javascript file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->jsAssetsRootDir;
	 * @param string $jsFullFile
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetJsSupportingFile ($jsSupportingFilePath) {
		$this->jsSupportingFile = $jsSupportingFilePath;
		return $this;
	}
	/**
	 * Set supporting javascript full class name.
	 * @param string $jsClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetJsClassName ($jsClassName) {
		$this->jsClassName = $jsClassName;
		return $this;
	}
	/**
	 * Set supporting css file relative path.
	 * Replacement '__MVCCORE_FORM_DIR__' is in rendering process
	 * replaced by \MvcCore\Ext\Form library root dir or by any other
	 * reconfigured value from $this->Form->cssAssetsRootDir;
	 * @param string $cssFullFile
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCssSupportingFile ($cssSupportingFilePath) {
		$this->cssSupportingFile = $cssSupportingFilePath;
		return $this;
	}
	/**
	 * Add field error message.
	 * This method is always called internaly from \MvcCore\Ext\Form
	 * in render preparing process. Do not use it.
	 * To add form error properly, use $field->Form->AddError();
	 * method isntead.
	 * @param string $errorText
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddError ($errorText) {
		$this->errors[] = $errorText;
		return $this;
	}
}
