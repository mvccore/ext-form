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
 */
trait Getters {

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetId () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->id;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetName () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->name;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetType () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->type;
	}

	/**
	 * @inheritDocs
	 * @return string|int|float|\string[]|\int[]|\float[]|array|NULL
	 */
	public function GetValue () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->value;
	}

	/**
	 * @inheritDocs
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->cssClasses;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetTitle () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->title;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetControlAttrs () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->controlAttrs;
	}

	/**
	 * @inheritDocs
	 * @param string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*') {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * @inheritDocs
	 * @return \string[]|\MvcCore\Ext\Forms\Validator[]
	 */
	public function & GetValidators () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->validators;
	}

	/**
	 * @inheritDocs
	 * @param string|\MvcCore\Ext\Forms\Validator $validatorNameOrInstance
	 * @return bool
	 */
	public function HasValidator ($validatorNameOrInstance) {
		/** @var $this \MvcCore\Ext\Forms\Field */
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
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->viewScript;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetJsClassName () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->jsClassName;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetJsSupportingFile () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->jsSupportingFile;
	}

	/**
	 * @inheritDocs
	 * @return string|NULL
	 */
	public function GetCssSupportingFile () {
		/** @var $this \MvcCore\Ext\Forms\Field */
		return $this->cssSupportingFile;
	}

	/**
	 * @inheritDocs
	 * @var bool
	 */
	public function GetTranslate () {
		/** @var $this \MvcCore\Ext\Forms\Field */
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
