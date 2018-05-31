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

class Url extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Validate URI string by PHP `filter_var($rawSubmittedValue, FILTER_VALIDATE_URL);`.
	 * @param string|array $submitValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = NULL;
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		if ($rawSubmittedValue === '') 
			return NULL;
		while (mb_strpos($rawSubmittedValue, '%') !== FALSE)
			$rawSubmittedValue = rawurldecode($rawSubmittedValue);
		$safeValue = filter_var($rawSubmittedValue, FILTER_VALIDATE_URL);
		if ($safeValue !== FALSE) {
			$result = $safeValue;
		} else {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::URL)
			);
		}
		return $result;
	}
}
