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
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/Exception.php');
require_once(__DIR__.'/../Core/View.php');

class SimpleForm_Validators_Integer extends SimpleForm_Core_Validator
{
	public function Validate ($submitValue, $fieldName, SimpleForm_Core_Field & $field) {
		$submitValue = trim($submitValue);
		$safeValue = preg_replace("#[^0-9]#", '', $submitValue);
		if (mb_strlen($safeValue) !== mb_strlen($submitValue)) {
			$this->addError($field, SimpleForm::$DefaultMessages[SimpleForm::INTEGER], function ($msg, $args) {
				return SimpleForm_Core_View::Format($msg, $args);
			});
		}
		return $safeValue;
	}
}
