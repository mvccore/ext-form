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
	public function Validate ($submitValue) {
		$result = NULL;
		if ($submitValue === NULL) 
			return NULL;
		$submitValue = trim((string) $submitValue);
		if ($submitValue === '') 
			return NULL;
		while (mb_strpos($submitValue, '%') !== FALSE)
			$submitValue = rawurldecode($submitValue);
		$safeValue = filter_var($submitValue, FILTER_VALIDATE_URL);
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
