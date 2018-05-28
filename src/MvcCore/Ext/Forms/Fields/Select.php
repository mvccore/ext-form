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

class Select extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Size;
	use \MvcCore\Ext\Forms\Field\Attrs\Options;
	use \MvcCore\Ext\Forms\Field\Attrs\NullOptionText;

	protected $type = 'select';

	/**
	 *	@var string|array|NULL
	 */
	protected $value = NULL;
	
	protected $validators = array('ValueInOptions');

	protected static $templates = array(
		'control'		=> '<select id="{id}" name="{name}"{multiple}{size}{attrs}>{options}</select>',
		'option'		=> '<option value="{value}"{selected}{class}{attrs}>{text}</option>',
		'optionsGroup'	=> '<optgroup{label}{class}{attrs}>{options}</optgroup>',
	);
	
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->options) $this->thrownInvalidArgumentException(
			'No `options` property defined.'
		);
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
			: array();
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
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->multiple ? $this->name . '[]' : $this->name ,
			'multiple'	=> $this->multiple ? ' multiple="multiple"' : '',
			'size'		=> $this->multiple ? ' size="' . $this->size . '"' : '',
			'options'	=> $optionsStr,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}

	public function RenderControlOptions () {
		$result = '';
		$valueTypeIsArray = gettype($this->value) == 'array';
		if ($this->nullOptionText !== NULL && strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, css class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', array(
					'value' => '',
					'text' => $this->nullOptionText,
					'attrs' => array('disabled' => 'disabled')
				), $valueTypeIsArray
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
		return $formViewClass::Format(static::$templates->option, array(
			'value'		=> $key,
			'selected'	=> $selected ? ' selected="selected"' : '',
			'text'		=> $value,
			'class'		=> '', // to fill prepared template control place for attribute class with empty string
			'attrs'		=> '', // to fill prepared template control place for other attributes with empty string
		));
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
		return $formViewClass::Format(static::$templates->optionsGroup, array(
			'options'	=> $optionsStr,
			'label'		=> ' label="' . $label. '"',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr
		));
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
		return $formViewClass::Format(static::$templates->option, array(
			'value'		=> $value,
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr,
			'text'		=> $option['text'],
		));
	}
}
