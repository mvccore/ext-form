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
 * Responsibility: init, pre-dispatch and render group of common form controls,
 *				   mostly `input` controls. This class is not possible to
 *				   instantiate, you need to extend this class to create own
 *				   specific form control.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class	FieldsGroup
extends			\MvcCore\Ext\Forms\Field
implements		\MvcCore\Ext\Forms\Fields\IVisibleField,
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IOptions,
				\MvcCore\Ext\Forms\Fields\IMultiple,
				\MvcCore\Ext\Forms\IFieldsGroup {

	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\Options;
	use \MvcCore\Ext\Forms\Field\Props\GroupLabelCssClasses;
	use \MvcCore\Ext\Forms\Field\Props\GroupLabelAttrs;

	/**
	 * Form group pseudo control type,
	 * unique type across all form field types.
	 * @var string|NULL
	 */
	protected $type = NULL;

	/**
	 * Form group controls value, in most cases it's an array of strings.
	 * For extended class `RadioGroup` - the type is only a `string` or `NULL`.
	 * @var \string[]|NULL
	 */
	protected $value = [];

	/**
	 * Standard field template strings for natural
	 * rendering - `label`, `control`, `togetherLabelLeft` and `togetherLabelRight`.
	 * @var string
	 */
	protected static $templates = [
		'label'				=> '<label for="{id}"{attrs}>{label}</label>',
		'control'			=> '<input id="{id}" name="{name}" type="{type}" value="{value}"{checked}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];

	/**
	 * Create new form control group instance.
	 * @param array $cfg Config array with public properties and it's
	 *					 values which you want to configure, presented
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public static function CreateInstance ($cfg = []) {
		return new static($cfg);
	}

	/**
	 * Get form group controls value, in most cases it's an array of strings.
	 * For extended class `RadioGroup` - the type is only a `string` or `NULL`.
	 * @return \string[]|string|NULL
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * Set form group controls value, in most cases - it could be an array with types,
	 * which are possible simply to convert into array of strings. `NULL` value is then
	 * an empty array. For extended class `RadioGroup` - the value type is only a `string`
	 * or `NULL`.
	 * @param \float[]|\int[]|\string[]|float|int|string|NULL $value
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetValue ($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Field group is always marked as multiple value control. This function
	 * always return `TRUE` for field group instance.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return bool
	 */
	public function GetMultiple () {
		return TRUE;
	}

	/**
	 * Field group is always marked as multiple value control. This function
	 * does nothing, because multiple option has to be `TRUE` for field group instance all time.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetMultiple ($multiple = TRUE) {
		return $this;
	}

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
	 * Do not use this method even if you don't develop any form field group.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Check if there are any options for current controls group.
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		parent::SetForm($form);
		if (!$this->options) $this->throwNewInvalidArgumentException(
			'No `options` property defined.'
		);
		return $this;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * Set up field properties before rendering process.
	 * - Set up field render mode.
	 * - Set up translation boolean.
	 * - Translate label property if any.
	 * - Translate all option texts if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
		if (!$this->translate) return;
		$form = $this->form;
		foreach ($this->options as $key => & $value) {
			if (is_string($value)) {
				// most simple key/value array options configuration
				if ($value)
					$this->options[$key] = $form->Translate((string) $value);
			} else if (is_array($value)) {
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
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering`
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 *
	 * Render field naturally by render mode.
	 * Field should be rendered with label beside, label around
	 * or without label by local field configuration. Also there
	 * could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally () {
		$result = '';
		if (
			$this->label && (
				$this->renderMode == \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL ||
				$this->renderMode == \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND
			)
		) {
			$result = $this->RenderLabelAndControl();
		} else if ($this->renderMode == \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL || !$this->label) {
			$result = $this->RenderControl();
			$errors = $this->RenderErrors();
			$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
			if ($formErrorsRenderMode == \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
				$result = $errors . $result;
			} else if ($formErrorsRenderMode == \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
				$result .= $errors;
			}
		}
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup`
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 *
	 * Render field naturally by configured property `$field->renderMode` if any
	 * or by default render mode without any label. Field should be rendered with
	 * label beside, label around or without label by local field configuration.
	 * Also there could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderControlInsideLabel () {
		if ($this->renderMode == \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL)
			return $this->RenderControl();
		$attrsStr = $this->renderAttrsWithFieldVars(
			['multiple'], $this->groupLabelAttrs, $this->groupLabelCssClasses, FALSE
		);
		/** @var $templates \stdClass */
		$templates = & static::$templates;
		$template = $this->labelSide == \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT
			? $templates->togetherLabelLeft
			: $templates->togetherLabelRight;
		$viewClass = $this->form->GetViewClass();
		$result = $viewClass::Format($template, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'control'	=> $this->RenderControl(),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
		$errors = $this->RenderErrors();
		$formErrorsRenderMode = $this->form->GetErrorsRenderMode();
		if ($formErrorsRenderMode == \MvcCore\Ext\IForm::ERROR_RENDER_MODE_BEFORE_EACH_CONTROL) {
			$result = $errors . $result;
		} else if ($formErrorsRenderMode == \MvcCore\Ext\IForm::ERROR_RENDER_MODE_AFTER_EACH_CONTROL) {
			$result .= $errors;
		}
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup`
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 *
	 * Render all sub-controls by multiple calls of `$field->RenderControlItem();`.
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
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup`
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 *
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel () {
		if ($this->renderMode == \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NO_LABEL)
			return '';
		$attrsStr = $this->renderAttrsWithFieldVars(
			['multiple'], $this->groupLabelAttrs, $this->groupLabelCssClasses, FALSE
		);
		$viewClass = $this->form->GetViewClass();
		/** @var $templates \stdClass */
		$templates = & static::$templates;
		return $viewClass::Format($templates->label, [
			'id'		=> $this->id,
			'label'		=> $this->label,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup`
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 *
	 * Render sub-controls with each sub-control label tag
	 * and without group label or without group specific errors.
	 * @param string $key
	 * @param string|array $option
	 * @return string
	 */
	public function RenderControlItem ($key, $option) {
		$result = '';
		$itemControlId = implode(\MvcCore\Ext\IForm::HTML_IDS_DELIMITER, [
			$this->form->GetId(), $this->name, $key
		]);
		list(
			$itemLabelText,
			$labelAttrsStr,
			$controlAttrsStr
		) = $this->renderControlItemCompleteAttrsClassesAndText($key, $option);
		if (!$this->form->GetFormTagRenderingStatus())
			$controlAttrsStr .= (strlen($controlAttrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		// render control, render label and put it together if necessary
		$checked = gettype($this->value) == 'array'
			? in_array($key, $this->value)
			: $this->value === $key;
		$viewClass = $this->form->GetViewClass();
		/** @var $templates \stdClass */
		$templates = & static::$templates;
		$itemControl = $viewClass::Format($templates->control, [
			'id'		=> $itemControlId,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars_decode(htmlspecialchars($key, ENT_QUOTES), ENT_QUOTES),
			'checked'	=> $checked ? ' checked="checked"' : '',
			'attrs'		=> strlen($controlAttrsStr) > 0 ? ' ' . $controlAttrsStr : '',
		]);
		if ($this->renderMode == \MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL) {
			// control and label
			$itemLabel = $viewClass::Format($templates->label, [
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
	 * Complete and return semi-finished strings for rendering by field key and option:
	 * - Label text string.
	 * - Label attributes string.
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
			$controlAttrsStr = $this->renderControlAttrsWithFieldVars(['accessKey', 'multiple']);
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
				['multiple'], $attrsArr, $classArr, FALSE
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				['multiple'], $attrsArr, $classArr, TRUE
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
