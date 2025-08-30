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

namespace MvcCore\Ext\Forms\Field;

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field (mostly 
 * configurable) properties getter methods.
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait Getters {

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetId () {
		return $this->id;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetType () {
		return $this->type;
	}
	
	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetFieldsetName () {
		return $this->fieldsetName;
	}
	
	/**
	 * @inheritDoc
	 * @return ?int
	 */
	public function GetFieldOrder () {
		return $this->fieldOrder;
	}

	/**
	 * @inheritDoc
	 * @return string|int|float|array<string|int|float|null>|null
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * @inheritDoc
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetTitle () {
		return $this->title;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetControlAttrs () {
		return $this->controlAttrs;
	}

	/**
	 * @inheritDoc
	 * @param  string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * @inheritDoc
	 * @return \string[]|\MvcCore\Ext\Forms\Validator[]
	 */
	public function & GetValidators () {
		return $this->validators;
	}

	/**
	 * @inheritDoc
	 * @param  string|\MvcCore\Ext\Forms\Validator $validatorNameOrInstance
	 * @return bool
	 */
	public function HasValidator ($validatorNameOrInstance) {
		if (is_string($validatorNameOrInstance)) {
			$validatorClassName = $validatorNameOrInstance;
		} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
			$validatorClassName = get_class($validatorNameOrInstance);
		} else {
			return $this->throwNewInvalidArgumentException(
				'Unknown validator type given: `' . $validatorNameOrInstance 
				. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
			);
		}
		$slashPos = strrpos($validatorClassName, '\\');
		$validatorName = $slashPos !== FALSE 
			? substr($validatorClassName, $slashPos + 1)
			: $validatorClassName;
		return isset($this->validators[$validatorName]);
	}

	/**
	 * @inheritDoc
	 * @return bool|string|null
	 */
	public function GetViewScript () {
		return $this->viewScript;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetJsClassName () {
		return $this->jsClassName;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetJsSupportingFile () {
		return $this->jsSupportingFile;
	}

	/**
	 * @inheritDoc
	 * @return ?string
	 */
	public function GetCssSupportingFile () {
		return $this->cssSupportingFile;
	}

	/**
	 * @inheritDoc
	 * @var ?bool
	 */
	public function GetTranslate () {
		return $this->translate;
	}
	
	/**
	 * @inheritDoc
	 * @return array
	 */
	public static function & GetTemplates () {
		return (array) static::$templates;
	}
}
