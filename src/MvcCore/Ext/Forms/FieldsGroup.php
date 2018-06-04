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

abstract class FieldsGroup extends Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\Options;
	use \MvcCore\Ext\Forms\Field\Attrs\GroupCssClasses;
	use \MvcCore\Ext\Forms\Field\Attrs\GroupLabelAttrs;
	
	/**
	 * Form group pseudo control type,
	 * unique type accross all form field types.
	 * @var string|NULL
	 */
	protected $type = NULL;

	/**
	 * Form control value,
	 * always as array of string or
	 * numbers for group of controls.
	 * @var array
	 */
	protected $value = [];

	/**
	 * Internal common templates how to render field group elements naturaly.
	 * @var array|\stdClass
	 */
	protected static $templates = [
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{checked}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];

	/* core methods **************************************************************************/

	/*
	// use this constructor in extended class to merge control or label automatic templates
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	*/

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form by `$form->AddField();` method. 
	 * Do not use it if you don't know what to do.
	 * Method does:
	 * - Check if there are any options for current controls group.
	 * Parent method does:
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->options) $this->throwNewInvalidArgumentException(
			'No `options` property defined.'
		);
		return $this;
	}

	/**
	 * Set up field properties before rendering process.
	 * - Translate all option texts
	 * Parent method:
	 * - Set up field render mode.
	 * - Set up translation boolean.
	 * - Translate label property if any.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		if (!$this->translate) return;
		$form = & $this->form;
		foreach ($this->options as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				if ($value) 
					$this->options[$key] = $form->Translate((string) $value);
			} else if ($valueType == 'array') {
				// advanced configuration with key, text, css class, and any other attributes for single option tag
				$text = isset($value['text']) 
					? $value['text']
					: $key;
				if ($text)
					$value['text'] = $form->Translate((string) $text);
			}
		}
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
		if (
			$this->label && (
				$this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL ||
				$this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_LABEL_AROUND
			)
		) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL || !$this->label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
			if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}

	/**
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return $this->RenderControl();
		$attrsStr = $this->renderAttrsWithFieldVars(
			[], $this->groupLabelAttrs, $this->groupCssClasses, TRUE
		);
		$template = $this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT 
			? static::$templates->togetherLabelLeft 
			: static::$templates->togetherLabelRight;
		$viewClass = $this->form->GetViewClass();
		$result = $viewClass::Format($template, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode == \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * Render all subcontrols by multiple calls of $field->RenderControlItem();
	 * @return string
	 */
	public function RenderControl () {
		$result = '';
		foreach ($this->options as $key => $value) {
			$result .= $this->RenderControlItem($key, $value);
		}
		return $result;
	}

	/**
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->renderMode == \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NO_LABEL) 
			return '';
		$attrsStr = $this->renderAttrsWithFieldVars(
			[], $this->groupLabelAttrs, $this->groupCssClasses
		);
		$viewClass = $this->form->GetViewClass();
		return $viewClass::Format(static::$templates->label, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}

	/**
	 * Render subcontrols with each subcontrol label tag
	 * and without group label or without group specific errors.
	 * @param string $key
	 * @param string|array $option
	 * @return string
	 */
	public function RenderControlItem ($key, & $option) {
		$result = '';
		$itemControlId = implode(\MvcCore\Ext\Forms\IForm::HTML_IDS_DELIMITER, [
			$this->form->GetId(), $this->name, $key
		]);
		list(
			$itemLabelText,
			$labelAttrsStr,
			$controlAttrsStr
		) = $this->renderControlItemCompleteAttrsClassesAndText($key, $option);
		// render control, render label and put it together if necessary
		$checked = gettype($this->value) == 'array'
			? in_array($key, $this->value)
			: $this->value === $key;
		$viewClass = $this->form->GetViewClass();
		$itemControl = $viewClass::Format(static::$templates->control, [
			'id'		=> $itemControlId,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $key,
			'checked'	=> $checked ? ' checked="checked"' : '',
			'attrs'		=> $controlAttrsStr ? " $controlAttrsStr" : '',
		]);
		if ($this->renderMode == \MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL) {
			// control and label
			$itemLabel = $viewClass::Format(static::$templates->label, [
				'id'		=> $itemControlId,
				'label'		=> $itemLabelText,
				'attrs'		=> $labelAttrsStr ? " $labelAttrsStr" : '',
			]);
			$result = ($this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) 
				? $itemControl . $itemLabel 
				: $itemLabel . $itemControl;
		} else if ($this->renderMode == \MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND) {
			// control inside label
			$templatesKey = 'togetherLabel' . (
				($this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) 
					? 'Right' 
					: 'Left'
			);
			$result = $viewClass::Format(
				static::$templates->$templatesKey,
				[
					'id'		=> $itemControlId,
					'label'		=> $itemLabelText,
					'control'	=> $itemControl,
					'attrs'		=> $labelAttrsStr ? " $labelAttrsStr" : '',
				]
			);
		}
		return $result;
	}

	/**
	 * Complete and return semifinished strings for rendering by field key and option:
	 * - Label text string.
	 * - Label attributes string string.
	 * - Control attributes string.
	 * @param string	   $key
	 * @param string|array $option
	 * @return array
	 */
	protected function renderControlItemCompleteAttrsClassesAndText ($key, & $option) {
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->required;
		if ($this->type == 'checkbox') 
			$this->required = FALSE;
		if ($optionType == 'string') {
			$itemLabelText = $option ? $option : $key;
			$labelAttrsStr = $this->renderLabelAttrsWithFieldVars();
			$controlAttrsStr = $this->renderControlAttrsWithFieldVars();
		} else if ($optionType == 'array') {
			$itemLabelText = $option['text'] ? $option['text'] : $key;
			$attrsArr = $this->controlAttrs;
			$classArr = $this->cssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->controlAttrs, $option['attrs']);
			}
			if (isset($option['class'])) {
				$classArrParam = [];
				if (gettype($option['class']) == 'array') {
					$classArrParam = $option['class'];
				} else if (gettype($option['class']) == 'string') {
					$classArrParam = explode(' ', $option['class']);
				}
				foreach ($classArrParam as $clsValue) if ($clsValue) $classArr[] = $clsValue;
			}
			$labelAttrsStr = $this->renderAttrsWithFieldVars(
				[], $attrsArr, $classArr
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				[], $attrsArr, $classArr, TRUE
			);
		}
		if ($this->type == 'checkbox') 
			$this->required = $originalRequired;
		return [
			$itemLabelText, 
			$labelAttrsStr, 
			$controlAttrsStr
		];
	}
}
