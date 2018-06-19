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

namespace MvcCore\Ext\Forms\Fields;

class Select 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IAccessKey, 
				\MvcCore\Ext\Forms\Fields\ITabIndex,
				\MvcCore\Ext\Forms\Fields\IMultiple, 
				\MvcCore\Ext\Forms\Fields\IOptions, 
				\MvcCore\Ext\Forms\Fields\IMinMaxOptions
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;
	use \MvcCore\Ext\Forms\Field\Attrs\TabIndex;
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Options;
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxOptions;
	use \MvcCore\Ext\Forms\Field\Attrs\NullOptionText;
	use \MvcCore\Ext\Forms\Field\Attrs\Size;

	protected $type = 'select';

	/**
	 *	@var string|array|NULL
	 */
	protected $value = NULL;
	
	protected $validators = ['ValueInOptions'];

	protected static $templates = [
		'control'		=> '<select id="{id}" name="{name}"{size}{attrs}>{options}</select>',
		'option'		=> '<option value="{value}"{selected}{class}{attrs}>{text}</option>',
		'optionsGroup'	=> '<optgroup{label}{class}{attrs}>{options}</optgroup>',
	];

	/**
	 * If select has multiple attribute, this function
	 * returns `array` of strings. If select has not multiple
	 * attribute, this function returns `string`.
	 * If there is no value, function return `NULL`.
	 * @return array|string|NULL
	 */
	public function GetValue () {
		return $this->value;
	}
	
	/**
	 * If select has multiple attribute, set to this function
	 * `array` of strings. If select has not multiple
	 * attribute, set to this function `string`.
	 * If you don't want any selected value, set `NULL`.
	 * @param array|string|NULL $value
	 * @return \MvcCore\Ext\Forms\Fields\Select
	 */
	public function & SetValue ($value) {
		$this->value = $value;
		return $this;
	}	
	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->options) $this->throwNewInvalidArgumentException(
			'No `options` property defined.'
		);
		// add minimum/maximum options count validator if necessary
		$this->setFormMinMaxOptions();
	}
	public function PreDispatch () {
		parent::PreDispatch();
		if (!$this->translate) return;
		$form = & $this->form;
		if ($this->nullOptionText !== NULL && $this->nullOptionText !== '')
			$this->nullOptionText = $form->translate($this->nullOptionText);
		foreach ($this->options as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				if ($value) 
					$options[$key] = $form->Translate((string)$value);
			} else if ($valueType == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$this->preDispatchTranslateOptionOptGroup($value);
				} else {
					// advanced configuration with key, text, css class, and any other attributes for single option tag
					$valueText = isset($value['text']) ? $value['text'] : $key;
					if ($valueText) $value['text'] = $form->Translate((string) $valueText);
				}
			}
		}
	}
	protected function preDispatchTranslateOptionOptGroup (& $optionsGroup) {
		$form = & $this->form;
		$groupLabel = isset($optionsGroup['label']) 
			? $optionsGroup['label'] 
			: '';
		if ($groupLabel)
			$optionsGroup['label'] = $form->Translate((string) $groupLabel);
		$groupOptions = $optionsGroup['options'] 
			? $optionsGroup['options'] 
			: [];
		foreach ($groupOptions as $key => & $groupOption) {
			$groupOptionType = gettype($groupOption);
			if ($groupOptionType == 'string') {
				// most simple key/value array options configuration
				if ($groupOption) 
					$optionsGroup['options'][$key] = $form->Translate((string) $groupOption);
			} else if ($groupOptionType == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$valueText = isset($groupOption['text']) ? $groupOption['text'] : $key;
				if ($valueText) $groupOption['text'] = $this->form->Translate((string) $valueText);
			}
		}
	}

	public function RenderControl () {
		$optionsStr = $this->RenderControlOptions();
		if ($this->multiple) {
			$this->multiple = 'multiple';
			$name = $this->name . '[]';
			$size = $this->size !== NULL ? ' size="' . $this->size . '"' : '';
		} else {
			$name = $this->name;
			$size = '';
		}
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accessKey', 
			'tabIndex',
			'multiple',
		]);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $name,
			'size'		=> $size,
			'options'	=> $optionsStr,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}

	public function RenderControlOptions () {
		$result = '';
		$valueTypeIsArray = gettype($this->value) == 'array';
		if ($this->nullOptionText !== NULL && strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, css class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', [
					'value'	=> '',
					'text'	=> htmlspecialchars($this->nullOptionText, ENT_QUOTES),
					'attrs'	=> ['disabled' => 'disabled']
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				$result .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if ($valueType == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$result .= $this->renderControlOptionsGroup($value, $valueTypeIsArray);
				} else {
					// advanced configuration with key, text, cs class, and any other attributes for single option tag
					$result .= $this->renderControlOptionsAdvanced($key, $value, $valueTypeIsArray);
				}
			}
		}
		return $result;
	}
	
	protected function renderControlOptionKeyValue ($key, & $value, $valueTypeIsArray) {
		$selected = $valueTypeIsArray
			? in_array($key, $this->value)
			: $this->value === $key ;
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->option, [
			'value'		=> htmlspecialchars($key, ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'text'		=> htmlspecialchars($value, ENT_QUOTES),
			'class'		=> '', // to fill prepared template control place for attribute class with empty string
			'attrs'		=> '', // to fill prepared template control place for other attributes with empty string
		]);
	}

	protected function renderControlOptionsGroup (& $optionsGroup, $valueTypeIsArray) {
		$optionsStr = '';
		foreach ($optionsGroup['options'] as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				$optionsStr .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if ($valueType == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$optionsStr .= $this->renderControlOptionsAdvanced($key, $value, $valueTypeIsArray);
			}
		}
		$label = isset($optionsGroup['label']) && strlen((string) $optionsGroup['label']) > 0
			? $optionsGroup['label']
			: NULL;
		if (!$optionsStr && !$label) return '';
		$formViewClass = $this->form->GetViewClass();
		$classStr = isset($optionsGroup['class']) && strlen((string) $optionsGroup['class'])
			? ' class="' . $optionsGroup['class'] . '"'
			: '';
		$attrsStr = isset($optionsGroup['attrs']) 
			? ' ' . $formViewClass::RenderAttrs($optionsGroup['attrs']) 
			: '';
		return $formViewClass::Format(static::$templates->optionsGroup, [
			'options'	=> $optionsStr,
			'label'		=> ' label="' . $label. '"',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr
		]);
	}

	protected function renderControlOptionsAdvanced ($key, $option, $valueTypeIsArray) {
		$value = isset($option['value']) 
			? $option['value'] 
			: $key;
		$selected = $valueTypeIsArray
			? in_array($key, $this->value)
			: $this->value === $key;
		$formViewClass = $this->form->GetViewClass();
		$classStr = isset($option['class']) && strlen((string) $option['class'])
			? ' class="' . $option['class'] . '"'
			: '';
		$attrsStr = isset($option['attrs']) 
			? ' ' . $formViewClass::RenderAttrs($option['attrs']) 
			: '';
		return $formViewClass::Format(static::$templates->option, [
			'value'		=> htmlspecialchars($value, ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr,
			'text'		=> htmlspecialchars($option['text'], ENT_QUOTES),
		]);
	}
}
