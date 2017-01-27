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

require_once(__DIR__.'/../Form.php');
require_once('Core/Field.php');
//require_once('Core/Exception.php');
//require_once('Core/View.php');

namespace MvcCore\Ext\Form;

class Select extends Core\Field
{
	public $Type = 'select';
	/** @var string|array */
	public $Value = '';
	public $Multiple = FALSE;
	public $Size = 3;
	public $FirstOptionText = '';
	public $Options = array();
	public $Validators = array('ValueInOptions');
	protected static $templates = array(
		'control'	=> '<select id="{id}" name="{name}"{multiple}{size}{attrs}>{options}</select>',
		'option'	=> '<option value="{value}"{selected}{class}{attrs}>{text}</option>',
		'optgroup'	=> '<optgroup{label}>{options}</optgroup>',
	);
	/* setters *******************************************************************************/
	public function SetMultiple ($multiple) {
		$this->Multiple = $multiple;
		return $this;
	}
	public function SetSize ($size) {
		$this->Size = $size;
		return $this;
	}
	public function SetFirstOptionText ($firstOptionText) {
		if ($this->Translate && $firstOptionText) {
			$firstOptionText = $this->Form->Translator((string)$firstOptionText, $this->Form->Lang);
		}
		$this->FirstOptionText = $firstOptionText;
		return $this;
	}
	public function SetOptions (array $options = array()) {
		$this->Options = $options;
		return $this;
	}
	/* core methods **************************************************************************/
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge((array)parent::$templates, (array)self::$templates);
	}
	public function OnAdded (\MvcCore\Ext\Form & $form) {
		parent::OnAdded($form);
		if (!$this->Options) {
			$clsName = get_class($this);
			include_once('Core/Exception.php');
			throw new Core\Exception("No 'Options' defined for form field: '$clsName'.");
		}
	}
	public function SetUp () {
		parent::SetUp();
		if (!$this->Translate) return;
		$lang = $this->Form->Lang;
		$translator = $this->Form->Translator;
		foreach ($this->Options as $key => $value) {
			if (gettype($value) == 'string') {
				// most simple key/value array options configuration
				if ($value) $options[$key] = call_user_func($translator, (string)$value, $lang);
			} else if (gettype($value) == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$this->setUpTranslateOptionOptGroup($value);
				} else {
					// advanced configuration with key, text, css class, and any other attributes for single option tag
					$this->setUpTranslateOptionsAdvanced($key, $value);
				}
			}
		}
	}
	protected function setUpTranslateOptionOptGroup (& $optGroup) {
		$lang = $this->Form->Lang;
		$translator = $this->Form->Translator;
		$label = isset($optGroup['label']) ? $optGroup['label'] : '';
		if ($label) {
			$optGroup['label'] = call_user_func($translator, (string)$label, $lang);
		}
		$options = $optGroup['options'] ? $optGroup['options'] : array();
		foreach ($options as $key => $value) {
			if (gettype($value) == 'string') {
				// most simple key/value array options configuration
				if ($value) $optGroup['options'][$key] = call_user_func($translator, (string)$value, $lang);
			} else if (gettype($value) == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$this->setUpTranslateOptionsAdvanced($key, $value);
			}
		}
	}
	protected function setUpTranslateOptionsAdvanced (& $key, & $option) {
		$optObj = (object) $option;
		$text = isset($optObj->text) ? $optObj->text : $key;
		if ($this->Translate && $text) {
			$option['text'] = call_user_func($this->Form->Translator, (string)$text, $this->Form->Lang);
		}
	}
	/* rendering ******************************************************************************/
	public function RenderControl () {
		$optionsStr = $this->RenderControlOptions();
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Multiple ? $this->Name . '[]' : $this->Name , 
			'multiple'	=> $this->Multiple ? ' multiple="multiple"' : '',
			'size'		=> $this->Multiple ? ' size="' . $this->Size . '"' : '',
			'options'	=> $optionsStr,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
	public function RenderControlOptions () {
		$result = '';
		if ($this->FirstOptionText) {
			// advanced configuration with key, text, cs class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', array(
					'value' => '', 
					'text' => $this->FirstOptionText, 
					'attrs' => array('disabled' => 'disabled')
				)
			);
		}
		foreach ($this->Options as $key => $value) {
			if (gettype($value) == 'string') {
				// most simple key/value array options configuration
				$result .= $this->renderControlOptionKeyValue($key, $value);
			} else if (gettype($value) == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$result .= $this->renderControlOptionOptGroup($value);
				} else {
					// advanced configuration with key, text, cs class, and any other attributes for single option tag
					$result .= $this->renderControlOptionsAdvanced($key, $value);
				}
			}
		}
		return $result;
	}
	/* protected renderers *******************************************************************/
	protected function renderControlOptionKeyValue (& $key, & $value) {
		$selected = FALSE;
		if (gettype($this->Value) == 'array') {
			$selected = in_array($key, $this->Value);
		} else {
			$selected = $this->Value === $key;
		}
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->option, array(
			'value'		=> $key,
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> '',
			'attrs'		=> '',
			'text'		=> $value,
		));
	}
	protected function renderControlOptionOptGroup (& $optGroup) {
		$optionsStr = "";
		foreach ($optGroup['options'] as $key => $value) {
			if (gettype($value) == 'string') {
				// most simple key/value array options configuration
				$optionsStr .= $this->renderControlOptionKeyValue($key, $value);
			} else if (gettype($value) == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$optionsStr .= $this->renderControlOptionsAdvanced($key, $value);
			}
		}
		$label = isset($optGroup['label']) ? $optGroup['label'] : '';
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->optgroup, array(
			'label'		=> $label ? ' label="' . $label . '"' : '',
			'options'	=> $optionsStr
		));
	}
	protected function renderControlOptionsAdvanced ($key, $option) {
		$optObj = (object) $option;
		$value = isset($optObj->value) ? $optObj->value : $key;
		$view = $this->Form->View;
		$selected = FALSE;
		if (gettype($this->Value) == 'array') {
			$selected = in_array($key, $this->Value);
		} else {
			$selected = $this->Value === $key;
		}
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->option, array(
			'value'		=> $value,
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> isset($option['class']) ? ' class="' . $option['class'] . '"' : '',
			'attrs'		=> isset($optObj->attrs) ? ' ' . $view->RenderAttrs($optObj->attrs) : '',
			'text'		=> $optObj->text,
		));
	}
}
