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

class Float extends \MvcCore\Ext\Forms\Validators\Number
{
	/**
	 * Validate numeric raw user input. Parse numeric value by locale conventions
	 * and check if number is float.
	 * @param string|array			$submitValue Raw user input.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = $this->getNumericValue($rawSubmittedValue);
		if ($result === NULL) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::FLOAT)	
			);
			return NULL;
		} else {
			$result = floatval($result);
		}
		return $result;
	}
}
