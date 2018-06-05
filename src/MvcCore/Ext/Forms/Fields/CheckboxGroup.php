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

class CheckboxGroup 
	extends \MvcCore\Ext\Forms\FieldsGroup 
	implements \MvcCore\Ext\Forms\Fields\IOptions
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxOptions;

	protected $type = 'checkbox';

	protected $validators = ["ValueInOptions"];

	protected $jsClassName = 'MvcCoreForm.CheckboxGroup';

	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/checkbox-group.js';

	protected static $templates = [
		'control'	=> '<input id="{id}" name="{name}[]" type="{type}" value="{value}"{checked}{attrs} />',
	];

	public function GetMultiple () {
		return TRUE;
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
			// add minimal chosen options count validator
		if ($this->minOptionsCount > 0)
			$this->validators[] = 'MinOptions';
			// add minimal chosen options count validator
		if ($this->maxOptionsCount > 0)
			$this->validators[] = 'MaxOptions';
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$minOptsDefined = $this->minOptionsCount > 0;
		$maxOptsDefined = $this->maxOptionsCount > 0;
		$form = & $this->form;
		$viewClass = $form->GetViewClass();
		if ($this->translate) {
			if ($minOptsDefined) {
				// add necessary error messages if there are empty strings
				if (!$this->minOptionsBubbleMessage)
					$this->minOptionsBubbleMessage = $form->GetDefaultErrorMsg(
						\MvcCore\Ext\Forms\IError::CHOOSE_MIN_OPTS_BUBBLE
					);
				$this->minOptionsBubbleMessage = $form->Translate($this->minOptionsBubbleMessage);
			}
			if ($maxOptsDefined) {
				// add necessary error messages if there are empty strings
				if (!$this->maxOptionsBubbleMessage)
					$this->maxOptionsBubbleMessage = $form->GetDefaultErrorMsg(
						\MvcCore\Ext\Forms\IError::CHOOSE_MAX_OPTS_BUBBLE
					);
				$this->maxOptionsBubbleMessage = $form->Translate($this->maxOptionsBubbleMessage);
			}
		}
		if ($minOptsDefined) $this->minOptionsBubbleMessage = $viewClass::Format(
			$this->minOptionsBubbleMessage, [$this->minOptionsCount]
		);
		if ($maxOptsDefined) $this->maxOptionsBubbleMessage = $viewClass::Format(
			$this->maxOptionsBubbleMessage, [$this->maxOptionsCount]
		);
		if ($this->required || $minOptsDefined || $maxOptsDefined)
			$form->AddJsSupportFile(
				$this->jsSupportingFile, 
				$this->jsClassName, 
				[
					$this->name . '[]', 
					$this->required,
					$this->minOptionsCount,
					$this->maxOptionsCount,
					$this->minOptionsBubbleMessage,
					$this->maxOptionsBubbleMessage,
					$this->maxOptionsClassName
				]
			);
		return $this;
	}

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
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				[], array_merge($this->controlAttrs, [
					'data-min-selected-options' => $this->minOptionsCount,
					'data-max-selected-options' => $this->maxOptionsCount,
				]), $this->cssClasses, TRUE
			);
		} else if ($optionType == 'array') {
			$itemLabelText = isset($option['text']) ? $option['text'] : $key;
			$attrsArr = $this->controlAttrs;
			$classArr = $this->cssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->controlAttrs, $option['attrs']);
			}
			$attrsArr = array_merge($attrsArr, [
				'data-min-selected-options' => $this->minOptionsCount,
				'data-max-selected-options' => $this->maxOptionsCount,
			]);
			if (isset($option['class'])) {
				$classArrParam = [];
				$cssClassType = gettype($option['class']);
				if ($cssClassType == 'array') {
					$classArrParam = $option['class'];
				} else if ($cssClassType == 'string') {
					$classArrParam = explode(' ', $option['class']);
				}
				foreach ($classArrParam as $clsValue) 
					if ($clsValue) $classArr[] = $clsValue;
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
		return [$itemLabelText, $labelAttrsStr, $controlAttrsStr];
	}
}
