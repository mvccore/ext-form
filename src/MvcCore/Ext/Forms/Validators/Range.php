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

class Range extends \MvcCore\Ext\Forms\Validators\Number
{
	/**
	 * Validate numeric raw user input. Parse numeric value or values by locale conventions
	 * and check minimum, maximum and step if necessary.
	 * @param string|array			$submitValue Raw user input.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$multiple = $this->field instanceof \MvcCore\Ext\Forms\Field\Attrs\Multiple &&
			$this->field->GetMultiple();
		if ($multiple) {
			$rawSubmitValues = is_array($rawSubmittedValue) 
				? $rawSubmittedValue 
				: explode(',', (string) $rawSubmittedValue);
			$result = array();
			foreach ($rawSubmitValues as $rawSubmitValue) 
				$result[] = parent::Validate($rawSubmitValue);
			return $result;
		} else {
			return parent::Validate((string) $rawSubmittedValue);
		}
	}
}
