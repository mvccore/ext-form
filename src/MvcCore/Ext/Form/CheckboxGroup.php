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

namespace MvcCore\Ext\Form;

require_once(__DIR__.'/../Form.php');
require_once('Core/FieldGroup.php');
//require_once('Core/View.php');

use MvcCore\Ext;

class CheckboxGroup extends Core\FieldGroup
{
	public $Type = 'checkbox';
	public $MinSelectedOptionsCount = 0;
	public $MinSelectedOptionsMessage = '';
	public $MinSelectedOptionsBubbleMessage = '';
	public $MaxSelectedOptionsCount = 0;
	public $MaxSelectedOptionsMessage = '';
	public $MaxSelectedOptionsBubbleMessage = '';
	public $MaxSelectedOptionsClassName = 'max-selected-options';
	public $Validators = array("ValueInOptions");
	public $JsClass = 'MvcCoreForm.CheckboxGroup';
	public $Js = '__MVCCORE_FORM_DIR__/fields/checkbox-group.js';
	
	protected static $templates = array(
		'control'			=> '<input id="{id}" name="{name}[]" type="{type}" value="{value}"{checked}{attrs} />',
	);
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge((array)parent::$templates, (array)self::$templates);
	}
	public function SetMinSelectedOptionsCount ($minSelectedOptionsCount) {
		$this->MinSelectedOptionsCount = $minSelectedOptionsCount;
		return $this;
	}
	public function SetMinSelectedOptionsMessage ($minSelectedOptionsMessage) {
		$this->MinSelectedOptionsMessage = $minSelectedOptionsMessage;
		return $this;
	}
	public function SetMinSelectedOptionsBubbleMessage ($minSelectedOptionsBubbleMessage) {
		$this->MinSelectedOptionsBubbleMessage = $minSelectedOptionsBubbleMessage;
		return $this;
	}
	public function SetMaxSelectedOptionsCount ($maxSelectedOptionsCount) {
		$this->MaxSelectedOptionsCount = $maxSelectedOptionsCount;
		return $this;
	}
	public function SetMaxSelectedOptionsMessage ($maxSelectedOptionsMessage) {
		$this->MaxSelectedOptionsMessage = $maxSelectedOptionsMessage;
		return $this;
	}
	public function SetMaxSelectedOptionsBubbleMessage ($maxSelectedOptionsBubbleMessage) {
		$this->MaxSelectedOptionsBubbleMessage = $maxSelectedOptionsBubbleMessage;
		return $this;
	}
	public function OnAdded (\MvcCore\Ext\Form & $form) {
		parent::OnAdded($form);
		$this->MinSelectedOptionsCount = min($this->MinSelectedOptionsCount, count($this->Options));
		if ($this->MinSelectedOptionsCount > 0) {
			// add minimal chosen options count validator
			$this->Validators[] = 'MinSelectedOptions';
			// add necessary error messages if there are empty strings
			if (mb_strlen($this->MinSelectedOptionsMessage) === 0) {
				$this->MinSelectedOptionsMessage = Ext\Form::$DefaultMessages[Ext\Form::CHOOSE_MIN_OPTS];
			}
			if (mb_strlen($this->MinSelectedOptionsBubbleMessage) === 0) {
				$this->MinSelectedOptionsBubbleMessage = Ext\Form::$DefaultMessages[Ext\Form::CHOOSE_MIN_OPTS_BUBBLE];
			}
		}
		if ($this->MaxSelectedOptionsCount > 0) {
			// add minimal chosen options count validator
			$this->Validators[] = 'MaxSelectedOptions';
			// add necessary error messages if there are empty strings
			if (mb_strlen($this->MaxSelectedOptionsMessage) === 0) {
				$this->MaxSelectedOptionsMessage = Ext\Form::$DefaultMessages[Ext\Form::CHOOSE_MAX_OPTS];
			}
			if (mb_strlen($this->MaxSelectedOptionsBubbleMessage) === 0) {
				$this->MaxSelectedOptionsBubbleMessage = Ext\Form::$DefaultMessages[Ext\Form::CHOOSE_MAX_OPTS_BUBBLE];
			}
		}
	}
	protected function renderControlItemCompleteAttrsClassesAndText ($key, $option) {
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->Required;
		if ($this->Type == 'checkbox') $this->Required = FALSE;
		if ($optionType == 'string') {
			$itemLabelText = $option ? $option : $key;
			$labelAttrsStr = $this->renderLabelAttrsWithFieldVars();
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				array(), array_merge($this->ControlAttrs, array(
					'data-min-selected-options' => $this->MinSelectedOptionsCount,
					'data-max-selected-options' => $this->MaxSelectedOptionsCount,
				)), $this->CssClasses, TRUE
			);
		} else if ($optionType == 'array') {
			$itemLabelText = $option['text'] ? $option['text'] : $key;
			$attrsArr = $this->ControlAttrs;
			$classArr = $this->CssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->ControlAttrs, $option['attrs']);
			}
			$attrsArr = array_merge($attrsArr, array(
				'data-min-selected-options' => $this->MinSelectedOptionsCount,
				'data-max-selected-options' => $this->MaxSelectedOptionsCount,
			));
			if (isset($option['class'])) {
				$classArrParam = array();
				if (gettype($option['class']) == 'array') {
					$classArrParam = $option['class'];
				} else if (gettype($option['class']) == 'string') {
					$classArrParam = explode(' ', $option['class']);
				}
				foreach ($classArrParam as $clsValue) if ($clsValue) $classArr[] = $clsValue;
			}
			$labelAttrsStr = $this->renderAttrsWithFieldVars(
				array(), $attrsArr, $classArr
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				array(), $attrsArr, $classArr, TRUE
			);
		}
		if ($this->Type == 'checkbox') $this->Required = $originalRequired;
		return array($itemLabelText, $labelAttrsStr, $controlAttrsStr);
	}

	public function SetUp () {
		parent::SetUp();
		if ($this->MinSelectedOptionsCount > 0) {
			include_once('Core/View.php');
			if ($this->Translate) {
				$translator = $this->Form->Translator;
				$this->MinSelectedOptionsBubbleMessage = call_user_func($translator, $this->MinSelectedOptionsBubbleMessage);
				$this->MaxSelectedOptionsBubbleMessage = call_user_func($translator, $this->MaxSelectedOptionsBubbleMessage);
			}
			$this->MinSelectedOptionsBubbleMessage = Core\View::Format(
				$this->MinSelectedOptionsBubbleMessage, array($this->MinSelectedOptionsCount)
			);
			$this->MaxSelectedOptionsBubbleMessage = Core\View::Format(
				$this->MaxSelectedOptionsBubbleMessage, array($this->MaxSelectedOptionsCount)
			);
		}
		if ($this->Required || $this->MinSelectedOptionsCount > 0 || $this->MaxSelectedOptionsCount > 0) {
			$params = array($this->Name . '[]', $this->Required);
			if ($this->MinSelectedOptionsCount > 0) $params[2] = $this->MinSelectedOptionsCount;
			if ($this->MaxSelectedOptionsCount > 0) $params[3] = $this->MaxSelectedOptionsCount;
			if ($this->MinSelectedOptionsBubbleMessage) $params[4] = str_replace('{0}', $this->MinSelectedOptionsCount, addslashes($this->MinSelectedOptionsBubbleMessage));
			if ($this->MaxSelectedOptionsBubbleMessage) $params[5] = str_replace('{0}', $this->MaxSelectedOptionsCount, addslashes($this->MaxSelectedOptionsBubbleMessage));
			if ($this->MaxSelectedOptionsClassName != 'max-selected-options') $params[6] = $this->MaxSelectedOptionsClassName;
			$this->Form->AddJs($this->Js, $this->JsClass, $params);
		}
	}
}