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
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetId () {
		return $this->id;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetType () {
		return $this->type;
	}
	
	/**
	 * @inheritDocs
	 * @return int|NULL
	 */
	public function GetFieldOrder () {
		return $this->fieldOrder;
	}

	/**
	 * @inheritDocs
	 * @return string|int|float|\string[]|\int[]|\float[]|array|NULL
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetTitle () {
		return $this->title;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetControlAttrs () {
		return $this->controlAttrs;
	}

	/**
	 * @inheritDocs
	 * @param  string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * @inheritDocs
	 * @return \string[]|\MvcCore\Ext\Forms\Validator[]
	 */
	public function & GetValidators () {
		return $this->validators;
	}

	/**
	 * @inheritDocs
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
	 * @inheritDocs
	 * @return bool|string|NULL
	 */
	public function GetViewScript () {
		return $this->viewScript;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetJsClassName () {
		return $this->jsClassName;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetJsSupportingFile () {
		return $this->jsSupportingFile;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetCssSupportingFile () {
		return $this->cssSupportingFile;
	}

	/**
	 * @inheritDocs
	 * @var bool
	 */
	public function GetTranslate () {
		return $this->translate;
	}
	
	/**
	 * @inheritDocs
	 * @return array
	 */
	public static function & GetTemplates () {
		return (array) static::$templates;
	}
}
