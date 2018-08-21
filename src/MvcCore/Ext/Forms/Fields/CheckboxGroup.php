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
	extends		\MvcCore\Ext\Forms\FieldsGroup 
	implements	\MvcCore\Ext\Forms\Fields\IMinMaxOptions
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxOptions;
	
	/**
	 * Valid email address error message index.
	 * @var int
	 */
	const ERROR_MIN_OPTIONS_BUBBLE = 0;
	const ERROR_MAX_OPTIONS_BUBBLE = 1;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_MIN_OPTIONS_BUBBLE	=> "Please select at least `{0}` options as minimum.",
		self::ERROR_MAX_OPTIONS_BUBBLE	=> "Please select up to `{0}` options at maximum.",
	];

	protected $type = 'checkbox';

	protected $validators = ["ValueInOptions"];

	protected $jsClassName = 'MvcCoreForm.CheckboxGroup';

	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/checkbox-group.js';

	/**
	 * Maximum options css class for javascript.
	 * @var string
	 */
	protected $maxOptionsClassName = 'max-selected-options';

	protected static $templates = [
		'control'	=> '<input id="{id}" name="{name}[]" type="{type}" value="{value}"{checked}{attrs} />',
	];

	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		// add minimum/maximum options count validator if necessary
		$this->setFormMinMaxOptions();
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$minOptsDefined = $this->minOptions !== NULL;
		$maxOptsDefined = $this->maxOptions !== NULL;
		$form = & $this->form;
		$viewClass = $form->GetViewClass();
		if ($this->translate) {
			if ($minOptsDefined) {
				// add necessary error messages if there are empty strings
				if (!$this->minOptionsBubbleMessage)
					$this->minOptionsBubbleMessage = $form->GetDefaultErrorMsg(
						static::$errorMessages[static::ERROR_MIN_OPTIONS_BUBBLE]
					);
				$this->minOptionsBubbleMessage = $form->Translate($this->minOptionsBubbleMessage);
			}
			if ($maxOptsDefined) {
				// add necessary error messages if there are empty strings
				if (!$this->maxOptionsBubbleMessage)
					$this->maxOptionsBubbleMessage = $form->GetDefaultErrorMsg(
						static::$errorMessages[static::ERROR_MAX_OPTIONS_BUBBLE]
					);
				$this->maxOptionsBubbleMessage = $form->Translate($this->maxOptionsBubbleMessage);
			}
		}
		if ($minOptsDefined) $this->minOptionsBubbleMessage = $viewClass::Format(
			$this->minOptionsBubbleMessage, [$this->minOptions]
		);
		if ($maxOptsDefined) $this->maxOptionsBubbleMessage = $viewClass::Format(
			$this->maxOptionsBubbleMessage, [$this->maxOptions]
		);
		if ($this->required || $minOptsDefined || $maxOptsDefined)
			$form->AddJsSupportFile(
				$this->jsSupportingFile, 
				$this->jsClassName, 
				[
					$this->name . '[]', 
					$this->required,
					$this->minOptions,
					$this->maxOptions,
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
					'data-min-selected-options' => $this->minOptions,
					'data-max-selected-options' => $this->maxOptions,
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
				'data-min-selected-options' => $this->minOptions,
				'data-max-selected-options' => $this->maxOptions,
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
