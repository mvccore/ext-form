<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once('/../Core/Validator.php');
require_once('/../Core/Field.php');

class SimpleForm_Validators_RangeField extends SimpleForm_Core_Validator
{
	public function Validate ($submitValue, $fieldName, SimpleForm_Core_Field & $field) {
		$validatorInstance = SimpleForm_Core_Validator::Create('NumberField', $field->Form);
		if ($field->Multiple) {
			$submitValues = is_array($submitValue) ? $submitValue : explode(',',$submitValue);
			$result = array();
			foreach ($submitValues as $item) {
				$result[] = $validatorInstance->Validate(
					$item, $fieldName, $field
				);
			}
			return $result;
		} else {
			return $validatorInstance->Validate(
				$submitValue, $fieldName, $field
			);
		}
	}
}
