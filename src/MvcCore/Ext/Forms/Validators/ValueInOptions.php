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

namespace MvcCore\Ext\Forms\Validators;

class ValueInOptions extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Field has to implement for this validator two methods:
	 * - `GetAllOptionsKeys()` - To get all keys from options array as `\string[]`
	 * - `GetMultiple()` - To get boolean flag if there could be more submitted options or not.
	 * @var \MvcCore\Ext\Forms\Fields\IOptions
	 */
	protected $field = NULL;

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * Check if given field implements `\MvcCore\Ext\Forms\Fields\IOptions`.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IOptions) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `ValueInOptions` validator, it has to implement '
				.'interface `'.\MvcCore\Ext\Forms\Fields\IOptions::class.'`.'
			);
		return parent::SetField($field);
	}

	/**
	 * Return array with only submitted values from options keys
	 * or return string which exists as key in options or `NULL` 
	 * if submitted value is `NULL`. Add error if submitted value 
	 * is not the same as value after existence check.
	 * @param string|array			$submitValue
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		list($result, $multiple) = $this->completeSafeValueByOptions($rawSubmittedValue);
		if (
			($multiple && count($result) !== count($rawSubmittedValue)) ||
			(!$multiple && $result === NULL)
		) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::VALID)
			);
		}
		return $result;
	}

	/**
	 * Return safe value(s), which exist(s) in field options 
	 * and return boolean (`TRUE`) if result is array or not.
	 * Example: `list($safeValue, $multiple) = $this->completeSafeValueByOptions($rawSubmittedValue);`;
	 * @param string|array $rawSubmittedValue 
	 * @return array
	 */
	protected function completeSafeValueByOptions ($rawSubmittedValue) {
		$multiple = $this->field->GetMultiple();
		$result = $multiple ? array() : NULL;
		$rawSubmittedValueArrayType = gettype($rawSubmittedValue) == 'array';
		if ($rawSubmittedValueArrayType) {
			if ($multiple) {
				$rawSubmittedValues = $rawSubmittedValue;
			} else {
				$rawSubmittedValue = (string) $rawSubmittedValue;
				$rawSubmittedValues = mb_strlen($rawSubmittedValue) > 0 
					? array($rawSubmittedValue) 
					: array();
			}
		} else {
			$rawSubmittedValue = (string) $rawSubmittedValue;
			$rawSubmittedValues = mb_strlen($rawSubmittedValue) > 0 
				? array($rawSubmittedValue) 
				: array();
		}
		$allOptionKeys = $this->field->GetAllOptionsKeys();
		foreach ($rawSubmittedValues as & $rawSubmittedValueItem) {
			if (in_array($rawSubmittedValueItem, $allOptionKeys)) {
				if ($multiple) {
					$result[] = $rawSubmittedValueItem;
				} else {
					$result = $rawSubmittedValueItem;
				}
				if (!$multiple) break;
			}
		}
		return array(
			$result, 
			$multiple
		);
	}
}
