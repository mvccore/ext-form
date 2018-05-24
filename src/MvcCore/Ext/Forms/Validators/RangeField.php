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

require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class RangeField extends Core\Validator
{
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Forms\IField & $field) {
		$validatorInstance = Core\Validator::Create($field->Form, 'NumberField');
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
