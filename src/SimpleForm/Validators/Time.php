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

require_once(__DIR__.'/../../SimpleForm.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/View.php');
require_once('Date.php');

class SimpleForm_Validators_Time extends SimpleForm_Validators_Date
{
	public function Validate ($submitValue, $fieldName, SimpleForm_Core_Field & $field) {
		$submitValue = trim($submitValue);
		$safeValue = preg_replace("#[^0-9\:]#", '', $submitValue);
		// http://stackoverflow.com/questions/11296536/regex-for-time-validation
		@preg_match("#([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?#", $safeValue, $matches);
		if (!$matches || mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError($field, SimpleForm::$DefaultMessages[SimpleForm::TIME], function ($msg, $args) {
				return SimpleForm_Core_View::Format($msg, $args);
			});
		} else {
			$this->checkMinMax($field, $safeValue, SimpleForm::TIME_TO_LOW, SimpleForm::TIME_TO_HIGH);
		}
		return $safeValue;
	}
}
